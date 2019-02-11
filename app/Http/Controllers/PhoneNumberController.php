<?php

namespace App\Http\Controllers;

use App\PhoneNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    function __construct(){}

    public function index(){
        $list_phonenumber = PhoneNumber::all();
        return response()->json($list_phonenumber);
    }

    public function show($id){
        $showPhoneNumber = PhoneNumber::find($id);
        return response()->json($showPhoneNumber);
    }

    public function store(Request $request){

        try {  

            $validator = Validator::make($request->all(), [
                "name" => 'required|string',
                "phone"  => 'required|string',
                "email" => "required|string|email"
            ]); 

            if ($validator->fails()){                
                return response()->json([
                    "success" => false,
                    "messages" => "Failed Created Phone Number . Please Check all field",
                    "data" => $validator->errors()
                ]);
            } else {

                $CreatePhoneNumber = new PhoneNumber([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ]);

                $CreatePhoneNumber->save(); 

                if(!$CreatePhoneNumber) {
                    
                    return response()->json([
                        "success" => false,
                        "messages" => "Failed Created Phone Number . Please Check all field",
                        "data" => $CreatePhoneNumber
                    ]);
                    
                }   

                return response()->json([
                    "success" => true,
                    "messages" => "Successfully Created Phone Number .",
                    "data" => $CreatePhoneNumber
                ]);
                
            }

        } catch(\Exception $e) {
            
            return $this->sendError('Server Error.',  $e->getMessage());
        }

    }

    public function update(Request $request){
        
        try {  

            $validator = Validator::make($request->all(), [
                "name" => 'required|string',
                "phone"  => 'required|string',
                "email" => "required|string|email"
            ]); 
                
            if ($validator->fails()){
                return response()->json([
                    "success" => false,
                    "messages" => "Failed Update Phone Number . Please Check all field",
                    "data" => $validator->errors()
                ]);
            } else {

                $updatePhoneNumber = PhoneNumber::find($request->id);
                
                $updatePhoneNumber->name = $request->name;
                $updatePhoneNumber->phone = $request->phone;
                $updatePhoneNumber->email = $request->email;

                $updatePhoneNumber->save();

                if(!$updatePhoneNumber){

                    return response()->json([
                        "success" => false,
                        "messages" => "Failed Update Phone Number .",
                        "data" => $updatePhoneNumber
                    ]);
                }
                
                return response()->json([
                    "success" => true,
                    "messages" => "Successfully Update Phone Number .",
                    "data" => $updatePhoneNumber
                ]);
                
            }

        } catch(\Exception $e) {
            
            return $this->sendError('Server Error.',  $e->getMessage());
        }

    }

    public function delete(Request $request){
        
        try {
        
            $deletePhoneNumber = PhoneNumber::find($request->id);
            $deletePhoneNumber->delete();

            if(!$deletePhoneNumber){
                
                return response()->json([
                    "success" => false,
                    "messages" => "Failed Delete Phone Number .",
                    "data" => $deletePhoneNumber
                ]);

            }

            return response()->json([
                "success" => true,
                "messages" => "Successfully Delete Phone Number",
                "data" => $deletePhoneNumber
            ]);

        } catch(\Exception $e){

            return response()->json([
                "success" => false,
                "messages" => $e->getMessage()
            ]);

        }
    }
}
