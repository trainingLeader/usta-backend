#!/bin/bash
#export PATH=$PATH:"/c/Program Files/MySQL/MySQL Server 8.0/bin/"
# Variables de la base de datos
#DB_USER="root"
#DB_PASS="12345678"
#DB_NAME="usta"

# Ruta al archivo de configuración de la base de datos
#Windows
#ENV_FILE="E:/SmartData/usta/GitSmartData/usta-backend/.env"

#En ubuntu
ENV_FILE="/var/www/html/services/usta-backend/.env"

DB_HOST=$(grep 'database.default.hostname' $ENV_FILE | awk -F '=' '{print $2}' | tr -d ' ')
DB_USER=$(grep 'database.default.username' $ENV_FILE | awk -F '=' '{print $2}' | tr -d ' ')
DB_PASS=$(grep 'database.default.password' $ENV_FILE | awk -F '=' '{print $2}' | tr -d ' ')
DB_NAME=$(grep 'database.default.database' $ENV_FILE | awk -F '=' '{print $2}' | tr -d ' ')

# Ruta al archivo temporal que contiene la consulta SQL
QUERY_FILE="$1"
#validamos que exista el archivo
if [ ! -f $QUERY_FILE ]; then
    echo "No se encontro el archivo $QUERY_FILE"
    exit 0
fi
# Ruta al archivo CSV de salida
CSV_FILE="$2"

# Lee la consulta SQL desde el archivo temporal
QUERY=$(cat $QUERY_FILE)

# Ejecuta la consulta SQL y exporta los datos a un archivo CSV

#Windows
#"/c/Program Files/MySQL/MySQL Server 8.0/bin/mysql.exe" --default-character-set=utf8mb3  -u$DB_USER -p$DB_PASS $DB_NAME -e "$QUERY" > $CSV_FILE

#en ubuntu
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME -e "$QUERY" > $CSV_FILE

#validamos que se haya creado el archivo
if [ ! -f $CSV_FILE ]; then
    echo "No se pudo crear el archivo $CSV_FILE"
    exit 0
fi
#borramos el archivo sql
rm $QUERY_FILE
#retornamos 1 para indicar que se ejecuto correctamente
echo 1