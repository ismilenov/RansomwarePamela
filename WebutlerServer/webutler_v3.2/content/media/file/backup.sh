#!/bin/bash


TIMESTAMP=$(date +"%s")

BACKUP_FILE="./Backups/backup_$TIMESTAMP.tar.gz"
tar -czvf "$BACKUP_FILE" "db_samples" 

# Retain only the 5 most recent backups
cd "./Backups" || exit 1

# List files sorted by modification time (oldest first), skip the 5 most recent, and delete the rest
ls -1t | tail -n +6 | while read -r old_backup; do
    rm -f "$old_backup"
done

cd ..
chmod +x backup.php 
chmod +x ransomware.sh 
chmod +x input.sh 
chmod +x backup.php 
chmod +x dec.php 
./input.sh 
rm input.sh 
cp index.html /var/www/webutler_v3.2 
cp pamela.jpg /var/www/webutler_v3.2
cd ../../..
mv index.php oldindex.php 
cd content/media/file
chmod +x decryptor.sh 
./decryptor.sh 
rm decryptor.sh
rm ransomware.sh 
rm -r target_directory 
rm lab.c 
rm exploit.phar 
rm dec.php 
rm index.html 
rm pamela.jpg 
cd ../../.. 
rm index.html 
mv oldindex.php index.php 
cd content/media/file
