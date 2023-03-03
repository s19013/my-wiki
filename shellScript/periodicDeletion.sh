#!/bin/sh
# 古いデータをデータベースから消す

# DBUSER='windows' #ローカル
DBUSER='root'
DBPASSWORD='gemini0522'
# DBHOST='xxx.xx.x.x'
DBNAME='my-wiki'
# PORT='3306'
# DATETIME=`date +%Y%m%d`

mysql -u $DBUSER -p$DBPASSWORD -D $DBNAME < periodicDeletion.sql
