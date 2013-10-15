all: test

test:
	phpunit --stop-on-error tests/
