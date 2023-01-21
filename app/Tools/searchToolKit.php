<?php

namespace App\Tools;

class searchToolKit
{
    //sqlでlike検索する前にするエスケープ処理
    public function sqlEscape($arg)
    {
        // %をエスケープ
        $escaped = preg_replace(
        '/%/',
        '\%',
        $arg);

        // _をエスケープ
        $escaped = preg_replace(
        '/_/',
        '\_',
        $escaped);

        return $escaped;
    }

    //and検索できるように空白で区切って､配列にする
    public function preparationToAndSearch($arg)
    {
        // 全角スペースを半角に変換
        $spaceConversion = mb_convert_kana($arg, 's');

        // 単語を半角スペースで区切り、配列にする
        return preg_split('/[\s,]+/', $spaceConversion, -1, PREG_SPLIT_NO_EMPTY);

    }
}
