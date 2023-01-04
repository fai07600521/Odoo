<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock_adjustment extends Model
{
    protected $table = 'stock_adjustment';
    public $timestamps = true;


    public function getItem(){
    	return $this->hasMany("App\Stockadj_item",'stockadj_id','id');
    }
}
