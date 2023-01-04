<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_variant extends Model
{
    protected $table = 'product_variant';
    public $timestamps = false;

    public function getProduct(){
    	return $this->hasOne('App\Products','id','product_id');
    }
    public function getStock(){
    	return $this->hasMany('App\Stocks','product_id','id');
    }
}
