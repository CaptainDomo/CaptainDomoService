# CaptainDomoService
This service provides a REST API to manage mailing list subscribers for [Majordomo mailinglists](http://old.greatcircle.com/majordomo/). It offers as three main features:
- Checking registration applicants by provided attributes (firstname, lastname, membership number). If these are matching to a predefined list the applicant's email is auto-registered. If not they are moved to a suspect list.
- Subscribing and unsubscribing participants on a [Majordomo mailinglist](http://old.greatcircle.com/majordomo/) via email commands.
- Managing the predefined list, the suspect list and the subscriber list through secured endpoints of this service.

For these REST-APIs there are two custom build consumers:
* [CaptainDomoUI](CaptainDomoUI): It provides a simplistic web form to apply for registration and do unsubscribes. It could also easily included in existing websites.
* [CaptainDomoManagementUI](https://github.com/CaptainDomo/CaptainDomoManagementUI): A standalone GUI to manage the predefined list, the suspect list and the subscriber list.

# Quickstart
Sounds cool, but how to I get it all running?

To start this backend service we recommend the following steps. (`./`is the startpoint in your local installation)
* Get an (SMTP) email account on a mailserver which is usable via TLS and username & password
* Copy the `./src/Config/Config.php_example` to `./captainDomoServiceConfig/Config.php` and adapt the file contents as you like
* [Get docker](https://www.docker.com/) and install on your machine
* Download the [`docker-compose.yaml`](https://github.com/CaptainDomo/CaptainDomoService/blob/master/docker-compose.yaml) from this repo and locate it in `./docker-compose.yaml`
* Within `./` bring this service up via `docker-compose up --force-recreate` (this will download the latest docker image build from Docker Hub)

Notes:
* The REST API is exposed at port 80 and starts below `src/pulic`. A complete example is: [http://localhost/src/public/management/member](http://localhost/src/public/management/member)
* The MySQL-Database is directly connectable at port 3386
* For your convenience we also included phpMyAdmin at [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/). Username: `root` and empty password

# Techstack
This service is built in PHP and based on the [Slim framework](https://www.slimframework.com/) and uses a MySQL-Database for persistence.
The secured endpoints are protected via basic access authentication.

# Dependencies
This service needs the following external services available to work correctly:
- An SMTP mail server, usable via TLS from public with username & password

## Deployment via Docker
In this repository there is already a Dockerfile included which is based on the LAMP environment provided by [janes/alpine-lamp/](https://hub.docker.com/r/janes/alpine-lamp/). 
Additionally, there is also a `docker-compose.yaml` with a standard configuration available. 
So, if you have a docker environment available, you can just use the pre-built image or built your own. 

## Deployment on a 'typical' Webhosting Environment
If you want to do a 'classic' deployment you need an environment consisting of
- Apache Webserver (we make use of `.htaccess` files and `mod_rewrite`, sorry ;-)
- PHP Runtime Environment on the Webserver
- MySQl (or any compatible) database

# Build & Run 
## Configuration
Before the source code and the application can be build and deployed the configuration as to be set in a central config file named `Config.php` located in the `src/Config` directory. To make you the live easier there is also a pre-structured `Config.php_example` available in this directory.

## Run
### Running a pre-built docker image
If you just want to run this service as a Docker service from the pre-built docker image you can just use the `docker-compose.yaml` together with the `Config.php` placed in the `./captainDomoServiceConfig/` directory. Then you can ignore all the following instructions.

### Running your own build
Modify the `docker-compose.yaml` to build from context instead of using the predefined image. Make sure the `Config.php` is placed in the `./captainDomoServiceConfig/` directory.

### Runnngin without docker
If you wan to, you can deploy the necessary files and directories to an Apache webserver directory of your choice. The following files and directories have to be copied:
- ./db/
- ./src/
- ./vendor/ (from composer, see the build steps)
- ./.htaccess

The `Config.php` has to be placed in the `./src/Config/` directory.

## Build
### Setup
A local PHP and [composer](https://getcomposer.org) installation is needed to build this service.

### Build Steps
To build the application only the external dependencies have to be installed via `php composer.phar install` into the standard `./vendor` directory.


