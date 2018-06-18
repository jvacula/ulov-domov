#!/bin/bash

PASSWORD='welcome'
DBNAME='ulov_domov'

sudo apt-get -y update

sudo apt-get -y install php php-cli php-curl php-mysql php-xdebug php-dom php-mbstring zip

sudo sh -c 'echo "\nxdebug.remote_enable=1\nxdebug.remote_host=10.0.2.2\nxdebug.remote_connect_back=1\nxdebug.remote_port=9000" >> /etc/php/7.2/mods-available/xdebug.ini'

sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"

sudo apt-get -y install mysql-server
sudo mysqladmin create ${DBNAME} --user=root --password=${PASSWORD} --host=127.0.0.1

mysql -u root -p${PASSWORD} -e "\
        GRANT ALL PRIVILEGES ON ${DBNAME}.* TO '${DBNAME}'@'%' \
		IDENTIFIED BY '${PASSWORD}' WITH GRANT OPTION; \
		FLUSH PRIVILEGES;"

mysql -u${DBNAME} -p${PASSWORD} ${DBNAME} < ././doc/db/ulov_domov_scheme.sql
mysql -u${DBNAME} -p${PASSWORD} ${DBNAME} < ././doc/db/ulov_domov_data.sql

sudo apt-get -y install git

curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
