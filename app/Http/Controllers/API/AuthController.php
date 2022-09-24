<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateJWT_Token;
use App\Http\Traits\ApiHandler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    use ApiHandler;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    public function login(Request $request)
    {
        try {
            $rules = [
                'email'=>'required|email',
                'password' => 'required',
            ];
            $validator  = Validator::make($request->all(),$rules);

            if ($validator->fails()){
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($validator,$code);
            }

            $credentials = $request->only(['email','password']);
            $token = Auth::guard('api')->attempt($credentials);

            if (!$token){
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
            }

            $user = Auth::guard('api')->user();
            $user->token = $token;
            //return token
            return $this->returnData('user', $user);

        }catch (\Exception $e){
            $this->returnError($e->getCode(),$e->getMessage());
        }
        return $this->returnError("","No Data Found");

    }


    public function register(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];

        $validator  = Validator::make($request->all(),$rules);

        if ($validator->fails()){
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($validator,$code);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user){
            return $this->login($request);
        }
        return $this->returnError("","some thing went wrong try again later");
    }

    public function logout(ValidateJWT_Token $request)
    {
        try {
            JWTAuth::invalidate($request->token);
            return $this->returnSuccessMessage('User has been logged out',"");
        } catch (JWTException $exception) {
            return $this->returnError("",'Sorry, user cannot be logged out');
        }
    }
    public function GetUser(ValidateJWT_Token $request)
    {
        $user = $this->JWT_Auth($request);
        return $this->returnData("User",$user,"User has been returned successfully");
    }

    public function GetRoles(ValidateJWT_Token $request)
    {
        $user = $this->JWT_Auth($request);
        $roles = $this->ExtractRoles($user);
        return $this->returnData("Roles",$roles,"User has been returned successfully");
    }

    public function CheckUserRole(ValidateJWT_Token $request,$roles = array())
    {
        $user = $this->JWT_Auth($request);
        $DB_Rules = $this->ExtractRoles($user);
        foreach ($DB_Rules as $rule){
            if (in_array($rule,$roles)){
                return true;
            }
        }
        return false;
    }

    public function JWT_Auth($request){
        try {
            $user = JWTAuth::authenticate($request->token);
        }catch (\Exception $e){
            return  $this->returnError("","some thing went wrong ,try again later");
        }
        return $user;
    }

    public function ExtractRoles($user){
        $roles = [];
        foreach ($user->roles as $arr){
            $roles[] = $arr->name;
        }
        return $roles;
    }


}
