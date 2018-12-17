VERSION := 1.0.0
PLUGINSLUG := better-tax-handling
SRCPATH := $(shell pwd)/src

bin/linux/amd64/github-release:
	wget https://github.com/aktau/github-release/releases/download/v0.7.2/linux-amd64-github-release.tar.bz2
	tar -xvf linux-amd64-github-release.tar.bz2
	chmod +x bin/linux/amd64/github-release
	rm linux-amd64-github-release.tar.bz2

vendor: src/vendor
	composer install --dev
	composer dump-autoload -a

test: vendor
	bin/phpunit --coverage-html=./reports

src/vendor:
	cd src && composer install
	cd src && composer dump-autoload -a

build: vendor
	sed -i "s/@##VERSION##@/${VERSION}/" src/better-tax-handling.php
	mkdir -p build
	rm -rf src/vendor
	cd src && composer install --no-dev
	cd src && composer dump-autoload -a
	cp -ar $(SRCPATH) $(PLUGINSLUG)
	zip -r $(PLUGINSLUG).zip $(PLUGINSLUG)
	rm -rf $(PLUGINSLUG)
	mv $(PLUGINSLUG).zip build/
	sed -i "s/${VERSION}/@##VERSION##@/" src/better-tax-handling.php

publish: build bin/linux/amd64/github-release
	bin/linux/amd64/github-release upload \
		--user woocart \
		--repo $(PLUGINSLUG) \
		--tag "v$(VERSION)" \
		--name $(PLUGINSLUG)-$(VERSION).zip \
		--file build/$(PLUGINSLUG).zip

release:
	git stash
	git fetch -p
	git checkout master
	git pull -r
	git tag v$(VERSION)
	git push origin v$(VERSION)
	git pull -r

fmt: vendor
	bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
	bin/phpcbf --standard=WordPress tests --ignore=src/vendor

lint: vendor
	bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
	bin/phpcs --standard=WordPress src --ignore=src/vendor

psr: src/vendor
	composer dump-autoload -a
	cd src && composer dump-autoload -a

i18n:
	wp i18n make-pot src src/framework/langs/$(PLUGINSLUG).pot
