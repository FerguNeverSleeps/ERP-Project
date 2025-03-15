#!/bin/bash
##VER 0.1
########## SCRIPT PARA LA CREACION DE TAREA DE BACKUP EN GNU/LINUX #####


#### Se asigna la ruta donde se va a colocar el script 

script_path=/var/www/amaxonia_mysql_backup.sh

##### Verificacion de que exista el script  ######

if [ -f $script_path  ]
then
echo "El archivo existe por favor elimine el archivo $script_path y vuelva ejecutar el script"
else

##### Creacion del script en el path 

cat << EOF > $script_path

###### SCRIPT PARA BACKUP DE LAS BASES DE DATOS SELECTRA #####

#Definicion de variables para el manejo del script de respaldo de las bases de datos de amaxonia
erp_conf=erp_conf
erp_administrativo=erp_administrativo
erp_rrhh=erp_rrhh
path_backup=/var/www/backup
userdb=root
passdb=root
fecha=\`date +"%d-%b-%Y"i\`

bd=(\$erp_conf \$erp_administrativo \$erp_rrhh)
i=0
while [ "\$i" -le 2  ];
do
#mysql -u \$userdb -p\$passdb bd[\$i] > \$path_backup
echo "mysql -u \$userdb -p\$passdb \${bd[\$i]} > \$path_backup-\$fecha.sql"
(( i++ ))
done
EOF
#se asignan permisos para la ejecución del script
chmod +x $script_path
#se agrega el script en el crontab para su ejecución todos los días a la 1:00 am
echo "0 1 * * * root $script_path" >> /etc/crontab
fi
