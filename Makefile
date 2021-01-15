include .env
export $(shell sed 's/=.*//' .env)

DOCKER_COMPOSE      = docker-compose
COMPOSER            = docker exec -it $(APP_NAME)-php-fpm composer
PHP                 = docker exec -it $(APP_NAME)-php-fpm php
SUPERVISOR          = docker exec -it $(APP_NAME)-php-fpm
BUILDKIT            = COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1

ROOT_DIR:=$(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

#### [ Docker 🐳 ]

build: ### build/rebuild images
	$(BUILDKIT) $(DOCKER_COMPOSE) build

up: ## Start the docker hub
	$(BUILDKIT) $(DOCKER_COMPOSE) up -d

start: ## Starts existing containers for a service.
	$(DOCKER_COMPOSE) start

stop: ## Stops running containers without removing them.
	$(DOCKER_COMPOSE) stop

down: ## Stops containers and removes containers, networks, volumes, and images created by up.
	$(DOCKER_COMPOSE) down --remove-orphans

#### [ Supervisor ]
supervisor_status:
	$(SUPERVISOR) tail -f /var/log/supervisor/supervisord.log

#### [ Composer 🎶]
composer_update: ## update composer and vendors
	$(COMPOSER) selfupdate --2
	$(COMPOSER) update

composer_clear_cache: ## clear composer cache
	@printf '\033[1m === [ Clear composer cache and remove vendor folder ] ===\033[0m\n'
	$(COMPOSER) clearcache
	sudo rm -rf vendor

composer_check_requirements: ## check symfony requirements
	@printf '\033[1m === [ Check platform requirements ] ===\033[0m\n'
	$(COMPOSER) check-platform-reqs


#### [ Symfony ]

sf_security_checker: ## check PHP security
	docker run --rm --mount type=bind,source="$(ROOT_DIR)"/,target="$(ROOT_DIR)" --workdir="$(ROOT_DIR)" symfonycorp/cli check:security

sf_env_vars: ## display syfmony environment variables
	$(PHP) bin/console debug:container --env-vars

#### [ Application deployment 🚀 ]

install: composer_check_requirements ## install composer dependencies
	@printf '\033[1m === [ Installation GO GO GO ] ===\033[0m\n'
	$(COMPOSER) install --no-interaction -o
	$(PHP) bin/console doctrine:schema:update --force
	$(PHP) bin/console messenger:setup-transports

fix_permissions:
	sudo chmod -R 777 var/cache var/log

deploy: composer_check_requirements composer_clear_cache ## deploy application
	@printf '\033[1m === [ Simulate a clean deployment ] ===\033[0m\n'
	$(COMPOSER) install --no-interaction --prefer-dist
	$(COMPOSER) dump-autoload --optimize
	$(PHP) bin/console doctrine:schema:update --force

#### [ Database 💽 ]
migrate_db:
	$(PHP) bin/console doctrine:migrations:migrate -n

#### [ Symfony Messenger 📩 ]
messenger_consume:
	$(PHP) bin/console messenger:consume -vv

messenger_debug_configuration:
	$(PHP) bin/console debug:config framework messenger

#### [ Tests Suite ✅ ]

rector_init:
	docker run --rm -v $(ROOT_DIR):/project rector/rector:latest init

rector_dry:
	docker run --rm -v $(ROOT_DIR):/project rector/rector:latest process src --dry-run

rector:
	docker run --rm -v $(ROOT_DIR):/project rector/rector:latest process src

phpunit:
	$(PHP) bin/phpunit

phpunit-coverage:
	$(PHP) bin/phpunit --coverage-html web/test-coverage

ci:
	# Composer validation
	$(COMPOSER) validate
	# Check Twig syntax
	$(PHP) bin/console lint:twig templates
	# Check Yaml configuration files
	$(PHP) bin/console lint:yaml config
	# Check Symfony containers - services injection
	$(PHP) bin/console lint:container
	# Check Code style
	#$(PHP) ./vendor/bin/phpcs
	# Unit and functional testing
	#$(PHP) bin/phpunit
	# Static analyse - require phpunit to be installed (autoload.php)
	#$(PHP) ./vendor/bin/phpstan analyse -c phpstan.neon --no-progress
	#$(PHP) ./vendor/bin/psalm
	#$(PHP) -d memory_limit=2000M ./vendor/bin/phpinsights analyse src -v
	# Load fixtures
	#$(PHP) bin/console doctrine:fixtures:load
