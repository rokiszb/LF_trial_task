#!/bin/bash

# Colors for console output
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Symfony Docker Starter Script ===${NC}"

# Function to display commands with a prefix
run_command() {
    echo -e "${YELLOW}➤ Running:${NC} $1"
    eval $1
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Command failed!${NC}"
        exit 1
    else
        echo -e "${GREEN}✓ Done!${NC}"
    fi
}

wait_for_db() {
    echo -e "${BLUE}Waiting for database to be ready...${NC}"

    MAX_TRIES=30
    TRIES=0

    while [ $TRIES -lt $MAX_TRIES ]; do
        # Use MYSQL_PWD environment variable to avoid exposing password on command line
        if docker compose exec -e MYSQL_PWD=symfony database mysqladmin ping -h"localhost" -u"symfony" --silent; then
            echo -e "${GREEN}✓ Database is ready!${NC}"
            # Add an extra sleep to ensure MySQL is fully initialized
            sleep 2
            return 0
        fi

        TRIES=$((TRIES+1))
        echo -e "${YELLOW}Database not ready yet. Waiting 2 seconds (Attempt $TRIES/$MAX_TRIES)${NC}"
        sleep 2
    done

    echo -e "${RED}✗ Database failed to become ready after $max_retries attempts!${NC}"
    return 1
}

check_php_user() {
    echo -e "${BLUE}Checking PHP process user...${NC}"
    run_command "docker compose exec php bash -c 'echo \"PHP is running as: \$(id)\"'"
}

case "$1" in
    start)
        echo -e "${GREEN}Starting Docker environment...${NC}"
        run_command "docker compose up -d"
        ;;
    stop)
        echo -e "${GREEN}Stopping Docker containers...${NC}"
        run_command "docker compose down"
        ;;
    restart)
        echo -e "${GREEN}Restarting Docker environment...${NC}"
        run_command "docker compose down"
        run_command "docker compose up -d"
        ;;
    install)
        echo -e "${GREEN}Installing dependencies...${NC}"
        run_command "docker compose up -d"
        run_command "docker compose exec php composer install"
        ;;
    setup)
        echo -e "${GREEN}Setting up the application...${NC}"
        run_command "docker compose build"
        run_command "docker compose up -d"
        run_command "docker compose exec php composer install"
        wait_for_db
        run_command "docker compose exec php bin/console doctrine:migrations:migrate --no-interaction"
        run_command "docker compose exec php bin/console doctrine:fixtures:load --no-interaction"

        echo -e "${GREEN}✓ Setup complete!${NC}"
        echo -e "${GREEN}✓ Access the application at: http://localhost:8080${NC}"
        echo -e "${GREEN}✓ Admin credentials: admin@example.com / pass${NC}"
        ;;
    migrate)
        echo -e "${GREEN}Running database migrations...${NC}"
        wait_for_db
        run_command "docker compose exec php bin/console doctrine:migrations:migrate --no-interaction"
        ;;
    fixtures)
        echo -e "${GREEN}Loading fixtures...${NC}"
        wait_for_db
        run_command "docker compose exec php bin/console doctrine:fixtures:load --no-interaction"
        ;;
    db-ready)
        echo -e "${GREEN}Checking if database is ready...${NC}"
        wait_for_db
        ;;
    cache-images)
        echo -e "${GREEN}Resolving Liip cache for news images...${NC}"
        run_command "docker compose exec php bash -c \"for img in \\\$(find public/uploads/news -type f -name \\\"*.jpg\\\"); do web_path=\\\${img#public/}; bin/console liip:imagine:cache:resolve \\\"\\\$web_path\\\" --filter=large; done\""
        ;;
    cache-clear)
        echo -e "${GREEN}Clearing application cache...${NC}"
        run_command "docker compose exec php bin/console cache:clear"
        ;;
    bash)
        echo -e "${GREEN}Opening bash shell in PHP container...${NC}"
        run_command "docker compose exec php bash"
        ;;
    mysql)
        echo -e "${GREEN}Connecting to MySQL...${NC}"
        wait_for_db
        run_command "docker compose exec -e MYSQL_PWD=symfony database mysql -usymfony symfony"
        ;;
    logs)
        echo -e "${GREEN}Showing logs...${NC}"
        run_command "docker compose logs -f"
        ;;
    clean)
        echo -e "${GREEN}Cleaning up Docker environment (including volumes)...${NC}"
        read -p "This will remove all data in Docker volumes. Are you sure? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            run_command "docker compose down -v"
            echo -e "${GREEN}✓ Environment cleaned successfully!${NC}"
        else
            echo -e "${YELLOW}Cleanup canceled.${NC}"
        fi
        ;;
    *)
        echo -e "${GREEN}Symfony Docker Starter Script${NC}"
        echo -e "Usage: $0 [command]"
        echo
        echo "Commands:"
        echo "  start        - Start Docker containers"
        echo "  stop         - Stop Docker containers"
        echo "  restart      - Restart Docker containers"
        echo "  install      - Install Composer dependencies"
        echo "  setup        - Complete setup (start, install, migrate, fixtures, uploads dir)"
        echo "  migrate      - Run database migrations"
        echo "  fixtures     - Load database fixtures"
        echo "  db-ready     - Check if database is ready"
        echo "  cache-images - Process news images with Liip imagine bundle"
        echo "  cache-clear  - Clear application cache"
        echo "  bash         - Open bash shell in PHP container"
        echo "  mysql        - Connect to MySQL database"
        echo "  logs         - Show Docker container logs"
        echo "  clean        - Remove Docker containers and volumes"
        ;;
esac
