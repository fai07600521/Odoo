<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stocktransfer_item extends Model
{
   protected $table = 'stocktransfer_item';
    public $timestamps = false;

     public function getProductVariant(){
    	return $this->hasOne('App\Product_variant','id','product_id');
    }
}
