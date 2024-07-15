<?php
include 'src/Initialer.inc.php';
include 'src/Util.inc.php';
include 'src/RelatedLaws.inc.php';

Initialer::initalizeProject();
$files = array_slice(scandir('html/'), 2);
$rows = [];
$rows[] = ['research_no', 'answer', 'pattern001', 'pattern002', 'pattern003', 'pattern004'];
foreach ($files as $filename) {
    $html_file_at = 'html/' . $filename;
    $dom = Util::getDom($html_file_at);
    $answer = RelatedLaws::getRelatedLaws($dom);
    $results = RelatedLaws::getAllPatternResults($dom);
    $dot_idx = strpos($filename, '.');
    $research_no = substr($filename, 0, $dot_idx);
    $row = [$research_no, $answer, ...$results];
    $rows[] = $row;
}

$fp = fopen('csv/lab_related_laws.csv', 'w');
foreach ($rows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);
