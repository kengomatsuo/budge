# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Build frontend assets (or use 'npm run dev' for development)
npm run build

# Run database migrations
php artisan migrate --seed

# Link storage for file uploads (receipts)
php artisan storage:link

# Start the Laravel development server
php artisan serve
