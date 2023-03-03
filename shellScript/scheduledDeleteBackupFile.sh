#!/bin/bash
DBNAME='my-wiki'

# ファイルがある場所
exportDirectryPath='/home/bitnami/backup/my-wiki'

#31日前の古いファイルを消す
olddate=`date --date "31 days ago" +%Y-%m-%d-%H-%M`
rm -f  $exportDirectryPath/$DBNAME-$olddate.dump.gz
