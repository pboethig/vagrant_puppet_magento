This is a vagrant image using puphpet.com to generate puppet configfiles to virtualize a common magento installation.

Puppet installes on the client-os
- MariaDB (Dropin replacement for MySql)
- php 5.6 (PHP-FPM) 
- apache 2.4
- look in puppetconfig to extend client (redis, rabbitmq, python and some more default puphpet packages are preconfigured and only have to activated for installation )

if you are using vagrant 1.8.0 or 1.8.1 you have to fix an rsync error
here is the link how to fix it:
http://magento2-tuts.blogspot.de/2016/02/vagrant-180-and-181-throws-error-on.html

Installed Magento-Packages
Magentoversion: 1.9.2.3 Core
- some usefull modules will follow

Prerequisits
- Installed Virtualbox
- Installed Vagrant
- Installed Vargant Hostmanager "vagrant plugin install vagrant-hostmanager"

Known Issues:
- magerun missing
- magento composer missing
- versionswich of magento missing

Usage:
- checkout sources
- vagrant up
- browse: http://magento.local:8888 or http://localhost:8888 reaches the client


