#!/bin/bash

# Startup script for the application
# Replace line 42 with the corrected command

# other initial commands

# line 42
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

# other commands to start the application