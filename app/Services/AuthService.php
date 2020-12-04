<?php
namespace App\Services;

use Validator;

use App\Models\User;

class AuthService
{
	private $response, $request;

	public function login($request)
	{
		$this->request = $request;
		if($this->validator()->fails()) {
			return $this->response;
		}

		if(!$this->isPasswordCorrect()) {
			return $this->generateToken();
		}

		return $this->response;
	}

	protected function isPasswordCorrect()
	{
        $user = User::where('email', $request->email)->first();
        app('hash')->check($request->password, $user->password) ?? false;
	}

	protected function generateToken()
	{
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
	}

	protected function validator()
	{
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        return $validator;
	}

}