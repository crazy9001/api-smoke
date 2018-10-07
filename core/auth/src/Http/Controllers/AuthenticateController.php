<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:32 AM
 */

namespace Vtv\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Vtv\Base\Http\Controllers\BaseController;
use JWTAuth;
use Auth;
use Validator;
use Hash;

class AuthenticateController extends BaseController
{

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password'  =>  'required',
        ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->sendError('Error.', 'Invalid credentials', 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return $this->sendError('Error.', 'Could not create token', 500);
        }
        // if no errors are encountered we can return a JWT
        return $this->sendResponse([ 'user' => Auth::user(), 'auth_token' => $token], 'Login success');
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        JWTAuth::invalidate($token);
        auth()->logout(true);
        return $this->sendResponse('Success', 'Đăng xuất thành công');
    }


}