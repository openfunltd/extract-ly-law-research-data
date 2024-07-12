<?php
include 'src/Initialer.inc.php';
include 'src/RelatedLaws.inc.php';

Initialer::initalizeProject(); //建立資料夾 doc/ html/ csv/

$files = array_slice(scandir('html/'), 2);

$rows = [];
$rows[] = ['research_no', 'pattern001', 'pattern002', 'pattern003', 'pattern004'];
foreach ($files as $filename) {
    $results = RelatedLaws::getRelatedLaws($filename);
    $dot_idx = strpos($filename, '.');
    $research_no = substr($filename, 0, $dot_idx);
    $row = [$research_no, ...$results];
    $rows[] = $row;
}

$fp = fopen('csv/lab_relaed_laws.csv', 'w');
foreach ($rows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);
