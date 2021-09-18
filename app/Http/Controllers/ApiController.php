<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;
use Auth;

class ApiController extends Controller
{
    //get api for fetch users 
    public function users($id=null){
        if($id==''){
           $users = User::get();
           return response()->json(['users'=>$users],200);  
        }else{
            $users = User::find($id);
            return response()->json(['users'=>$users],200); 
        }
    }

   //secure get api for fetch users 
   public function usersList(Request $request){
       $header = $request->header('Authorization');
       if(empty($header)){
        return response()->json(['message'=>'Authorization is missing'],422);
       }else{
           if($header == 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Ik5hem11bCBIb3F1ZSIsImlhdCI6MTUxNjIzOTAyMn0.eXQUr8Mq2ulWt-aVRlxsezDju-URRLLvmcs6XamhGO4'){
            $users = User::get();
            return response()->json(['users'=>$users],200);
           }else{
            return response()->json(['message'=>'Authorization is incorrect'],422); 
           }
       }
       
   }

    //add single user data
    public function addUsers(Request $request){
        if($request->isMethod('Post')){
            $data = $request->all();
            // return $data;
            //check if any user field is empty
            // if(empty($data['name']) || empty($data['email']) || empty($data['password'])){
            //     $error_message = "Please complete all user data";
            // }
            // // check email is valid
            // if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            //     $error_message = "Please enter valid email address";
            //   }
            //   // check email already exists
            //   $emailCount = User::where('email',$data['email'])->count();
            //   if($emailCount>0){
            //     $error_message = "Email already exists";
            //   }
            //   if(isset($error_message) && !empty($error_message)){
            //     return response()->json(['status'=>'false','message'=> $error_message],422);
            //   }
            $rules = [
    			'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' =>'required|email|unique:users',
                'password' =>'required',
    		];
    		$customMessage = [
    			'name.required' => 'Name is required',
    			'name.regex' => 'Valid name is required',
    			'email.required' => 'Email is required',
    			'email.email' => 'Valid email is required',
    			'password.required' => 'Password is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $addUser = new User();
            $addUser->name = $data['name'];
            $addUser->email = $data['email'];
            $addUser->password = bcrypt($data['password']);
            $message = 'User Successfully Added';
            $addUser->save();
            return response()->json(['message'=>$message],201);
        }
    }
    
    //register api to add single user with api token
    public function registerUsers(Request $request){
        if($request->isMethod('Post')){
            $data = $request->input();
            $api_token = Str::random('50');
            
            $rules = [
    			'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' =>'required|email|unique:users',
                'password' =>'required',
    		];
    		$customMessage = [
    			'name.required' => 'Name is required',
    			'name.regex' => 'Valid name is required',
    			'email.required' => 'Email is required',
    			'email.email' => 'Valid email is required',
    			'password.required' => 'Password is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $registerUser = new User();
            $registerUser->name = $data['name'];
            $registerUser->email = $data['email'];
            $registerUser->password = bcrypt($data['password']);
            $registerUser->api_token = $api_token;
            $message = 'User Successfully Registerd';
            $registerUser->save();
            return response()->json([
                'message'=>$message,
                'api_token'=>$api_token
            ],201);
        }
    }

    //register user with passport
    public function registerWithPassport(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' =>'required|email|unique:users',
                'password' =>'required',
            ];
            $customMessage = [
                'name.required' => 'Name is required',
                'name.regex' => 'Valid name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Valid email is required',
                'password.required' => 'Password is required',
            ];
            $validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $registerUser = new User();
            $registerUser->name = $data['name'];
            $registerUser->email = $data['email'];
            $registerUser->password = bcrypt($data['password']);
            $registerUser->save();

            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                $user = User::where('email',$data['email'])->first();
                $api_token = $user->createToken($data['email'])->accessToken;
                
                User::where('email',$data['email'])->update(['api_token'=>$api_token]);

                $message = 'User Successfully Registerd';
                return response()->json(['message'=>$message,'api_token'=>$api_token],201);
            }else{
                 $message = 'Error';
                 return response()->json(['message'=>$message],422);
            }

            
        }

    }

    //user login api /update token
    public function loginUsers(Request $request){
        if($request->isMethod('post')){
            $data = $request->input();
            
            // add custom validation
            $rules = [
                'email' =>'required|email|exists:users',
                'password' =>'required',
    		];
    		$customMessage = [
    			'email.required' => 'Email is required',
    			'email.email' => 'Valid email is required',
    			'email.exists' => 'Email does not exists',
    			'password.required' => 'Password is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            // get user details
            $userDetails = User::where('email',$data['email'])->first();
            // return $userDetails;
            if(password_verify($data['password'], $userDetails->password)){
                $api_token = Str::random('50');
                User::where('email',$data['email'])->update(['api_token'=>$api_token]);
                $message = 'User Successfully Login';
                return response()->json(['message'=>$message,'api_token'=>$api_token],201);
            }else{
                return response()->json(['message'=>'Password is incorrect'],422);
            }
        }
    }

    //user logout api /update token
    public function logoutUsers(Request $request){
        $api_token = $request->header('Authorization');
        if(empty($api_token)){
            $message = 'User Token is missing in API Header';
            return response()->json(['status'=>'false','message'=>$message],422);
        }else{
            $api_token = str_replace('Bearer ','', $api_token);
            $user_count = User::where('api_token',$api_token)->count();
            if($user_count>0){
                //Update user api_token to null
                User::where('api_token',$api_token)->update(['api_token'=>NULL]);
                $message = 'User Successfully Logout';
                return response()->json(['status'=>true,'message'=>$message],201);
            }else{
                return response()->json(['message'=>'User api token is incorrect'],422);
            }
        }
    }

    //add multiple user data
    public function addMultipleUsers(Request $request){
        if($request->isMethod('Post')){
            $data = $request->input();
            // return $data;
            $rules = [
    			'users.*.name' => 'required|regex:/^[\pL\s\-]+$/u',
                'users.*.email' =>'required|email|unique:users',
                'users.*.password' =>'required',
    		];
    		$customMessage = [
    			'users.*.name.required' => 'Name is required',
    			'users.*.name.regex' => 'Valid name is required',
    			'users.*.email.required' => 'Email is required',
    			'users.*.email.email' => 'Valid email is required',
    			'users.*.password.required' => 'Password is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            foreach($data['users'] as $value){
                $addUser = new User();
                $addUser->name = $value['name'];
                $addUser->email = $value['email'];
                $addUser->password = bcrypt($value['password']);
                $message = 'User Successfully Added';
                $addUser->save();
            }
            return response()->json(['message'=>$message],201);
        }
    }

    // update user details
    public function updateUserDetails(Request $request,$id){
        if($request->isMethod('Put')){
            $data = $request->input();
            $rules = [
    			'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'password' =>'required',
    		];
    		$customMessage = [
    			'name.required' => 'Name is required',
    			'name.regex' => 'Valid name is required',
    			'password.required' => 'Password is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            User::where('id',$id)->update(['name'=>$data['name'],'password'=>bcrypt($data['password'])]);
            $message = 'User Details Successfully Updated';
            return response()->json(['message'=>$message],202);
        }
    }

    // update user single record using patch
    public function updateUserName(Request $request,$id){
        if($request->isMethod('Patch')){
            $data = $request->input();
            $rules = [
    			'name' => 'required|regex:/^[\pL\s\-]+$/u',
    		];
    		$customMessage = [
    			'name.required' => 'Name is required',
    			'name.regex' => 'Valid name is required',
    		];
    		$validator = Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            User::where('id',$id)->update(['name'=>$data['name']]);
            $message = 'User Name Successfully Updated';
            return response()->json(['message'=>$message],202);
        }
    }

    //delete single user
    public function deleteUser($id){
        User::where('id',$id)->delete();
        return response()->json(['message'=>'User Successfully Deleted'],200);
    }
    //delete single user with json
    public function deleteUserWithJson(Request $request){
        if($request->isMethod('Delete')){
            // $data = $request->input();
            $data = $request->all();
            User::where('id',$data['id'])->delete();
            return response()->json(['message'=>'User Successfully Deleted'],200);
        } 
    }

    //delete multiple user with param
    public function deleteMultipleUser($ids){
        $ids = explode(',',$ids);
        User::whereIn('id',$ids)->delete();
        return response()->json(['message'=>'User Successfully Deleted'],200);
    }

    //delete multiple user with json 
    public function deleteMultipleUserWithJson(Request $request){
        if($request->isMethod('Delete')){
          $data = $request->all();
          User::whereIn('id',$data['ids'])->delete();
          return response()->json(['message'=>'User Successfully Deleted'],200);
        }
    }

}
