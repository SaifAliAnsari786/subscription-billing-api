# Subscription Billing & Metering API

## Overview

This project is a RESTful API built with Laravel 12 for managing subscription billing and usage-based invoicing. It provides subscription lifecycle management, usage event tracking, automated invoice generation, and role-based access control.

The application is containerized using Docker and documented with Swagger (OpenAPI). Automated feature tests are included to verify the core business functionality.

## Technology Stack

- PHP 8.2
- Laravel 12
- MySQL
- Redis
- Laravel Sanctum
- Docker & Docker Compose
- Swagger (L5 Swagger)

## Features

### Authentication

- Login using Laravel Sanctum
- Logout
- Protected API endpoints

### Subscription Management

- Create a subscription
- Change subscription plan with proration
- Cancel subscription at the end of the billing period
- Restrict customers to a single active subscription

### Usage Metering

- Record customer usage events
- Idempotent request handling
- Prevent duplicate usage records

### Billing

- Generate invoices
- Calculate monthly subscription charges
- Calculate overage charges
- Apply tax
- List invoices
- View invoice details

### Authorization

- Administrators can access all invoices
- Customers can access only their own invoices

### Additional Features

- Scheduled invoice generation
- Dockerized development environment
- Swagger API documentation
- Automated feature tests

## Project Structure

```text
app/
├── Console/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/

database/
├── migrations/
├── factories/

routes/

tests/
├── Feature/
└── Unit/
```

## Getting Started

### Clone the Repository

```bash
git clone https://github.com/SaifAliAnsari786/subscription-billing-api.git
cd subscription-billing-api
```

### Configure Environment

```bash
cp .env.example .env
```

### Start Docker Containers

```bash
docker-compose up -d --build
```

### Install Dependencies

```bash
docker-compose exec app composer install
```

### Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### Run Database Migrations

```bash
docker-compose exec app php artisan migrate
```

## Running the Application

Application URL

```text
http://localhost:8000
```

## Docker Commands

Start containers

```bash
docker-compose up -d
```

Stop containers

```bash
docker-compose down
```

View running containers

```bash
docker-compose ps
```

Access the application container

```bash
docker-compose exec app bash
```

## Common Artisan Commands

Run migrations

```bash
docker-compose exec app php artisan migrate
```

Generate Swagger documentation

```bash
docker-compose exec app php artisan l5-swagger:generate
```

Run automated tests

```bash
docker-compose exec app php artisan test
```

Generate invoices manually

```bash
docker-compose exec app php artisan billing:generate-invoices
```

Run scheduled tasks

```bash
docker-compose exec app php artisan schedule:run
```

Clear application cache

```bash
docker-compose exec app php artisan optimize:clear
```

## API Documentation

Generate Swagger documentation

```bash
docker-compose exec app php artisan l5-swagger:generate
```

Swagger UI

```text
http://localhost:8000/api/documentation
```

## Authentication

Login endpoint

```http
POST /api/login
```

Use the returned Bearer token when accessing protected endpoints.

Example:

```text
Authorization: Bearer YOUR_ACCESS_TOKEN
```

## API Endpoints

### Authentication

| Method | Endpoint |
|--------|----------|
| POST | `/api/login` |
| POST | `/api/logout` |

### Subscriptions

| Method | Endpoint |
|--------|----------|
| POST | `/api/subscriptions` |
| POST | `/api/subscriptions/{subscription}/change-plan` |
| POST | `/api/subscriptions/{subscription}/cancel` |

### Usage Events

| Method | Endpoint |
|--------|----------|
| POST | `/api/usage` |

### Invoices

| Method | Endpoint |
|--------|----------|
| POST | `/api/subscriptions/{subscription}/invoice` |
| GET | `/api/invoices` |
| GET | `/api/invoices/{invoice}` |

## Running Tests

Execute the automated test suite.

```bash
docker-compose exec app php artisan test
```

Current test coverage includes:

- Authentication
- Subscription Management
- Usage Events
- Invoice APIs

## Scheduled Invoice Generation

Generate invoices manually

```bash
docker-compose exec app php artisan billing:generate-invoices
```

Run the scheduler

```bash
docker-compose exec app php artisan schedule:run
```

## Assignment Coverage

This implementation includes:

- Authentication using Laravel Sanctum
- Subscription management
- Plan changes with proration
- Subscription cancellation
- Usage metering
- Idempotent event processing
- Invoice generation
- Overage billing
- Tax calculation
- Invoice listing
- Invoice details
- Role-based authorization
- Docker support
- Swagger documentation
- Automated feature tests

## Author

**Saif Ali Ansari**

Laravel Developer

GitHub: https://github.com/SaifAliAnsari786/subscription-billing-api