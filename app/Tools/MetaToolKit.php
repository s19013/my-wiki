<?php

namespace App\Tools;

class MetaToolKit
{
    protected static $NameMeta = [];
    protected static $PropertyMeta = [];
    protected static $title = "sundlf";

    public static function replaceNameMeta($object)
    {
        static::$NameMeta = $object;
    }

    public static function replacePropertyMeta($object)
    {
        static::$PropertyMeta = $object;
    }

    public static function setTitle($title)
    {
        static::$title = $title;
    }

    public static function setNameMeta($name, $content)
    {
        static::$NameMeta[$name] = $content;
    }

    public static function setPropertyMeta($property, $content)
    {
        static::$PropertyMeta[$property] = $content;
    }

    public static function render()
    {
        $html = '';
        $html .= '<title>'.static::$title.'</title>'.PHP_EOL;
        foreach (static::$NameMeta as $name => $content) {
            $html .= '<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
        }

        foreach (static::$PropertyMeta as $property => $content) {
            $html .= '<meta property="'.$property.'" content="'.$content.'" />'.PHP_EOL;
        }
        return $html;
    }

    public static function cleanup()
    {
        static::$meta = [];
    }
}
