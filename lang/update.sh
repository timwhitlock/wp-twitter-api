#!/bin/bash
#
# Script pulls down latest translations from Loco system.
# Note that the API key is reset between releases.
#

APIKEY="f81c647f83248b38348c23304b6027a4";

cd "`dirname $0`"

function loco {
    if [ "enGB" = "$1$2" ]; then
        wget "http://localise.biz/api/export/locale/$1-$2.pot?key=$APIKEY" -O "twitter-api.pot"
    else
        wget "http://localise.biz/api/export/locale/$1-$2.po?key=$APIKEY" -O "twitter-api-$1_$2.po"
        msgfmt "twitter-api-$1_$2.po" -o "twitter-api-$1_$2.mo"
    fi
}

loco en GB
loco pt BR
loco de DE

