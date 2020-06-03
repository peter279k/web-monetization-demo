#!/bin/bash

mode=$1

if [[ $mode == '' ]]; then
    echo 'Please set mode option!'

    exit 1;
fi;

echo 'Hi, I am the Web Monetization Demo Installer'

echo 'Check settings...'

if [[ -f "${PWD}/config/settings.php" ]]; then
    echo 'The config/settings.php file is not created... Please create it.'
    echo 'You can refer config/settings.php.example file.'

    exit 1;
fi;

echo 'Check .env file...'

if [[ -f "${PWD}/.env" ]]; then
    echo 'The .env file is not created... Please create it.'
    echo 'You can refer .env file.'

    exit 1;
fi;

cat "${PWD}/.env" | grep "WEB_MONETIZATION_POINTER="

if [[ $? != 0 ]]; then
    echo 'Sorry. The .env file should contain "WEB_MONETIZATION_POINTER" variable'

    exit 1;
fi;

if [[ $mode == '--dev' ]]; then
    echo 'It is devlopment mode...'
    echo 'Create Certification...'
    ./create_trust_local_cert.sh "web-monetization.local"

    echo "Create stunnel.conf..."

    which stunnel 2>&1 > /dev/null

    if [[ $? != 0 ]]; then
        echo 'Please install stunnel package'

        exit 1;
    fi;

    echo '[http]' > "${PWD}/stunnel.conf"
    echo "cert = ${PWD}/fullchain.pem" >> "${PWD}/stunnel.conf"
    echo "key = ${PWD}/privkey.pem" >> "${PWD}/stunnel.conf"
    echo "accept = 3000" >> "${PWD}/stunnel.conf"
    echo "connect = web-monetization.local:5000" >> "${PWD}/stunnel.conf"

    sudo stunnel "${PWD}/stunnel.conf"

    echo 'The web-monetization.local:5000 should be served on built-in PHP server.'
    echo "Please don't forget to run php -S web-monetization.local:5000 -t ./public/ after this installer script is done."
    echo 'And the SSL tunnedl is served on 3000. You can visit https://web-monetization.local:3000 :)'
fi;

if [[ $mode == '--prod' ]]; then
    echo 'It is production mode...'
    echo 'No additional works to check on production mode.'

    echo "Don't forget to set: ini_set('display_errors', 0); on ${pwd}/config/settings.php of line 5"
    echo "Don't forget to set: 'display_error_details' => false, on ${pwd}/config/settings.php line 22"
    echo "Don't forget to set: Database settings on ${pwd}/config/settings.php line 53"
fi;

if [[ -d "{$PWD}/logs" ]]; then
    mkdir "${PWD}/logs"
fi;

echo 'All installation has been completed.'

echo "Please don't forget to impport ${PWD}/monetization_amounts.sql in your MySQL Database :)"
