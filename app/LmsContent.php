<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LmsContent extends Model
{
    protected $table = 'lmscontents';



    public static function getRecordWithSlug($slug)
    {
        return LmsContent::where('slug', '=', $slug)->first();
    }

    public static function getRecordWithId($slug)
    {
        return LmsContent::where('id', '=', $slug)->first();
    }

    public function category()
    {
        return $this->belongsTo('App\Lmscategory', 'category_id');
    }
}
