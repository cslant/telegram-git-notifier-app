#!/bin/bash

# Load environment variables from .env file
set -o allexport
# shellcheck disable=SC1091
source .env
set +o allexport

php -S "$APP_HOST":"$APP_PORT"
