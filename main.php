<?php
include 'src/Config.inc.php';
include 'src/Initialer.inc.php';
include 'src/Util.inc.php';
include 'src/Downloader.inc.php';
include 'src/Authors.inc.php';
include 'src/RelatedLaws.inc.php';
include 'src/Content.inc.php';
$doc_base = 'doc/';
$html_base = 'html/';

Initialer::initalizeProject(); //建立資料夾
unlink('csv/new.csv');

$reports = Downloader::queryAPI();
if (count($reports) == 0) {
    echo "Program end with no new research." . "\n";
    exit;
}

// packing research metadata into csv fields
$rows = [];
foreach ($reports as $research) {
    $row = [];
    $research_no = trim($research->{'@LawReportNo'});
    $row['research_no'] = $research_no;
    $row['title'] = trim($research->Title);
    $row['related_laws'] = null;
    $row['authors'] = Authors::repack(trim($research->{'@Author'}));
    $row['published_date'] = substr($research->{'@CompletionDate'}, 0, 10);
    $row['content'] = null;
    $row['doc_url'] = (isset($research->FilePath)) ? trim($research->FilePath) : null;
    $rows[$research_no] = $row;
}

$TIKA_URL = Config::get('TIKA_URL');
foreach ($rows as $row) {
    // skip row without doc_url
    $doc_url = $row['doc_url'];
    if (is_null($doc_url)) {
        continue;
    }

    // donwload original files
    $research_no = $row['research_no'];
    $dot_idx = strrpos($doc_url, '.');
    $file_extension = mb_substr($doc_url, $dot_idx);
    $save_file_at = $doc_base . $research_no . $file_extension;
    if (! file_exists($save_file_at)) {
        file_put_contents($save_file_at, file_get_contents($doc_url));
    }

    // convert file html via tika
    $save_html_at = $html_base . $research_no . '.html';
    if (! file_exists($save_html_at)) {
        $command = sprintf("curl --silent -T %s %s -H 'Accept: text/html' --output %s", $save_file_at, $TIKA_URL, $save_html_at);
        exec($command);
    }
}

// packing extracted document content into csv
foreach ($rows as &$row) {
    // skip row without doc_url
    $doc_url = $row['doc_url'];
    if (is_null($doc_url)) {
        continue;
    }

    // extract data in document
    $html_file_at = $html_base . $row['research_no'] . '.html';
    $dom = Util::getDom($html_file_at);
    $related_laws_str = RelatedLaws::getRelatedLaws($dom);
    $row['related_laws'] = $related_laws_str;
    $row['content'] = Content::getFullText($dom);
}


// write csv
$file = fopen('csv/new.csv', 'w');
foreach ($rows as $data) {
    fputcsv($file, $data);
}
fclose($file);
