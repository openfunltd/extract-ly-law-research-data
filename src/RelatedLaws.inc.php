<?php

class RelatedLaws {

    public static function getRelatedLaws($filename)
    {
        $html = file_get_contents("html/$filename");
        $dom = new DOMDocument();
        @$dom->loadHTML(self::$content_type . $html);
        $laws_str_001 = self::getByPattern001($dom);
        $laws_str_002 = self::getByPattern002($dom);
        $laws_str_003 = self::getByPattern003($dom);
        return [$laws_str_001, $laws_str_002, $laws_str_003];
    }

    private static function getByPattern001($dom)
    {
        foreach ($dom->getElementsByTagName('b') as $b_tag) {
            $text = $b_tag->nodeValue;
            if (self::withKeywords($text, self::$keywords) !== false) {
                $b_dom = $b_tag;            
                break;
            }
        }
        if (! isset($b_dom)) {
            return '';
        }
        $p_dom = $b_dom->parentNode->nextSibling->nextSibling;
        $str = trim($p_dom->nodeValue);
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        return $str;
    }

    private static function getByPattern002($dom)
    {
        foreach ($dom->getElementsByTagName('p') as $p_tag) {
            $text = $p_tag->nodeValue;
            if (self::withKeywords($text, self::$keywords) !== false) {
                $p_dom = $p_tag;            
                break;
            }
        }
        if (! isset($p_dom)) {
            return '';
        }
        $p_dom = $p_dom->nextSibling->nextSibling;
        $str = trim($p_dom->nodeValue);
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        return $str;
    }

    private static function getByPattern003($dom)
    {
        foreach ($dom->getElementsByTagName('h1') as $h1_tag) {
            $text = $h1_tag->nodeValue;
            if (self::withKeywords($text, self::$keywords) !== false) {
                $h1_dom = $h1_tag;            
                break;
            }
        }
        if (! isset($h1_dom)) {
            return '';
        }
        $p_dom = $h1_dom->nextSibling->nextSibling;
        $str = trim($p_dom->nodeValue);
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        return $str;
    }

    private static function withKeywords($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (mb_strpos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private static $content_type = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    private static $keywords = [
        '所涉法規',
        '所涉法律',
    ];

    private static $excluded_keywords = [
        '背景說明',
        '探討研析',
    ];
}
