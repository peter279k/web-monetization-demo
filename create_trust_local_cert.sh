#!/bin/bash

# Setup local trusted cert on dev server

host_name=$1

if [[ ${host_name} == "" ]]; then
    echo "Please set host name"
    exit 1;
fi;

wget https://github.com/FiloSottile/mkcert/releases/download/v1.3.0/mkcert-v1.3.0-linux-amd64

sudo mv mkcert-v1.3.0-linux-amd64 /usr/bin/mkcert
sudo chmod +x /usr/bin/mkcert

mkcert -install

mkcert "*.${host_name}" "${host_name}" localhost 127.0.0.1 ::1

mv "_wildcard.${host_name}+4-key.pem" ./privkey.pem
mv "_wildcard.${host_name}+4.pem" ./fullchain.pem

echo "Please remember to add 127.0.0.1 ${host_name} to /etc/hosts file..."
