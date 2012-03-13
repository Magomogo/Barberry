#!/bin/bash

echo "Creating scripts/magic.mime.mgc file for content type euristic recognition..."

DIR="$( cd "$( dirname "$0" )" && pwd )"
TARGET_FILE=$DIR"/../../magic.mime"

>$TARGET_FILE
find $DIR/libmagic/ -type f | while read F
do
    cat $F >> $TARGET_FILE
    echo -e "\n" >> $TARGET_FILE
done

cat ${DIR}/patch.mime >> $TARGET_FILE

cd "$(dirname "$TARGET_FILE")"
file -C -m $TARGET_FILE
rm $TARGET_FILE

echo "DONE"