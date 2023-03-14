#!/bin/bash

# shellcheck disable=SC1091

echo "Init shell script for development environment"

if [ ! -d "vendor" ]; then
  echo "Installing dependencies. Running 'composer install'..."
  composer install -v --ignore-platform-reqs
fi

if [ ! -f ".env" ]; then
  echo "File '.env' not found. Creating with content from '.env.example'..."
  cp .env.example .env
  php artisan key:generate
fi

php artisan serve --host 0.0.0.0 --port 8000
