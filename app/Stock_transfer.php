<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock_transfer extends Model
{
     protected $table = 'stock_transfer';
    public $timestamps = true;

    public function getItem(){
    	return $this->hasMany("App\Stocktransfer_item",'stocktransfer_id','id');
    }
        public function getAdmin(){
        return $this->hasOne('App\User','id','admin_id');
    }

    public function getSource(){
    	return $this->hasOne("App\Branch",'id','src_id');
    }
    public function getDestination(){
    	return $this->hasOne("App\Branch",'id','dst_id');
    }
    
}
