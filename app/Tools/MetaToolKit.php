<?php

namespace App\Tools;

class MetaToolKit
{
    protected static $title = [
        "ja" => "sundlf  -- タグを使ってメモ､ブックマークを整理",
        "en" => "sundlf  -- Organize article and bookmarks using tags",
    ];
    protected static $description = [
        "ja" => "メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです",
        "en" => "this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them."
    ];
    protected static $url = [
        "ja" => "https://sundlf.com/",
        "en" => "https://sundlf.com/en"
    ];
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
        static::$title["ja"] = $ja;
        static::$title["en"] = $en;
    }

    public static function setUrl($ja,$en)
    {
        static::$url["ja"] = $ja;
        static::$url["en"] = $en;
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
        static::$description["ja"] = $ja;
        static::$description["en"] = $en;
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
        if (static::$lang == "ja") {
            $html .= "<title inertia>".static::$title["ja"]."</title>".PHP_EOL;
            $html .= '<meta inertia name="description" content="'.static::$description["ja"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:description" content="'.static::$description["ja"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:title" content="'.static::$title["ja"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:url" content="'.static::$url["ja"].'"/>'.PHP_EOL;
        } else {
            $html .= "<title inertia>".static::$title["en"]."</title>".PHP_EOL;
            $html .= '<meta inertia name="description" content="'.static::$description["en"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:description" content="'.static::$description["en"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:title" content="'.static::$title["en"].'"/>'.PHP_EOL;
            $html .= '<meta inertia property="og:url" content="'.static::$url["en"].'"/>'.PHP_EOL;
        }

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
