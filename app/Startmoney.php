<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Startmoney extends Model
{
    protected $table = 'startmoney';
    public $timestamps = true;

    public function getBranch(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
}
