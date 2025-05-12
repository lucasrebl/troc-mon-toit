# Troc mon Toit - Property Rental Platform

A comprehensive property rental platform for Troc mon Toit agency, built with Symfony.

## Features

- Property listings with detailed information
- Advanced search and filtering capabilities
- User authentication and profile management
- Property booking system with availability calendar
- Reviews and ratings for properties
- User favorites system
- Admin panel for complete CRUD operations

## Setup Instructions

### Prerequisites

- Docker and Docker Compose
- Composer (for local development)

### Installation

1. Clone the repository
2. Build and start the Docker containers:

```bash
docker-compose up -d
```

3. Install dependencies:

```bash
docker-compose exec php composer install
```

4. Create database schema:

```bash
docker-compose exec php php bin/console make:migration
docker-compose exec php php bin/console doctrine:migrations:migrate
```

5. Load fixtures (sample data):

```bash
docker-compose exec php php bin/console doctrine:fixtures:load
```

6. Access the application at http://localhost:8080

### Default Users

- Admin: admin@trocmontoit.com / admin123
- Regular User: user@example.com / password

## Technical Stack

- Symfony 6.3
- MySQL 8.0
- PHP 8.2
- Bootstrap 5
- Font Awesome
- Docker

## Key Directories

- `/src/Entity`: Database entities
- `/src/Controller`: Application controllers
- `/src/Repository`: Database repositories
- `/src/Form`: Form types
- `/templates`: Twig templates
- `/public`: Web-accessible files

## Development

For local development without Docker:

1. Configure `.env` file with your database credentials
2. Run Symfony development server:

```bash
symfony server:start
```