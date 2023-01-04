<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
     protected $table = 'tags';
    public $timestamps = false;

     public function getProduct(){
        return $this->hasMany('App\Product_tag','tag_id','id');
    }

}
