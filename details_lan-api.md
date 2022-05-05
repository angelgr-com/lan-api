# lan-backend

## New Laravel project

 ```bash
 laravel new lan-backend --github --branch="main"
 ```

```bash
git push origin main
```

Edit .env file:

```bash
DB_CONNECTION=mysql 
DB_HOST=127.0.0.1 
DB_PORT=3306 
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Create schema in MySQL Workbench localhost:8000

## Passport

https://larainfo.com/blogs/laravel-9-rest-api-authentication-with-passport

### Install

```bash
composer require laravel/passport
```

Register providers in **config/app.php**:

```php
'providers' =>[
    	Laravel\Passport\PassportServiceProvider::class,
],
```

### Encryption keys

Migrate database:

```bash
php artisan migrate
```

Generate encryption keys:

```bash
php artisan passport:install --uuids
```

Save encryption keys  in .env file:

```bash
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=""
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=""
PASSPORT_PERSONAL_GRANT_CLIENT_ID=""
PASSPORT_PERSONAL_GRANT_CLIENT_SECRET=""
```

Loading keys from the environment:

```
php artisan vendor:publish --tag=passport-config
```

Add encryption keys in .env file copying them from **storage/oauth-private.key** and **storage/oauth-public.key**

```
PASSPORT_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
<private key here>
-----END RSA PRIVATE KEY-----"
 
PASSPORT_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----
<public key here>
-----END PUBLIC KEY-----"
```

### Configuration

In **App\Models\User.php**:

- Substitute the `Laravel\Sanctum\HasApiTokens` trait for `Laravel\Passport\HasApiTokens`:

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
```

 In **App\Providers\AuthServiceProvider.php**:

- Uncomment Model Policy
- Add Passport routes in boot function

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(7));
        Passport::refreshTokensExpireIn(now()->addDays(7));
        Passport::personalAccessTokensExpireIn(now()->addDays(7));
    }
}
```

In **config\auth.php**:

- Add 'api' guards

```php
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

### Run Migration

Generate tables in the database:

```bash
php artisan migrate
```

## Change timezone

Change timezone to get local time when created_at and updated_at attributes in user table.

In **config/app.php**:

```php
    'timezone' => 'Europe/Madrid',
```

## Database

### Use UUIDs

https://www.larashout.com/using-uuids-in-laravel-models

#### Trait

Create a new folder inside app folder and name it Traits. Now add a new PHP file in this folder named Uuids.php. Now add below code in this newly created file.

- By default, Eloquent assumes that the primary key on a model is incrementing integer value.
- To use UUID, we need to specify that the primary key is a string value and not an incrementing value. We achieve this by setting the **$keyType** to string and **$incrementing** to false.
- We also override the boot method of the model to use UUID. We use the **Illuminate\Support\Str** facade to generate the UUID and then convert it to string value.

```php
<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Uuids
{
   /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

   /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

   /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
```

#### Updating migration files

Instead of: 

```php
$table->id();
```

Use:

```php
$table->uuid('id')->primary();
```

For foreign keys, instead of:

```php
$table->bigInteger('user_id')->unsigned();
```

Use:

```php
$table->uuid('user_id');
```

#### Update passport oauth migrations

##### create_oauth_auth_codes_table

Instead of:

```php
$table->unsignedBigInteger('user_id')->index();
```

Use:

```php
$table->uuid('user_id')->index();
```

##### create_oauth_access_tokens_table

Instead of:

```php
$table->unsignedBigInteger('user_id')->nullable()->index();
```

Use:

```php
$table->uuid('user_id')->nullable()->index();
```

##### create_oauth_clients_table

Instead of:

```php
$table->unsignedBigInteger('user_id')->nullable()->index();
```

Use:

```php
$table->uuid('user_id')->nullable()->index();
```

##### create_oauth_personal_access_clients_table

Instead of:

```php
$table->unsignedBigInteger('client_id');
```

Use:

```php
$table->uuid('client_id');
```

#### Using Uuids

We need to add the **Uuid.php** trait to our **User** model (lines 5 and 13)

```php
<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use Uuids;
    use HasFactory, Notifiable;
```



### Models

#### User

Edit model, migration, factory and generate seeder:

##### Model

```php
    protected $fillable = [
        'first_name',
        'last_name',
        'country_id',
        'profile_picture',
        'username',
        'email',
        'password',
    ];

    public function users(){
        // One user can have many roles
        return $this->belongsToMany(User::class, 'role_users');
        // One user is able to review many translations
        return $this->belongsToMany(User::class, 'translation__users');
        // One user is able to learn many languages
        return $this->belongsToMany(User::class, 'learn__users');
        // One user is able to speak many languages
        return $this->belongsToMany(User::class, 'speak__users');
    }
```

##### Migration

```php
public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('country_id')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
```

##### Factory

```php
public function definition()
    {
        // $countryIds = Country::all()->pluck('id')->toArray();

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            // 'country_id' => $this->faker->randomElement($countryIds),
            'profile_picture' => $this->faker->url(),
            'username' => $this->faker->username(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
```

##### Seeder

```php
php artisan make:seeder UserSeeder
```

```php
    public function run()
    {
        \App\Models\User::factory(10)->create();
    }
```

#### Country

Generate model, migration, factory and seeder:

```
php artisan make:model Country -a
```

##### Model

```php
class Country extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];
}
```

##### Migration

```php
public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique;
            $table->string('code', 2)->unique;
            $table->timestamps();
        });
    }
```

##### Factory

```php

```

##### Seeder

https://www.nicesnippets.com/blog/how-to-get-country-list-in-laravel

```php
<?php

namespace Database\Seeders;

use App\Models\Text;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DateTime;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');

        $countries = [
            ['code' => 'AF', 'id' => Str::uuid(), 'name' => 'Afghanistan'],
            ['code' => 'AX', 'id' => Str::uuid(), 'name' => 'Aland Islands'],
            ['code' => 'AL', 'id' => Str::uuid(), 'name' => 'Albania'],
            ['code' => 'DZ', 'id' => Str::uuid(), 'name' => 'Algeria'],
            ['code' => 'AS', 'id' => Str::uuid(), 'name' => 'American Samoa'],
            ['code' => 'AD', 'id' => Str::uuid(), 'name' => 'Andorra'],
            ['code' => 'AO', 'id' => Str::uuid(), 'name' => 'Angola'],
            ['code' => 'AI', 'id' => Str::uuid(), 'name' => 'Anguilla'],
            ['code' => 'AQ', 'id' => Str::uuid(), 'name' => 'Antarctica'],
            ['code' => 'AG', 'id' => Str::uuid(), 'name' => 'Antigua and Barbuda'],
            ['code' => 'AR', 'id' => Str::uuid(), 'name' => 'Argentina'],
            ['code' => 'AM', 'id' => Str::uuid(), 'name' => 'Armenia'],
            ['code' => 'AW', 'id' => Str::uuid(), 'name' => 'Aruba'],
            ['code' => 'AU', 'id' => Str::uuid(), 'name' => 'Australia'],
            ['code' => 'AT', 'id' => Str::uuid(), 'name' => 'Austria'],
            ['code' => 'AZ', 'id' => Str::uuid(), 'name' => 'Azerbaijan'],
            ['code' => 'BS', 'id' => Str::uuid(), 'name' => 'Bahamas'],
            ['code' => 'BH', 'id' => Str::uuid(), 'name' => 'Bahrain'],
            ['code' => 'BD', 'id' => Str::uuid(), 'name' => 'Bangladesh'],
            ['code' => 'BB', 'id' => Str::uuid(), 'name' => 'Barbados'],
            ['code' => 'BY', 'id' => Str::uuid(), 'name' => 'Belarus'],
            ['code' => 'BE', 'id' => Str::uuid(), 'name' => 'Belgium'],
            ['code' => 'BZ', 'id' => Str::uuid(), 'name' => 'Belize'],
            ['code' => 'BJ', 'id' => Str::uuid(), 'name' => 'Benin'],
            ['code' => 'BM', 'id' => Str::uuid(), 'name' => 'Bermuda'],
            ['code' => 'BT', 'id' => Str::uuid(), 'name' => 'Bhutan'],
            ['code' => 'BO', 'id' => Str::uuid(), 'name' => 'Bolivia, Plurinational State of'],
            ['code' => 'BQ', 'id' => Str::uuid(), 'name' => 'Bonaire, Sint Eustatius and Saba'],
            ['code' => 'BA', 'id' => Str::uuid(), 'name' => 'Bosnia and Herzegovina'],
            ['code' => 'BW', 'id' => Str::uuid(), 'name' => 'Botswana'],
            ['code' => 'BV', 'id' => Str::uuid(), 'name' => 'Bouvet Island'],
            ['code' => 'BR', 'id' => Str::uuid(), 'name' => 'Brazil'],
            ['code' => 'IO', 'id' => Str::uuid(), 'name' => 'British Indian Ocean Territory'],
            ['code' => 'BN', 'id' => Str::uuid(), 'name' => 'Brunei Darussalam'],
            ['code' => 'BG', 'id' => Str::uuid(), 'name' => 'Bulgaria'],
            ['code' => 'BF', 'id' => Str::uuid(), 'name' => 'Burkina Faso'],
            ['code' => 'BI', 'id' => Str::uuid(), 'name' => 'Burundi'],
            ['code' => 'KH', 'id' => Str::uuid(), 'name' => 'Cambodia'],
            ['code' => 'CM', 'id' => Str::uuid(), 'name' => 'Cameroon'],
            ['code' => 'CA', 'id' => Str::uuid(), 'name' => 'Canada'],
            ['code' => 'CV', 'id' => Str::uuid(), 'name' => 'Cape Verde'],
            ['code' => 'KY', 'id' => Str::uuid(), 'name' => 'Cayman Islands'],
            ['code' => 'CF', 'id' => Str::uuid(), 'name' => 'Central African Republic'],
            ['code' => 'TD', 'id' => Str::uuid(), 'name' => 'Chad'],
            ['code' => 'CL', 'id' => Str::uuid(), 'name' => 'Chile'],
            ['code' => 'CN', 'id' => Str::uuid(), 'name' => 'China'],
            ['code' => 'CX', 'id' => Str::uuid(), 'name' => 'Christmas Island'],
            ['code' => 'CC', 'id' => Str::uuid(), 'name' => 'Cocos (Keeling) Islands'],
            ['code' => 'CO', 'id' => Str::uuid(), 'name' => 'Colombia'],
            ['code' => 'KM', 'id' => Str::uuid(), 'name' => 'Comoros'],
            ['code' => 'CG', 'id' => Str::uuid(), 'name' => 'Congo'],
            ['code' => 'CD', 'id' => Str::uuid(), 'name' => 'Congo, the Democratic Republic of the'],
            ['code' => 'CK', 'id' => Str::uuid(), 'name' => 'Cook Islands'],
            ['code' => 'CR', 'id' => Str::uuid(), 'name' => 'Costa Rica'],
            ['code' => 'CI', 'id' => Str::uuid(), 'name' => 'Côte d\'Ivoire'],
            ['code' => 'HR', 'id' => Str::uuid(), 'name' => 'Croatia'],
            ['code' => 'CU', 'id' => Str::uuid(), 'name' => 'Cuba'],
            ['code' => 'CW', 'id' => Str::uuid(), 'name' => 'Curaçao'],
            ['code' => 'CY', 'id' => Str::uuid(), 'name' => 'Cyprus'],
            ['code' => 'CZ', 'id' => Str::uuid(), 'name' => 'Czech Republic'],
            ['code' => 'DK', 'id' => Str::uuid(), 'name' => 'Denmark'],
            ['code' => 'DJ', 'id' => Str::uuid(), 'name' => 'Djibouti'],
            ['code' => 'DM', 'id' => Str::uuid(), 'name' => 'Dominica'],
            ['code' => 'DO', 'id' => Str::uuid(), 'name' => 'Dominican Republic'],
            ['code' => 'EC', 'id' => Str::uuid(), 'name' => 'Ecuador'],
            ['code' => 'EG', 'id' => Str::uuid(), 'name' => 'Egypt'],
            ['code' => 'SV', 'id' => Str::uuid(), 'name' => 'El Salvador'],
            ['code' => 'GQ', 'id' => Str::uuid(), 'name' => 'Equatorial Guinea'],
            ['code' => 'ER', 'id' => Str::uuid(), 'name' => 'Eritrea'],
            ['code' => 'EE', 'id' => Str::uuid(), 'name' => 'Estonia'],
            ['code' => 'ET', 'id' => Str::uuid(), 'name' => 'Ethiopia'],
            ['code' => 'FK', 'id' => Str::uuid(), 'name' => 'Falkland Islands (Malvinas)'],
            ['code' => 'FO', 'id' => Str::uuid(), 'name' => 'Faroe Islands'],
            ['code' => 'FJ', 'id' => Str::uuid(), 'name' => 'Fiji'],
            ['code' => 'FI', 'id' => Str::uuid(), 'name' => 'Finland'],
            ['code' => 'FR', 'id' => Str::uuid(), 'name' => 'France'],
            ['code' => 'GF', 'id' => Str::uuid(), 'name' => 'French Guiana'],
            ['code' => 'PF', 'id' => Str::uuid(), 'name' => 'French Polynesia'],
            ['code' => 'TF', 'id' => Str::uuid(), 'name' => 'French Southern Territories'],
            ['code' => 'GA', 'id' => Str::uuid(), 'name' => 'Gabon'],
            ['code' => 'GM', 'id' => Str::uuid(), 'name' => 'Gambia'],
            ['code' => 'GE', 'id' => Str::uuid(), 'name' => 'Georgia'],
            ['code' => 'DE', 'id' => Str::uuid(), 'name' => 'Germany'],
            ['code' => 'GH', 'id' => Str::uuid(), 'name' => 'Ghana'],
            ['code' => 'GI', 'id' => Str::uuid(), 'name' => 'Gibraltar'],
            ['code' => 'GR', 'id' => Str::uuid(), 'name' => 'Greece'],
            ['code' => 'GL', 'id' => Str::uuid(), 'name' => 'Greenland'],
            ['code' => 'GD', 'id' => Str::uuid(), 'name' => 'Grenada'],
            ['code' => 'GP', 'id' => Str::uuid(), 'name' => 'Guadeloupe'],
            ['code' => 'GU', 'id' => Str::uuid(), 'name' => 'Guam'],
            ['code' => 'GT', 'id' => Str::uuid(), 'name' => 'Guatemala'],
            ['code' => 'GG', 'id' => Str::uuid(), 'name' => 'Guernsey'],
            ['code' => 'GN', 'id' => Str::uuid(), 'name' => 'Guinea'],
            ['code' => 'GW', 'id' => Str::uuid(), 'name' => 'Guinea-Bissau'],
            ['code' => 'GY', 'id' => Str::uuid(), 'name' => 'Guyana'],
            ['code' => 'HT', 'id' => Str::uuid(), 'name' => 'Haiti'],
            ['code' => 'HM', 'id' => Str::uuid(), 'name' => 'Heard Island and McDonald Mcdonald Islands'],
            ['code' => 'VA', 'id' => Str::uuid(), 'name' => 'Holy See (Vatican City State)'],
            ['code' => 'HN', 'id' => Str::uuid(), 'name' => 'Honduras'],
            ['code' => 'HK', 'id' => Str::uuid(), 'name' => 'Hong Kong'],
            ['code' => 'HU', 'id' => Str::uuid(), 'name' => 'Hungary'],
            ['code' => 'IS', 'id' => Str::uuid(), 'name' => 'Iceland'],
            ['code' => 'IN', 'id' => Str::uuid(), 'name' => 'India'],
            ['code' => 'ID', 'id' => Str::uuid(), 'name' => 'Indonesia'],
            ['code' => 'IR', 'id' => Str::uuid(), 'name' => 'Iran, Islamic Republic of'],
            ['code' => 'IQ', 'id' => Str::uuid(), 'name' => 'Iraq'],
            ['code' => 'IE', 'id' => Str::uuid(), 'name' => 'Ireland'],
            ['code' => 'IM', 'id' => Str::uuid(), 'name' => 'Isle of Man'],
            ['code' => 'IL', 'id' => Str::uuid(), 'name' => 'Israel'],
            ['code' => 'IT', 'id' => Str::uuid(), 'name' => 'Italy'],
            ['code' => 'JM', 'id' => Str::uuid(), 'name' => 'Jamaica'],
            ['code' => 'JP', 'id' => Str::uuid(), 'name' => 'Japan'],
            ['code' => 'JE', 'id' => Str::uuid(), 'name' => 'Jersey'],
            ['code' => 'JO', 'id' => Str::uuid(), 'name' => 'Jordan'],
            ['code' => 'KZ', 'id' => Str::uuid(), 'name' => 'Kazakhstan'],
            ['code' => 'KE', 'id' => Str::uuid(), 'name' => 'Kenya'],
            ['code' => 'KI', 'id' => Str::uuid(), 'name' => 'Kiribati'],
            ['code' => 'KP', 'id' => Str::uuid(), 'name' => 'Korea, Democratic People\'s Republic of'],
            ['code' => 'KR', 'id' => Str::uuid(), 'name' => 'Korea, Republic of'],
            ['code' => 'KW', 'id' => Str::uuid(), 'name' => 'Kuwait'],
            ['code' => 'KG', 'id' => Str::uuid(), 'name' => 'Kyrgyzstan'],
            ['code' => 'LA', 'id' => Str::uuid(), 'name' => 'Lao People\'s Democratic Republic'],
            ['code' => 'LV', 'id' => Str::uuid(), 'name' => 'Latvia'],
            ['code' => 'LB', 'id' => Str::uuid(), 'name' => 'Lebanon'],
            ['code' => 'LS', 'id' => Str::uuid(), 'name' => 'Lesotho'],
            ['code' => 'LR', 'id' => Str::uuid(), 'name' => 'Liberia'],
            ['code' => 'LY', 'id' => Str::uuid(), 'name' => 'Libya'],
            ['code' => 'LI', 'id' => Str::uuid(), 'name' => 'Liechtenstein'],
            ['code' => 'LT', 'id' => Str::uuid(), 'name' => 'Lithuania'],
            ['code' => 'LU', 'id' => Str::uuid(), 'name' => 'Luxembourg'],
            ['code' => 'MO', 'id' => Str::uuid(), 'name' => 'Macao'],
            ['code' => 'MK', 'id' => Str::uuid(), 'name' => 'Macedonia, the Former Yugoslav Republic of'],
            ['code' => 'MW', 'id' => Str::uuid(), 'name' => 'Malawi'],
            ['code' => 'MG', 'id' => Str::uuid(), 'name' => 'Madagascar'],
            ['code' => 'MY', 'id' => Str::uuid(), 'name' => 'Malaysia'],
            ['code' => 'MV', 'id' => Str::uuid(), 'name' => 'Maldives'],
            ['code' => 'ML', 'id' => Str::uuid(), 'name' => 'Mali'],
            ['code' => 'MT', 'id' => Str::uuid(), 'name' => 'Malta'],
            ['code' => 'MH', 'id' => Str::uuid(), 'name' => 'Marshall Islands'],
            ['code' => 'MQ', 'id' => Str::uuid(), 'name' => 'Martinique'],
            ['code' => 'MR', 'id' => Str::uuid(), 'name' => 'Mauritania'],
            ['code' => 'MU', 'id' => Str::uuid(), 'name' => 'Mauritius'],
            ['code' => 'YT', 'id' => Str::uuid(), 'name' => 'Mayotte'],
            ['code' => 'MX', 'id' => Str::uuid(), 'name' => 'Mexico'],
            ['code' => 'FM', 'id' => Str::uuid(), 'name' => 'Micronesia, Federated States of'],
            ['code' => 'MD', 'id' => Str::uuid(), 'name' => 'Moldova, Republic of'],
            ['code' => 'MC', 'id' => Str::uuid(), 'name' => 'Monaco'],
            ['code' => 'MN', 'id' => Str::uuid(), 'name' => 'Mongolia'],
            ['code' => 'ME', 'id' => Str::uuid(), 'name' => 'Montenegro'],
            ['code' => 'MS', 'id' => Str::uuid(), 'name' => 'Montserrat'],
            ['code' => 'MA', 'id' => Str::uuid(), 'name' => 'Morocco'],
            ['code' => 'MZ', 'id' => Str::uuid(), 'name' => 'Mozambique'],
            ['code' => 'MM', 'id' => Str::uuid(), 'name' => 'Myanmar'],
            ['code' => 'NA', 'id' => Str::uuid(), 'name' => 'Namibia'],
            ['code' => 'NR', 'id' => Str::uuid(), 'name' => 'Nauru'],
            ['code' => 'NP', 'id' => Str::uuid(), 'name' => 'Nepal'],
            ['code' => 'NL', 'id' => Str::uuid(), 'name' => 'Netherlands'],
            ['code' => 'NC', 'id' => Str::uuid(), 'name' => 'New Caledonia'],
            ['code' => 'NZ', 'id' => Str::uuid(), 'name' => 'New Zealand'],
            ['code' => 'NI', 'id' => Str::uuid(), 'name' => 'Nicaragua'],
            ['code' => 'NE', 'id' => Str::uuid(), 'name' => 'Niger'],
            ['code' => 'NG', 'id' => Str::uuid(), 'name' => 'Nigeria'],
            ['code' => 'NU', 'id' => Str::uuid(), 'name' => 'Niue'],
            ['code' => 'NF', 'id' => Str::uuid(), 'name' => 'Norfolk Island'],
            ['code' => 'MP', 'id' => Str::uuid(), 'name' => 'Northern Mariana Islands'],
            ['code' => 'NO', 'id' => Str::uuid(), 'name' => 'Norway'],
            ['code' => 'OM', 'id' => Str::uuid(), 'name' => 'Oman'],
            ['code' => 'PK', 'id' => Str::uuid(), 'name' => 'Pakistan'],
            ['code' => 'PW', 'id' => Str::uuid(), 'name' => 'Palau'],
            ['code' => 'PS', 'id' => Str::uuid(), 'name' => 'Palestine, State of'],
            ['code' => 'PA', 'id' => Str::uuid(), 'name' => 'Panama'],
            ['code' => 'PG', 'id' => Str::uuid(), 'name' => 'Papua New Guinea'],
            ['code' => 'PY', 'id' => Str::uuid(), 'name' => 'Paraguay'],
            ['code' => 'PE', 'id' => Str::uuid(), 'name' => 'Peru'],
            ['code' => 'PH', 'id' => Str::uuid(), 'name' => 'Philippines'],
            ['code' => 'PN', 'id' => Str::uuid(), 'name' => 'Pitcairn'],
            ['code' => 'PL', 'id' => Str::uuid(), 'name' => 'Poland'],
            ['code' => 'PT', 'id' => Str::uuid(), 'name' => 'Portugal'],
            ['code' => 'PR', 'id' => Str::uuid(), 'name' => 'Puerto Rico'],
            ['code' => 'QA', 'id' => Str::uuid(), 'name' => 'Qatar'],
            ['code' => 'RE', 'id' => Str::uuid(), 'name' => 'Réunion'],
            ['code' => 'RO', 'id' => Str::uuid(), 'name' => 'Romania'],
            ['code' => 'RU', 'id' => Str::uuid(), 'name' => 'Russian Federation'],
            ['code' => 'RW', 'id' => Str::uuid(), 'name' => 'Rwanda'],
            ['code' => 'BL', 'id' => Str::uuid(), 'name' => 'Saint Barthélemy'],
            ['code' => 'SH', 'id' => Str::uuid(), 'name' => 'Saint Helena, Ascension and Tristan da Cunha'],
            ['code' => 'KN', 'id' => Str::uuid(), 'name' => 'Saint Kitts and Nevis'],
            ['code' => 'LC', 'id' => Str::uuid(), 'name' => 'Saint Lucia'],
            ['code' => 'MF', 'id' => Str::uuid(), 'name' => 'Saint Martin (French part)'],
            ['code' => 'PM', 'id' => Str::uuid(), 'name' => 'Saint Pierre and Miquelon'],
            ['code' => 'VC', 'id' => Str::uuid(), 'name' => 'Saint Vincent and the Grenadines'],
            ['code' => 'WS', 'id' => Str::uuid(), 'name' => 'Samoa'],
            ['code' => 'SM', 'id' => Str::uuid(), 'name' => 'San Marino'],
            ['code' => 'ST', 'id' => Str::uuid(), 'name' => 'Sao Tome and Principe'],
            ['code' => 'SA', 'id' => Str::uuid(), 'name' => 'Saudi Arabia'],
            ['code' => 'SN', 'id' => Str::uuid(), 'name' => 'Senegal'],
            ['code' => 'RS', 'id' => Str::uuid(), 'name' => 'Serbia'],
            ['code' => 'SC', 'id' => Str::uuid(), 'name' => 'Seychelles'],
            ['code' => 'SL', 'id' => Str::uuid(), 'name' => 'Sierra Leone'],
            ['code' => 'SG', 'id' => Str::uuid(), 'name' => 'Singapore'],
            ['code' => 'SX', 'id' => Str::uuid(), 'name' => 'Sint Maarten (Dutch part)'],
            ['code' => 'SK', 'id' => Str::uuid(), 'name' => 'Slovakia'],
            ['code' => 'SI', 'id' => Str::uuid(), 'name' => 'Slovenia'],
            ['code' => 'SB', 'id' => Str::uuid(), 'name' => 'Solomon Islands'],
            ['code' => 'SO', 'id' => Str::uuid(), 'name' => 'Somalia'],
            ['code' => 'ZA', 'id' => Str::uuid(), 'name' => 'South Africa'],
            ['code' => 'GS', 'id' => Str::uuid(), 'name' => 'South Georgia and the South Sandwich Islands'],
            ['code' => 'SS', 'id' => Str::uuid(), 'name' => 'South Sudan'],
            ['code' => 'ES', 'id' => Str::uuid(), 'name' => 'Spain'],
            ['code' => 'LK', 'id' => Str::uuid(), 'name' => 'Sri Lanka'],
            ['code' => 'SD', 'id' => Str::uuid(), 'name' => 'Sudan'],
            ['code' => 'SR', 'id' => Str::uuid(), 'name' => 'Suriname'],
            ['code' => 'SJ', 'id' => Str::uuid(), 'name' => 'Svalbard and Jan Mayen'],
            ['code' => 'SZ', 'id' => Str::uuid(), 'name' => 'Swaziland'],
            ['code' => 'SE', 'id' => Str::uuid(), 'name' => 'Sweden'],
            ['code' => 'CH', 'id' => Str::uuid(), 'name' => 'Switzerland'],
            ['code' => 'SY', 'id' => Str::uuid(), 'name' => 'Syrian Arab Republic'],
            ['code' => 'TW', 'id' => Str::uuid(), 'name' => 'Taiwan'],
            ['code' => 'TJ', 'id' => Str::uuid(), 'name' => 'Tajikistan'],
            ['code' => 'TZ', 'id' => Str::uuid(), 'name' => 'Tanzania, United Republic of'],
            ['code' => 'TH', 'id' => Str::uuid(), 'name' => 'Thailand'],
            ['code' => 'TL', 'id' => Str::uuid(), 'name' => 'Timor-Leste'],
            ['code' => 'TG', 'id' => Str::uuid(), 'name' => 'Togo'],
            ['code' => 'TK', 'id' => Str::uuid(), 'name' => 'Tokelau'],
            ['code' => 'TO', 'id' => Str::uuid(), 'name' => 'Tonga'],
            ['code' => 'TT', 'id' => Str::uuid(), 'name' => 'Trinidad and Tobago'],
            ['code' => 'TN', 'id' => Str::uuid(), 'name' => 'Tunisia'],
            ['code' => 'TR', 'id' => Str::uuid(), 'name' => 'Turkey'],
            ['code' => 'TM', 'id' => Str::uuid(), 'name' => 'Turkmenistan'],
            ['code' => 'TC', 'id' => Str::uuid(), 'name' => 'Turks and Caicos Islands'],
            ['code' => 'TV', 'id' => Str::uuid(), 'name' => 'Tuvalu'],
            ['code' => 'UG', 'id' => Str::uuid(), 'name' => 'Uganda'],
            ['code' => 'UA', 'id' => Str::uuid(), 'name' => 'Ukraine'],
            ['code' => 'AE', 'id' => Str::uuid(), 'name' => 'United Arab Emirates'],
            ['code' => 'GB', 'id' => Str::uuid(), 'name' => 'United Kingdom'],
            ['code' => 'US', 'id' => Str::uuid(), 'name' => 'United States'],
            ['code' => 'UM', 'id' => Str::uuid(), 'name' => 'United States Minor Outlying Islands'],
            ['code' => 'UY', 'id' => Str::uuid(), 'name' => 'Uruguay'],
            ['code' => 'UZ', 'id' => Str::uuid(), 'name' => 'Uzbekistan'],
            ['code' => 'VU', 'id' => Str::uuid(), 'name' => 'Vanuatu'],
            ['code' => 'VE', 'id' => Str::uuid(), 'name' => 'Venezuela, Bolivarian Republic of'],
            ['code' => 'VN', 'id' => Str::uuid(), 'name' => 'Viet Nam'],
            ['code' => 'VG', 'id' => Str::uuid(), 'name' => 'Virgin Islands, British'],
            ['code' => 'VI', 'id' => Str::uuid(), 'name' => 'Virgin Islands, U.S.'],
            ['code' => 'WF', 'id' => Str::uuid(), 'name' => 'Wallis and Futuna'],
            ['code' => 'EH', 'id' => Str::uuid(), 'name' => 'Western Sahara'],
            ['code' => 'YE', 'id' => Str::uuid(), 'name' => 'Yemen'],
            ['code' => 'ZM', 'id' => Str::uuid(), 'name' => 'Zambia'],
            ['code' => 'ZW', 'id' => Str::uuid(), 'name' => 'Zimbabwe'],
        ];
 
        DB::table('countries')->insert($countries);
    }
}
```

#### Role

Generate model, migration, factory and seeder:

```
php artisan make:model Role -a
```

##### Model

```php
class Role extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'type',
    ];

    // A role can have many users
    public function roles(){
        return $this->belongsToMany(Role::class, 'role_users');
    }
}
```

##### Migration

```php
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['admin', 'student', 'teacher'])->unique;
            $table->timestamps();
        });
    }
```

##### Factory

```php
```

##### Seeder

```php
public function run()
    {
        $roles = [
			['id' => Str::uuid(), 'type' => 'admin'],
			['id' => Str::uuid(), 'type' => 'student'],
            ['id' => Str::uuid(), 'type' => 'teacher'],
        ];
 
        DB::table('roles')->insert($roles);
    }
```

#### Language

Generate model, migration, factory and seeder:

```
php artisan make:model Language -a
```

##### Model

```php
class Language extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function languages(){
        // A language can be learned by many users
        return $this->belongsToMany(Language::class, 'learn__users');
        // A language can be speaked by many users
        return $this->belongsToMany(Language::class, 'speak__users');
    }
}
```

##### Migration

```php
public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 2)->unique;
            $table->string('name', 20)->unique;
            $table->timestamps();
        });
    }
```

##### Factory

##### Seeder

```php
public function run()
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');
 
        $languages = [
            ['code' => 'ab', 'id' => Str::uuid(), 'name' => 'Abkhazian'],
            ['code' => 'aa', 'id' => Str::uuid(), 'name' => 'Afar'],
            ['code' => 'af', 'id' => Str::uuid(), 'name' => 'Afrikaans'],
            ['code' => 'ak', 'id' => Str::uuid(), 'name' => 'Akan'],
            ['code' => 'sq', 'id' => Str::uuid(), 'name' => 'Albanian'],
            ['code' => 'am', 'id' => Str::uuid(), 'name' => 'Amharic'],
            ['code' => 'ar', 'id' => Str::uuid(), 'name' => 'Arabic'],
            ['code' => 'an', 'id' => Str::uuid(), 'name' => 'Aragonese'],
            ['code' => 'hy', 'id' => Str::uuid(), 'name' => 'Armenian'],
            ['code' => 'as', 'id' => Str::uuid(), 'name' => 'Assamese'],
            ['code' => 'av', 'id' => Str::uuid(), 'name' => 'Avaric'],
            ['code' => 'ae', 'id' => Str::uuid(), 'name' => 'Avestan'],
            ['code' => 'ay', 'id' => Str::uuid(), 'name' => 'Aymara'],
            ['code' => 'az', 'id' => Str::uuid(), 'name' => 'Azerbaijani'],
            ['code' => 'bm', 'id' => Str::uuid(), 'name' => 'Bambara'],
            ['code' => 'ba', 'id' => Str::uuid(), 'name' => 'Bashkir'],
            ['code' => 'eu', 'id' => Str::uuid(), 'name' => 'Basque'],
            ['code' => 'be', 'id' => Str::uuid(), 'name' => 'Belarusian'],
            ['code' => 'bn', 'id' => Str::uuid(), 'name' => 'Bengali'],
            ['code' => 'bi', 'id' => Str::uuid(), 'name' => 'Bislama'],
            ['code' => 'bs', 'id' => Str::uuid(), 'name' => 'Bosnian'],
            ['code' => 'br', 'id' => Str::uuid(), 'name' => 'Breton'],
            ['code' => 'bg', 'id' => Str::uuid(), 'name' => 'Bulgarian'],
            ['code' => 'my', 'id' => Str::uuid(), 'name' => 'Burmese'],
            ['code' => 'ca', 'id' => Str::uuid(), 'name' => 'Catalan'],
            ['code' => 'ch', 'id' => Str::uuid(), 'name' => 'Chamorro'],
            ['code' => 'ce', 'id' => Str::uuid(), 'name' => 'Chechen'],
            ['code' => 'ny', 'id' => Str::uuid(), 'name' => 'Chichewa'],
            ['code' => 'zh', 'id' => Str::uuid(), 'name' => 'Chinese'],
            ['code' => 'cu', 'id' => Str::uuid(), 'name' => 'Church Slavic'],
            ['code' => 'cv', 'id' => Str::uuid(), 'name' => 'Chuvash'],
            ['code' => 'kw', 'id' => Str::uuid(), 'name' => 'Cornish'],
            ['code' => 'co', 'id' => Str::uuid(), 'name' => 'Corsican'],
            ['code' => 'cr', 'id' => Str::uuid(), 'name' => 'Cree'],
            ['code' => 'hr', 'id' => Str::uuid(), 'name' => 'Croatian'],
            ['code' => 'cs', 'id' => Str::uuid(), 'name' => 'Czech'],
            ['code' => 'da', 'id' => Str::uuid(), 'name' => 'Danish'],
            [ 'code' => 'v', 'id' => Str::uuid(), 'name' => 'Divehi'],
            ['code' => 'nl', 'id' => Str::uuid(), 'name' => 'Dutch'],
            ['code' => 'dz', 'id' => Str::uuid(), 'name' => 'Dzongkha'],
            ['code' => 'en', 'id' => Str::uuid(), 'name' => 'English'],
            ['code' => 'eo', 'id' => Str::uuid(), 'name' => 'Esperanto'],
            ['code' => 'et', 'id' => Str::uuid(), 'name' => 'Estonian'],
            ['code' => 'ee', 'id' => Str::uuid(), 'name' => 'Ewe'],
            ['code' => 'fo', 'id' => Str::uuid(), 'name' => 'Faroese'],
            ['code' => 'fj', 'id' => Str::uuid(), 'name' => 'Fijian'],
            ['code' => 'fi', 'id' => Str::uuid(), 'name' => 'Finnish'],
            ['code' => 'fr', 'id' => Str::uuid(), 'name' => 'French'],
            ['code' => 'fy', 'id' => Str::uuid(), 'name' => 'Western Frisian'],
            ['code' => 'ff', 'id' => Str::uuid(), 'name' => 'Fulah'],
            ['code' => 'gd', 'id' => Str::uuid(), 'name' => 'Gaelic'],
            ['code' => 'gl', 'id' => Str::uuid(), 'name' => 'Galician'],
            ['code' => 'lg', 'id' => Str::uuid(), 'name' => 'Ganda'],
            ['code' => 'ka', 'id' => Str::uuid(), 'name' => 'Georgian'],
            ['code' => 'de', 'id' => Str::uuid(), 'name' => 'German'],
            ['code' => 'el', 'id' => Str::uuid(), 'name' => 'Greek'],
            ['code' => 'kl', 'id' => Str::uuid(), 'name' => 'Kalaallisut'],
            ['code' => 'gn', 'id' => Str::uuid(), 'name' => 'Guarani'],
            ['code' => 'gu', 'id' => Str::uuid(), 'name' => 'Gujarati'],
            ['code' => 'ht', 'id' => Str::uuid(), 'name' => 'Haitian'],
            ['code' => 'ha', 'id' => Str::uuid(), 'name' => 'Hausa'],
            ['code' => 'he', 'id' => Str::uuid(), 'name' => 'Hebrew'],
            ['code' => 'hz', 'id' => Str::uuid(), 'name' => 'Herero'],
            ['code' => 'hi', 'id' => Str::uuid(), 'name' => 'Hindi'],
            ['code' => 'ho', 'id' => Str::uuid(), 'name' => 'Hiri Motu'],
            ['code' => 'hu', 'id' => Str::uuid(), 'name' => 'Hungarian'],
            ['code' => 'is', 'id' => Str::uuid(), 'name' => 'Icelandic'],
            ['code' => 'io', 'id' => Str::uuid(), 'name' => 'Ido'],
            ['code' => 'ig', 'id' => Str::uuid(), 'name' => 'Igbo'],
            ['code' => 'id', 'id' => Str::uuid(), 'name' => 'Indonesian'],
            ['code' => 'ia', 'id' => Str::uuid(), 'name' => 'Interlingua'],
            ['code' => 'ie', 'id' => Str::uuid(), 'name' => 'Interlingue'],
            ['code' => 'iu', 'id' => Str::uuid(), 'name' => 'Inuktitut'],
            ['code' => 'ik', 'id' => Str::uuid(), 'name' => 'Inupiaq'],
            ['code' => 'ga', 'id' => Str::uuid(), 'name' => 'Irish'],
            ['code' => 'it', 'id' => Str::uuid(), 'name' => 'Italian'],
            ['code' => 'ja', 'id' => Str::uuid(), 'name' => 'Japanese'],
            ['code' => 'jv', 'id' => Str::uuid(), 'name' => 'Javanese'],
            ['code' => 'kn', 'id' => Str::uuid(), 'name' => 'Kannada'],
            ['code' => 'kr', 'id' => Str::uuid(), 'name' => 'Kanuri'],
            ['code' => 'ks', 'id' => Str::uuid(), 'name' => 'Kashmiri'],
            ['code' => 'kk', 'id' => Str::uuid(), 'name' => 'Kazakh'],
            ['code' => 'km', 'id' => Str::uuid(), 'name' => 'Central Khmer'],
            ['code' => 'ki', 'id' => Str::uuid(), 'name' => 'Kikuyu'],
            ['code' => 'rw', 'id' => Str::uuid(), 'name' => 'Kinyarwanda'],
            ['code' => 'ky', 'id' => Str::uuid(), 'name' => 'Kirghiz'],
            ['code' => 'kv', 'id' => Str::uuid(), 'name' => 'Komi'],
            ['code' => 'kg', 'id' => Str::uuid(), 'name' => 'Kongo'],
            ['code' => 'ko', 'id' => Str::uuid(), 'name' => 'Korean'],
            ['code' => 'kj', 'id' => Str::uuid(), 'name' => 'Kuanyama'],
            ['code' => 'ku', 'id' => Str::uuid(), 'name' => 'Kurdish'],
            ['code' => 'lo', 'id' => Str::uuid(), 'name' => 'Lao'],
            ['code' => 'la', 'id' => Str::uuid(), 'name' => 'Latin'],
            ['code' => 'lv', 'id' => Str::uuid(), 'name' => 'Latvian'],
            ['code' => 'li', 'id' => Str::uuid(), 'name' => 'Limburgan'],
            ['code' => 'ln', 'id' => Str::uuid(), 'name' => 'Lingala'],
            ['code' => 'lt', 'id' => Str::uuid(), 'name' => 'Lithuanian'],
            ['code' => 'lu', 'id' => Str::uuid(), 'name' => 'Luba-Katanga'],
            ['code' => 'lb', 'id' => Str::uuid(), 'name' => 'Luxembourgish'],
            ['code' => 'mk', 'id' => Str::uuid(), 'name' => 'Macedonian'],
            ['code' => 'mg', 'id' => Str::uuid(), 'name' => 'Malagasy'],
            ['code' => 'ms', 'id' => Str::uuid(), 'name' => 'Malay'],
            ['code' => 'ml', 'id' => Str::uuid(), 'name' => 'Malayalam'],
            ['code' => 'mt', 'id' => Str::uuid(), 'name' => 'Maltese'],
            ['code' => 'gv', 'id' => Str::uuid(), 'name' => 'Manx'],
            ['code' => 'mi', 'id' => Str::uuid(), 'name' => 'Maori'],
            ['code' => 'mr', 'id' => Str::uuid(), 'name' => 'Marathi'],
            ['code' => 'mh', 'id' => Str::uuid(), 'name' => 'Marshallese'],
            ['code' => 'mn', 'id' => Str::uuid(), 'name' => 'Mongolian'],
            ['code' => 'na', 'id' => Str::uuid(), 'name' => 'Nauru'],
            ['code' => 'nv', 'id' => Str::uuid(), 'name' => 'Navajo'],
            ['code' => 'nd', 'id' => Str::uuid(), 'name' => 'North Ndebele'],
            ['code' => 'nr', 'id' => Str::uuid(), 'name' => 'South Ndebele'],
            ['code' => 'ng', 'id' => Str::uuid(), 'name' => 'Ndonga'],
            ['code' => 'ne', 'id' => Str::uuid(), 'name' => 'Nepali'],
            ['code' => 'no', 'id' => Str::uuid(), 'name' => 'Norwegian'],
            ['code' => 'nb', 'id' => Str::uuid(), 'name' => 'Norwegian Bokmål'],
            ['code' => 'nn', 'id' => Str::uuid(), 'name' => 'Norwegian Nynorsk'],
            ['code' => 'ii', 'id' => Str::uuid(), 'name' => 'Sichuan Yi'],
            ['code' => 'oc', 'id' => Str::uuid(), 'name' => 'Occitan'],
            ['code' => 'oj', 'id' => Str::uuid(), 'name' => 'Ojibwa'],
            ['code' => 'or', 'id' => Str::uuid(), 'name' => 'Oriya'],
            ['code' => 'om', 'id' => Str::uuid(), 'name' => 'Oromo'],
            ['code' => 'os', 'id' => Str::uuid(), 'name' => 'Ossetian'],
            ['code' => 'pi', 'id' => Str::uuid(), 'name' => 'Pali'],
            ['code' => 'ps', 'id' => Str::uuid(), 'name' => 'Pashto'],
            ['code' => 'fa', 'id' => Str::uuid(), 'name' => 'Persian'],
            ['code' => 'pl', 'id' => Str::uuid(), 'name' => 'Polish'],
            ['code' => 'pt', 'id' => Str::uuid(), 'name' => 'Portuguese'],
            ['code' => 'pa', 'id' => Str::uuid(), 'name' => 'Punjabi'],
            ['code' => 'qu', 'id' => Str::uuid(), 'name' => 'Quechua'],
            ['code' => 'ro', 'id' => Str::uuid(), 'name' => 'Romanian'],
            ['code' => 'rm', 'id' => Str::uuid(), 'name' => 'Romansh'],
            ['code' => 'rn', 'id' => Str::uuid(), 'name' => 'Rundi'],
            ['code' => 'ru', 'id' => Str::uuid(), 'name' => 'Russian'],
            ['code' => 'se', 'id' => Str::uuid(), 'name' => 'Northern Sami'],
            ['code' => 'sm', 'id' => Str::uuid(), 'name' => 'Samoan'],
            ['code' => 'sg', 'id' => Str::uuid(), 'name' => 'Sango'],
            ['code' => 'sa', 'id' => Str::uuid(), 'name' => 'Sanskrit'],
            ['code' => 'sc', 'id' => Str::uuid(), 'name' => 'Sardinian'],
            ['code' => 'sr', 'id' => Str::uuid(), 'name' => 'Serbian'],
            ['code' => 'sn', 'id' => Str::uuid(), 'name' => 'Shona'],
            ['code' => 'sd', 'id' => Str::uuid(), 'name' => 'Sindhi'],
            ['code' => 'si', 'id' => Str::uuid(), 'name' => 'Sinhala'],
            ['code' => 'sk', 'id' => Str::uuid(), 'name' => 'Slovak'],
            ['code' => 'sl', 'id' => Str::uuid(), 'name' => 'Slovenian'],
            ['code' => 'so', 'id' => Str::uuid(), 'name' => 'Somali'],
            ['code' => 'st', 'id' => Str::uuid(), 'name' => 'Southern Sotho'],
            ['code' => 'es', 'id' => Str::uuid(), 'name' => 'Spanish'],
            ['code' => 'su', 'id' => Str::uuid(), 'name' => 'Sundanese'],
            ['code' => 'sw', 'id' => Str::uuid(), 'name' => 'Swahili'],
            ['code' => 'ss', 'id' => Str::uuid(), 'name' => 'Swati'],
            ['code' => 'sv', 'id' => Str::uuid(), 'name' => 'Swedish'],
            ['code' => 'tl', 'id' => Str::uuid(), 'name' => 'Tagalog'],
            ['code' => 'ty', 'id' => Str::uuid(), 'name' => 'Tahitian'],
            ['code' => 'tg', 'id' => Str::uuid(), 'name' => 'Tajik'],
            ['code' => 'ta', 'id' => Str::uuid(), 'name' => 'Tamil'],
            ['code' => 'tt', 'id' => Str::uuid(), 'name' => 'Tatar'],
            ['code' => 'te', 'id' => Str::uuid(), 'name' => 'Telugu'],
            ['code' => 'th', 'id' => Str::uuid(), 'name' => 'Thai'],
            ['code' => 'bo', 'id' => Str::uuid(), 'name' => 'Tibetan'],
            ['code' => 'ti', 'id' => Str::uuid(), 'name' => 'Tigrinya'],
            ['code' => 'to', 'id' => Str::uuid(), 'name' => 'Tonga'],
            ['code' => 'ts', 'id' => Str::uuid(), 'name' => 'Tsonga'],
            ['code' => 'tn', 'id' => Str::uuid(), 'name' => 'Tswana'],
            ['code' => 'tr', 'id' => Str::uuid(), 'name' => 'Turkish'],
            ['code' => 'tk', 'id' => Str::uuid(), 'name' => 'Turkmen'],
            ['code' => 'tw', 'id' => Str::uuid(), 'name' => 'Twi'],
            ['code' => 'ug', 'id' => Str::uuid(), 'name' => 'Uighur'],
            ['code' => 'uk', 'id' => Str::uuid(), 'name' => 'Ukrainian'],
            ['code' => 'ur', 'id' => Str::uuid(), 'name' => 'Urdu'],
            ['code' => 'uz', 'id' => Str::uuid(), 'name' => 'Uzbek'],
            ['code' => 've', 'id' => Str::uuid(), 'name' => 'Venda'],
            ['code' => 'vi', 'id' => Str::uuid(), 'name' => 'Vietnamese'],
            ['code' => 'vo', 'id' => Str::uuid(), 'name' => 'Volapük'],
            ['code' => 'wa', 'id' => Str::uuid(), 'name' => 'Walloon'],
            ['code' => 'cy', 'id' => Str::uuid(), 'name' => 'Welsh'],
            ['code' => 'wo', 'id' => Str::uuid(), 'name' => 'Wolof'],
            ['code' => 'xh', 'id' => Str::uuid(), 'name' => 'Xhosa'],
            ['code' => 'yi', 'id' => Str::uuid(), 'name' => 'Yiddish'],
            ['code' => 'yo', 'id' => Str::uuid(), 'name' => 'Yoruba'],
            ['code' => 'za', 'id' => Str::uuid(), 'name' => 'Zhuang'],
            ['code' => 'zu', 'id' => Str::uuid(), 'name' => 'Zulu'],
        ];
 
        DB::table('languages')->insert($languages);
    }
```

#### Role-User

Generate model, migration, factory and seeder:

```bash
php artisan make:model Role_User -a
```

##### Model

```php
class Role_User extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'role_id',
        'user_id',
    ];
}
```

##### Migration

```php
    public function up()
    {
        Schema::create('role__users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $roleIds = Role::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'role_id'=>$this->faker->randomElement($roleIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Role_User::factory()->times(10)->create();
    }
```

#### Cefr

Generate model, migration, factory and seeder:

```bash
php artisan make:model Cefr -a
```

##### Model

```php
class Cefr extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'level',
    ];
}
```

##### Migration

```php
    public function up()
    {
        Schema::create('cefrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->unique;
            $table->timestamps();
        });
    }
```

##### Factory

```php

```

##### Seeder

```php
public function run()
    {
        $cefrs = [
			['id' => Str::uuid(), 'level' => 'A1'],
			['id' => Str::uuid(), 'level' => 'A2'],
            ['id' => Str::uuid(), 'level' => 'B1'],
			['id' => Str::uuid(), 'level' => 'B2'],
			['id' => Str::uuid(), 'level' => 'C1'],
            ['id' => Str::uuid(), 'level' => 'C2'],
        ];
 
        DB::table('cefrs')->insert($cefrs);
    }
```

#### Source

Generate model, migration, factory and seeder:

```bash
php artisan make:model Source -a
```

##### Model

```php
class Source extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
    	'chapter',
    	'paragraph',
        'url',
        'author_id',
    ];

    // A source can have many authors
    public function sources(){
        return $this->belongsToMany(Source::class, 'author__sources');
    }
}
```

##### Migration

```php
public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100)->nullable();
            $table->integer('chapter')->length(100)->nullable();
            $table->integer('paragraph')->length(1000)->nullable();
            $table->string('url', 255)->nullable();
            $table->uuid('author_id');

            $table->foreign('author_id')
            ->references('id')
            ->on('authors')
            ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();

        return [
            'title'=>$this->faker->sentence(),
            'chapter'=>$this->faker->randomNumber(2, false),
            'paragraph'=>$this->faker->randomNumber(2, false),
            'url'=>$this->faker->url(),
            'author_id'=>$this->faker->randomElement($authorIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Source::factory()->times(10)->create();
    }
```

#### Type

Generate model, migration, factory and seeder:

```bash
php artisan make:model Type -a
```

##### Model

```php
class Type extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'type',
    ];
}
```

##### Migration

```php
    public function up()
    {
		Schema::create('types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 30)->unique;
            $table->timestamps();
        });
    }
```

##### Factory

```php

```

##### Seeder

```php
    public function run()
    {
        $cefrs = [
			['id' => Str::uuid(), 'type' => 'poetry'],
			['id' => Str::uuid(), 'type' => 'quote'],
        ];
 
        DB::table('types')->insert($cefrs);
    }
```

#### Author

Generate model, migration, factory and seeder:

```bash
php artisan make:model Author -a
```

##### Model

```php
class Author extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    // An author can have many texts
    public function authors(){
        return $this->belongsToMany(Author::class, 'author__texts');
    }
}
```

##### Migration

```php
public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->timestamps();
        });
    }
```

##### Factory

```php
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Author::factory()->times(10)->create();
    }
```

#### Author_Source

Generate model, migration, factory and seeder:

```bash
php artisan make:model Author_Source -a
```

##### Model

```php
class Author_Source extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'author_id',
        'source_id',
    ];
}
```

##### Migration

```php
public function up()
    {
        Schema::create('author__sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('author_id');
            $table->uuid('source_id');

            $table->foreign('author_id')
                  ->references('id')
                  ->on('authors')
                  ->onDelete('cascade');
            $table->foreign('source_id')
                  ->references('id')
                  ->on('sources')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();
        $sourceIds = Source::all()->pluck('id')->toArray();

        return [
            'author_id'=>$this->faker->randomElement($authorIds), 
            'source_id'=>$this->faker->randomElement($sourceIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Author_Source::factory()->times(10)->create();
    }
```

####

#### Text

Generate model, migration, factory and seeder:

```bash
php artisan make:model Text -a
```

##### Model

```php
class Text extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'text',
    	'difficulty',
        'source_id',
        'cefr_id',
    	'type_id',
    ];
}
```

##### Migration

```php
public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text', 65535);
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->uuid('source_id');
            $table->uuid('cefr_id');
            $table->uuid('type_id');
            $table->timestamps();

            $table->foreign('source_id')
                  ->references('id')
                  ->on('sources')
                  ->onDelete('cascade');
            $table->foreign('cefr_id')
                  ->references('id')
                  ->on('cefrs')
                  ->onDelete('cascade');
            $table->foreign('type_id')
                  ->references('id')
                  ->on('types')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
public function definition()
    {
        $cefrIds = Cefr::all()->pluck('id')->toArray();
        $difficulty = ['easy', 'medium', 'hard'];
        $sourceIds = Source::all()->pluck('id')->toArray();
        $typeIds = Type::all()->pluck('id')->toArray();

        return [
            'text'=>$this->faker->sentence(),
            'cefr_id'=>$this->faker->randomElement($cefrIds),
            'difficulty'=>$difficulty[rand(0, 2)], 
            'source_id'=>$this->faker->randomElement($sourceIds), 
            'type_id'=>$this->faker->randomElement($typeIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Text::factory()->times(10)->create();
    }
```

#### Estext

Generate model, migration, factory and seeder:

```bash
php artisan make:model Estext -a
```

##### Model

```php
class Estext extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'text',
        'text_id',
    ];
}
```

##### Migration

```php
    public function up()
    {
        Schema::create('estexts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text', 65534);
            $table->uuid('text_id');
            $table->timestamps();

            $table->foreign('text_id')
                  ->references('id')
                  ->on('texts')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $textIds = Text::all()->pluck('id')->toArray();
        
        return [
            'text'=>$this->faker->sentence(),
            'text_id'=>$this->faker->randomElement($textIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Text::factory()->times(10)->create();
    }
```

#### Translation

Generate model, migration, factory and seeder:

```bash
php artisan make:model Translation -a
```

##### Model

```php
class Translation extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'date_time',
        'hit_rate',
        'text',
    	'user_id',
    	'text_id',
    	'language_id',
    ];

    // A translation can be reviewed by many users
    public function translations() {
        return $this->belongsToMany(Translation::class, 'translation_users');
    }
}
```

##### Migration

```php
     public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('date_time')->useCurrent();
            // hit_rate: percentage, two decimal number between 0 and 1:
            $table->decimal('hit_rate', $precision = 3, $scale = 2);
            $table->text('text', 65534);
            $table->uuid('user_id');
            $table->uuid('text_id');
            $table->uuid('language_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('text_id')
                  ->references('id')
                  ->on('texts')
                  ->onDelete('cascade');
            $table->foreign('language_id')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $now = new DateTime();
        $languageIds = Language::all()->pluck('id')->toArray();
        $textIds = Text::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'date'=>$now,
            'hit_rate'=>$this->faker->randomFloat(2, 0, 1),
            'text'=>$this->faker->sentence(),
            'language_id'=>$this->faker->randomElement($languageIds), 
            'text_id'=>$this->faker->randomElement($textIds),
            'user_id'=>$this->faker->randomElement($userIds), 
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Translation::factory()->times(10)->create();
    }
```

#### Translation_User

Generate model, migration, factory and seeder:

```bash
php artisan make:model Translation_User -a
```

##### Model

```php
class Translation_User extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'author_id',
        'text_id',
    ];
}
```

##### Migration

```php
    public function up()
    {
        Schema::create('translation__users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('translation_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('translation_id')
                  ->references('id')
                  ->on('translations')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $translationIds = Translation::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'translation_id'=>$this->faker->randomElement($translationIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Translation_User::factory()->times(10)->create();
    }
```

#### Student

Generate model, migration, factory and seeder:

```bash
php artisan make:model Student -a
```

##### Model

```php
class Student extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'language_id',
    ];
}
```

##### Migration

```php
public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('language_id');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('language_id')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
    public function definition()
    {
        $languageIds = Language::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'language_id'=>$this->faker->randomElement($languageIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Student::factory()->times(10)->create();
    }
```

#### Native

Generate model, migration, factory and seeder:

```bash
php artisan make:model Native -a
```

##### Model

```php
class Native extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'language_id',
    ];
}
```

##### Migration

```php
	 public function up()
    {
        Schema::create('natives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('language_id');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('language_id')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }
```

##### Factory

```php
public function definition()
    {
        $languageIds = Language::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'language_id'=>$this->faker->randomElement($languageIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
```

##### Seeder

```php
public function run()
    {
        Native::factory()->times(10)->create();
    }

```

### Run seeders

#### DatabaseSeeder configuration

```php
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            RoleSeeder::class,
            LanguageSeeder::class,
            DifficultySeeder::class,
            CefrSeeder::class,
            TypeSeeder::class,
        ]);

        \App\Models\User::factory(10)->create();
        \App\Models\Role_User::factory(10)->create();
        \App\Models\Author::factory(10)->create();
        \App\Models\Text::factory(10)->create();
        \App\Models\Es_text::factory(10)->create();
        \App\Models\Translation::factory(10)->create();
        \App\Models\Translation_User::factory(10)->create();
        \App\Models\Author_Text::factory(10)->create();
        \App\Models\Learn_User::factory(10)->create();
        \App\Models\Speak_User::factory(10)->create();
    }
}
```

#### Fresh migrate and seed

```bash
php artisan migrate:fresh --seed

php artisan cache:clear
php artisan config:clear
php artisan route:clear
composer dumpautoload
composer update

php artisan migrate:fresh --seed
```

### Regenerate secret after fresh

To avoid **Attempt to read property \"secret\" on null** or **Personal access client not found. Please create one** error when generating user token after seeding from fresh the database,

```
php artisan migrate:fresh --seed
```

Is necessary to regenerate secret:

```
php artisan passport:client --personal
```

https://laravel.com/docs/9.x/passport#creating-a-personal-access-client

"After creating your personal access client, place the client's ID and plain-text secret value in your application's `.env` file:"

```
PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="unhashed-client-secret-value"
```

## Endpoints

### logging

We can register performed actions in our server by storing logs in `/storage/logs`

In order to use daily loggin, in `config/loging.php` change:

```php
    'default' => env('LOG_CHANNEL', 'stack'),
```

to

```php
    'default' => env('LOG_CHANNEL', 'daily'),
```

In controller:

```php
use Illuminate\Support\Facades\Log;
```

In function:

```php
Log::info('Showing the user profile for user: '.auth()->user()->id);
```

### user / auth

#### register, login

Create UserController:

```
php artisan make:controller UserController 
```

Create register and login functions in **App\Http\Controllers\UserController**:

 ```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate request data
        $data = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:64',
            'email' => 'required|string|unique:users|email|min:8|max:64',
            'password' => 'required|string|min:8|max:32|',
        ]);

        if ($data->fails()){
            return response()->json(['message' => $data->errors()->first(), 'status' => false], 400);
        }

        // If data is validated, encrypt password and store user data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'User registered successfully'], 200);    }

    public function login(Request $request)
    {
        // Validate request data
        $credentials = $request->validate([
            'email' => 'required|string|email|min:8|max:64',
            'password' => 'required|string|min:8|max:32|'
        ]);

        // Attempt to login user with provided credentials
        if (auth()->attempt($credentials)) {
            return response()->json([
                'user' => auth()->user(),
                'access_token' => auth()->user()->createToken('authToken')->accessToken
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid user or password.'
            ], 401);
        }
    }
}
 ```

When testing endpoint, if Postman returns 200 OK but it doesn't show the correct answer, it will be needed to add the header

Accept: application/json

https://stackoverflow.com/questions/63706546/laravel-api-not-accepting-json-request-from-postman

https://stackoverflow.com/questions/66886978/laravel-post-route-returns-http-code-200-but-no-record-is-created

#### forget

##### ForgetRequest

```bash
php artisan make:request ForgetRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email|min:8|max:64',
        ];
    }
}
```

##### view

In `resources /  views ` create `forget.blade.php`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Forget Password</title>
</head>
<body>
Hi<br/>
To change Your Pasword <a href="https://quiet-shelf-00426.herokuapp.com/reset/{{$data}}">click here</a><br/>
    Pincode : {{ $data }}
</body>
```

##### ForgetMail.php

In `app` create `Mail` folder and `ForgetMail.php` file

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetMail extends Mailable
{
    use Queueable, SerializesModels;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->data = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->data;
        return $this->from('dev.angelgr.com@gmail.com')->view('forget', compact('data'))->subject('Password Reset Link');
    }
}
```

##### UserController.php

In `app/http/Controllers/Api/UserController.php`:

```php
public function forget(ForgetRequest $request) {
        $email = $request->email;
        
        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'Invalid Email'
            ], 401);
        }
           
        try {
            // generate Random Token
            $token = rand(10, 100000);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token
            ]);
            
            // Mail send to user
            Mail::to($email)->send(new ForgetMail($token));
             
            return response([
                'message' => 'Reset password email sent.'
            ], 200);
            
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()], 400);
        }
    }
```

#### reset

##### ResetRequest

```bash
php artisan make:request ResetRequest
```



```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|string|min:8|max:32|',
        ];
    }
}

```

##### UserController.php

```php
public function reset(ResetRequest $request)
    {
        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);

        // Check if email and pin exist in password_resets table
        $emailcheck = DB::table('password_resets')->where('email',$email)->first();
        $pincheck = DB::table('password_resets')->where('token',$token)->first();

        // Show error if email or pin don't exist
        if(!$emailcheck) {
            return response([
                'message' => "Email not found."
            ],401);
        }
        if(!$pincheck) {
            return response([
                'message' => "Invalid pin code."
            ],401);
        }

        // If they exist, update password and delete email from password_resets table
        DB::table('users')->where('email',$email)->update(['password'=>$password]);
        DB::table('password_resets')->where('email',$email)->delete();
        
        return response([
            'message' => 'Password changed succesfully.'
        ]);
    }
```

#### editProfile

##### EditProfileRequest

```bash
php artisan make:request EditProfileRequest
```

##### UserController.php

```php
public function editProfile(EditProfileRequest $request)
    {
        $isDataChanged = false;

        // Search for logged in user
        $user = User::where('id', '=', auth('api')->user()->id)->first();

        // Reject changes if an aunthenticated user tries to delete another user
        if($user->id != auth()->user()->id) {
            return response()->json([
                'message' => 'Action unauthorized',
            ], 400);
        }

        // Change data if request is different from user's data: 
        if($user->first_name != $request->first_name) {
            $user->first_name = $request->first_name;
            $isDataChanged = true;
        }
        if($user->last_name != $request->last_name) {
            $user->last_name = $request->last_name;
            $isDataChanged = true;
        }
        if($user->username != $request->username) {
            $user->username = $request->username;
            $isDataChanged = true;
        }
        if($user->email != $request->email) {
            $user->email = $request->email;
            $isDataChanged = true;
        }
        // Check if request password is correct
        if (Hash::check($user->password, $request->password)) {
            // Change data if request password is different from user's password
            if (!Hash::check($user->password, $request->newPassword)) {
                $user->password = Hash::make($request->newPassword);
                $isDataChanged = true;
            }
        } else {
            return response()->json(['message' => 'Incorrect password'], 401);
        }
        // Check if newPassword and ConfirmeNewPassword match
        if ($request->newPassword === $request->newPassword_confirmation) {
            // Change data if request password is different from user's password
            if (!Hash::check($request->newPassword, $user->password)) {
                $user->Password = Hash::make($request->newPassword);
                $isDataChanged = true;
            } else {
                return response()->json(['message' => 'New password and confirmed new password don\'t match'], 400);
            }
        }
        
        // Save data is there have been any change
        if($isDataChanged) {
            $user->save();
            return response()->json([
                'message' => 'User has been edited successfully',
                'user' => auth()->user(),
            ], 200);
        } else {
            return response()->json([
                'message' => 'User has not been edited',
                'user' => auth()->user(),
            ], 400);
        }
    }
```

#### deleteProfile

##### UserController.php

```php
public function deleteProfile ($user_id) {
        // Search for id received in request 
        $user = User::where('id', '=', $user_id)->first();

        // If id doesn't exist
        if($user === []) {
            return response()->json([
                'message' => 'Invalid user',
            ], 400);
        }
        // If an aunthenticated user tries to delete another user
        if($user->id != auth()->user()->id) {
            return response()->json([
                'message' => 'Action unauthorized',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Your user has been deleted successfully.'
        ], 200);
    }
```

#### IsAdmin middleware

https://gopalkildoliya.medium.com/add-simple-admin-middleware-in-your-laravel-app-3428953b3fa3

in UserController.php:

```php
    public function isAdmin() {
        return $this->is_admin === 'true';
    }
```

To create a middleware:

```bash
php artisan make:middleware IsAdmin
```

This will create a middleware file app/Http/Middleware/IsAdmin.php

```php
	public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user->isAdmin(), 403, 'Sorry, you are unauthorized to access this resource.');

        // It only proceeds with next if the user is admin
        return $next($request);
    }
```

We can register this middleware to our application. To register, add this to `$routeMiddleware` array of `app/Http/Kernel.php` with key as `admin`

```php
protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,

        'admin' => \App\Http\Middleware\IsAdmin::class,
    ];
```

 There are many ways you can use this middleware. Here we are going to use this with routes.

```php
Route::delete('/users/delete/{user_id}', [UserController::class, 'deleteById'])->middleware('admin');
```

### text

```bash
php artisan make:controller Api/TextController
```

####
