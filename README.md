---

## Setup Instructions

### Prerequisites
- Docker installed on your machine

### 1. Clone the repository

```bash
git clone <repo-url>
cd Isuna-Product-API
```

### 2. Install dependencies

```bash
docker run --rm -v $(PWD):/app -w /app composer:2.8 install --ignore-platform-reqs --no-interaction
```ompose up -d --build
```

### 3. Copy environment file

```bash
cp .env.example .env
```


### 4. Start the container

```bash
docker compose up -d --build
```

### 5. Generate app key

```bash
docker compose exec app php artisan key:generate
```

### 6. Run migrations and seed

```bash
docker compose exec app php artisan migrate --seed
```

The app will be running at **http://localhost:8000**

---

## .env Example

```env
APP_NAME=Isuna-Product-API
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
```

---

## API Documentation

After starting the app, visit:
http://localhost:8000/docs

Scribe generates interactive documentation with request/response examples for all endpoints.

---

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/register` | Register a new user | No |
| POST | `/api/v1/login` | Login and get token | No |
| POST | `/api/v1/logout` | Revoke token | Yes |

### Products

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/products` | List all products | No |
| POST | `/api/v1/products` | Create a product | Yes |
| GET | `/api/v1/products/{id}` | Get a product | No |
| PUT | `/api/v1/products/{id}` | Update a product | Yes |
| DELETE | `/api/v1/products/{id}` | Delete a product | Yes |

---

## Query Parameters

The `GET /api/v1/products` endpoint supports the following query parameters:

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `search` | string | Search by name or description | `?search=chair` |
| `min_price` | number | Filter by minimum price | `?min_price=10` |
| `max_price` | number | Filter by maximum price | `?max_price=500` |
| `in_stock` | boolean | Only return in-stock products | `?in_stock=true` |
| `sort_by` | string | Sort by `price`, `name`, `created_at` | `?sort_by=price` |
| `sort_dir` | string | Sort direction `asc` or `desc` | `?sort_dir=asc` |
| `page` | integer | Page number | `?page=2` |

---

## API Usage Examples

### Register

```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "George",
    "email": "george@test.com",
    "password": "password",
    "password_confirmation": "password"
  }'
```

### Login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "george@test.com",
    "password": "password"
  }'
```

### List Products

```bash
curl http://localhost:8000/api/v1/products
```

### List Products with Filters

```bash
curl "http://localhost:8000/api/v1/products?search=chair&min_price=50&max_price=500&sort_by=price&sort_dir=asc"
```

### Get Single Product

```bash
curl http://localhost:8000/api/v1/products/{id}
```

### Create Product

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {YOUR_TOKEN}" \
  -d '{
    "name": "Premium Chair",
    "description": "A very comfortable chair.",
    "price": 99.99,
    "stock": 50
  }'
```

### Update Product

```bash
curl -X PUT http://localhost:8000/api/v1/products/{id} \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {YOUR_TOKEN}" \
  -d '{
    "price": 149.99,
    "stock": 30
  }'
```

### Delete Product

```bash
curl -X DELETE http://localhost:8000/api/v1/products/{id} \
  -H "Authorization: Bearer {YOUR_TOKEN}"
```

---

## Architecture

This API follows Laravel best practices with a clear separation of concerns:

- **Controllers** — Thin controllers that handle HTTP requests and delegate to the appropriate layer
- **Form Requests** — Validation and authorization logic isolated from controllers
- **API Resources** — Transform model data into consistent JSON response shapes
- **Policies** — Authorization rules defined per model action
- **Factories & Seeders** — Realistic test data generation for quick database bootstrapping

---

## Error Responses

All errors return consistent JSON:

| Status | Scenario |
|--------|----------|
| `401` | Missing or invalid token |
| `404` | Resource not found |
| `422` | Validation failed |

Example:
```json
{
  "message": "Validation failed.",
  "errors": {
    "price": ["The price field is required."]
  }
}
```