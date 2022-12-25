<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\User;

use Auth;
use DB;

class UserController extends Controller
{

    public function __construct()
    {

    }

    public function delete()
    {
        $user = User::find(Auth::id());
        $user->delete();
        return redirect('/');
    }
}
