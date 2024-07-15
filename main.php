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
$headers = ['research_no', 'title', 'published_date', 'authors', 'doc_url'];
foreach ($reports as $research) {
    $row = [];
    $research_no = trim($research->{'@LawReportNo'});
    $row['research_no'] = $research_no;
    $row['title'] = trim($research->Title);
    $row['related_laws'] = null;
    $row['authors'] = trim($research->{'@Author'});
    $row['published_date'] = substr($research->{'@CompletionDate'}, 0, 10);
    $row['content'] = null;
    $row['doc_url'] = trim($research->FilePath);
    $rows[$research_no] = $row;
}

foreach ($rows as $row) {
    // donwload original files
    $research_no = $row['research_no'];
    $doc_url = $row['doc_url'];
    $dot_idx = strrpos($doc_url, '.');
    $file_extension = mb_substr($doc_url, $dot_idx);
    $save_file_at = $doc_base . $research_no . $file_extension;
    file_put_contents($save_file_at, file_get_contents($doc_url));

    // convert file html via tika
    $save_html_at = $html_base . $research_no . '.html';
    $TIKA_URL = Config::get('TIKA_URL');
    $command = sprintf("curl --silent -T %s %s -H 'Accept: text/html' --output %s", $save_file_at, $TIKA_URL, $save_html_at);
    exec($command);
}

// packing extracted document content into csv
foreach ($rows as $row) {
    $html_file_at = $html_base . $row['research_no'] . '.html';
    echo $html_file_at . "\n";
    $dom = Util::getDom($html_file_at);
    $related_laws_str = RelatedLaws::getRelatedLaws($dom);
    $row['related_laws'] = $related_laws_str;
}
