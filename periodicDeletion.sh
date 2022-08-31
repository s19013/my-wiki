#!/bin/sh
# DBUSER='windows'
DBUSER='root'
DBPASSWORD='gemini0522'
# DBHOST='xxx.xx.x.x'
DBNAME='my-wiki'
# PORT='3306'
# DATETIME=`date +%Y%m%d`

# eval echo $(mysql -u $DBUSER -p$DBPASSWORD -D $DBNAME --table -B < periodicDeletion.sql)
# eval echo $(mysql -u $DBUSER -p$DBPASSWORD -D $DBNAME --table < periodicDeletion.sql ) > result.txt
echo "test ok " >result.txt

