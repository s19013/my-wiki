<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class searchToolKit extends Controller
{
    public static function sqlEscape($arg)
    {
        // %と_をエスケープ
        $escaped = preg_replace(
        '/%/',
        '\%',
        $arg);

        $escaped = preg_replace(
        '/_/',
        '\_',
        $escaped);

        return $escaped;
    }

    public static function preparationToAndSearch($arg)
    {
        // 全角スペースを半角に変換
        $spaceConversion = mb_convert_kana($arg, 's');

        // 単語を半角スペースで区切り、配列にする
        return preg_split('/[\s,]+/', $spaceConversion, -1, PREG_SPLIT_NO_EMPTY);

    }
}