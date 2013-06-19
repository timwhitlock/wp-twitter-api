#!/bin/bash
#
# Script pulls down latest translations from Loco system.
# Note that the API key is reset between releases.
#

APIKEY="535225653ef7eb710e53c8e421e335e1";

cd "`dirname $0`"

function loco {
    if [ "enGB" = "$1$2" ]; then
        wget "http://localise.biz/api/export/locale/$1-$2.po?key=$APIKEY" -O "twitter-api.pot"
    else
        wget "http://localise.biz/api/export/locale/$1-$2.po?key=$APIKEY" -O "twitter-api-$1_$2.po"
        msgfmt "twitter-api-$1_$2.po" -o "twitter-api-$1_$2.mo"
    fi
}

loco en GB
loco pt BR
loco de DE

