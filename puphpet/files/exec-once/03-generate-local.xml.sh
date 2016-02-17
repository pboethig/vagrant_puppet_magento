##############################################################################################################################################################################################################################################################################
#set enviroment. availabe dev production stage
enviroment=dev
##############################################################################################################################################################################################################################################################################
#generate local.xml
echo  "###########################################################################################################################"
echo  "exec 03-set-local-xml.sh"

echo  "set accessrights 0777 for dev `iniconfig $enviroment filesystem documentroot`"
sudo chmod -R 777 `iniconfig $enviroment filesystem documentroot`

#change to webroot to get n98-magerun in the right location
cd `iniconfig $enviroment filesystem documentroot`

echo  "remove /tmp/magento/var"
sudo rm -rf /tmp/magento/var


echo  "delete `iniconfig $enviroment filesystem documentroot`/app/etc/local.xml to prevent sql exception on wrong accessdata"
sudo rm -rf `iniconfig $enviroment filesystem documentroot`/app/etc/local.xml

echo  "genetrate new local.xml"
n98-magerun local-config:generate `iniconfig $enviroment database host` `iniconfig $enviroment database username` `iniconfig $enviroment database password` `iniconfig $enviroment database name` `iniconfig $enviroment session save`  `iniconfig $enviroment store frontname`