<?php

class Downloader {
    public static function queryAPI($target_date = null) {
        if ($target_date == 'all') {
            $now = time();
            $year = date('Y', $now);
            $yearROC = $year - 1911;
            $date = date('md', $now);
            $date_from = '1050301';
            $date_to = $yearROC . $date;
        } elseif (isset($target_date)) {
            $date_from = $target_date;
            $date_to = $target_date;
        } else {
            $year = date('Y', strtotime('-2 days'));
            $yearROC = $year - 1911;
            $date = date('md',strtotime('-2 days'));
            $date = $yearROC . $date;
            $date_from = $date;
            $date_to = $date;
        }

        $query = sprintf('https://www.ly.gov.tw/WebAPI/LawBureauResearch.aspx?type=議題研析&from=%s&to=%s&mode=json', $date_from, $date_to);
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
        if (! is_array($reports)) {
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
        $date1 = trim(substr($r1->{'@CompletionDate'}, 0, 10));
        $date2 = trim(substr($r2->{'@CompletionDate'}, 0, 10));
        if ($date1 != $date2) {
            return $date1 > $date2 ? -1 : 1;
        }
        return ($no1 > $no2) ? -1 : 1;
    }
}
