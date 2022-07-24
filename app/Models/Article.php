<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'body',
    ];

    public static function storeArticle($title,$body,$userId,$category)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == null) {
            DB::transaction(function () use($body,$userId,$category){
                Article::create([
                'user_id'  => $userId,
                'title'    => Carbon::now(),
                'body'     => $body,
                'category' => $category]);
            });
            return;
        }
        DB::transaction(function () use($title,$body,$userId,$category){
            Article::create([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'category' => $category]);
        });
    }
}
