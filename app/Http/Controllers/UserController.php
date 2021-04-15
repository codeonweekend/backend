<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\User;
use Validator;

class UserController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['getUser']]);
    }

    public function get() {
        $user = auth()->user();
        
        try {
            $profile = UserProfile::where('user_id', $user->id)->first();
            if ($profile) {
                return $this->sendSuccess($profile, 'Profile available');
            }
            throw new \Throwable();
        } catch (\Throwable $th) {
            return $this->sendNotFound();
        }
    }

    public function post(Request $request) {
        $input = $request->all();
        $user = auth()->user();

        $profile = UserProfile::where('user_id', $user->id)->count();
        if ($profile == 1) {
            return $this->sendBadRequest('Failed! You have been created profile.');
        }

        $input['user_id'] = $user->id;

        try {
            $profile = UserProfile::create($input);
            return $this->sendSuccess($profile, 'Profile Created!');
        } catch (\Throwable $th) {
            return $this->sendBadRequest();
        }
    }

    public function put(Request $request) {
        $input = $request->all();
        $user = auth()->user();

        $profile = UserProfile::where('user_id', $user->id)->count();
        if ($profile == 0) {
            return $this->sendBadRequest('Failed! You dont have created profile before.');
        }

        $data = $request->only(['profile_picture', 'bio', 'website', 'repository']);
        $data['user_id'] = $user->id;

        try {
            $profile = UserProfile::where('user_id', $user->id)->update($data);
            return $this->sendSuccess($data, 'Profile Updated!');
        } catch (\Throwable $th) {
            return $this->sendBadRequest();
        }
    }

    public function getUser(Request $request) {
        try {
            $user = User::where('username', $request->username)->first();
            $profile = UserProfile::where('user_id', $user->id)->first();

            $data = [
                'user' => $user,
                'profile' => $profile
            ];
            return $this->sendSuccess($data, 'User available');
        } catch (\Throwable $th) {
            return $this->sendNotFound();
        }
    }

}
