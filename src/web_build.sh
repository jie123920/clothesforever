#!/bin/bash
gulp build
cp -rf ./web/Public/src/static ./web/Public/dist
cp -rf ./web/Public/src/Common ./web/Public/dist
cp -rf ./web/Public/src/cf  ./web/Public/dist
cp -rf ./web/Public/src/cfhome  ./web/Public/dist