#
#

. dbcredentials.sh

mysql -u $database_user --password=$database_pwd -e "drop database $database_name"
mysql -u $database_user --password=$database_pwd -e "create database $database_name charset utf8"
mysql -u $database_user --password=$database_pwd $database_name < ./dbgen/supermoon-schema.sql

php ./dbgen/import-onix-code-lists.php $database_user $database_name $database_pwd
