#!/bin/bash

cd /ellaisys/solutions/crmomni/$DEPLOYMENT_GROUP_NAME

echo "Clearing logs..."
cat /dev/null > storage/logs/laravel.log
echo "Logs cleared."