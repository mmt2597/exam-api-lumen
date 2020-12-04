<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Passport\CLient as PassportClient;
use GuzzleHttp\Client;
use Validator;

use App\Models\User;

class AuthController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = PassportClient::where('password_client', 1)->firstOrFail();
    }

    /**
     * Register 
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'message' => 'Validation Errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $input = $request->all();
            $input['password'] = app('hash')->make($input['password']);
            $user = User::create($input);

            // Authentication Access Token Is  Generated Here
            // return $user->createToken($request->email)->token;

            return response()->json(['error' => false, 'message' => 'Successfully Registration'], 201);

        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'message' => 'Validation Errors', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {

            if (app('hash')->check($request->password, $user->password)) {
                $params = [
                    'grant_type'    => 'password',
                    'client_id'     => $this->client->id,
                    'client_secret' => $this->client->secret,
                    'username'      => request('email'),
                    'password'      => request('password'),
                    'scope'         => '*',
                ];

                $proxy = Request::create('oauth/token', 'POST');
                $proxy->request->add($params);

                $token = app()->dispatch($proxy);
                $token = json_decode($token->getContent());

                return response()->json($token);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    /**
     * REFRESH TOKEN
     */
    public function refreshToken (Request $request)
    {
        $validator = Validator::make($request, [
            'refresh_token' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'message' => 'Validation Errors', 'errors' => $validator->errors()], 422);
        }

        $params = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id'     => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope'         => '*',
        ];

        $request->request->add($params);

        $proxy = Request::create('oauth/token', 'POST');
        $proxy->request->add($params);

        $token = app()->dispatch($proxy);
        $token = json_decode($token->getContent());

        return response()->json($token);
    }
}
