<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'products';
    public $timestamps = true;

    public function getUnit(){
    	return $this->hasOne('App\System_unit','id','unit_id');
    }

    public function getVariant(){
    	return $this->hasMany('App\Product_variant','product_id','id');
    }

    public function getUser(){
    	return $this->hasOne('App\User','id','user_id');
    }

    public function getTags(){
        return $this->hasMany('App\Product_tag','product_id','id');
    }

}
