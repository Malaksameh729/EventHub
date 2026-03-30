<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token]);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email','password');
        if(!Auth::attempt($credentials)){
            return response()->json(['message'=>'Invalid credentials'],401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }


    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $user->update($request->only('name','email'));
        return response()->json($user);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'=>'required',
            'new_password'=>'required|min:6'
        ]);

        $user = $request->user();

        if(!Hash::check($request->current_password,$user->password)){
            return response()->json(['message'=>'Current password is incorrect'],400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message'=>'Password changed']);
    }

    public function forgotPassword(Request $request)
    {

        return response()->json(['message'=>'Reset link sent (simulate)']);
    }


    public function resetPassword(Request $request)
    {

        return response()->json(['message'=>'Password reset (simulate)']);
    }
}
