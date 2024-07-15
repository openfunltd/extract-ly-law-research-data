<?php

class Util {
    public static function getDom($html_file_at) {
        $html = file_get_contents($html_file_at);
        $dom = new DOMDocument();
        @$dom->loadHTML(self::$content_type . $html);
        return $dom;
    }

    private static $content_type = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
}
