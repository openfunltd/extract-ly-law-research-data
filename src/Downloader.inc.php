<?php

class Downloader {
    public static function queryAPI() {
        $year = date('Y', strtotime('-2 days'));
        $yearROC = $year - 1911;
        $date = date('md',strtotime('-2 days'));
        $date = $yearROC . $date;

        $query = sprintf('https://www.ly.gov.tw/WebAPI/LawBureauResearch.aspx?type=議題研析&from=%s&to=%s&mode=json', $date, $date);
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

        //如果只有一個結果 API 會沒有把它包進 array 裡頭，導致只有一個結果跟多個結果的 json 結構不一樣需要額外處理
        if (property_exists($reports, 'Title')) {
            $uniqueArray[] = $reports;
            return $uniqueArray;
        }

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
