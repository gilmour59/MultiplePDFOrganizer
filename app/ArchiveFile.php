<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class ArchiveFile extends Model
{
    use Searchable;

    public function category(){
        return $this->belongsTo('App\Category');
    }
    
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }
}
