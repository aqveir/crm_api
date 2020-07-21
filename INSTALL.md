# INSTALLATION STEPS
This is the installation steps for Omni CRM API solution structure.

## STEP-1: Clone Code from GIT
```sh
$ git clone https://ellaisys@bitbucket.org/ellaisys/eis_crmomni_api.git <<INSTALLATION_FOLDER_NAME>>
```

## STEP-2: Install dependencies
```sh
$ php composer.phar install  <-- IN WINDOWS
```

## STEP-3: Set Environment veriables
- Rename .env.dev to .env
- Modily .env file using Visual Studio Code

## STEP-4: Set virtual path in XAMPP
- Open the "httpd-vhosts.conf" in the "<xampp_install_directory>\apache\conf\extra" folder
- Add below content

> Listen 8181
> 
> <VirtualHost *:8181>
> 	ServerName localhost
> 	DocumentRoot "<<INSTALLATION_PATH>>/<<INSTALLATION_FOLDER_NAME>>/public"
> 	DirectoryIndex index.php
> 	<Directory "<<INSTALLATION_PATH>>/<<INSTALLATION_FOLDER_NAME>>/public">
> 		Options Indexes FollowSymLinks
> 		AllowOverride All
> 		Require all granted
> 	</Directory>
> 	ErrorLog "<<INSTALLATION_PATH>>/logs/error.log"
> 	CustomLog "<<INSTALLATION_PATH>>/logs/access.log" common
> </VirtualHost>

## STEP-5: Execute App Install Script
```sh
$ crmomni_reload
```