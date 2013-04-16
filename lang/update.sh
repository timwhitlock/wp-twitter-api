#!/bin/bash
#
# Script pulls down latest translations from Loco system.
# The API key is read-only and accesses this project only.
#

cd "`dirname $0`"

function loco {
    if [ "enGB" = "$1$2" ]; then
        wget "http://localise.biz/api/export/locale/$1-$2.po?key=535225653ef7eb710e53c8e421e335e1" -O "twitter-api.pot"
    else
        wget "http://localise.biz/api/export/locale/$1-$2.po?key=535225653ef7eb710e53c8e421e335e1" -O "twitter-api-$1_$2.po"
        msgfmt "twitter-api-$1_$2.po" -o "twitter-api-$1_$2.mo"
    fi
}

loco en GB
loco pt BR


