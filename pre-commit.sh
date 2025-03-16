#!/bin/bash
mysql_user="root"
mysql_pass=""
db_administrativo="fortaleza_administrativo"
db_administrativo_sql=`pwd`"/db/administrativo.sql"
db_administrativo_temporal="administrativo_temporal"
os=`uname -s`
path_project=`pwd`"/db/"$db_administrativo_temporal".sql"
path_project_truncate=`pwd`"/db/scripts/truncate.sql"


if [ "$os" = "Darwin" ] ; then
	# Mac
	echo "creating backup for database $db_administrativo...";
	/Applications/XAMPP/xamppfiles/bin/mysqldump -u $mysql_user $mysql_pass $db_administrativo > $path_project
	echo "creating database $db_administrativo_temporal...";
	/Applications/XAMPP/xamppfiles/bin/mysql -u $mysql_user $mysql_pass -e "DROP database IF EXISTS administrativo_temporal; create database administrativo_temporal";
	echo "importing backup to database $db_administrativo_temporal...";
    /Applications/XAMPP/xamppfiles/bin/mysql -u $mysql_user $mysql_pas  $db_administrativo_temporal < $path_project
	echo "truncate database $db_administrativo_temporal...";
    /Applications/XAMPP/xamppfiles/bin/mysql -u $mysql_user $mysql_pas  $db_administrativo_temporal < $path_project_truncate
	echo "creating backup for database $administrativo_temporal...";
    /Applications/XAMPP/xamppfiles/bin/mysqldump -u $mysql_user $mysql_pas  $db_administrativo_temporal > $db_administrativo_sql
	/Applications/XAMPP/xamppfiles/bin/mysqladmin -u $mysql_user $mysql_pass -f DROP $db_administrativo_temporal;
	rm  $path_project
#	cd "$path_project/selectra"
#	git add "$path_project/db/administrativo.sql"
	
elif [ "$os" = "Linux" ] ; then
	# "Linux"
	mysqldump -u $mysql_user $mysql_pass $db_administrativo > $path_project
	mysql -u $mysql_user $mysql_pass -e "DROP database IF EXISTS administrativo_temporal; create database administrativo_temporal";
	mysql -u $mysql_user $mysql_pass  $db_administrativo_temporal < $path_project
	mysql -u root $mysql_pass administrativo_temporal < $path_project_truncate
	mysqldump -u root $mysql_pass $db_administrativo_temporal > $db_administrativo_sql
	mysql -u root $mysql_pass -e "drop database administrativo_temporal";
	rm $path_project
	#cd /var/www/pyme_db/selectra
	#git add /var/www/pyme_db/selectra/db/administrativo.sql
else
	# "OTHER"
	echo "OTHER"
fi


echo "DONE!"