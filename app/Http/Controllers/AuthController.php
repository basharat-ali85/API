<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'new_password' => 'required|min:8',
            'password_confirmation' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Create a new user
        $user = User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('new_password')),
        ]);

        // Send verification email
        $this->sendVerificationEmail($user->email, $user->id);

        return response()->json(['message' => 'User registered successfully & Verification e-mail has sent'], 200);
    }

    private function sendVerificationEmail($email, $userId)
    {
        $verificationLink = url('/verify-email/' . $userId);
        $data = ['link' => $verificationLink];

        Mail::send('emails.verification', $data, function ($message) use ($email) {
            $message->to($email)->subject('Verify Your Email Address');
        });
    }

    public function verify($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 422);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User Loged in',
            'token' => $token
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'User successfully logged out'], 200);
    }
}
