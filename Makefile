PHP_BIN := php

migration:
	$(PHP_BIN) migrations/bin/run

test:
	$(PHP_BIN) vendor/bin/phpunit

coverage:
	$(PHP_BIN) vendor/bin/phpunit --coverage-html html

