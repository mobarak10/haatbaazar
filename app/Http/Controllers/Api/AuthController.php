<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))){
            return $this->error('', 'Credential do not match', 401);
        }

        $user = User::where('email', $request->email)->first();
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of '. $user->name)->plainTextToken
        ]);
    }

    public function register(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = \Hash::make($request->password);

        $user = User::create($data);
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of '. $user->name)->plainTextToken,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'You have successfully logout'
        ]);
    }
}
