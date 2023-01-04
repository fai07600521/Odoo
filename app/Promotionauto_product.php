<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotionauto_product extends Model
{
        protected $table = 'promotionauto_product';
    public $timestamps = false;

    public function getProductVariant(){
    	return $this->hasOne("App\Product_variant",'id','product_id');
    }
    public function getPromotion(){
    	return $this->hasOne("App\Promotion_auto",'id','promotionauto_id');
    }
}
