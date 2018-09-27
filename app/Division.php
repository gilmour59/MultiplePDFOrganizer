<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    //
    public function categories(){
        return $this->hasMany('App\ArchiveFile');
    }
}
