This is a vagrant image using puphpet.com to generate  a common magento installation.

Puppet installes on the client-os
- MariaDB (Dropin replacement for MySql)
- php 5.6 (PHP-FPM) 
- apache 2.4
- look in puppetconfig to extend client (redis, rabbitmq, python and some more default puphpet packages are preconfigured and only have to activated for installation )
- n98-magerun
- IniParser (Feel free to ask)
- IniParser Commandlinetool (Feel free to ask)

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
  the most imporant storeconfig parameter are configurable during provisioning the box





