<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // in seconds
            'user' => auth()->user()
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'email' => 'email',
            'name' => 'string|max:255',
            'username' => 'string|max:255',
        ]);

        try {
            $user = User::find(Auth::id());

            if ($request->has('email')) {
                $user->email = $request->input('email');
            }

            if ($request->has('name')) {
                $user->name = $request->input('name');
            }

            if ($request->has('username')) {
                $existingUser = User::where('username', $request->input('username'))->first();
                if($existingUser && $existingUser->id !== $user->id) {
                    return response()->json([
                        'message' => 'Username already exists'
                    ], 409);
                }
                $user->username = $request->input('username');
            }

            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
