#!/bin/bash

OS := $(shell uname)

ifeq ($(OS),Darwin)
	UID = $(shell id -u)
	IP_DEBUG = host.docker.internal
else ifeq ($(OS),Linux)
	UID = $(shell id -u)
	IP_DEBUG = 172.17.0.1
else
	UID = 1000
	IP_DEBUG = host.docker.internal
endif

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

start: ## Start the containers
	docker start server-summa-develop

stop: ## Stop the containers
	docker stop server-summa-develop

restart: ## Restart the containers
	docker restart server-summa-develop

composer-config:
	docker exec server-summa-develop composer install
	docker exec server-summa-develop composer config minimum-stability dev
	docker exec server-summa-develop composer config prefer-stable true
	docker exec server-summa-develop composer require maker orm-fixtures fakerphp/faker doctrine twig symfony/security-bundle symfony/password-hasher jms/serializer-bundle friendsofsymfony/rest-bundle symfony/maker-bundle symfony/orm-pack --with-all-dependencies lexik/jwt-authentication-bundle:* symfony/http-client nelmio/api-doc-bundle symfony/asset symfony/twig-bundle symfony/validator
	docker exec server-summa-develop composer require everapi/freecurrencyapi-php

doctrine-migration:
	docker exec -i server-summa-develop symfony console make:migration
	docker exec -i server-summa-develop symfony console doctrine:migrations:migrate

lexik-certs: ## Installs composer dependencies
	docker exec -i server-summa-develop symfony console lexik:jwt:generate-keypair

#doctrine-generate-migration:
#	docker exec -i server-summa-develop symfony console make:migration
#
#doctrine-run-migrations:
#	docker exec -i server-summa-develop symfony console doctrine:migrations:migrate

seed-fixtures: ## Installs composer dependencies
	docker exec -i server-summa-develop symfony console doctrine:fixtures:load

seed-sql: ## Installs composer dependencies
	docker exec -i database mysql -u summa-admin -p1234 summa < generate_data.sql

sh-api-server: ## ssh's into the be container php
	docker exec -ti server-summa-develop /bin/sh