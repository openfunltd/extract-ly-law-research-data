<?php
include 'src/Downloader.inc.php';
include 'src/Initialer.inc.php';
include 'src/RelatedLaws.inc.php';

Initialer::initalizeProject(); //建立資料夾 doc/ html/ csv/

$reports = Downloader::queryAPI();
$rows = [];
$rows[] = ['research_no', 'title', 'published_date', 'authors', 'file_path'];
foreach ($reports as $research) {
    $row = [];
    $row[] = trim($research->{'@LawReportNo'});
    $row[] = trim($research->Title);
    $row[] = substr($research->{'@CompletionDate'}, 0, 10);
    $row[] = trim($research->{'@Author'});
    $row[] = trim($research->FilePath);
    $rows[] = $row;
}
var_dump($rows);

/*
$files = array_slice(scandir('html/'), 2);
$rows = [];
$rows[] = ['research_no', 'answer', 'pattern001', 'pattern002', 'pattern003', 'pattern004'];
foreach ($files as $filename) {
    $answer = RelatedLaws::getRelatedLaws($filename);
    $results = RelatedLaws::getAllPatternResults($filename);
    $dot_idx = strpos($filename, '.');
    $research_no = substr($filename, 0, $dot_idx);
    $row = [$research_no, $answer, ...$results];
    $rows[] = $row;
}

$fp = fopen('csv/lab_relaed_laws.csv', 'w');
foreach ($rows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);
*/
