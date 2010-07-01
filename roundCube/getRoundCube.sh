#!/bin/bash

#################################################
# listOfFiles.txt must be in same directory before running
#
# Downloads plugins from 3 different sources. 
# Puts the files in 3 separate directories
# "proprietary", "thirdPartyGoogle", and "remainingFiles"
#################################################

echo "Getting all Roundcube Plugins"

echo "Checkout Plugins Supplied by Roundcube"
svn co https://svn.roundcube.net/trunk/plugins
mv plugins proprietary

echo "Grabbing all files from Google Code"
wget http://myroundcube.googlecode.com/files/trunk-r-3358.zip
unzip trunk-r*.zip
rm trunk-r*.zip
mv trunk thirdPartyGoogle
mv thirdPartyGoogle/plugins/* thirdPartyGoogle/.
rm -rf thirdPartyGoogle/plugins/

echo "Getting rest of random plugins"
mkdir remainingFiles
mv listOfFiles.txt remainingFiles/.
cd remainingFiles/

while read line
do
    wget "$line"
done <listOfFiles.txt

