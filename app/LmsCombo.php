<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class LmsCombo extends Model
{
    protected $table = 'lmsseries_combo';



    public static function getRecordWithSlug($slug)
    {
        return LmsCombo::where('slug', '=', $slug)->first();
    }



}
