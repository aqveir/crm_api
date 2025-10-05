# INSTALLATION STEPS
This is the installation steps for Omni CRM API solution structure.
In case you plan to install the application in the folder named 'crmomni_api', such that this is your application root folder, then replace the placeholder below **{{INSTALLATION_FOLDER_NAME}}** with **crmomni_api**

## STEP-1: Clone Code from GIT
```sh
$ git clone https://bitbucket.org/ellaisys/eis_crmomni_api.git {{INSTALLATION_FOLDER_NAME}}
```

## STEP-2: Install dependencies
```sh
$ php composer.phar install
```

## STEP-3: Set Environment veriables
- Rename .env.dev to .env
- Modily .env file using Visual Studio Code

## STEP-4: Set virtual path in XAMPP
- Open the "httpd-vhosts.conf" in the "<xampp_install_directory>\apache\conf\extra" folder
- Add below content

```xml
Listen 8181

<VirtualHost *:8181>
    ServerName localhost
    DocumentRoot "{{INSTALLATION_PATH}}/{{INSTALLATION_FOLDER_NAME}}/public"
    DirectoryIndex index.php
    <Directory "{{INSTALLATION_PATH}}/{{INSTALLATION_FOLDER_NAME}}/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "{{INSTALLATION_PATH}}/logs/error.log"
    CustomLog "{{INSTALLATION_PATH}}/logs/access.log" common
</VirtualHost>
```

## STEP-5: Execute App Install Script
```sh
$ crmomni_reload
```


# CICD Pipeline - AWS CodePipeline and CodeDeploy

## User Data Script for EC2 Instance
The script below needs to be copied into the User Data section of the EC2 Instance. In case you have already provisioned the instance, you will need to STOP it and then update the User Data. The scipt will need modification based on the type of linux flavor, this is intended for the AWS Linux AMI

Also, you will need to modify the endpoint and region for wget call based on your AWS data centers.

```sh
    #!/bin/bash

    yum update -y

    yum install ruby -y

    yum install wget -y

    # Delete the old code deploy agent
    CODEDEPLOY_BIN="/opt/codedeploy-agent/bin/codedeploy-agent"
    $CODEDEPLOY_BIN stop
    yum erase codedeploy-agent -y

    cd /home/ec2-user

    # Downlaod and install new codedeploy agent
    wget https://aws-codedeploy-ap-south-1.s3.ap-south-1.amazonaws.com/latest/install
    chmod +x ./install
    ./install auto

    # Start the codedeploy agent service
    service codedeploy-agent start
```

## Refernce Material
1. Refer to this video link for step-by-step reference https://youtu.be/K8J6ngMekx4

# Docker

## Build Docker Image
```cmd

    docker build --file Dockerfile -t aqveir-api .

```
