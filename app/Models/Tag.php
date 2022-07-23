<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;



class Tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
    ];

    public static function getUserAllTag($id)
    {

        $allTag = Tag::select('id','name')
        ->where('user_id','=',$id)
        ->get();

        return $allTag;
    }
}
