<?php

namespace App\Http\Controllers;

use App\Http\Requests\Waitlist\VerificationMailRequest;
use App\Http\Requests\Waitlist\WaitlistRequest;
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

            WaitlistService::create($validated);

            $url = env('FRONTEND_URL') . '/verified-mail?token=' . $validated['verification_token'] . '&mail=' . urlencode($validated['email']);

            $html = view('mails.waitlist_verification', compact('url'))->render();

            Resend::emails()->send([
                'from' => 'Arises <contact@arises.app>',
                'to' => [$validated['email']],
                'subject' => 'Arises Confirmation',
                'html' => $html,
            ]);

            return response()->json("Verification mail sent", 201);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json("Error while sending the mail", 500);
        }
    }


    public function verifyMail(VerificationMailRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            WaitlistService::updateByMailAndToken($validated['mail'], $validated['token']);

            return response()->json([
                'status' => 'success',
                'message' => 'Email verified',
                'already_verified' => false,
            ], 200);
        } catch (\Throwable $e) {
            \Sentry\captureException($e);

            if ($e->getMessage() === 'Email already verified') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email already verified',
                    'already_verified' => true,
                ], 200); // volontairement 200 car c’est pas une vraie erreur
            }
            return response()->json("Error while verifying email", 500);
        }
    }

}
