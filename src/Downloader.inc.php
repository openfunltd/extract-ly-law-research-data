<?php

class Downloader {
    public static function queryAPI() {
        $year = date('Y', strtotime('-1 days'));
        $yearROC = $year - 1911;
        $date = date('md',strtotime('-1 days'));
        $date = $yearROC . $date;

        //TODO to be removed
        //$date = '1130711';

        $query = sprintf('https://www.ly.gov.tw/WebAPI/LawBureauResearch.aspx?type=議題研析&from=%s&to=%s&mode=json', $date, $date);
        $res = file_get_contents($query);

        if (trim($res) == '{}') {
            return [];
        }

        $json = json_decode($res);
        return $json->LawBureauResearch->Category->Report;
    }
}
