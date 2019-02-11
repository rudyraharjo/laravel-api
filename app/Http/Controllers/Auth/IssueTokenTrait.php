<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;

trait IssueTokenTrait{

    private $client;
    private $response;

	public function RequestToken(Request $request, $grantType, $clientID){
        
        $this->client = Client::find($clientID);
        
        if( count($this->client) > 0 || $this->client != 0){

            $params = [
                'grant_type' => $grantType,
                'client_id' => $this->client->id,
                'client_secret' => $this->client->secret,    
                'password' => $request->password
            ];
            
            if($grantType !== 'social'){
                $params['username'] = $request->username ?: $request->email;
            }
            
            $http = new \GuzzleHttp\Client();

            $this->response = $http->post(config('services.passport.url_endpoint'), [
                'form_params' => $params,
            ]);

        }
        
        return $this->response;

    }

    public function RefreshToken(Request $request, $grantType, $clientID){
        
        $this->client = Client::find($clientID);
        
        $params = [
            'grant_type' => $grantType,
            'refresh_token' => $request->refresh_token,
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,    
        ];
        
        $http = new \GuzzleHttp\Client();

        $response = $http->post(config('services.passport.url_endpoint'), [
            'form_params' => $params,
        ]);
        return $response;
    }

    // CHECK TOKEN EXPIRE , TARUH DI FRONTEND
    public function tokenExpired(Request $request)
    {
        if (Carbon::parse($request->expires_at) < Carbon::now()) {
            return true;
        }
        return false;
    }
    
}
