<?php

$fp = fopen("csv/old.csv", "r");
$missing_cnt = 0;
$row_cnt = 0;
$ids = [];
while (($data = fgetcsv($fp, 10000000, ",")) !== false) {
    $idx = count($data) - 1;
    if (trim($data[$idx]) == '') {
        $missing_cnt++;
        $ids[] = $data[0];
    }
    $row_cnt++;
}
echo "Total $row_cnt rows with $missing_cnt missing doc_url \n";
var_dump($ids);
