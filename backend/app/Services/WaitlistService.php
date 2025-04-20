<?php

namespace App\Services;

use App\Models\Waitlist;
use Random\RandomException;

class WaitlistService
{
    public static function create(array $data)
    {
        return Waitlist::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'verification_token' => $data['verification_token'],
        ]);
    }

    /**
     * @throws RandomException
     */
    public static function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function updateByMailAndToken(string $mail, string $token): void
    {
        $user = Waitlist::where('email', $mail)
            ->where('verification_token', $token)
            ->firstOrFail();
        if ($user->verified) {
            throw new \Exception('Email already verified');
        }

        $user->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public static function checkIfEmailExists(string $email): void
    {
        if (Waitlist::where('email', $email)->exists()) {
            throw new \Exception('Email already exists');
        }
    }
}
