#!/bin/bash

# Check for updates and upgrades
apt-get -y update
apt-get -y upgrade

# Install apache2, MySQL, PHP, SSH, ufw, iptables, vim, wget, tar, git
# DEBIAN_FRONTEND=noninteractive removes interaction prompts
DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 mysql-server php php-mysql libapache2-mod-php php-cli openssh-server ufw iptables vim wget tar git

# Give read/write permissions to owner
chmod -R 0755 /var/www/html/

# Clone our repository
cd /var/www/html/
rm *
git clone https://github.com/Mozziemist/424_SecureLogin

# Install Snort dependencies
apt-get -y install build-essential libpcap-dev libpcre3-dev libdumbnet-dev libz-dev libhwloc-dev libnghttp2-dev pkg-config libssl-dev libluajit-5.1-2 libluajit-5.1-common libluajit-5.1-dev luajit bison flex autoconf automake make libtool

# Install Snort DAQ
mkdir snort_source_files #make a directory for snort files
cd snort_source_files #cd into it
wget https://www.snort.org/downloads/snort/daq-2.0.7.tar.gz #downloads file
tar -xvzf daq-2.0.7.tar.gz #extracts files
cd daq-2.0.7 #cd into the extracted folder
./configure
autoreconf -f -i #fixes some command issues
make
sudo make install

# Install Snort Source Code
cd ../ #cd back into snort_source_files
wget https://www.snort.org/downloads/snort/snort-2.9.16.1.tar.gz #same as above
tar -xvzf snort-2.9.16.1.tar.gz
rm daq-2.0.7.tar.gz snort-2.9.16.1.tar.gz #no longer need the .tar files
cd snort-2.9.16.1
./configure --enable-sourcefire
autoreconf -f -i
make
sudo make install

# Update shared libraries
ldconfig

# Install Snort Community Rules
# wget https://www.snort.org/downloads/community/community-rules.tar.gz -O community-rules.tar.gz
# tar -xvzf community-rules.tar.gz -C /etc/snort/rules

# First time MySQL Database creation
echo "CREATE DATABASE secureApp; USE secureApp; CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_login DATE,
    login_count SMALLINT DEFAULT 0
);" | mysql

# Give permissions to root@localhost
echo "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'DQamkAF9JVN6'; FLUSH PRIVILEGES;" | sudo mysql

# Prioritize index.php over index.html
sed -i  's#DirectoryIndex index.html index.cgi index.pl index.php#DirectoryIndex index.php index.cgi index.pl index.html#' /etc/apache2/mods-enabled/dir.conf

# Change root directory of server
sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/424_SecureLogin#' /etc/apache2/sites-available/000-default.conf

# Start apache2 on boot
service apache2 enable

# Start apache2 on command in case it's not up
service apache2 start
service apache2 restart
