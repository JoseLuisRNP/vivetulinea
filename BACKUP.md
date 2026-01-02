# Guía de Backup de Base de Datos - vivetulinea.es

## 📦 Backup de Producción

### Uso Rápido

```bash
./backup-production.sh
```

Este script:
1. ✅ Se conecta al servidor de producción (159.65.193.194)
2. ✅ Crea un backup completo de la base de datos MySQL
3. ✅ Comprime el backup con gzip
4. ✅ Descarga el backup a tu máquina local (`~/Desktop/backups-vivetulinea/`)
5. ✅ Verifica la integridad del archivo
6. ✅ Limpia backups antiguos en el servidor (mantiene los últimos 5)

### Requisitos Previos

- Tener acceso SSH al servidor: `ploi@159.65.193.194`
- Tu clave SSH debe estar configurada y autorizada
- El servidor debe tener `mysqldump` instalado (normalmente viene con MySQL)

### Primera Vez

Si es la primera vez que te conectas al servidor, acepta la fingerprint SSH:

```bash
ssh ploi@159.65.193.194
# Escribe 'yes' cuando te pregunte si confías en el servidor
exit
```

Luego ya puedes ejecutar el script de backup:

```bash
./backup-production.sh
```

### Ubicación de los Backups

Los backups se guardan en:
- **Local**: `~/Desktop/backups-vivetulinea/vivetulinea-backup-YYYYMMDD-HHMMSS.sql.gz`
- **Servidor**: `/home/ploi/vivetulinea.es/storage/backups/`

### Restaurar un Backup

Para restaurar un backup en local o en desarrollo:

```bash
# 1. Descomprimir el backup
gunzip ~/Desktop/backups-vivetulinea/vivetulinea-backup-20240102-153045.sql.gz

# 2. Restaurar en la base de datos local (usando Sail)
vendor/bin/sail mysql < ~/Desktop/backups-vivetulinea/vivetulinea-backup-20240102-153045.sql

# O si no usas Sail
mysql -u usuario -p nombre_base_datos < ~/Desktop/backups-vivetulinea/vivetulinea-backup-20240102-153045.sql
```

### Restaurar en Producción (¡CUIDADO!)

⚠️ **PELIGRO**: Solo hazlo si estás seguro de lo que estás haciendo:

```bash
# 1. Subir el backup al servidor
scp ~/Desktop/backups-vivetulinea/backup.sql.gz ploi@159.65.193.194:/home/ploi/vivetulinea.es/storage/backups/

# 2. Conectarse al servidor
ssh ploi@159.65.193.194

# 3. Restaurar
cd /home/ploi/vivetulinea.es
gunzip storage/backups/backup.sql.gz
mysql -u $(grep DB_USERNAME .env | cut -d '=' -f2) -p$(grep DB_PASSWORD .env | cut -d '=' -f2) $(grep DB_DATABASE .env | cut -d '=' -f2) < storage/backups/backup.sql
```

## 🔄 Automatizar Backups

### Opción 1: Cron Job en el Servidor

Para crear backups automáticos diarios, puedes configurar un cron job en el servidor:

```bash
# Conectarse al servidor
ssh ploi@159.65.193.194

# Editar el crontab
crontab -e

# Añadir esta línea para backup diario a las 3 AM
0 3 * * * cd /home/ploi/vivetulinea.es && mysqldump -u $(grep DB_USERNAME .env | cut -d '=' -f2) -p$(grep DB_PASSWORD .env | cut -d '=' -f2) $(grep DB_DATABASE .env | cut -d '=' -f2) | gzip > storage/backups/auto-backup-$(date +\%Y\%m\%d).sql.gz
```

### Opción 2: GitHub Actions / CI/CD

También puedes configurar backups programados usando GitHub Actions o tu sistema de CI/CD preferido.

## 📝 Solución de Problemas

### Error: "Permission denied"

Si obtienes un error de permisos:

```bash
chmod +x backup-production.sh
```

### Error: "No such file or directory"

Si el script no encuentra el directorio del proyecto, verifica que la ruta sea correcta:

```bash
ssh ploi@159.65.193.194 "ls -la /home/ploi/vivetulinea.es"
```

### Error: "mysqldump: command not found"

MySQL no está instalado o no está en el PATH. Contacta con tu administrador del servidor.

### Error de Conexión SSH

Verifica que puedes conectarte manualmente:

```bash
ssh ploi@159.65.193.194
```

Si no puedes, verifica tu clave SSH o contacta con el administrador del servidor.

## 🛡️ Seguridad

- ✅ Los backups locales contienen datos sensibles. Guárdalos de forma segura.
- ✅ No compartas los backups públicamente.
- ✅ Considera encriptar los backups con GPG si contienen información muy sensible.
- ✅ Los backups en el servidor se limpian automáticamente (se mantienen solo los últimos 5).

## 📞 Soporte

Si tienes problemas con el script, verifica:
1. Conexión SSH al servidor
2. Permisos de lectura del archivo `.env` en el servidor
3. MySQL está instalado y funcionando
4. Hay suficiente espacio en disco en el servidor y local


