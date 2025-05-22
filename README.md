## Laravel 11 + FilamentPHP v3 Boilerplate

Laravel 11 and FilamentPHP v3 Boilerplate for start a new project

### Features
- Roles and Permissions using [Shield](https://github.com/bezhanSalleh/filament-shield)

### Installation
Clone the repository
```bash
git clone https://github.com/meeftah/larafila
```

Run composer install
```bash
cd larafila && composer install
```

Copy .env.example to .env
```bash
cp .env.example .env
```

Set valid database credentials of env variables 
```
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Generate application key
```bash
php artisan key:generate
```

Migrate
```bash
php artisan migrate --seed
```

Serve
```bash
php artisan serve
```

That's it, Open http://localhost:8000/login and login using this credential

```
username: superadmin
password: 12345678
```



## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
