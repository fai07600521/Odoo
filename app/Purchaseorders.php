<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchaseorders extends Model
{
    protected $table = 'purchaseorders';
    public $timestamps = true;

    public function getUser(){
    	return $this->hasOne('App\User','id','user_id');
    }
    public function getBranch(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
    public function getItem(){
    	return $this->hasMany('App\Purchaseorder_item','purchaseorder_id','id');
    }

    public function getAdmin(){
        return $this->hasOne('App\User','id','admin_id');
    }
}
