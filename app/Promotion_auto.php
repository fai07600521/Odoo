<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion_auto extends Model
{
    protected $table = 'promotion_auto';
    public $timestamps = true;

    public function getProduct(){
    	return $this->hasMany("App\Promotionauto_product",'promotionauto_id','id');
    }

    public function getBranch(){
    	return $this->hasMany("App\Auto_branch","promotion_id",'id');
    }
}
