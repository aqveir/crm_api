#!/bin/bash

echo "Settng file permissions access..."
sudo chown -R ec2-user:ec2-user .
sudo chmod -R 777 storage/.
sudo chmod -R 777 public/.
echo "Permissions granted."