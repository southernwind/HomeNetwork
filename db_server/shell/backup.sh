#!/bin/bash

if [ $# -ne 3 ]; then
    echo "ERROR" 1>&2
    exit 1
fi

MYSQL_PASSWORD=$1
RSYNC_HOST=$2
FORMAT=$3

DATE=`date +${FORMAT}`

# dump
mysqldump -uroot -p${MYSQL_PASSWORD} -h 127.0.0.1 -A --ignore-table=mysql.* --ignore-table=information_schema.* --ignore-table=performance_schema.* --extended-insert --single-transaction > /tmp/database.dump

# compress
tar -cjf /root/backup/database.dump-${DATE}.bz2 /tmp/database.dump --overwrite

# rsync
rsync -rav /root/backup rsync://${RSYNC_HOST}/db_server_backup