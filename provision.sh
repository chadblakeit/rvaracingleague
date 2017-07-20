#!/usr/bin/env bash

# Bash doesn't like spaces before or after the '=' sign when assignning commandline args to local variables
SERVER_NAME=$1
SERVER_ALIAS=$2
DB_NAME=$3
DB_USER=$4
DB_PASS=$5

echo "************************************************"
echo "Building server with:"
echo "SERVER_NAME  = $1"
echo "SERVER_ALIAS = $2"
echo "DB_NAME      = $3"
echo "DB_USER      = $4"
echo "DB_PASS      = $5"
echo ""
echo "************************************************"
echo ""
echo ""

echo "***************************************************"
echo "***Set Sever Time Zone To EST***"
echo "***************************************************"
sudo rm /etc/localtime
sudo ln -s /usr/share/zoneinfo/America/New_York /etc/localtime

echo "***************************************************"
echo "***Installing Apache and PHP from APT Repository***"
echo "***************************************************"
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y apache2 php7.0 libapache2-mod-php7.0 php7.0-mcrypt php7.0-cli php7.0-curl php7.0-xml php7.0-gd php7.0-bz2 php7.0-zip debconf-utils # install apache2 and php7
echo ""
echo ""

echo "***************************************************"
echo "*** create a symlink for /vagrant from /var/www ***"
echo "***************************************************"
echo ""
echo "Creating symlink"
rm -rf /var/www # remove /var/www, this will be symlinked later
ln -fs /vagrant /var/www # create a symlink for /vagrant from /var/www
echo ""
echo ""

echo "***************************************************"
echo "**************** Installing MYSQL *****************"
echo "***************************************************"

export DEBIAN_FRONTEND="noninteractive"
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password vagrant' # enter your database's root password
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password vagrant' # re-enter your database's root password
sudo apt-get install -y mariadb-server # install mysql-server. This will prompt for a password and re-enter password, the above two lines will be used for selection
sudo apt-get install -y libapache2-mod-auth-mysql php7.0-mysql # install php7 mysql dependencies
echo ""
echo ""

echo "***************************************************"
echo "*************** Create Apache Vhost ***************"
echo "***************************************************"

a2enmod rewrite # enable the mod_rewrite apache2 mod. This will be needed for the next step
# Create a new apache config for your site
cat >/etc/apache2/sites-available/$SERVER_NAME.conf <<EOL
<VirtualHost *:80>
  ServerName $SERVER_NAME
  ServerAlias $SERVER_ALIAS
  DocumentRoot /var/www/web
  <Directory /var/www/web>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
  </Directory>
  ErrorLog /vagrant/var/logs/rvadev-error.log
  CustomLog /vagrant/var/logs/rvadev-access.log combined
</VirtualHost>
EOL

sudo a2dissite 000-default # disable the default apache site config
sudo a2ensite $SERVER_NAME.conf # enable your site's config that you just created above

sudo service apache2 restart # restart apache
echo ""
echo ""

echo -e "\n--- Install base packages ---\n"
apt-get -y install build-essential python-software-properties git

echo "***************************************************"
echo "***************** Create Database *****************"
echo "***************************************************"
echo "Creating DATABASE: ${DB_NAME}"
mysql -u root -pvagrant -e "CREATE DATABASE ${DB_NAME};" # Creates a new database in mysql. Use the same name as your production database
echo "Creating user: ${DB_USER}  pass: ${DB_PASS}"
mysql -u root -pvagrant -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" # creates a new mysql user, use the same name and password as your production
echo "Granting ALL Priveleges to '${DB_USER}'@'localhost'"
mysql -u root -pvagrant -e "GRANT ALL PRIVILEGES ON *.* TO '${DB_USER}'@'localhost';" # For a production or a publicly accessable database you would not normally grant all privileges across all databases
echo "FLUSHING PRIVILEGES"
mysql -u root -pvagrant -e "FLUSH PRIVILEGES;" # reload the privileges table
echo ""
echo ""

#echo "***************************************************"
#echo "****************** Seed Database ******************"
#echo "***************************************************"
#echo "SEEDING DATABASE"
#mysql -u root -pvagrant $DB_NAME < /vagrant/dump.sql # load the schema and seed the new database from your production dump
#TODO run cron to create current month's file
#echo ""
#echo ""

echo "***************************************************"
echo "****************** Setup Complete *****************"
echo "***************************************************"
echo "  Server Setup:"
echo "    SERVER_NAME  = $1"
echo "    SERVER_ALIAS = $2"
echo "    DB_NAME      = $3"
echo "    DB_USER      = $4"
echo "    DB_PASS      = $5"
echo ""
echo "run 'vagrant ssh' to get instructions to SSH into box."
echo "************************************************"

#echo "Downloading Composer";
cd /vagrant
curl -Ss https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer
sudo chmod 705 /usr/bin/composer