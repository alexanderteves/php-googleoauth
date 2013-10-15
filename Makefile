all: test

test:
	phpunit --stop-on-failure -c tests/config.xml
