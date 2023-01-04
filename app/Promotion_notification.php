<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion_notification extends Model
{
    protected $table = 'promotion_notification';
    public $timestamps = true;

    public function getProduct(){
    	return $this->hasMany("App\Promotion_product",'promotion_id','id');
    }

    public function getBranch(){
    	return $this->hasMany("App\Notification_branch","promotion_id",'id');
    }
}
