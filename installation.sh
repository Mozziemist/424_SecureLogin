#!/bin/bash

# Check for updates and upgrades
apt-get update
apt-get upgrade

# Install apache2, MySQL, PHP, SSH, ufw, iptables, vim
# DEBIAN_FRONTEND=noninteractive removes interaction prompts
DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 mysql-server php php-mysql libapache2-mod-php php-cli openssh-server ufw iptables vim

# Start apache2 on boot
service apache2 enable

# Start apache2 on command in case it's not up
service apache2 start

# Give read/write permissions to owner
chmod -R 0755 /var/www/html/

# Check if php works
# echo "<?php phpinfo(); ?>" > /var/www/html/info.php
