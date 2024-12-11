#!/bin/bash

if [ $OSTYPE == "linux-gnu" ] || [ $OSTYPE == "darwin" ]; then
    cp -r ../.hooks/. ../.git/hooks
else 
    cp -r ../.hooks/* ../.git/hooks
fi