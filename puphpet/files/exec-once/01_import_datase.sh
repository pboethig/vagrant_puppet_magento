#!/bin/bash
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"

#set enviroment to dev
enviroment=dev

#type iniconfig -a to get all configitems. Type ./getconfig -h for help
username=`iniconfig $enviroment database username`
password=`iniconfig $enviroment database password`
databasename=`iniconfig $enviroment database name`

#if samopledata installation was activated (@see config.ini)
#core database
if [ -f "$storagePath/database.sql.gz" ]
then

    echo "########################## Install core + sampledata for magento #####################################"

    echo "backup current database under:$backupStorage/database_bak`date`.sql.gz"

    mysqldump -u$username -p$password $databasename | gzip >"/$backupStorage/database_bak.`date`.sql.gz"

    mysql -u $username -p$password -e "DROP DATABASE IF EXISTS $databasename"

    mysql -u$username -p$password -e "CREATE DATABASE $databasename"

    echo "unpacking dump under:$storagePath/database.sql"

    gunzip -c $storagePath/database.sql.gz > $storagePath/database.sql

    echo "importing dump under: $storagePath/database.sql.gz"

    mysql -u$username -p$password $databasename <$storagePath/database.sql

    rm -rf $storagePath/database.sql
fi

