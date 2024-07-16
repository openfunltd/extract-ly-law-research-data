<?php

class Content {
    public static function getFullText($dom) {
        $content = $dom->getElementsByTagName('body')[0]->nodeValue;
        $content = Util::superTrim($content);
        return $content;
    }
}
