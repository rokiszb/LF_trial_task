# Symfony Docker Project

This is a Symfony 7.2.5 application configured with Docker for easy development.

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop) installed on your system
- [Docker Compose](https://docs.docker.com/compose/install/) installed on your system

### Setup Instructions

1. Clone this repository:
   ```
   git clone <repository-url>
   cd <project-directory>
   ```

2. Start the Docker environment:
   ```
   docker compose up -d
   ```

3. Install dependencies:
   ```
   docker compose exec php composer install
   ```

4. Set up the database:
   ```
   docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
   ```

5. Load fixtures (sample data):
   ```
   docker compose exec php bin/console doctrine:fixtures:load --no-interaction
   ```

6. Access the application:
    - Web: http://localhost:8080
    - Admin: admin@example.com pass
    - Database: localhost:3306 (Username: symfony, Password: symfony)

### Useful Commands

- Stop the containers: `docker compose down`
- View logs: `docker compose logs -f`
- Access PHP container: `docker compose exec php bash`
- Access database: `docker-compose exec database mysql -usymfony -psymfony symfony`

## Project Structure

- `/src` - Application source code
- `/templates` - Twig templates
- `/public` - Web accessible files
- `/docker` - Docker configuration files