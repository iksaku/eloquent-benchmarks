## ORM Testing Project

This project uses PHP ^8.1 and Laravel ^9.0.

For the sake of simplicity, we default to SQLite as the database driver,
however, you may use any other database driver supported by Laravel by
changing your `.env` file configuration.

### Usage
First of all, you need to seed your database:
```shell
php artisan migrate:fresh --seed
```

> If you're using SQLite, make sure to `touch database/database.sqlite` beforehand.

List of available benchmarks can be found using `php artisan list benchmark`,
and you can individually run each benchmark by calling `php artisan benchmark:{name}`.

If you want to run all benchmarks, you can use `php artisan benchmark`.

### Notes
Relevant code can be found in:
 * `app/Console` for the benchmark code
 * `app/Models` for the database models tied to the ORM
 * `database/factories` for the model factories used to seed the database.
 * `database/migrations` for the database definitions used in the database.
 * `database/seeders` for the actual database seeder.
