<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public function archiveFiles(){
        return $this->hasMany('App\ArchiveFiles');
    }

    public function division(){
        return $this->belongsTo('App\Division');
    }
}
