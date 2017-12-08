# CaptainDomoService
This service provides a REST API to manage mailing list subscribers for [MajorDomo mailinglists](http://old.greatcircle.com/majordomo/) as three main features:
- Checking registration applicants for provided attributes (firstname, lastname, membership number). If these are matching to a predefined list the applicant's email is auto-registered. If not they are moved to a suspect list.
- Subscribing and unsubscribing participants on a [MajorDomo mailinglist](http://old.greatcircle.com/majordomo/) via email commands.
- Managing the predefined list, the suspect list and the subscriber list through secured endpoints of this service.

# Techstack
This service is built in PHP and based on the [Slim framework](https://www.slimframework.com/) and uses a MySQL-Database for persistence.
The secured endpoints are basic auth protected.

# Dependencies
This service needs the following external services available to work correctly:
- An SMTP mail server reachable via TLS with username & password

## Deployment via Docker
In this repository there is already a Dockerfile included which is based on the LAMP environment provided by [janes/alpine-lamp/](https://hub.docker.com/r/janes/alpine-lamp/). 
Additionally, there is also a `docker-compose.yaml` with a standard configuration available. 
So, if you have a docker environment available, you can just use the pre-built image or built your own. 

## Deployment on a 'typical' Webhosting Environment
If you want to do a classic deployment you need an environment consisting of
- Apache Webserver (we make use of `.htaccess` files and mod_rewrite, sorry ;-)
- PHP Runtime Environment on the Webserver
- MySQl (or any compatible) database

# Build & Run 
## Configuration
Before the source code and the application can be build and deployed the configuration as to be set in a central config file named `Config.php`. To make you the live easier there is also a pre-structured `Config.php_example` available in this directory.

## Run
### Running a pre-built docker image
If you just want to run this service as a Docker service from the pre-built docker image you can just use the `docker-compose.yaml` together with the `Config.php` placed in the `./captainDomoServiceConfig/` directory. Then you can ignore all the following instructions..

### Running your own build
Modify the `docker-compose.yaml` to build from context instead of using the predefined image. Make sure the `Config.php` is placed in the `./captainDomoServiceConfig/` directory.

### Runnngin without docker
If you wan to, you can deploy the necessary files and directories to an Apache webserver directory of your choise. The following files and directories have to be copied:
- ./db/
- ./src/
- ./vendor/ (from composer, see the build steps)
- ./.htaccess

The `Config.php` has to be placed in the `./src/Config/` directory.

## Build
### Setup
A local PHP and [composer](https://getcomposer.org) installation is needed to build this service.

### Build Steps
To build the application only the external dependencies have to be installed via `php composer.phar install` into the standard `vendor` directory.


