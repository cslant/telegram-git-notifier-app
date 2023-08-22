#!/bin/bash

if [ ! -f .env ]; then
    cp .env.example .env
fi

json_files=(
    "github-event.json"
    "gitlab-event.json"
    "tg-setting.json"
)

for file in "${json_files[@]}"; do
    if [ ! -f "storage/json/$file" ]; then
        cp "config/jsons/$file" "storage/json/$file"
    fi
done

chmod -R 777 storage/json/*.json
