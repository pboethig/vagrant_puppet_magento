#!/bin/bash
#############################################################################################################
#define paths
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"
#############################################################################################################
#set enviroment to dev
enviroment=dev
#############################################################################################################
echo "########################## Install core + sampledata for magento #####################################"

if [ ! -d "$backupStorage" ]
then
    echo "=> Backupstorage $backupStorage does not exist. Creating it"
    sudo mkdir $backupStorage

    echo "=> charmod backupstorage $backupStorage"
    sudo chmod 770 $backupStorage;
fi
#############################################################################################################
#type iniconfig -a to get all configitems. Type ./getconfig -h for help

echo "=> get database login data from configfile"
username=`iniconfig $enviroment database username`
password=`iniconfig $enviroment database password`
databasename=`iniconfig $enviroment database name`
#############################################################################################################
#if samopledata installation was activated (@see config.ini)
#core database
if [ -f "$storagePath/database.sql.gz" ]
then


    backupFileName=database.`date +'%Y-%m-%d-%H-%m-%S'`.sql.gz
    echo "=> backup current database under:$backupStorage/$backupFileName"

    mysqldump -u$username -p$password $databasename | gzip >"/$backupStorage/$backupFileName"

    echo "=> drop database: $databasename"
    mysql -u $username -p$password -e "DROP DATABASE IF EXISTS $databasename"

    echo "=> create database: $databasename"
    mysql -u$username -p$password -e "CREATE DATABASE $databasename"

    echo "=> unpacking dump under:$storagePath/database.sql.gz"
    gunzip -c $storagePath/database.sql.gz > $storagePath/database.sql

    echo "=> importing dump under: $storagePath/database.sql"
    mysql -u$username -p$password $databasename <$storagePath/database.sql

    echo "=> deleting importdump under: $storagePath/database.sql"
    rm -rf $storagePath/database.sql

    echo "=> import database :$databasename finished"
    echo "####################################################################################################"
fi