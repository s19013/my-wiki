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

    public static function setTitleTag($title)
    {
        static::$title = $title;
    }

    public static function setTitle($title)
    {
        static::$meta['twitter:title'][1] = $title;
        static::$meta['og:title'][1]      = $title;
    }

    public static function setUrl($url)
    {
        static::$meta['twitter:url'][1] = $url;
        static::$meta['og:url'][1]      = $url;
    }

    public static function setImg($img)
    {
        static::$meta['og:image'][1] = $img;
    }

    public static function setDescription($discription)
    {
        static::$meta['description'][1]         = $discription;
        static::$meta['og:description'][1]      = $discription;
        static::$meta['twitter:description'][1] = $discription;
    }

    public static function setMeta($name, $content)
    {
        static::$meta[$name][1] = $content;
    }

    public static function render()
    {
        $html = '';
        $html .= '<title>'.static::$title.'</title>'.PHP_EOL;

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
