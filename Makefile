PHP_BIN := php

migration:
	$(PHP_BIN) migrations/bin/run

fixture:
	$(PHP_BIN) migrations/bin/fixtures

test:
	$(PHP_BIN) vendor/bin/phpunit

coverage:
	$(PHP_BIN) vendor/bin/phpunit --coverage-html html

add-migration:
	$(PHP_BIN) migrations/bin/create
