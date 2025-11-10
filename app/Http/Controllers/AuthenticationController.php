<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\AuthenticationResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    use HttpResponses;

    // register the user
    public function register(RegistrationRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'user_type' => 'admin',
        ]);

        return $this->success(
            AuthenticationResource::make($user),
            'Admin Registered Successfully'
        );
    }

    // login the user
    public function login(LoginRequest $request)
    {
        $validated = $request->validated(); 

        if(!Auth::attempt($validated)){
           return $this->error(
            null,
            'Credentials do not match',
            401
           );
        }

        $user = Auth::user()->user_type === 'admin' ? "Admin" : "Staff";

        
        $token = Auth::user()->createToken('Api Token of' . Auth::user()->name)->plainTextToken;
        
        return $this->success(
            [
                'user' => AuthenticationResource::make(Auth::user()),
                'token' => $token,
            ],
            `$user Logged In Successfully`
        );
    }

    // logout the user
    public function logout()
    {
        Auth::user()->tokens()->delete();   
        return $this->success(
            null,
            'User Logged Out Successfully'
        );
    }

}