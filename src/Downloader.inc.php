<?php

class Downloader {
    public static function queryAPI() {
        $year = date('Y', strtotime('-1 days'));
        $yearROC = $year - 1911;
        $date = date('md',strtotime('-1 days'));
        $date = $yearROC . $date;

        $query = sprintf('https://www.ly.gov.tw/WebAPI/LawBureauResearch.aspx?type=議題研析&from=%s&to=%s&mode=json', '1130716', '1130716');
        $res = file_get_contents($query);

        if (trim($res) == '{}') {
            return [];
        }

        $json = json_decode($res);
        $reports = $json->LawBureauResearch->Category->Report;
        $reports = self::removeDuplicates($reports);
        usort($reports, [self::class, 'orderByDateByResearchNo']);

        return $reports;
    }

    private static function removeDuplicates($reports)
    {
        $uniqueArray = [];
        $usedLawReportNos = [];

        foreach ($reports as $research) {
            $lawReportNo = $research->{'@LawReportNo'};
            if (!in_array($lawReportNo, $usedLawReportNos)) {
                $uniqueArray[] = $research;
                $usedLawReportNos[] = $lawReportNo;
            }
        }

        return $uniqueArray;
    }

    private static function orderByDateByResearchNo($r1, $r2)
    {
        $no1 = trim($r1->{'@LawReportNo'});
        $no2 = trim($r2->{'@LawReportNo'});
        $date1 = trim($r1->{'@CompletionDate'});
        $date2 = trim($r2->{'@CompletionDate'});
        if ($date1 != $date2) {
            return $date1 > $date2 ? -1 : 1;
        }
        return ($no1 > $no2) ? -1 : 1;
    }
}
