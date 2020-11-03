#!/bin/bash

# Allow traffic on localhost
iptables -A INPUT -i lo -j ACCEPT

# Enable connections on HTTP, HTTPS, and SSH
iptables -A INPUT -p tcp --dport 80 -j ACCEPT #HTTP
iptables -A INPUT -p tcp --dport 443 -j ACCEPT #HTTPS
iptables -A INPUT -p tcp --dport 22 -j ACCEPT #SSH

# Drop all other traffic
iptables -A INPUT -j DROP

# Save rules
/sbin/iptables-save
