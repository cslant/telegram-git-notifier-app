#!/bin/bash

docker-compose up -d
docker-compose run --rm server bash -c "composer install"
