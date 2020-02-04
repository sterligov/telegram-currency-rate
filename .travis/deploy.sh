#!/bin/bash

set -e
git config --global push.default simple
git remote add production ssh://${USER}@${HOST}${PROJECT_PATH}
git push production master

ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no ${USER}@${HOST} "\
    set -e && \
    cd ${PROJECT_PATH} && \
    rm -f .env && \
    echo APP_PORT=${APP_PORT} >> .env && \
    echo TG_WEBHOOK=${TG_WEBHOOK} >> .env && \
    echo TG_TOKEN=${TG_TOKEN} >> .env && \
    echo TG_BOT_NAME=${TG_BOT_NAME} >> .env && \
    docker-compose up -d --build && \
    docker exec -t php composer install"