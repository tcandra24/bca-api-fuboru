<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Valid credential
        $validator = Validator::make($credentials, [
            'username' => 'required|string',
            'password' => 'required|string|min:6|max:50'
        ]);

        // Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        // Request is validated
        // Create token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'responseMessage' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'responseMessage' => 'Could not create token.',
                ], 500);
        }
 	
        $request->headers->set('Authorization','Bearer '.$token);

 		// Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'responseMessage' => 'Token successfully created',
            'tokenType' => 'bearer',
            'token' => $token,
            'expiresIn' =>  env('JWT_TTL')
        ]);
    }
}