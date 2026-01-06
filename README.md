# Budge Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Build assets:
```bash
npm run build
```

3. Link storage:
```bash
php artisan storage:link
```

4. Start the server:
```bash
php artisan serve
```

5. Register your account and verify your email

6. Seed dummy data (adds other users and sample expenses for your account):
```bash
php artisan db:seed
```
