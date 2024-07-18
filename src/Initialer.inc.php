<?php

class Initialer {
    public static function initalizeProject() {
        if (! file_exists('doc')) {
            mkdir('doc');
        }
        if (! file_exists('html')) {
            mkdir('html');
        }
        if (! file_exists('csv')) {
            mkdir('csv');
        }
        if (! file_exists('log')) {
            mkdir('log');
        }
    }
}
