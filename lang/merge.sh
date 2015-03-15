#!/bin/bash
#
# Script merges all PO files with latest POT and builds MOs
#



cd "`dirname $0`"

function loco {
    echo "Merging $1_$2..."
    msgmerge  "twitter-api-$1_$2.po" "twitter-api.pot" -o "twitter-api-$1_$2.po" && \
    msgfmt --no-hash "twitter-api-$1_$2.po" -o "twitter-api-$1_$2.mo"
}

loco pt BR
loco de DE
loco ru RU
loco nl NL
loco es ES

echo Done.
