<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchaseorder_item extends Model
{
   protected $table = 'purchaseorder_item';
    public $timestamps = false;

    public function getProductVariant(){
    	return $this->hasOne('App\Product_variant','id','product_id');
    }
}
