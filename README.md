# Expense Tracker Setup

Follow these steps to set up and run the application:

## 1. Install PHP dependencies
```bash
composer install
```

## 2. Install Node.js dependencies
```bash
npm install
```

## 3. Build frontend assets
For production:
```bash
npm run build
```
For development (hot reload):
```bash
npm run dev
```

## 4. Run database migrations and seed data
```bash
php artisan migrate --seed
```

## 5. Link storage for file uploads (receipts)
```bash
php artisan storage:link
```

## 6. Start the Laravel development server
```bash
php artisan serve
```

The application will be available at [http://localhost:8000](http://localhost:8000).
