#!/bin/bash
#31日分保存

# DBUSER='windows' #ローカル
DBUSER='root'
DBPASSWORD='gemini0522'
# DBHOST='xxx.xx.x.x'
DBNAME='my-wiki'
# PORT='3306'
# DATETIME=`date +%Y%m%d`

# 保存先
exportDirectryPath='/home/hideya670/backup/my-wiki'
# exportDirectryPath='/c/xampp/htdocs/larvel/myWiki/'

date=`date +%Y-%m-%d-%H-%M`

#31日前の古いファイル
# 31日立ってから実験して見ようと思う
oldfile=`date --date "31 days ago" +%Y-%m-%d-%H-%M`

# バックアップ実行
mysqldump --single-transaction -u $DBUSER -p$DBPASSWORD $DBNAME | gzip > $exportDirectryPath/$DBNAME-$date.dump.gz
