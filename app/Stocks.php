<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $table = 'stocks';
    public $timestamps = true;

    public function getBranch(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
}
