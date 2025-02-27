#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Database path
DB_PATH="/var/www/database/database.sqlite"
DB_PATH_TESTING="/var/www/database/testing.sqlite"

echo -e "${GREEN}Starting Crypto Aggregator Services...${NC}\n"

# Function to check if a process is running
is_running() {
    pgrep -f "$1" >/dev/null
}

# Function to start a service
start_service() {
    if ! is_running "$1"; then
        echo -e "${YELLOW}Starting $2...${NC}"
        $1 &
        sleep 2
        
        # Verify service started successfully
        if ! is_running "$1"; then
            echo -e "${RED}Failed to start $2${NC}"
            return 1
        fi
    else
        echo -e "${GREEN}$2 is already running${NC}"
    fi
}


# Clear database queue (since you're using database driver)
echo -e "${YELLOW}Install composer dependencies...${NC}"
if [ ! -e "/var/www/vendor" ]; then
    composer install --no-interaction --no-progress
fi

# Create the SQLite database file if it does not exist
if [ ! -e "$DB_PATH" ]; then
    echo -e "${YELLOW}Creating SQLite database file...${NC}"
    touch "$DB_PATH"
fi

# Set permissions to ensure it's writable
echo -e "${YELLOW}Setting permissions for SQLite database...${NC}"
chmod 666 "$DB_PATH"


# Run vite build
echo -e "${YELLOW} Building Frontend...${NC}"
npm install
npm run build


Run migrations
php artisan key:generate
php artisan migrate:fresh --seed # Ensure fresh job tables

Clear cache configurations
echo -e "${YELLOW}Clearing cache configurations...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear


#Set Db for testing
# Create the SQLite database file if it does not exist
if [ ! -e "$DB_PATH_TESTING" ]; then
    echo -e "${YELLOW}Creating SQLite database file...${NC}"
    touch "$DB_PATH_TESTING"
fi


# Set permissions to ensure it's writable
echo -e "${YELLOW}Setting permissions for SQLite database...${NC}"
chmod 666 "$DB_PATH_TESTING"


# Clear database queue (since you're using database driver)
echo -e "${YELLOW}Clearing queues...${NC}"
php artisan key:generate
php artisan migrate:fresh --force # Ensure fresh job tables

# Run available tests
echo -e "${YELLOW} Run Available Test...${NC}"
php artisan test

## Stop existing queue workers gracefully
echo -e "${YELLOW}Stopping existing queue workers...${NC}"
php artisan queue:restart
sleep 5  # Give workers time to finish current jobs
pkill -f "php queue:work"
sleep 2


# Start Laravel Queue Workers with proper settings
start_service "php artisan queue:work --queue=prices,default --tries=3 --timeout=30 --sleep=3 --max-time=3600" "Queue Worker 1"

# Start Price Fetcher
echo -e "${YELLOW}Starting Price Fetcher...${NC}"
php artisan config:cache # Ensure config is cached
php artisan crypto:fetch-prices

# Monitor queue workers and jobs
echo -e "\n${GREEN}All services started. Monitoring queue workers...${NC}"
while true; do
    # Check queue workers
    if ! is_running "queue:work.*prices"; then
        echo -e "${RED}Queue worker died, restarting...${NC}"
        start_service "php artisan queue:work --queue=prices,default --tries=3 --timeout=30 --sleep=3 --max-time=3600" "Queue Worker"
    fi

    # Show queue statistics
    echo -e "\n${YELLOW}Queue Statistics:${NC}"
    php artisan queue:monitor prices,default
    
    sleep 10
done

# Cleanup on script exit
cleanup() {
    echo -e "\n${YELLOW}Shutting down services...${NC}"
    php artisan queue:restart
    sleep 5
    pkill -f "artisan queue:work"
    echo -e "${GREEN}Services stopped${NC}"
    exit 0
}

trap cleanup SIGINT SIGTERM