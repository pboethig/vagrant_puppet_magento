#!/bin/bash
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"

#set enviroment to deb
enviroment=dev

#type iniconfig -a to get all configitems. Type ./getconfig -h for help
username=`iniconfig $enviroment database username`
password=`iniconfig $enviroment database password`
databasename=`iniconfig $enviroment database name`

#if samopledata installation was activated (@see config.ini)
if [ "`iniconfig $enviroment sampledata install`" == "1" ]
then
    #core database
    if [ -f "$storagePath/database.sql.gz" ]
    then

        echo "##########################Install Sampledata for magento#####################################"

        echo "backup current database under:$backupStorage"

        mysqldump -u$username -p$password $databasename | gzip >"/$backupStorage/database_bak.sql.gz"

        mysql -u $username -p$password -e "DROP DATABASE IF EXISTS $databasename"

        mysql -u$username -p$password -e "CREATE DATABASE $databasename"

        echo "unpacking dump under:$storagePath/database.sql"

        gunzip -c $storagePath/database.sql.gz > $storagePath/database.sql

        echo "importing dump under: $storagePath/database.sql.gz"

        mysql -u$username -p$password $databasename <$storagePath/database.sql

        rm -rf $storagePath/database.sql


        # copy sampledatacodebase to magento root
        echo "copiing sampledata to magentoroot"

        sourcePath=/vagrant/puphpet/files/magentosampledata/*
        targetPath=`iniconfig $enviroment filesystem documentroot`
        echo "sampledatapath: $sourcePath"
        echo "targetPath: $targetPath"

        cp -fr $sourcePath $targetPath
    fi
fi

