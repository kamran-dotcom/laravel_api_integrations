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
            'password'=> 'required|string|min:6|confirmed'
        ]);

        if($validator->fails())
        {
            return response()->json(['message'=>'validation fails','errors'=>$validator->errors()]);
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
}
