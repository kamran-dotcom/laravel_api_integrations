<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required|string|min:3|max:100',
            'email'=> 'required|string|email',
            'password'=> 'required|string|min:6|unique:users|confirmed'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors());
        }
        else
        {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'message' => 'User inserted successfully',
                'user' => $user
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|min:3|',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors());
        }
        else
        {
            if(!$token = auth()->attempt($validator->validated()))
            {
                return response()->json(['success'=>false,'message'=>'Username or password not correct']);
            }

            return $this->respondWithToken($token);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL()*60
        ]);
    }

    public function logout()
    {
        try{
            auth()->logout();
            return response()->json(['success'=>true, 'message'=>'User Successfully loggedout']);
        } catch(\Exception $e){
            return response()->json(['success'=>false, 'message'=>$e->getMessage()]);
        }
    }
}
