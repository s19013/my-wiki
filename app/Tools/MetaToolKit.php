<?php

namespace App\Tools;

class MetaToolKit
{
    protected static $title = "sundlf  -- タグを使ってメモ､ブックマークを整理";
    protected static $description = "メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです";
    protected static $url = "https://sundlf.com/";
    protected static $lang  = "en";
    protected static $meta = [
        'og:type'        => ['property','website'],
        'og:image'       => ['property','https://sundlf.com/sundlf_logo_og.png'],
        'og:site_name'   => ['property','sundlf'],

        'twitter:card'   => ['name','summary_large_image'],
    ];

    public static function setLang($lang)
    {
        static::$lang = $lang;
    }

    public static function setTitle($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$title = $ja;
            return;
        }
        static::$title = $en;
    }

    public static function setUrl($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$url = $ja;
            return;
        }
        static::$url = $en;
    }

    public static function setImg($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta['og:image'][1]      = $ja;
            return;
        }
        static::$meta['og:image'][1]      = $en;
    }

    public static function setDescription($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$description = $ja;
            return;
        }
        static::$description = $en;
    }

    public static function setMeta($place, $ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta[$place][1] = $ja;
            return;
        }
        static::$meta[$place][1] = $en;
    }

    public static function render()
    {
        $html = "";
        $html .= "<title inertia>".static::$title."</title>".PHP_EOL;
        $html .= '<meta inertia name="description" content="'.static::$description.'"/>'.PHP_EOL;
        $html .= '<meta inertia property="og:description" content="'.static::$description.'"/>'.PHP_EOL;
        $html .= '<meta inertia property="og:title" content="'.static::$title.'"/>'.PHP_EOL;
        $html .= '<meta inertia property="og:url" content="'.static::$url.'"/>'.PHP_EOL;

        foreach (static::$meta as $prop => [$type,$content]) {
            if ($type=='name') {
                $html .= '<meta name="'.$prop.'" content="'.$content.'"/>'.PHP_EOL;
            } else {
                $html .= '<meta property="'.$prop.'" content="'.$content.'"/>'.PHP_EOL;
            }
        }
        return $html;
    }

    public static function cleanup()
    {
        static::$meta = [];
    }
}
