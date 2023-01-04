<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
     protected $table = 'branch';
    public $timestamps = false;

    public function getItem(){
    	return $this->hasMany("App\Branch_user","branch_id","id");
    }
}
