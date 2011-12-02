#!/bin/sh

COMPONENTS='ClassLoader Console Finder Process EventDispatcher'

cd vendor/Symfony/Component
for COMPONENT in $COMPONENTS
do
cd $COMPONENT && git fetch origin && git reset --hard origin/master && cd ..
done