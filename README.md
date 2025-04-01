# News Portal Application

A Symfony-based news portal application with features like news management, categorization, and automated weekly statistics emails.

## Features

- News management with categories and tags
- User authentication and role-based permissions
- Image upload and processing
- Responsive frontend design
- Weekly top news statistics emails
- Docker containerization for easy deployment

## Tech Stack

- PHP 8.2
- Symfony 7.2
- MySQL 8.4
- Docker & Docker Compose
- Twig templating (Bootstrap for styling)
- Doctrine ORM
- Symfony Scheduler for automated tasks

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop) installed on your system
- [Docker Compose](https://docs.docker.com/compose/install/) installed on your system

### Setup Instructions

1. Clone this repository:
```
git clone git@github.com:rokiszb/LF_trial_task.git
cd LF_trial_task
```

2. Run the setup script to initialize everything automatically, make sure you can execute file, run:
```
./setup.sh setup
```
And if you dont' have permissions:
```
chmod +x setup.sh
./setup.sh setup
```

```
for first time launch caching images will speed up first page load time
./setup.sh cache-images
```

This script will:
- Start the Docker environment 
- Install dependencies
- Run database migrations
- Load fixtures with sample data
- Set up everything you need to start developing

3. Access the application:
   - Web: http://localhost:8080
   - Admin: admin@example.com pass
   - Database: localhost:3306 (Username: symfony_user, Password: pass)
   - Email testing interface (Mailpit): http://localhost:8025

### Manual Setup (if needed)

If you prefer to run commands individually or if the setup script fails:

1. Start the Docker environment:
```
docker compose up -d
```

2. Install dependencies:
```
docker compose exec php composer install
```

3. Set up the database:
```
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

4. Load fixtures (sample data):
```
docker compose exec php bin/console doctrine:fixtures:load --no-interaction
```

### Useful Commands

- Stop the containers: `docker compose down`
- View logs: `docker compose logs -f`
- Access PHP container: `docker compose exec php bash`
- Access database: `docker compose exec database mysql -usymfony -psymfony symfony`
- Send top news emails (for testing): `docker compose exec php bin/console app:send-top-news`

## Project Structure

- `/src` - Application source code
- `/templates` - Twig templates
- `/public` - Web accessible files
- `/docker` - Docker configuration files

## Email Testing

The project includes Mailpit for testing emails:
- SMTP server runs on port 1025 (used internally by the application)
- To send test email `docker compose exec php bin/console app:send-top-statistics`
- Web interface available at http://localhost:8025 you can check sent emails there

## Development Workflow

1. Start the environment with `./setup.sh setup` or `docker compose up -d`
2. Make changes to the code
3. Test your changes at http://localhost:8080
4. When finished, shut down the environment with `docker compose stop`
