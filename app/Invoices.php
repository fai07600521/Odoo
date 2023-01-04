<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = 'invoices';
    public $timestamps = true;

    public function getBranch(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }

   	public function getUser(){
   		return $this->hasOne('App\User','id','admin_id');
   	}

   	public function getPaymentType(){
   		return $this->hasOne('App\Paymenttypes','id','paymenttype_id');
   	}

    public function getMember(){
      return $this->hasOne('App\Members','id','member_id');
    }
    public function getItem(){
      return $this->hasMany('App\Invoice_item','invoice_id','id');
    }

    public function getPromotion(){
      return $this->hasMany('App\Invoice_promotion','invoice_id','id');
    }
}
