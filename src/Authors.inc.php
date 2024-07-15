<?php

class Authors {
    public static function repack($text)
    {
        if (mb_strpos($text, '、') !== false) {
            $authors = explode('、', $text);
            foreach ($authors as &$author) {
                $author = Util::superTrim($author);
            }
            $text = implode(';', $authors);
            return $text;
        }
        $text = Util::superTrim($text);
        return $text;
    }
}
