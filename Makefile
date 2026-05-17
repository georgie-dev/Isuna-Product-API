
EXEC = docker compose exec app

scaffold:
	docker run --rm -v $(PWD):/app composer:2.7 \
		create-project laravel/laravel . --prefer-dist --no-interaction
	cp .env.example .env
	touch database/database.sqlite

up:
	docker compose up -d --build
	$(EXEC) php artisan key:generate
	$(EXEC) php artisan migrate
	@echo "App running at http://localhost:8000"

down:
	docker compose down

logs:
	docker compose logs -f app

migrate:
	$(EXEC) php artisan migrate

migrate-fresh:
	$(EXEC) php artisan migrate:fresh --seed

seed:
	$(EXEC) php artisan db:seed

routes:
	$(EXEC) php artisan route:list --path=api

shell:
	$(EXEC) sh

tinker:
	$(EXEC) php artisan tinker

make-all:
	$(EXEC) php artisan make:model Product -mfsp
	$(EXEC) php artisan make:controller Api/ProductController --api
	$(EXEC) php artisan make:request StoreProductRequest
	$(EXEC) php artisan make:request UpdateProductRequest
	$(EXEC) php artisan make:resource ProductResource
	$(EXEC) php artisan make:policy ProductPolicy --model=Product
	@echo "All files generated."


install-packages:
	$(EXEC) composer require laravel/sanctum
	$(EXEC) composer require knuckleswtf/scribe --dev
	$(EXEC) php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
	$(EXEC) php artisan vendor:publish --provider="Knuckles\Scribe\ScribeServiceProvider" --tag=scribe-config
	@echo "Sanctum + Scribe installed."

docs:
	$(EXEC) php artisan scribe:generate
	@echo "📖 Docs at http://localhost:8000/docs"

.PHONY: scaffold up down logs migrate migrate-fresh seed routes shell tinker make-all install-packages docs