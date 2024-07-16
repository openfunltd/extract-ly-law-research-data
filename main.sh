#!/bin/bash
BASE=$(dirname "$0");
cd $BASE

php main.php
if test -f "csv/new.csv"; then
    cd csv
    cp old.csv veryold.csv
    cat new.csv old.csv > old.csv
    cat header.csv old.csv > research.csv
    mv research.csv ../taiwan-ly-law-research/research.csv
    mv html/* ../taiwan-ly-law-research/html/
    cd ../taiwan-ly-law-research
    git add --all
    git commit -m "Daily update by cronjob"
fi
