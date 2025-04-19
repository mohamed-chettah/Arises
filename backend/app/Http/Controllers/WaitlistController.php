<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaitlistRequest;
use App\Services\WaitlistService;
use Illuminate\Http\JsonResponse;
use Resend\Laravel\Facades\Resend;

class WaitlistController
{
    public function store(WaitlistRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $validated['verification_token'] = WaitlistService::generateVerificationToken();

            $waitlistUser = WaitlistService::create($validated);
            WaitlistService::sendMail($validated);

            return response()->json("Verification mail sent", 201);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json("Error while sending the mail", 500);
        }
    }


    public function mailVerification(WaitlistRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            Resend::emails()->send([
                'from' => 'Acme <onboarding@resend.dev>',
                'to' => [$request->user()->email],
                'subject' => 'hello world',
                'html' => ("test"),
            ]);

            return response()->json("Verification mail sent", 201);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json("Error while sending the mail", 500);
        }
    }

    public function verifyMail(string $mail): JsonResponse
    {
        try {
            WaitlistService::verifyEmail($mail);

            return response()->json("Email verified", 200);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json("Error while verifying email", 500);
        }
    }
}
