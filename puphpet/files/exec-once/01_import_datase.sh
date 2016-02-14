#!/bin/bash
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"

#type ./getconfig -a to get all configitems. Type ./getconfig -h for help
cd ./



sudo chmod -R 777 /vagrant/puphpet/files/exec-once
username=`php /vagrant/puphpet/files/libraries/IniParser/src/getconfig.php dev database username`
password=`php /vagrant/puphpet/files/libraries/IniParser/src/getconfig.php dev database password`
databasename=`php /vagrant/puphpet/files/libraries/IniParser/src/getconfig.php dev database name`


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