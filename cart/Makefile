ifndef APP_ENV
	include .env
endif

cache-clear:
	@bin/console cache:clear --no-warmup || rm -rf var/cache/*
.PHONY: cache-clear

cache-warmup: cache-clear
	@bin/console cache:warmup
.PHONY: cache-warmup

serve:
	@printf "\033[32;49mServer listening on http://127.0.0.1:8000\033[39m\n"
	@printf "Quit the server with CTRL-C.\n"
	php -S 127.0.0.1:8000 -t web
.PHONY: serve
