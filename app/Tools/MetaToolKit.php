<?php

namespace App\Tools;

class MetaToolKit
{
    protected static $meta = [
        'description'    => ['name','詳細'],

        'og:locale' => ['property','ja_JP'],
        'og:url'         => ['property','url'],
        'og:title'       => ['property','タイトル'],
        'og:type'        => ['property','website'],
        'og:description' => ['property','詳細'],
        'og:image'       => ['property','画像'],
        'og:site_name'   => ['property','sundlf'],

        'twitter:card'   => ['name','summary_large_image'],
        'twitter:domain' => ['property','sundlf.com'],
        'twitter:url'    => ['property','url'],
        'twitter:title'  => ['name','title'],
        'twitter:image'  => ['name','summary_large_image'],
        'twitter:description' => ['name','summary_large_image'],
    ];
    protected static $title = "sundlf";
    protected static $lang  = "en";

    public static function setLang($lang)
    {
        static::$lang = $lang;
    }

    public static function setTitleTag($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$title = $ja;
            return;
        }
        static::$title = $en;
    }

    public static function setTitle($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta['twitter:title'][1] = $ja;
            static::$meta['og:title'][1]      = $ja;
            return;
        }
        static::$meta['twitter:title'][1] = $en;
        static::$meta['og:title'][1]      = $en;
    }

    public static function setUrl($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta['twitter:url'][1] = $ja;
            static::$meta['og:url'][1]      = $ja;
            return;
        }
        static::$meta['twitter:url'][1] = $en;
        static::$meta['og:url'][1]      = $en;
    }

    public static function setImg($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta['og:image'][1]      = $ja;
            static::$meta['twitter:image'][1] = $ja;
            return;
        }
        static::$meta['og:image'][1]      = $en;
        static::$meta['twitter:image'][1] = $en;
    }

    public static function setDescription($ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta['description'][1]         = $ja;
            static::$meta['og:description'][1]      = $ja;
            static::$meta['twitter:description'][1] = $ja;
            return;
        }
        static::$meta['description'][1]         = $en;
        static::$meta['og:description'][1]      = $en;
        static::$meta['twitter:description'][1] = $en;
    }

    public static function setMeta($name, $ja,$en)
    {
        if (static::$lang == "ja") {
            static::$meta[$name][1] = $ja;
            return;
        }
        static::$meta[$name][1] = $en;
    }

    public static function render()
    {
        $html = '';
        // $html .= '<title>'.static::$title.'</title>'.PHP_EOL;

        foreach (static::$meta as $prop => [$type,$content]) {
            if ($type=='name') {
                $html .= '<meta name="'.$prop.'" content="'.$content.'" />'.PHP_EOL;
            } else {
                $html .= '<meta property="'.$prop.'" content="'.$content.'" />'.PHP_EOL;
            }
        }
        return $html;
    }

    public static function cleanup()
    {
        static::$meta = [];
    }
}
