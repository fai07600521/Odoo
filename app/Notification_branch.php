<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification_branch extends Model
{
    protected $table = 'notification_branch';
    public $timestamps = false;

    public function getBranchinfo(){
    	return $this->hasOne('App\Branch','id','branch_id');
    }
}
