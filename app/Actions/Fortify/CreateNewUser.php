<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Domain\Identity\Actions\RegisterUser;
use App\Domain\Identity\AgeGate;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as ValidatorInstance;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly AgeGate $ageGate,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $input['username'] = Str::lower(trim((string) ($input['username'] ?? '')));

        $validator = Validator::make($input, [
            ...$this->profileRules(),
            'username' => $this->usernameRules(),
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'guardian_email' => ['nullable', 'string', 'email', 'max:255', 'different:email'],
            'timezone' => ['nullable', 'string', 'timezone:all'],
            'wants_teacher_access' => ['sometimes', 'boolean'],
            'terms' => ['accepted'],
            'password' => $this->passwordRules(),
        ], [
            'terms.accepted' => 'Please accept the Terms and Privacy Policy to continue.',
            'guardian_email.different' => 'Please enter a parent or guardian\'s email, not your own.',
        ]);

        $validator->after(function (ValidatorInstance $validator) use ($input) {
            if ($validator->errors()->has('date_of_birth')) {
                return;
            }

            $dob = Carbon::createFromFormat('!Y-m-d', (string) $input['date_of_birth']);

            if (! $this->ageGate->meetsMinimumAge($dob)) {
                $validator->errors()->add('date_of_birth', 'Please check your date of birth.');

                return;
            }

            if ($this->ageGate->needsParentalConsent($dob) && blank($input['guardian_email'] ?? null)) {
                $validator->errors()->add(
                    'guardian_email',
                    'Because you are under '.$this->ageGate->consentAge().', we need a parent or guardian\'s email to approve your account.',
                );
            }
        });

        $validated = $validator->validate();

        return $this->registerUser->handle([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'date_of_birth' => $validated['date_of_birth'],
            'timezone' => $validated['timezone'] ?? null,
            'guardian_email' => $validated['guardian_email'] ?? null,
            'wants_teacher_access' => (bool) ($validated['wants_teacher_access'] ?? false),
        ]);
    }
}
