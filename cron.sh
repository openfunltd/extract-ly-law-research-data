#!/bin/bash
BASE=$(dirname "$0");
cd $BASE

php main.php
if test -f "csv/new.csv"; then
    cd csv
    cp old.csv veryold.csv
    cat new.csv old.csv > merged.csv
    cat header.csv merged.csv > research.csv
    mv merged.csv old.csv
    mv research.csv ../../hf/taiwan-ly-law-research/research.csv
    cd ..
    mv html/* ../hf/taiwan-ly-law-research/html/
    cd ../hf/taiwan-ly-law-research
    git add --all
    git commit -m "Daily update by cronjob"
    git push
fi
