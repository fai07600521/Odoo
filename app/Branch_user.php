<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch_user extends Model
{
    protected $table = 'branch_user';
    public $timestamps = false;

    public function getBranch(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
    public function getUser(){
    	return $this->hasOne('App\User','id','user_id');
    }
}
