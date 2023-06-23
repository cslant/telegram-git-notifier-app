#!/bin/bash

# Load environment variables from .env file
set -o allexport
source .env
set +o allexport

php -S "$APP_HOST":"$APP_PORT"
