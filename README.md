This is a vagrant image using puphpet.com to generate  a common magento installation.

Puppet installes on the client-os
- MariaDB (Dropin replacement for MySql)
- php 5.6 (PHP-FPM) 
- apache 2.4
- look in puppetconfig to extend client (redis, rabbitmq, python and some more default puphpet packages are preconfigured and only have to activated for installation )
- n98-magerun

Installed Magento-Packages
- Magentoversion: 1.9.2.3 Core
- Sampladata

Prerequisits
- Installed Virtualbox
- Installed Vagrant
- Installed Vargant Hostmanager "vagrant plugin install vagrant-hostmanager"

Usage:
- checkout this repository
- vagrant up
- browse: http://magento.dev or http://localhost reaches the client


Addition:
- you can configure the installation by editing /vagrant/puphpet/files/config/config.ini

following parameters are available
environment = dev


[dev]
filesystem.documentroot = /var/www/magento

sampledata.install = 1

debug = true
database.host = localhost
database.name = magento
database.username = root
database.password = 123

session.save = files

store.frontname = admin
storeconfig.web/secure/base_url= http://magento.dev/
storeconfig.web/unsecure/base_url= http://magento.dev/
storeconfig.web/seo/use_rewrites = 0
storeconfig.design/head/demonotice = 0

storeconfig.web/cookie/cookie_lifetime = 9000000
storeconfig.admin/security/session_cookie_lifetime = 900000
storeconfig.web/cookie/cookie_domain = magento.dev
storeconfig.web/cookie/cookie_httponly = 1
storeconfig.web/cookie/cookie_restriction = 0


defaultadmin.username = admin
defaultadmin.password = ibrams
defaultadmin.email = shopadmin@ibrams.com
defaultadmin.firstname = admin
defaultadmin.lastname = lastname
defaultadmin.defaultrole = Administrators

defaultuser.username = admin
defaultuser.password = ibrams
defaultuser.email = shopcustomer@ibrams.com
defaultuser.firstname = shopcustomer
defaultuser.lastname = lastname

storeconfig.design/package/name = rwd
storeconfig.design/theme/locale = default
storeconfig.design/theme/template = default
storeconfig.design/theme/skin = default
storeconfig.design/theme/layout = default


[staging : dev]
database.name = stage

[production : staging]
debug = false;
database.name = production




