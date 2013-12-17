PHP = $(OPENSHIFT_DATA_DIR)/php-$(PHP_VERSION)/bin/php

all: vendor

.PHONY: all

vendor: composer.lock
	$(PHP) composer.phar install
	mkdir vendor 2>/dev/null || touch vendor

composer.lock: composer.json
	$(PHP) composer.phar update

composer.json: composer.phar
	$(PHP) composer.phar validate

composer.phar:
	curl https://getcomposer.org/installer | $(PHP)
