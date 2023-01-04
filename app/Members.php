<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'members';
    public $timestamps = true;

    public function getOrder(){
    	return $this->hasMany('App\Invoices','member_id','id');
    }

}
