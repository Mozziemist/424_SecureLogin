#!/bin/bash

# Check for updates and upgrades
apt-get -y update
apt-get -y upgrade

# Install apache2, MySQL, PHP, SSH, ufw, iptables, vim, wget, tar, make
# DEBIAN_FRONTEND=noninteractive removes interaction prompts
DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 mysql-server php php-mysql libapache2-mod-php php-cli openssh-server ufw iptables vim wget tar make

# Install Snort dependencies
# apt-get -y install build-essential libpcap-dev libpcre3-dev libdumbnet-dev bison flex

# Install Snort DAQ
mkdir snort_source_files #make a directory for snort files
cd snort_source_files #cd into it
wget https://www.snort.org/downloads/snort/daq-2.0.7.tar.gz #downloads file
tar -xvzf daq-2.0.7.tar.gz #extracts files
cd daq-2.0.7 #cd into the extracted folder
autoreconf -f -i #fixes some command issues
./configure
make
sudo make install

# Install Snort Source Code
cd ../ #cd back into snort_source_files
wget https://www.snort.org/downloads/snort/snort-2.9.16.1.tar.gz #same as above
tar -xvzf snort-2.9.16.1.tar.gz
cd snort-2.9.16.1
./configure --enable-sourcefire
make
sudo make install

# Update shared libraries
ldconfig

# Install Snort Community Rules
# wget https://www.snort.org/downloads/community/community-rules.tar.gz -O community-rules.tar.gz
# tar -xvzf community-rules.tar.gz -C /etc/snort/rules

# Start apache2 on boot
service apache2 enable

# Start apache2 on command in case it's not up
service apache2 start

# Give read/write permissions to owner
chmod -R 0755 /var/www/html/
