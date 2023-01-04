<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auto_branch extends Model
{
    protected $table = 'auto_branch';
    public $timestamps = false;

    public function getBranchinfo(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
}
