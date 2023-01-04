<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_item extends Model
{
   protected $table = 'invoice_item';
    public $timestamps = false;

    public function getInvoice(){
    	return $this->hasOne('App\Invoices','id','invoice_id');
    }

    public function getProductVariant(){
    	return $this->hasOne('App\Product_variant','id','product_id');
    }
}
