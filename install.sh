#!/bin/bash

if [ ! -f .env ]; then
    cp .env.example .env
fi

json_files=(
    "github-events.json"
    "gitlab-events.json"
    "tgn-settings.json"
)

for file in "${json_files[@]}"; do
    if [ ! -f "storage/json/tgn/$file" ]; then
        cp "vendor/lbiltech/telegram-git-notifier/config/jsons/$file" "storage/json/tgn/$file"
    fi
done

chmod -R 777 storage/json/*.json
