#!/bin/bash

[ "$DEBUG" = "true" ] && set -x

if [[ "$UPDATE_UID_GID" = "true" ]]; then
    echo "Updating www-data uid and gid"

    # shellcheck disable=SC2006
    DOCKER_UID=`stat -c "%u" /var/www/html`
    # shellcheck disable=SC2006
    DOCKER_GID=`stat -c "%g" /var/www/html`

    # shellcheck disable=SC2006
    INCUMBENT_USER=`getent passwd "$DOCKER_UID" | cut -d: -f1`
    # shellcheck disable=SC2006
    INCUMBENT_GROUP=`getent group "$DOCKER_GID" | cut -d: -f1`

    echo "Docker: uid = $DOCKER_UID, gid = $DOCKER_GID"
    echo "Incumbent: user = $INCUMBENT_USER, group = $INCUMBENT_GROUP"

    # shellcheck disable=SC2236
    [ ! -z "${INCUMBENT_USER}" ] && usermod -u 99"$DOCKER_UID" "$INCUMBENT_USER"
    usermod -u "$DOCKER_UID" www-data

    # shellcheck disable=SC2236
    [ ! -z "${INCUMBENT_GROUP}" ] && groupmod -g 99"$DOCKER_GID" "$INCUMBENT_GROUP"
    groupmod -g "$DOCKER_GID" www-data
fi

chown www-data:www-data /var/www/html
chmod 755 /var/www/html

exec "$@"
