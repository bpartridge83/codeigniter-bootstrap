#!/bin/bash
# Run with curl -s https://raw.github.com/gist/77e517f5b5a57e10bc8e | bash

set -x  # Print each command

IP=`ifconfig eth0 | awk '/inet addr/ {split ($2,A,":"); print A[2]}'`

START=`git log --pretty=format:'%h' -n 1`

#cd /var/www
#git reset --hard origin/master
git reset --hard HEAD
git pull

rm -R application/cache
mkdir application/cache
chmod 777 application/cache
rm -R web/cache
mkdir web/cache
chmod 777 web/cache
chmod 777 deploy.sh

# cache assets, force
php index.php cloud cacheAssets

/etc/init.d/apache2 restart

END=`git log --pretty=format:'%h' -n 1`

if [ $START != $END ];
then
    codebase deploy $START $END -s "Small Ball Stats ($1:$IP)" \
        -e $1 -b master \
        -h smallball.codebasehq.com -r development:smallball \
        --protocol https
fi

#    codebase deploy $START $END -s Small Ball Stats ($1:$IP) \
#        -e $1 -b master \
#        -h smallball.codebasehq.com -r development:smallball \
#        --protocol https