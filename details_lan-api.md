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
    protected $fillable = [
        'type',
    ];

    // A role can have many users
    public function roles(){
        return $this->belongsToMany(Role::class, 'role_users');
    }
```

##### Migration

```php
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
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
    	DB::table('roles')->truncate();
 
        $roles = [
			['type' => 'admin'],
			['type' => 'student'],
            ['type' => 'teacher'],
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
        DB::table('languages')->truncate();
 
        $languages = [
            ['name' => 'Abkhazian', 'code' => 'ab'],
            ['name' => 'Afar', 'code' => 'aa'],
            ['name' => 'Afrikaans', 'code' => 'af'],
            ['name' => 'Akan', 'code' => 'ak'],
            ['name' => 'Albanian', 'code' => 'sq'],
            ['name' => 'Amharic', 'code' => 'am'],
            ['name' => 'Arabic', 'code' => 'ar'],
            ['name' => 'Aragonese', 'code' => 'an'],
            ['name' => 'Armenian', 'code' => 'hy'],
            ['name' => 'Assamese', 'code' => 'as'],
            ['name' => 'Avaric', 'code' => 'av'],
            ['name' => 'Avestan', 'code' => 'ae'],
            ['name' => 'Aymara', 'code' => 'ay'],
            ['name' => 'Azerbaijani', 'code' => 'az'],
            ['name' => 'Bambara', 'code' => 'bm'],
            ['name' => 'Bashkir', 'code' => 'ba'],
            ['name' => 'Basque', 'code' => 'eu'],
            ['name' => 'Belarusian', 'code' => 'be'],
            ['name' => 'Bengali', 'code' => 'bn'],
            ['name' => 'Bislama', 'code' => 'bi'],
            ['name' => 'Bosnian', 'code' => 'bs'],
            ['name' => 'Breton', 'code' => 'br'],
            ['name' => 'Bulgarian', 'code' => 'bg'],
            ['name' => 'Burmese', 'code' => 'my'],
            ['name' => 'Catalan', 'code' => 'ca'],
            ['name' => 'Chamorro', 'code' => 'ch'],
            ['name' => 'Chechen', 'code' => 'ce'],
            ['name' => 'Chichewa', 'code' => 'y'],
            ['name' => 'Chinese', 'code' => 'zh'],
            ['name' => 'Church Slavic', 'code' => 'cu'],
            ['name' => 'Chuvash', 'code' => 'cv'],
            ['name' => 'Cornish', 'code' => 'kw'],
            ['name' => 'Corsican', 'code' => 'co'],
            ['name' => 'Cree', 'code' => 'cr'],
            ['name' => 'Croatian', 'code' => 'hr'],
            ['name' => 'Czech', 'code' => 'cs'],
            ['name' => 'Danish', 'code' => 'da'],
            ['name' => 'Divehi', 'code' => 'v'],
            ['name' => 'Dutch', 'code' => 'nl'],
            ['name' => 'Dzongkha', 'code' => 'dz'],
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Esperanto', 'code' => 'eo'],
            ['name' => 'Estonian', 'code' => 'et'],
            ['name' => 'Ewe', 'code' => 'ee'],
            ['name' => 'Faroese', 'code' => 'fo'],
            ['name' => 'Fijian', 'code' => 'fj'],
            ['name' => 'Finnish', 'code' => 'fi'],
            ['name' => 'French', 'code' => 'fr'],
            ['name' => 'Western Frisian', 'code' => 'fy'],
            ['name' => 'Fulah', 'code' => 'ff'],
            ['name' => 'Gaelic', 'code' => 'gd'],
            ['name' => 'Galician', 'code' => 'gl'],
            ['name' => 'Ganda', 'code' => 'lg'],
            ['name' => 'Georgian', 'code' => 'ka'],
            ['name' => 'German', 'code' => 'de'],
            ['name' => 'Greek', 'code' => 'el'],
            ['name' => 'Kalaallisut', 'code' => 'kl'],
            ['name' => 'Guarani', 'code' => 'gn'],
            ['name' => 'Gujarati', 'code' => 'gu'],
            ['name' => 'Haitian', 'code' => 'ht'],
            ['name' => 'Hausa', 'code' => 'ha'],
            ['name' => 'Hebrew', 'code' => 'he'],
            ['name' => 'Herero', 'code' => 'hz'],
            ['name' => 'Hindi', 'code' => 'hi'],
            ['name' => 'Hiri Motu', 'code' => 'ho'],
            ['name' => 'Hungarian', 'code' => 'hu'],
            ['name' => 'Icelandic', 'code' => 'is'],
            ['name' => 'Ido', 'code' => 'io'],
            ['name' => 'Igbo', 'code' => 'ig'],
            ['name' => 'Indonesian', 'code' => 'id'],
            ['name' => 'Interlingua', 'code' => 'ia'],
            ['name' => 'Interlingue', 'code' => 'ie'],
            ['name' => 'Inuktitut', 'code' => 'iu'],
            ['name' => 'Inupiaq', 'code' => 'ik'],
            ['name' => 'Irish', 'code' => 'ga'],
            ['name' => 'Italian', 'code' => 'it'],
            ['name' => 'Japanese', 'code' => 'ja'],
            ['name' => 'Javanese', 'code' => 'jv'],
            ['name' => 'Kannada', 'code' => 'kn'],
            ['name' => 'Kanuri', 'code' => 'kr'],
            ['name' => 'Kashmiri', 'code' => 'ks'],
            ['name' => 'Kazakh', 'code' => 'kk'],
            ['name' => 'Central Khmer', 'code' => 'km'],
            ['name' => 'Kikuyu', 'code' => 'ki'],
            ['name' => 'Kinyarwanda', 'code' => 'rw'],
            ['name' => 'Kirghiz', 'code' => 'ky'],
            ['name' => 'Komi', 'code' => 'kv'],
            ['name' => 'Kongo', 'code' => 'kg'],
            ['name' => 'Korean', 'code' => 'ko'],
            ['name' => 'Kuanyama', 'code' => 'kj'],
            ['name' => 'Kurdish', 'code' => 'ku'],
            ['name' => 'Lao', 'code' => 'lo'],
            ['name' => 'Latin', 'code' => 'la'],
            ['name' => 'Latvian', 'code' => 'lv'],
            ['name' => 'Limburgan', 'code' => 'li'],
            ['name' => 'Lingala', 'code' => 'ln'],
            ['name' => 'Lithuanian', 'code' => 'lt'],
            ['name' => 'Luba-Katanga', 'code' => 'lu'],
            ['name' => 'Luxembourgish', 'code' => 'lb'],
            ['name' => 'Macedonian', 'code' => 'mk'],
            ['name' => 'Malagasy', 'code' => 'mg'],
            ['name' => 'Malay', 'code' => 'ms'],
            ['name' => 'Malayalam', 'code' => 'ml'],
            ['name' => 'Maltese', 'code' => 'mt'],
            ['name' => 'Manx', 'code' => 'gv'],
            ['name' => 'Maori', 'code' => 'mi'],
            ['name' => 'Marathi', 'code' => 'mr'],
            ['name' => 'Marshallese', 'code' => 'mh'],
            ['name' => 'Mongolian', 'code' => 'mn'],
            ['name' => 'Nauru', 'code' => 'na'],
            ['name' => 'Navajo', 'code' => 'nv'],
            ['name' => 'North Ndebele', 'code' => 'nd'],
            ['name' => 'South Ndebele', 'code' => 'nr'],
            ['name' => 'Ndonga', 'code' => 'ng'],
            ['name' => 'Nepali', 'code' => 'ne'],
            ['name' => 'Norwegian', 'code' => 'no'],
            ['name' => 'Norwegian Bokmål', 'code' => 'nb'],
            ['name' => 'Norwegian Nynorsk', 'code' => 'nn'],
            ['name' => 'Sichuan Yi', 'code' => 'ii'],
            ['name' => 'Occitan', 'code' => 'oc'],
            ['name' => 'Ojibwa', 'code' => 'oj'],
            ['name' => 'Oriya', 'code' => 'or'],
            ['name' => 'Oromo', 'code' => 'om'],
            ['name' => 'Ossetian', 'code' => 'os'],
            ['name' => 'Pali', 'code' => 'pi'],
            ['name' => 'Pashto', 'code' => 'ps'],
            ['name' => 'Persian', 'code' => 'fa'],
            ['name' => 'Polish', 'code' => 'pl'],
            ['name' => 'Portuguese', 'code' => 'pt'],
            ['name' => 'Punjabi', 'code' => 'pa'],
            ['name' => 'Quechua', 'code' => 'qu'],
            ['name' => 'Romanian', 'code' => 'ro'],
            ['name' => 'Romansh', 'code' => 'rm'],
            ['name' => 'Rundi', 'code' => 'rn'],
            ['name' => 'Russian', 'code' => 'ru'],
            ['name' => 'Northern Sami', 'code' => 'se'],
            ['name' => 'Samoan', 'code' => 'sm'],
            ['name' => 'Sango', 'code' => 'sg'],
            ['name' => 'Sanskrit', 'code' => 'sa'],
            ['name' => 'Sardinian', 'code' => 'sc'],
            ['name' => 'Serbian', 'code' => 'sr'],
            ['name' => 'Shona', 'code' => 'sn'],
            ['name' => 'Sindhi', 'code' => 'sd'],
            ['name' => 'Sinhala', 'code' => 'si'],
            ['name' => 'Slovak', 'code' => 'sk'],
            ['name' => 'Slovenian', 'code' => 'sl'],
            ['name' => 'Somali', 'code' => 'so'],
            ['name' => 'Southern Sotho', 'code' => 'st'],
            ['name' => 'Spanish', 'code' => 'es'],
            ['name' => 'Sundanese', 'code' => 'su'],
            ['name' => 'Swahili', 'code' => 'sw'],
            ['name' => 'Swati', 'code' => 'ss'],
            ['name' => 'Swedish', 'code' => 'sv'],
            ['name' => 'Tagalog', 'code' => 'tl'],
            ['name' => 'Tahitian', 'code' => 'ty'],
            ['name' => 'Tajik', 'code' => 'tg'],
            ['name' => 'Tamil', 'code' => 'ta'],
            ['name' => 'Tatar', 'code' => 'tt'],
            ['name' => 'Telugu', 'code' => 'te'],
            ['name' => 'Thai', 'code' => 'th'],
            ['name' => 'Tibetan', 'code' => 'bo'],
            ['name' => 'Tigrinya', 'code' => 'ti'],
            ['name' => 'Tonga', 'code' => 'to'],
            ['name' => 'Tsonga', 'code' => 'ts'],
            ['name' => 'Tswana', 'code' => 'tn'],
            ['name' => 'Turkish', 'code' => 'tr'],
            ['name' => 'Turkmen', 'code' => 'tk'],
            ['name' => 'Twi', 'code' => 'tw'],
            ['name' => 'Uighur', 'code' => 'ug'],
            ['name' => 'Ukrainian', 'code' => 'uk'],
            ['name' => 'Urdu', 'code' => 'ur'],
            ['name' => 'Uzbek', 'code' => 'uz'],
            ['name' => 'Venda', 'code' => 've'],
            ['name' => 'Vietnamese', 'code' => 'vi'],
            ['name' => 'Volapük', 'code' => 'vo'],
            ['name' => 'Walloon', 'code' => 'wa'],
            ['name' => 'Welsh', 'code' => 'cy'],
            ['name' => 'Wolof', 'code' => 'wo'],
            ['name' => 'Xhosa', 'code' => 'xh'],
            ['name' => 'Yiddish', 'code' => 'yi'],
            ['name' => 'Yoruba', 'code' => 'yo'],
            ['name' => 'Zhuang', 'code' => 'za'],
            ['name' => 'Zulu', 'code' => 'zu'],
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
    protected $fillable = [
        'role_id',
        'user_id',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('role_users', function (Blueprint $table) {
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
        Role_user::factory()->times(10)->create();
    }
```

#### Difficulty

Generate model, migration, factory and seeder:

```bash
php artisan make:model Difficulty -a
```

##### Model

```php
    protected $fillable = [
        'level',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('difficulties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level');
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
    	DB::table('difficulties')->truncate();
 
        $difficulties = [
			['level' => 'easy'],
			['level' => 'medium'],
            ['level' => 'hard'],
        ];
 
        DB::table('difficulties')->insert($difficulties);
    }
```

#### Cefr

Generate model, migration, factory and seeder:

```bash
php artisan make:model Difficulty -a
```

##### Model

```php
    protected $fillable = [
        'level',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('cefrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level', 2)->unique;
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
    	DB::table('cefrs')->truncate();
 
        $cefrs = [
			['level' => 'A1'],
			['level' => 'A2'],
            ['level' => 'B1'],
			['level' => 'B2'],
			['level' => 'C1'],
            ['level' => 'C2'],
        ];
 
        DB::table('cefrs')->insert($cefrs);
    }
```

#### Type

Generate model, migration, factory and seeder:

```bash
php artisan make:model Type -a
```

##### Model

```php
    protected $fillable = [
        'type',
    ];
```

##### Migration

```php
    public function up()
    {
		Schema::create('types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 20)->unique;
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
        DB::table('cefrs')->truncate();
 
        $cefrs = [
			['type' => 'poetry'],
			['type' => 'quote'],
        ];
 
        DB::table('cefrs')->insert($cefrs);
    }
```

#### Author

Generate model, migration, factory and seeder:

```bash
php artisan make:model Author -a
```

##### Model

```php
	protected $fillable = [
        'first_name',
        'last_name',
    ];

    // An author can have many texts
    public function authors(){
        return $this->belongsToMany(Author::class, 'author__texts');
    }
```

##### Migration

```php
public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name, 50');
            $table->string('last_name, 50');
            $table->timestamps();
        });
    }
```

##### Factory

```php
    public function definition()
    {
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
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

#### Text

Generate model, migration, factory and seeder:

```bash
php artisan make:model Text -a
```

##### Model

```php
protected $fillable = [
        'text',
        'author_id',
        'cefr_id',
    	'difficulty_id',
    	'type_id',
    ];

    // A text can have many authors
    public function texts(){
        return $this->belongsToMany(Text::class, 'author__texts');
    }
```

##### Migration

```php
    public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text', 65534)->unique();
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->uuid('author_id');
            $table->uuid('cefr_id');
            $table->uuid('type_id');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')
                  ->on('authors')
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
        $authorIds = Author::all()->pluck('id')->toArray();
        $cefrIds = Cefr::all()->pluck('id')->toArray();
        $difficultyIds = Difficulty::all()->pluck('id')->toArray();
        $typeIds = Type::all()->pluck('id')->toArray();

        return [
            'text'=>$this->faker->sentence(),
            'author_id'=>$this->faker->randomElement($authorIds), 
            'user_id'=>$this->faker->randomElement($cefrIds),
            'role_id'=>$this->faker->randomElement($difficultyIds), 
            'user_id'=>$this->faker->randomElement($typeIds),
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

#### Es_text

Generate model, migration, factory and seeder:

```bash
php artisan make:model Es_text -a
```

##### Model

```php
    protected $fillable = [
        'text',
        'text_id',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('estexts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text', 65534)->unique();
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
        Text::factory()->times(10)->create();
    }
```

#### Translation_User

Generate model, migration, factory and seeder:

```bash
php artisan make:model Translation_User -a
```

##### Model

```php
    protected $fillable = [
        'author_id',
        'text_id',
    ];
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

#### Author_Text

Generate model, migration, factory and seeder:

```bash
php artisan make:model Autor_Text -a
```

##### Model

```php
    protected $fillable = [
        'author_id',
        'text_id',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('author__texts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('author_id');
            $table->uuid('text_id');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')
                  ->on('authors')
                  ->onDelete('cascade');
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
        $authorIds = Author::all()->pluck('id')->toArray();
        $textIds = Text::all()->pluck('id')->toArray();

        return [
            'author_id'=>$this->faker->randomElement($authorIds), 
            'text_id'=>$this->faker->randomElement($textIds),
        ];
    }
```

##### Seeder

```php
    public function run()
    {
        Author_Text::factory()->times(10)->create();
    }
```

#### Learn_User

Generate model, migration, factory and seeder:

```bash
php artisan make:model Learn_User -a
```

##### Model

```php
    protected $fillable = [
        'user_id',
        'language_id',
    ];
```

##### Migration

```php
    public function up()
    {
        Schema::create('learn__users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('language_id');
            $table->timestamps();

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
        Learn_User::factory()->times(10)->create();
```

#### Speak_User

Generate model, migration, factory and seeder:

```bash
php artisan make:model Speak_User -a
```

##### Model

```php
    protected $fillable = [
        'user_id',
        'language_id',
    ];
```

##### Migration

```php
	public function up()
    {
        Schema::create('speak__users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('language_id');
            $table->timestamps();

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
        Speak_User::factory()->times(10)->create();

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
