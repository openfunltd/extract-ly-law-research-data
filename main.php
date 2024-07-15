<?php
include 'src/Config.inc.php';
include 'src/Initialer.inc.php';
include 'src/Util.inc.php';
include 'src/Downloader.inc.php';
include 'src/RelatedLaws.inc.php';
$doc_base = 'doc/';
$html_base = 'html/';

Initialer::initalizeProject(); //建立資料夾 doc/ html/ csv/

$reports = Downloader::queryAPI();
if (count($reports) == 0) {
    echo "Program end with no new research.";
    exit;
}

// packing research metadata into csv fields
$rows = [];
$headers = ['research_no', 'title', 'published_date', 'authors', 'file_path'];
foreach ($reports as $research) {
    $row = [];
    $research_no = trim($research->{'@LawReportNo'});
    $row['research_no'] = $research_no;
    $row['title'] = trim($research->Title);
    $row['published_date'] = substr($research->{'@CompletionDate'}, 0, 10);
    $row['authors'] = trim($research->{'@Author'});
    $row['file_path'] = trim($research->FilePath);
    $rows[$research_no] = $row;
}

// downlaod original files and convert files into html via tika
foreach ($rows as $row) {
    //donwload original files
    $research_no = $row['research_no'];
    $file_path = $row['file_path'];
    $dot_idx = strrpos($file_path, '.');
    $file_extension = mb_substr($file_path, $dot_idx);
    $save_file_at = $doc_base . $research_no . $file_extension;
    file_put_contents($save_file_at, file_get_contents($file_path));

    //convert file html via tika
    $save_html_at = $html_base . $research_no . '.html';
    $TIKA_URL = Config::get('TIKA_URL');
    $command = sprintf("curl --silent -T %s %s -H 'Accept: text/html' --output %s", $save_file_at, $TIKA_URL, $save_html_at);
    exec($command);
}

//packing extracted document content into csv
foreach ($rows as $row) {
    $html_file_at = $html_base . $research_no . '.html';
    $dom = Util::getDom($html_file_at);
}

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
