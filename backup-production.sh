#!/bin/bash

##############################################################################
# Script de Backup de Base de Datos - vivetulinea.es
##############################################################################
# Uso: ./backup-production.sh
# 
# Este script se conecta al servidor de producción, crea un backup de la 
# base de datos MySQL, lo comprime y lo descarga a tu máquina local.
##############################################################################

set -e  # Salir si hay algún error

# Configuración
SERVER="ploi@159.65.193.194"
PROJECT_PATH="/home/ploi/vivetulinea.es"
BACKUP_DIR="storage/backups"
LOCAL_BACKUP_DIR="$HOME/Desktop/backups-vivetulinea"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_NAME="vivetulinea-backup-$TIMESTAMP.sql"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Backup de Base de Datos - vivetulinea.es${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

# Crear directorio local de backups si no existe
if [ ! -d "$LOCAL_BACKUP_DIR" ]; then
    echo -e "${YELLOW}📁 Creando directorio local de backups...${NC}"
    mkdir -p "$LOCAL_BACKUP_DIR"
fi

# Paso 1: Crear directorio de backups en el servidor
echo -e "${YELLOW}📂 Verificando directorio de backups en servidor...${NC}"
ssh $SERVER "mkdir -p $PROJECT_PATH/$BACKUP_DIR"

# Paso 2: Crear el backup en el servidor
echo -e "${YELLOW}💾 Creando backup de la base de datos...${NC}"
ssh $SERVER "cd $PROJECT_PATH && \
    DB_USER=\$(grep '^DB_USERNAME=' .env | cut -d '=' -f2) && \
    DB_PASS=\$(grep '^DB_PASSWORD=' .env | cut -d '=' -f2 | tr -d '\r' | tr -d '\"' | tr -d \"'\") && \
    DB_NAME=\$(grep '^DB_DATABASE=' .env | cut -d '=' -f2) && \
    echo 'Ejecutando mysqldump...' && \
    mysqldump -u \$DB_USER -p\$DB_PASS \$DB_NAME > $BACKUP_DIR/$BACKUP_NAME 2>/dev/null || \
    mysqldump -u \$DB_USER -p\"\$DB_PASS\" \$DB_NAME > $BACKUP_DIR/$BACKUP_NAME"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Backup creado exitosamente en el servidor${NC}"
else
    echo -e "${RED}❌ Error al crear el backup${NC}"
    exit 1
fi

# Paso 3: Comprimir el backup
echo -e "${YELLOW}🗜️  Comprimiendo backup...${NC}"
ssh $SERVER "cd $PROJECT_PATH && gzip -f $BACKUP_DIR/$BACKUP_NAME"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Backup comprimido exitosamente${NC}"
else
    echo -e "${RED}❌ Error al comprimir el backup${NC}"
    exit 1
fi

# Paso 4: Obtener tamaño del backup
BACKUP_SIZE=$(ssh $SERVER "du -h $PROJECT_PATH/$BACKUP_DIR/$BACKUP_NAME.gz | cut -f1")
echo -e "${BLUE}📊 Tamaño del backup: $BACKUP_SIZE${NC}"

# Paso 5: Descargar el backup
echo -e "${YELLOW}⬇️  Descargando backup a tu máquina local...${NC}"
scp $SERVER:$PROJECT_PATH/$BACKUP_DIR/$BACKUP_NAME.gz "$LOCAL_BACKUP_DIR/"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Backup descargado exitosamente${NC}"
else
    echo -e "${RED}❌ Error al descargar el backup${NC}"
    exit 1
fi

# Paso 6: Verificar integridad
echo -e "${YELLOW}🔍 Verificando integridad del backup descargado...${NC}"
if gzip -t "$LOCAL_BACKUP_DIR/$BACKUP_NAME.gz" 2>/dev/null; then
    echo -e "${GREEN}✅ Backup verificado correctamente${NC}"
else
    echo -e "${RED}❌ El archivo descargado está corrupto${NC}"
    exit 1
fi

# Paso 7: Limpiar backups antiguos (opcional - mantener solo los últimos 5)
echo -e "${YELLOW}🧹 Limpiando backups antiguos en el servidor (manteniendo los últimos 5)...${NC}"
ssh $SERVER "cd $PROJECT_PATH/$BACKUP_DIR && ls -t vivetulinea-backup-*.sql.gz | tail -n +6 | xargs -r rm --"

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  ✅ BACKUP COMPLETADO EXITOSAMENTE${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${BLUE}📍 Ubicación del backup:${NC}"
echo -e "   ${YELLOW}$LOCAL_BACKUP_DIR/$BACKUP_NAME.gz${NC}"
echo ""
echo -e "${BLUE}📊 Información del backup:${NC}"
echo -e "   Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo -e "   Tamaño: $BACKUP_SIZE"
echo ""
echo -e "${BLUE}💡 Para restaurar este backup:${NC}"
echo -e "   ${YELLOW}gunzip $LOCAL_BACKUP_DIR/$BACKUP_NAME.gz${NC}"
echo -e "   ${YELLOW}mysql -u user -p database < $LOCAL_BACKUP_DIR/$BACKUP_NAME${NC}"
echo ""


