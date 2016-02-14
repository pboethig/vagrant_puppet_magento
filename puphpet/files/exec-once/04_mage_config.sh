#!/bin/bash

echo  "exec 04_mage_config"
##############################################################################################################################################################################################################################################################################
storagePath=/vagrant/puphpet/files/databases
backupStorage="$storagePath/backup"
##############################################################################################################################################################################################################################################################################
#configfile found under puphpet/files/config/config.ini
#type iniconfig -a to get all configitems. Type ./getconfig -h for help
##############################################################################################################################################################################################################################################################################
#set enviroment. availabe dev production stage
enviroment=dev
##############################################################################################################################################################################################################################################################################
# Setting storeconfigs
#baseurls
n98-magerun config:set web/secure/base_url `iniconfig $enviroment storeconfig web/secure/base_url`
n98-magerun config:set web/unsecure/base_url `iniconfig $enviroment storeconfig web/unsecure/base_url`
##############################################################################################################################################################################################################################################################################
#package / theme
n98-magerun config:set design/package/name `iniconfig $enviroment storeconfig design/package/name`
n98-magerun config:set design/theme/locale `iniconfig $enviroment storeconfig design/theme/locale`
n98-magerun config:set design/theme/template `iniconfig $enviroment storeconfig design/theme/template`
n98-magerun config:set design/theme/skin `iniconfig $enviroment storeconfig design/theme/skin`
n98-magerun config:set design/theme/layout `iniconfig $enviroment storeconfig design/theme/layout`
n98-magerun config:set design/head/demonotice `iniconfig $enviroment storeconfig design/head/demonotice`
##############################################################################################################################################################################################################################################################################
#rewrites
n98-magerun config:set web/seo/use_rewrites `iniconfig $enviroment storeconfig web/seo/use_rewrites`
##############################################################################################################################################################################################################################################################################
#cookies
n98-magerun config:set admin/security/session_cookie_lifetime `iniconfig $enviroment storeconfig admin/security/session_cookie_lifetime`
n98-magerun config:set web/cookie/cookie_lifetime `iniconfig $enviroment storeconfig web/cookie/cookie_lifetime`
n98-magerun config:set web/cookie/cookie_domain `iniconfig $enviroment storeconfig web/cookie/cookie_domain`
n98-magerun config:set web/cookie/cookie_httponly `iniconfig $enviroment storeconfig web/cookie/cookie_httponly`
n98-magerun config:set web/cookie/cookie_restriction `iniconfig $enviroment storeconfig web/cookie/cookie_restriction`
##############################################################################################################################################################################################################################################################################
#if ibrams is installed
#ibrams soap
n98-magerun config:set ibrams/soapconf/disabled `iniconfig $enviroment storeconfig ibrams/soapconf/disabled`
n98-magerun config:set ibrams/soapconf/baseurl `iniconfig $enviroment storeconfig ibrams/soapconf/baseurl`
n98-magerun config:set ibrams/soapconf/adminuser `iniconfig $enviroment storeconfig ibrams/soapconf_adminuser`
n98-magerun config:set ibrams/soapconf/adminpass `iniconfig $enviroment storeconfig ibrams/soapconf/adminpass`
n98-magerun config:set ibrams/ssoconf/disableredirectafterlogin `iniconfig $enviroment storeconfig ibrams/ssoconf/disableredirectafterlogin`
n98-magerun config:set ibrams/soapconf/debugmode `iniconfig $enviroment storeconfig ibrams/soapconf/debugmode`
##############################################################################################################################################################################################################################################################################
#jquery
n98-magerun config:set ibrams/layout/usejquery `iniconfig $enviroment storeconfig ibrams/layout/usejquery`
##############################################################################################################################################################################################################################################################################
#defaultusers
#adminuser

#delete adminuser
n98-magerun admin:user:delete -f `iniconfig $enviroment defaultadmin username`
#add adminuser
n98-magerun admin:user:create `iniconfig $enviroment defaultadmin username` `iniconfig $enviroment defaultadmin email` `iniconfig $enviroment defaultadmin password` `iniconfig $enviroment defaultadmin firstname` `iniconfig $enviroment defaultadmin lastname`  `iniconfig $enviroment defaultadmin defaultrole`

#frontenduser
#delete frontenduser
n98-magerun customer:delete -f `iniconfig $enviroment defaultuser email`

#add adminuser
n98-magerun customer:create `iniconfig $enviroment defaultuser email` `iniconfig $enviroment defaultuser password` `iniconfig $enviroment defaultuser firstname` `iniconfig $enviroment defaultuser lastname`
##############################################################################################################################################################################################################################################################################
#clear cache
n98-magerun cache:clean
##############################################################################################################################################################################################################################################################################
echo  "############################################################################"
echo  "# Your store is reachable under:" `iniconfig $enviroment storeconfig web/secure/base_url`
echo  "# Admin-Username: `iniconfig $enviroment defaultadmin username`"
echo  "# Admin-Password: `iniconfig $enviroment defaultadmin password`"
echo  ""
echo  "# Customer-email: `iniconfig $enviroment defaultuser email`"
echo  "# Customer-Password: `iniconfig $enviroment defaultuser password`"
echo  "############################################################################"
##############################################################################################################################################################################################################################################################################
