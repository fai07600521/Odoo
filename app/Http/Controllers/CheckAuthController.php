<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class CheckAuthController extends Controller
{
    public function getCheck(){
    	$user = Auth::user();
    	if($user->role=="1"){
    		return redirect('/');
    	}else{
    		return redirect('/admin');
    	}
    }
}
