<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_tag extends Model
{
     protected $table = 'product_tag';
    public $timestamps = false;

     public function getTagDetail(){
        return $this->hasOne('App\Tags','id','tag_id');
    }

}
