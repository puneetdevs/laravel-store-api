<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserLoginAPIController extends AppBaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $user['token'] = $user->generateUserToken($user->id);

            return $this->sendResponse($user, 'You have been successfully logged in');
        } else {
            return $this->sendError('The email address or password you entered is invalid. Please, try again', 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'unique:users,email|required|email',
            'password' => 'required|confirmed | min:8',
            'is_merchant' => 'nullable',
            'store_name' => 'required_if:is_merchant,1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);

        if (isset($validated['is_merchant'])) {
            if ($validated['is_merchant'] == 1) {
                $userType = 'Merchant';
            }
        } else {
            $userType = 'Customer';
            $validated['is_merchant'] = 0;
            unset($validated['store_name']);
        }

        $user = User::create($validated);

        return $this->sendSuccess($userType . ' registered successfully');
    }

}
