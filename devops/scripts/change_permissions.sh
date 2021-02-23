#!/bin/bash

cd /ellaisys/solutions/crmomni/$DEPLOYMENT_GROUP_NAME

echo "Settng file permissions access..."
chown -R ec2-user:ec2-user .
chmod -R 777 storage/.
chmod -R 777 public/.
echo "Permissions granted."