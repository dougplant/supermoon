
. dbcredentials.sh
. datasourcecredentials.sh

echo "Using a hardcoded isbn for now."

php oniximport/importbiblioshare.php databasename=$database_name databaseuser=$database_user databasepwd=$database_pwd bibliosharetoken=$biblioshareid isbnfile=9781926972794

