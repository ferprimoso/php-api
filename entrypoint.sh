#!/bin/bash
set -e

# Run migrations
echo "Running migrations..."
php yii migrate --interactive=0

# Execute the main container command
exec "$@"
