<?php

namespace App\Tools;

use Illuminate\Http\Request;

class MakeListToolKit
{
    // tagIdのリストを返す
    public function makeTagIdList($list)
    {
        if (empty($list)) {return [];}
        $temp = [];
        foreach($list as $tag){array_push($temp,$tag['id']);}
        return $temp;
    }
}
