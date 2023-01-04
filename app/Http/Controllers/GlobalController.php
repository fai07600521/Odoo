<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Help;

class GlobalController extends Controller
{
        public function __construct(){
        $this->middleware('auth'); 

    }
    public function getHelp(Request $request){
    	$user = Auth::user();
    	if($user->role=="2"){
    		$help = Help::where('id','=',$request->id)->first();
    	}else{
    		$help = Help::where('id','=',$request->id)->where('role','=','1')->where('status','=','1')->first();
    	}
    	if(isset($help)){
    		return view('help',compact('help'));
    	}else{
    		$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบคู่มือการใช้งาน"
			);
    		return redirect('/')->with('sysmessage',$sysmessage);
    	}

    	

    }
}
