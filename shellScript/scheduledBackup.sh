#31日分保存

DBUSER='windows' #ローカル
# DBUSER='root'
DBPASSWORD='gemini0522'
# DBHOST='xxx.xx.x.x'
DBNAME='my-wiki'
# PORT='3306'
# DATETIME=`date +%Y%m%d`

# 保存先
exportDirectryPath='/home/hideya670/backup/my-wiki'

date=`date +%Y-%m-%d-%H:%M`

filename="my-wiki-"

#31日前の古いファイル
# 31日立ってから実験して見ようと思う
# oldfile=`date --date "31 days ago" +%Y-%m-%d-%H:%M`

# echo $filename$date > result.txt
# echo $oldfile >> result.txt

# mysqldump --opt --all-databases --events --default-character-set=binary -u root --password=パスワード | gzip > $dirpath/$filename.sql.gz;
# mysqldump --single-transaction -u root -p sample > ./sample.dump

# バックアップ実行
mysqldump --single-transaction -u $DBUSER -p$DBPASSWORD $DBNAME | gzip > $dirpath/$filename$date.dump.gz
