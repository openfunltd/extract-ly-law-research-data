<?php

class Util {
    public static function getDom($html_file_at) {
        $html = file_get_contents($html_file_at);
        $dom = new DOMDocument();
        @$dom->loadHTML(self::$content_type . $html);
        return $dom;
    }

    public static function superTrim($text)
    {
        return mb_ereg_replace('^\s+|\s+$', '', $text);
    }

    private static $content_type = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
}
