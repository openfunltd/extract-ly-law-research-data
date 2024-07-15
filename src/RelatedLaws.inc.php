<?php

class RelatedLaws {

    public static function getRelatedLaws($dom)
    {
        $str = self::getByPattern001($dom);
        if ($str != '') {
            return $str;
        }
        $str = self::getByPattern002($dom);
        if ($str != '') {
            return $str;
        }
        $str = self::getByPattern003($dom);
        if ($str != '') {
            return $str;
        }
        $str = self::getByPattern004($dom);
        return $str;
    }

    public static function getAllPatternResults($dom)
    {
        $laws_str_001 = self::getByPattern001($dom);
        $laws_str_002 = self::getByPattern002($dom);
        $laws_str_003 = self::getByPattern003($dom);
        $laws_str_004 = self::getByPattern004($dom);
        return [$laws_str_001, $laws_str_002, $laws_str_003, $laws_str_004];
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
        $str = $p_dom->nodeValue;
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        $str = self::removeRef($str);
        $str = self::superTrim($str);
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
        $str = $p_dom->nodeValue;
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        $str = self::removeRef($str);
        $str = self::superTrim($str);
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
        $str = $p_dom->nodeValue;
        if (self::withKeywords($str, self::$excluded_keywords)) {
            return '';
        }
        $str = self::removeRef($str);
        $str = self::superTrim($str);
        return $str;
    }

    private static function getByPattern004($dom)
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
        $sibling_p_dom = $p_dom->nextSibling->nextSibling;
        $sibling_p_str = $sibling_p_dom->nodeValue;
        if (self::withKeywords($sibling_p_str, self::$excluded_keywords)) {
            $str = $p_dom->nodeValue;
            $str = preg_replace('/：/', ':', $str);
            $str_array = explode(':', $str);
            if (count($str_array) < 2) {
                return '';
            }
            $str = self::removeRef($str_array[1]);
            $str = self::superTrim($str);
            return $str;
        }
        return '';
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

    private static function removeRef($text)
    {
        return preg_replace('/\[[^\]]*\]/', '', $text);
    }

    private static function superTrim($text)
    {
        return mb_ereg_replace('^\s+|\s+$', '', $text);
    }

    private static $keywords = [
        '所涉法規',
        '所涉法律',
    ];

    private static $excluded_keywords = [
        '背景說明',
        '探討研析',
    ];
}
