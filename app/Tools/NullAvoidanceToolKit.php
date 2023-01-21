<?php

namespace App\Tools;

class NullAvoidanceToolKit
{
    // 引数1がnullだったら引数2を返す
    public function ifnull($arg1,$arg2)
    {
        if (is_null($arg1)) { return $arg2; }
        else {return $arg1;}
    }
}
