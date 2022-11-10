<?php

namespace App\Tools;

use Illuminate\Http\Request;

class MakeListToolKit
{
    // tagIdのリストを返す
    public function makeTagIdList($list)
    {
        $temp = [];
        foreach($list as $tag){array_push($temp,$tag['id']);}
        return $temp;
        // try {

        // } catch (\Throwable $th) {
        //     // $listがnullだったり,idがないとか言われたら空の配列が帰る
        //     return [];
        // }
    }
}
