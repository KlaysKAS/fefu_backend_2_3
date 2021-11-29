<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthenticationController extends Controller
{
    public function registration(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), RegistrationRequest::rules());

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['message' => $messages], 422);
        }

        $credentials = $validator->validated();
        $user = new User();
        $user->name = strtolower($credentials['name']);
        $user->email = $user->name . "@example.org";
        $user->password = Hash::make($credentials['password']);
        $user->save();

        $token = $user->createToken('token');
        return response()->json(['token' => $token, 'user' => new UserResource($user)]);
    }

    public function login(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'regex:/^[-a-zA-Z_0-9]{5,30}$/'],
            'password' => ['required', 'regex:/(?=.*[0-9])(?=.*[!@#$%^&*.,])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*.,]{10,30}/']
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json((['message' => $messages]), 422);
        }

        $credentials = $validator->validated();
        $credentials['name'] = strtolower($credentials['name']);
        $user = User::query()
            ->where('name', $credentials['name'])
            ->first();
        if (!$user || !Hash::check($credentials['password'], $user->password))
            return response()->json(['message' => 'Bad login or password']);

        $token = $user->createToken('token');
        return response()->json(['token' => $token, 'user' => new UserResource($user)]);
    }

    public function logout(Request $request) : JsonResponse {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile(Request $request) : JsonResponse {
        return response()->json([new UserResource($request->user())]);
    }
}
