```shell
composer require laravel/breeze --dev

php artisan breeze:install

composer require laravel/passport
```

##### config passport
```shell
php artisan migrate

php artisan passport:install
```
###### App\User
```php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```

###### AuthServiceProvider
```php
use Laravel\Passport\Passport;

protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
 ];
 
 public function boot()
{
    $this->registerPolicies();

    Passport::routes();
}
```

##### config/auth.php
###### TokenGuard
```
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

[passport link](https://laravel.com/docs/master/passport)
##### end config passport
##### Fortify
[laravel/fortify](https://github.com/laravel/fortify)

```
composer require laravel/fortify

php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

php artisan migrate
```
[Link to setting FortifyServiceProvider](https://github.com/menma977/jekdi.net/blob/main/app/Providers/FortifyServiceProvider.php)