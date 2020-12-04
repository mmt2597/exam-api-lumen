<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
// use Yajra\DataTables\Datatables;
use Datatables;
use DB;

class UserController extends Controller
{
	private $user;
	public function __construct()
	{
		$this->user = auth()->user();
	}

    public function getUsers()
    {
        $users = User::select(['username', 'email', 'firstname', 'lastname']);
        return Datatables::of($users)->make(true);
    }

    public function logout()
    {
        $this->user->token()->revoke();
        return response()->json([], 204);
    }
}
