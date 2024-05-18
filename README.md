#  Laravel JWT Sample
A simple example of Laravel JWT login and registration usage.

## Installation
## install
1.  Crate project
    - composer create-project laravel/laravel ship-api
    - cd ship-api
2. Modify .env to set mysql database
3. Install api
    - php artisan install:api
4. Install JWT
    - composer require tymon/jwt-auth
    - php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
5. Generate JWT secret
    - php artisan jwt:secret
    - jwt-auth secret 
6. Modify config/auth.php  
    ```php 
    'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
        ],
 
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
    ```
7. Add user model methods
    - getJWTIdentifier() Get key id
    - getJWTCustomClaims()
    - app/Models/User.php [Add a series of user registration, login and other methods]()
8. Create AuthController
    - php artisan make:controller AuthController
    - app/Http/Controllers/AuthController.php [Add a series of user registration, login and other methods]()
9. Register route
    - routes/api.php  [Add a series of user registration, login and other routes]()
    - Add registration and other routes
