<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Comment extends Model
{
    protected $table = 'comments';

   /* public static function getRecordWithSlug($slug)
    {
        return Comment::where('slug', '=', $slug)->first();
    }*/



}
