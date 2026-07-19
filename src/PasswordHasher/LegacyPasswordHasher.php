<?php
declare(strict_types=1);

namespace App\PasswordHasher;

use Authentication\PasswordHasher\AbstractPasswordHasher;

/**
 * Verifies passwords hashed with the unsalted md5 scheme used before the site
 * switched to bcrypt, so accounts created under that scheme can still log in
 * and get rehashed via FallbackPasswordHasher.
 */
class LegacyPasswordHasher extends AbstractPasswordHasher
{
    public function hash(string $password): string
    {
        return md5($password);
    }

    public function check(string $password, string $hashedPassword): bool
    {
        return md5($password) === $hashedPassword;
    }
}