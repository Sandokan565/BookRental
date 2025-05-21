#!/bin/bash
set -e

APP_NAME=$(grep '^APP_NAME=' backend/.env | cut -d '=' -f2)

APP_NAME=${APP_NAME:-app}

echo "Building Docker images..."
make build

echo "Starting Node container with sleep to keep it alive for setup..."
docker run -d --rm \
  --name ${APP_NAME}-node-setup \
  -v $(pwd)/frontend:/app \
  -w /app \
  node:latest sleep infinity

echo "Installing Node dependencies inside setup container..."
docker exec -it ${APP_NAME}-node-setup npm install
docker exec -it ${APP_NAME}-node-setup npm install bootstrap bootstrap-icons

echo "Stopping setup container..."
docker stop ${APP_NAME}-node-setup

echo "Starting all containers normally..."
make up

echo "Waiting for containers to be fully up..."
sleep 10

echo "Installing PHP dependencies..."
docker exec -it ${APP_NAME}-phpfpm composer install

echo "Running Laravel migrations and seeders..."
docker exec -it ${APP_NAME}-phpfpm php artisan migrate --force
docker exec -it ${APP_NAME}-phpfpm php artisan db:seed --force

echo "Setup complete!"