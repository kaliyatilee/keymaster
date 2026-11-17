<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * Stores a date encrypted at rest (used for dates of birth) and exposes it
 * as a Carbon date.
 *
 * @implements CastsAttributes<Carbon|null, \DateTimeInterface|string|null>
 */
class EncryptedDate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::createFromFormat('!Y-m-d', Crypt::decryptString((string) $value)) ?: null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = $value instanceof \DateTimeInterface
            ? Carbon::instance($value)
            : Carbon::parse((string) $value);

        return Crypt::encryptString($date->format('Y-m-d'));
    }
}
