#!/bin/sh
sudo wget http://files.magerun.net/n98-magerun-latest.phar
chmod +x ./n98-magerun-latest.phar
sudo cp ./n98-magerun-latest.phar /usr/local/bin/n98-magerun

echo "exec-once 02_install_magerun.sh executed"