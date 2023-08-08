<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function cekEmailRegister(Request $request)
    {
        $data = User::where('statusenabled', 1)->where('email', $request['email'])->first();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveRegistrasiUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => ['required'],
            'username' => ['required', 'unique:users'],
            'email' => ['required', 'unique:users'],
            'password' => ['required', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = User::create([
                'statusenabled' => 1,
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_role' => $request->roleuser
            ]);
            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Registrasi berhasil',
                'data' => $user
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed' . $err->errorInfo
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal!'
            ]);
        }

        try {
            $response = [
                'success' => true,
                'message' => 'Login Berhasil!',
                'data' => $user,
                'token' => $user->createToken('authToken')->accessToken
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function logout(Request $request)
    {
        $removeToken = $request->user()->tokens()->delete();

        if ($removeToken) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout'
            ]);
        }
    }
}
