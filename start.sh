#!/bin/bash

cd "$(dirname "$0")"
npm install
npm start &
php -S 0.0.0.0:80 -t "$(dirname "$0")" &
wait
