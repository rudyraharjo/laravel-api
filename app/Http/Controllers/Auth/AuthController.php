<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\User;

use Ramsey\Uuid\Uuid;

class AuthController extends ResponseBaseController
{

    use IssueTokenTrait;

    public function signin(Request $request)
    {

        try{
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);    

            $credentials = request(['email', 'password']);          

            if ($validator->fails()){
                
                return $this->sendError('Failed to login. Please Check Email & Password',  $validator->errors());

            } else if(!Auth::attempt($credentials)) {

                return $this->sendError('Unauthorized Please Check Email & Password',  $credentials);

            } else {

                $user = $request->user();
                $role = User::find($user->role_id)->role;
                
                $dataUser = array(
                    'name'  => $user->name,   
                    'email' => $user->email,   
                    'id_role'   => $role->id,    
                    'active'    => $user->isActive
                );
                
                $clientID=2;

                $GetIssuToken = $this->RequestToken($request, 'password', $clientID);
                
                if($GetIssuToken){

                    $TokenType = json_decode($GetIssuToken->getBody())->token_type;

                    if($TokenType == "Bearer"){

                        $token_user = array(
                            "token" => json_decode((string) $GetIssuToken->getBody(), true),
                            "user" => $dataUser
                        );
                        
                        return $this->sendResponse($token_user, 'SuccessLogin');

                    }
                    
                    return $this->sendError('Failed to login 1.',  'Failed to Created Token ..');

                } else {
                    
                    return $this->sendError('Failed to login 2.',  'Failed to Created Token ..');
                }

            } 

        } catch(Exception $e){
            
            return $this->sendError('Server Error.',  $e->getMessage());

        }
        
    }

    public function signup(Request $request)
    {

        try{ 

            $validator = Validator::make($request->all(), [
                "name" => 'required|string',
                "email" => "required|string|email|unique:users",
                "password"  => 'required|string',
                "role_id" => 'required|numeric'  
            ]); 

            if ($validator->fails()) {
                
                return $this->sendError('Failed Created User.',  $validator->errors());

            } else if($request->role_id != 1){

                $CreateUser = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'activation_token' => str_random(60),
                    'role_id' => $request->role_id
                ]);
                
                $CreateUser->save();    
                
                if(!$CreateUser) {
                    
                    return $this->sendError('Failed Created User', $CreateUser);
                    
                }   
                return $this->sendResponse($CreateUser, 'Successfully Created User');
                
            } else {

                return $this->sendError('Failed Created user', 'Method Not Allowed');
            }

        } catch(\Exception $e){
            
            return $this->sendError('Server Error.',  $e->getMessage());
        }
        
    }

    public function Me(){
        return response()->json(auth()->user());  
    }
    
    public function token_refresh(Request $request){
        //return response()->json($request);
        
        try{

            $GetIssuToken = $this->RefreshToken($request, 'refresh_token', 4);

            if($GetIssuToken){
                $token_user = array(
                    "token" => json_decode((string) $GetIssuToken->getBody(), true)
                );
                return $this->sendResponse($token_user, 'SuccessLogin Refresh Token');
            }

            //return $this->sendError('Failed refresh',  'Failed Refresh token');

        } catch(\Exception $e){            
            return $this->sendError('Failed refresh.',  $e->getMessage());
        }
        

    }

    public function logout(Request $request)
    {   
        
        try{

            $logout = $request->user()->tokens->each(function ($token, $key) {
                //$token->delete();
                $token->revoke();
            });

            if(!$logout){
                return $this->sendError('Failed to logout',  'Method Not Allowed');
            }

            return $this->sendResponse('Revoked TRUE', 'Successfully logged out');

        } catch(\Exception $e){            
            return $this->sendError('Server Error.',  $e->getMessage());
        }
        
    }
}
