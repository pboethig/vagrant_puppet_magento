#!/bin/bash
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"

#type iniconfig -a to get all configitems. Type ./getconfig -h for help
username=`iniconfig dev database username`
password=`iniconfig dev database password`
databasename=`iniconfig dev database name`

if [ -f "$storagePath/database.sql.gz" ]
then
    echo "backup current database under:$backupStorage"

    mysqldump -u$username -p$password $databasename | gzip >"/$backupStorage/database_$date.sql.gz"

    mysql -u $username -p$password -e "DROP DATABASE IF EXISTS $databasename"

    mysql -u$username -p$password -e "CREATE DATABASE $databasename"

    echo "unpacking dump under:$storagePath/database.sql"

    gunzip -c $storagePath/database.sql.gz > $storagePath/database.sql

    echo "importing dump under: $storagePath/database.sql.gz"

    mysql -u$username -p$password $databasename <$storagePath/database.sql

    rm -rf $storagePath/database.sql
fi