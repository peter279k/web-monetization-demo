#!/bin/bash

mode=$1

if [[ $mode == '' ]]; then
    echo 'Please set mode option!'

    exit 1;
fi;

echo 'Hi, I amd the Web Monetization Demo Installer'

echo 'Check settings...'

if [[ -f "$PWD/config/settings.php" ]]; then
    echo 'The config/settings.php file is not created... Please create it.'
    echo 'You can refer config/settings.php.example file.'

    exit 1;
fi;

echo 'Check .env file...'

if [[ -f "$PWD/.env" ]]; then
    echo 'The .env file is not created... Please create it.'
    echo 'You can refer .env file.'

    exit 1;
fi;

if [[ $mode == '--dev' ]]; then
    echo 'It is devlopment mode...'

fi;

if [[ $mode == '--prod' ]]; then
    echo 'It is production mode...'
fi;
