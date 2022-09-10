.PHONY: tests install fixtures database prepare tests phpstan php-cs-fixer composer-valid doctrine fix analyse

install:
	composer install
	make install-env env=dev db_user=$(db_user) db_password=$(db_password) db_name=$(db_name) db_host=$(db_host) google_api_key=$(google_api_key) google_client_id=$(google_client_id) google_client_secret=$(google_client_secret) twitter_api_key=$(twitter_api_key) twitter_api_secret=$(twitter_api_secret) twitter_token=$(twitter_token)
	make install-env env=test db_user=$(db_user) db_password=$(db_password) db_name=$(db_name) db_host=$(db_host) google_api_key=$(google_api_key) google_client_id=$(google_client_id) google_client_secret=$(google_client_secret) twitter_api_key=$(twitter_api_key) twitter_api_secret=$(twitter_api_secret) twitter_token=$(twitter_token)

install-env:
	cp .env.dist .env.$(env).local
	sed -i -e 's/DATABASE_USER/$(db_user)/' .env.$(env).local
	sed -i -e 's/DATABASE_PASSWORD/$(db_password)/' .env.$(env).local
	sed -i -e 's/DATABASE_HOST/$(db_host)/' .env.$(env).local
	sed -i -e 's/DATABASE_NAME/$(db_name)/' .env.$(env).local
	sed -i -e 's/GOOGLE_API_KEY_ENV/$(google_api_key)/' .env.$(env).local
	sed -i -e 's/GOOGLE_CLIENT_ID_ENV/$(google_client_id)/' .env.$(env).local
	sed -i -e 's/GOOGLE_CLIENT_SECRET_ENV/$(google_client_secret)/' .env.$(env).local
	sed -i -e 's/TWITTER_API_KEY_ENV/$(twitter_api_key)/' .env.$(env).local
	sed -i -e 's/TWITTER_API_SECRET_ENV/$(twitter_api_secret)/' .env.$(env).local
	sed -i -e 's/TWITTER_TOKEN_ENV/$(twitter_token)/' .env.$(env).local
	sed -i -e 's/ENV/$(env)/' .env.$(env).local
	make prepare env=$(env)

fixtures:
	php bin/console doctrine:fixtures:load -n --env=$(env)

database:
	php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	php bin/console doctrine:database:create --env=$(env)
	php bin/console doctrine:query:sql "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" --env=$(env)
	php bin/console doctrine:schema:update --force --env=$(env)

prepare:
	make database env=$(env)
	make fixtures env=$(env)

tests:
	php bin/phpunit --testdox

phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon

fix:
	php vendor/bin/php-cs-fixer fix

composer-valid:
	composer valid

doctrine:
	php bin/console doctrine:schema:valid --skip-sync

twig:
	php bin/console lint:twig templates

yaml:
	php bin/console lint:yaml config

container:
	php bin/console lint:container

analyse: twig yaml composer-valid container doctrine phpstan
