#!/bin/bash
#
# Script pulls down latest translations from Loco.
# Note that the API key is reset between releases.
#

APIKEY=""

cd "`dirname $0`"

function loco {
    echo "Pulling $1_$2..."
    if [ "enGB" = "$1$2" ]; then
        wget --quiet "http://localise.biz/api/export/locale/$1-$2.pot?key=$APIKEY" -O "twitter-api.pot"
    else
        wget --quiet "http://localise.biz/api/export/locale/$1-$2.po?key=$APIKEY" -O "twitter-api-$1_$2.po"
        msgfmt --no-hash "twitter-api-$1_$2.po" -o "twitter-api-$1_$2.mo"
    fi
}

loco en GB
loco pt BR
loco de DE
loco ru RU
loco nl NL

echo Done.
