This is a vagrant image using puphpet.com to generate puppet configs to virtualize a common magento installation.

Puppet installes
- Mariadb
- php 5.6 (PHP-FPM) 
- apache 2.4
look in puppetconfig to extend packages (rdis, rebbitmq, pthon and some more are available. )

Installed Magento-Packages
Magentoversion: 1.9.2.3 Core
- some usefull modules will followup


Prerequisists
- Installed Virtualbox
- Installed Vagrant

Known Issues:
- magerun missing
- magento composer missing
- versionswich of magento missing

Usage:
- checkout sources
- vagrant up
- browse: http://magento.local:8888 or http://localost:8888 reaches the client


