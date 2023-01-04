<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_promotion extends Model
{
      protected $table = 'invoice_promotion';
    public $timestamps = true;

    public function getPromotion(){
    	return $this->hasOne('App\Promotions','id','promotion_id');
    }
}
