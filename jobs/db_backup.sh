#!/bin/bash

db_name="s56092__wiki"

backup_dir=~/databasebackup

mkdir -p "$backup_dir"

base_name="wiki-$(date -I)"
suffix=""
counter=1

while [ -e "$backup_dir/${base_name}${suffix}.sql.gz" ]; do
    suffix="_$counter"
    ((counter++))
done

backup_file="${base_name}${suffix}.sql.gz"
full_path="$backup_dir/$backup_file"

echo "backup_file: $full_path"
echo "Starting backup..."

mysqldump --defaults-file=~/replica.my.cnf --host=tools.db.svc.wikimedia.cloud "$db_name" | gzip -9 > "$full_path"

if [ $? -eq 0 ]; then
    echo "Backup and compression completed successfully: $full_path"
else
    echo "Backup or compression failed."
fi

