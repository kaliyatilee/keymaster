<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    public const TIMEZONE_CHANGE_COOLDOWN_DAYS = 7;

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }

        if (is_string($this->input('username'))) {
            $this->merge(['username' => Str::lower(trim($this->input('username')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => $this->nameRules(),
            'username' => $this->usernameRules($user->id),
            'email' => $user->isSchoolManaged()
                ? ['prohibited']
                : $this->emailRules($user->id),
            'display_name' => ['required', 'string', 'max:50'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'daily_goal_minutes' => ['required', 'integer', 'between:1,120'],
            'is_public' => ['required', 'boolean'],
            'show_on_leaderboards' => ['required', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var User $user */
                $user = $this->user();

                if ($user->isMinor() && $this->boolean('is_public')) {
                    $validator->errors()->add('is_public', 'Profiles of users under 18 stay private.');
                }

                $changedAt = $user->timezone_changed_at;

                if ($this->input('timezone') !== $user->timezone
                    && $changedAt !== null
                    && $changedAt->gt(now()->subDays(self::TIMEZONE_CHANGE_COOLDOWN_DAYS))) {
                    $validator->errors()->add(
                        'timezone',
                        'Your timezone can be changed once every '.self::TIMEZONE_CHANGE_COOLDOWN_DAYS.' days (it decides when your practice day starts).',
                    );
                }
            },
        ];
    }
}
