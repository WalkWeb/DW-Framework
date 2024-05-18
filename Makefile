PHP_BIN := php

migration:
	migrations/bin/run

fixture:
	migrations/bin/fixtures

test:
	$(PHP_BIN) vendor/bin/phpunit

coverage:
	$(PHP_BIN) vendor/bin/phpunit --coverage-html html

add-migration:
	migrations/bin/create
