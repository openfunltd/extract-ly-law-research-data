<?php

class RelatedLaws {

    public static function getRelatedLaws($filename)
    {
        $content_type = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html = file_get_contents("html/$filename");
        $dom = new DOMDocument();
        @$dom->loadHTML($content_type . $html);
        $laws_str_001 = self::getByPattern001($dom);
        $laws_str_002 = self::getByPattern002($dom);
        $laws_str_003 = self::getByPattern003($dom);
        return [$laws_str_001, $laws_str_002, $laws_str_003];
    }

    private static function getByPattern001($dom)
    {
        foreach ($dom->getElementsByTagName('b') as $b_tag) {
            $text = $b_tag->nodeValue;
            if (mb_strpos($text, '所涉法規') !== false) {
                $b_dom = $b_tag;            
                break;
            }
        }
        if (! isset($b_dom)) {
            return '';
        }
        $p_dom = $b_dom->parentNode->nextSibling->nextSibling;
        return trim($p_dom->nodeValue);
    }

    private static function getByPattern002($dom)
    {
        foreach ($dom->getElementsByTagName('p') as $p_tag) {
            $text = $p_tag->nodeValue;
            if (mb_strpos($text, '所涉法規') !== false) {
                $p_dom = $p_tag;            
                break;
            }
        }
        if (! isset($p_dom)) {
            return '';
        }
        $p_dom = $p_dom->nextSibling->nextSibling;
        return trim($p_dom->nodeValue);
    }

    private static function getByPattern003($dom)
    {
        foreach ($dom->getElementsByTagName('h1') as $h1_tag) {
            $text = $h1_tag->nodeValue;
            if (mb_strpos($text, '所涉法規') !== false) {
                $h1_dom = $h1_tag;            
                break;
            }
        }
        if (! isset($h1_dom)) {
            return '';
        }
        $p_dom = $h1_dom->nextSibling->nextSibling;
        return trim($p_dom->nodeValue);
    }
}
