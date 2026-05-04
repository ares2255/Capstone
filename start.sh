#!/bin/bash

# Railway injects $PORT — Apache must listen on it
PORT="${PORT:-80}"

# Update Apache to listen on the correct port
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-enabled/000-default.conf

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
