# Laravel Service Layer Generator

[![Latest Version](https://img.shields.io/packagist/v/myohanhtet/service-layer.svg?style=flat-square)](https://packagist.org/packages/myohanhtet/service-layer)
[![Total Downloads](https://img.shields.io/packagist/dt/myohanhtet/service-layer.svg?style=flat-square)](https://packagist.org/packages/myohanhtet/service-layer)
[![License](https://img.shields.io/packagist/l/myohanhtet/service-layer.svg?style=flat-square)](https://packagist.org/packages/myohanhtet/service-layer)
[![PHP Version](https://img.shields.io/packagist/php-v/myohanhtet/service-layer.svg?style=flat-square)](https://packagist.org/packages/myohanhtet/service-layer)

A SOLID-compliant service layer generator for Laravel applications that enforces best practices through interface-implementation separation.

## ✨ Features

- Automatic interface and implementation generation
- Auto-binding in Service Provider
- Proper namespace organization (`Services` and `Services/Impl`)
- Stub customization support
- SOLID architecture enforcement
- Laravel container integration

## 📦 Installation

Install via Composer:

```bash
composer require myohanhtet/service-layer
```
## 🚀 Usage
### Basic Service Generation
```shell
php artisan make:service UserService
```
This creates:

```
app/
└── Services/
    ├── UserService.php          # Interface
    └── Impl/
        └── UserServiceImpl.php  # Implementation
```
## 🏗️ Generated Files
1. **Interface**: `app/Services/UserService.php`
```php
<?php

namespace App\Services;

interface UserService
{
    // Define your service contract here
    public function getAllUsers();
}
```
2. **Implementation**: `app/Services/Impl/UserServiceImpl.php`
```php
<?php

namespace App\Services\Impl;

use App\Services\UserService;

class UserServiceImpl implements UserService
{
    public function getAllUsers()
    {
        // Your implementation here
        return User::all();
    }
}
```
3. **Auto-generated Binding in**: `AppServiceProvider`:
```php
$this->app->bind(
    \App\Services\UserService::class,
    \App\Services\Impl\UserServiceImpl::class
);
```
## 🔧 Advanced Usage
### Customizing Stubs
Publish the stubs to customize:
```bash
php artisan vendor:publish --provider="Myohanhtet\ServiceLayer\ServiceLayerServiceProvider" --tag=stubs
```
Then modify:
- `stubs/service.stub`
- `stubs/service-impl.stub`

Manual Binding:
if auto-binding fails, you can manually bind the service in your `AppServiceProvider`:
```php
$this->app->bind(
    \App\Services\UserService::class,
    \App\Services\Impl\UserServiceImpl::class
);
```
## 🛠️ Requirements
- PHP 8.0 or higher
- Laravel 11 or higher

## 🤝 Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss.
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a pull request

## 📄 License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---
Made with ❤️ by Myo Han Htet

