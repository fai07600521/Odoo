<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion_product extends Model
{
        protected $table = 'promotion_product';
    public $timestamps = false;

    public function getProductVariant(){
    	return $this->hasOne("App\Product_variant",'id','product_id');
    }
    public function getPromotion(){
    	return $this->hasOne("App\Promotion_notification",'id','promotion_id');
    }
}
