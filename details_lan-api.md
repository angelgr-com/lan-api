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

Generate model, migration, factory and seeder:

```

```

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
        $countryIds = Country::all()->pluck('id')->toArray();

        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'country_id' => $this->faker->randomElement($countryIds),
            'profile_picture' => $this->faker->url(),
            'username' => $this->faker->name(),
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
protected $fillable = [
    'name',
    'code',
];
```

##### Migration

```php
public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code');
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
public function run()
    {
        DB::table('countries')->truncate();
 
        $countries = [
            ['name' => 'Afghanistan', 'code' => 'AF'],
            ['name' => 'Åland Islands', 'code' => 'AX'],
            ['name' => 'Albania', 'code' => 'AL'],
            ['name' => 'Algeria', 'code' => 'DZ'],
            ['name' => 'American Samoa', 'code' => 'AS'],
            ['name' => 'Andorra', 'code' => 'AD'],
            ['name' => 'Angola', 'code' => 'AO'],
            ['name' => 'Anguilla', 'code' => 'AI'],
            ['name' => 'Antarctica', 'code' => 'AQ'],
            ['name' => 'Antigua and Barbuda', 'code' => 'AG'],
            ['name' => 'Argentina', 'code' => 'AR'],
            ['name' => 'Armenia', 'code' => 'AM'],
            ['name' => 'Aruba', 'code' => 'AW'],
            ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Austria', 'code' => 'AT'],
            ['name' => 'Azerbaijan', 'code' => 'AZ'],
            ['name' => 'Bahamas', 'code' => 'BS'],
            ['name' => 'Bahrain', 'code' => 'BH'],
            ['name' => 'Bangladesh', 'code' => 'BD'],
            ['name' => 'Barbados', 'code' => 'BB'],
            ['name' => 'Belarus', 'code' => 'BY'],
            ['name' => 'Belgium', 'code' => 'BE'],
            ['name' => 'Belize', 'code' => 'BZ'],
            ['name' => 'Benin', 'code' => 'BJ'],
            ['name' => 'Bermuda', 'code' => 'BM'],
            ['name' => 'Bhutan', 'code' => 'BT'],
            ['name' => 'Bolivia, Plurinational State of', 'code' => 'BO'],
            ['name' => 'Bonaire, Sint Eustatius and Saba', 'code' => 'BQ'],
            ['name' => 'Bosnia and Herzegovina', 'code' => 'BA'],
            ['name' => 'Botswana', 'code' => 'BW'],
            ['name' => 'Bouvet Island', 'code' => 'BV'],
            ['name' => 'Brazil', 'code' => 'BR'],
            ['name' => 'British Indian Ocean Territory', 'code' => 'IO'],
            ['name' => 'Brunei Darussalam', 'code' => 'BN'],
            ['name' => 'Bulgaria', 'code' => 'BG'],
            ['name' => 'Burkina Faso', 'code' => 'BF'],
            ['name' => 'Burundi', 'code' => 'BI'],
            ['name' => 'Cambodia', 'code' => 'KH'],
            ['name' => 'Cameroon', 'code' => 'CM'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'Cape Verde', 'code' => 'CV'],
            ['name' => 'Cayman Islands', 'code' => 'KY'],
            ['name' => 'Central African Republic', 'code' => 'CF'],
            ['name' => 'Chad', 'code' => 'TD'],
            ['name' => 'Chile', 'code' => 'CL'],
            ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Christmas Island', 'code' => 'CX'],
            ['name' => 'Cocos (Keeling) Islands', 'code' => 'CC'],
            ['name' => 'Colombia', 'code' => 'CO'],
            ['name' => 'Comoros', 'code' => 'KM'],
            ['name' => 'Congo', 'code' => 'CG'],
            ['name' => 'Congo, the Democratic Republic of the', 'code' => 'CD'],
            ['name' => 'Cook Islands', 'code' => 'CK'],
            ['name' => 'Costa Rica', 'code' => 'CR'],
            ['name' => 'Côte d\'Ivoire', 'code' => 'CI'],
            ['name' => 'Croatia', 'code' => 'HR'],
            ['name' => 'Cuba', 'code' => 'CU'],
            ['name' => 'Curaçao', 'code' => 'CW'],
            ['name' => 'Cyprus', 'code' => 'CY'],
            ['name' => 'Czech Republic', 'code' => 'CZ'],
            ['name' => 'Denmark', 'code' => 'DK'],
            ['name' => 'Djibouti', 'code' => 'DJ'],
            ['name' => 'Dominica', 'code' => 'DM'],
            ['name' => 'Dominican Republic', 'code' => 'DO'],
            ['name' => 'Ecuador', 'code' => 'EC'],
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'El Salvador', 'code' => 'SV'],
            ['name' => 'Equatorial Guinea', 'code' => 'GQ'],
            ['name' => 'Eritrea', 'code' => 'ER'],
            ['name' => 'Estonia', 'code' => 'EE'],
            ['name' => 'Ethiopia', 'code' => 'ET'],
            ['name' => 'Falkland Islands (Malvinas)', 'code' => 'FK'],
            ['name' => 'Faroe Islands', 'code' => 'FO'],
            ['name' => 'Fiji', 'code' => 'FJ'],
            ['name' => 'Finland', 'code' => 'FI'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'French Guiana', 'code' => 'GF'],
            ['name' => 'French Polynesia', 'code' => 'PF'],
            ['name' => 'French Southern Territories', 'code' => 'TF'],
            ['name' => 'Gabon', 'code' => 'GA'],
            ['name' => 'Gambia', 'code' => 'GM'],
            ['name' => 'Georgia', 'code' => 'GE'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'Ghana', 'code' => 'GH'],
            ['name' => 'Gibraltar', 'code' => 'GI'],
            ['name' => 'Greece', 'code' => 'GR'],
            ['name' => 'Greenland', 'code' => 'GL'],
            ['name' => 'Grenada', 'code' => 'GD'],
            ['name' => 'Guadeloupe', 'code' => 'GP'],
            ['name' => 'Guam', 'code' => 'GU'],
            ['name' => 'Guatemala', 'code' => 'GT'],
            ['name' => 'Guernsey', 'code' => 'GG'],
            ['name' => 'Guinea', 'code' => 'GN'],
            ['name' => 'Guinea-Bissau', 'code' => 'GW'],
            ['name' => 'Guyana', 'code' => 'GY'],
            ['name' => 'Haiti', 'code' => 'HT'],
            ['name' => 'Heard Island and McDonald Mcdonald Islands', 'code' => 'HM'],
            ['name' => 'Holy See (Vatican City State)', 'code' => 'VA'],
            ['name' => 'Honduras', 'code' => 'HN'],
            ['name' => 'Hong Kong', 'code' => 'HK'],
            ['name' => 'Hungary', 'code' => 'HU'],
            ['name' => 'Iceland', 'code' => 'IS'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'Indonesia', 'code' => 'ID'],
            ['name' => 'Iran, Islamic Republic of', 'code' => 'IR'],
            ['name' => 'Iraq', 'code' => 'IQ'],
            ['name' => 'Ireland', 'code' => 'IE'],
            ['name' => 'Isle of Man', 'code' => 'IM'],
            ['name' => 'Israel', 'code' => 'IL'],
            ['name' => 'Italy', 'code' => 'IT'],
            ['name' => 'Jamaica', 'code' => 'JM'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'Jersey', 'code' => 'JE'],
            ['name' => 'Jordan', 'code' => 'JO'],
            ['name' => 'Kazakhstan', 'code' => 'KZ'],
            ['name' => 'Kenya', 'code' => 'KE'],
            ['name' => 'Kiribati', 'code' => 'KI'],
            ['name' => 'Korea, Democratic People\'s Republic of', 'code' => 'KP'],
            ['name' => 'Korea, Republic of', 'code' => 'KR'],
            ['name' => 'Kuwait', 'code' => 'KW'],
            ['name' => 'Kyrgyzstan', 'code' => 'KG'],
            ['name' => 'Lao People\'s Democratic Republic', 'code' => 'LA'],
            ['name' => 'Latvia', 'code' => 'LV'],
            ['name' => 'Lebanon', 'code' => 'LB'],
            ['name' => 'Lesotho', 'code' => 'LS'],
            ['name' => 'Liberia', 'code' => 'LR'],
            ['name' => 'Libya', 'code' => 'LY'],
            ['name' => 'Liechtenstein', 'code' => 'LI'],
            ['name' => 'Lithuania', 'code' => 'LT'],
            ['name' => 'Luxembourg', 'code' => 'LU'],
            ['name' => 'Macao', 'code' => 'MO'],
            ['name' => 'Macedonia, the Former Yugoslav Republic of', 'code' => 'MK'],
            ['name' => 'Madagascar', 'code' => 'MG'],
            ['name' => 'Malawi', 'code' => 'MW'],
            ['name' => 'Malaysia', 'code' => 'MY'],
            ['name' => 'Maldives', 'code' => 'MV'],
            ['name' => 'Mali', 'code' => 'ML'],
            ['name' => 'Malta', 'code' => 'MT'],
            ['name' => 'Marshall Islands', 'code' => 'MH'],
            ['name' => 'Martinique', 'code' => 'MQ'],
            ['name' => 'Mauritania', 'code' => 'MR'],
            ['name' => 'Mauritius', 'code' => 'MU'],
            ['name' => 'Mayotte', 'code' => 'YT'],
            ['name' => 'Mexico', 'code' => 'MX'],
            ['name' => 'Micronesia, Federated States of', 'code' => 'FM'],
            ['name' => 'Moldova, Republic of', 'code' => 'MD'],
            ['name' => 'Monaco', 'code' => 'MC'],
            ['name' => 'Mongolia', 'code' => 'MN'],
            ['name' => 'Montenegro', 'code' => 'ME'],
            ['name' => 'Montserrat', 'code' => 'MS'],
            ['name' => 'Morocco', 'code' => 'MA'],
            ['name' => 'Mozambique', 'code' => 'MZ'],
            ['name' => 'Myanmar', 'code' => 'MM'],
            ['name' => 'Namibia', 'code' => 'NA'],
            ['name' => 'Nauru', 'code' => 'NR'],
            ['name' => 'Nepal', 'code' => 'NP'],
            ['name' => 'Netherlands', 'code' => 'NL'],
            ['name' => 'New Caledonia', 'code' => 'NC'],
            ['name' => 'New Zealand', 'code' => 'NZ'],
            ['name' => 'Nicaragua', 'code' => 'NI'],
            ['name' => 'Niger', 'code' => 'NE'],
            ['name' => 'Nigeria', 'code' => 'NG'],
            ['name' => 'Niue', 'code' => 'NU'],
            ['name' => 'Norfolk Island', 'code' => 'NF'],
            ['name' => 'Northern Mariana Islands', 'code' => 'MP'],
            ['name' => 'Norway', 'code' => 'NO'],
            ['name' => 'Oman', 'code' => 'OM'],
            ['name' => 'Pakistan', 'code' => 'PK'],
            ['name' => 'Palau', 'code' => 'PW'],
            ['name' => 'Palestine, State of', 'code' => 'PS'],
            ['name' => 'Panama', 'code' => 'PA'],
            ['name' => 'Papua New Guinea', 'code' => 'PG'],
            ['name' => 'Paraguay', 'code' => 'PY'],
            ['name' => 'Peru', 'code' => 'PE'],
            ['name' => 'Philippines', 'code' => 'PH'],
            ['name' => 'Pitcairn', 'code' => 'PN'],
            ['name' => 'Poland', 'code' => 'PL'],
            ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Puerto Rico', 'code' => 'PR'],
            ['name' => 'Qatar', 'code' => 'QA'],
            ['name' => 'Réunion', 'code' => 'RE'],
            ['name' => 'Romania', 'code' => 'RO'],
            ['name' => 'Russian Federation', 'code' => 'RU'],
            ['name' => 'Rwanda', 'code' => 'RW'],
            ['name' => 'Saint Barthélemy', 'code' => 'BL'],
            ['name' => 'Saint Helena, Ascension and Tristan da Cunha', 'code' => 'SH'],
            ['name' => 'Saint Kitts and Nevis', 'code' => 'KN'],
            ['name' => 'Saint Lucia', 'code' => 'LC'],
            ['name' => 'Saint Martin (French part)', 'code' => 'MF'],
            ['name' => 'Saint Pierre and Miquelon', 'code' => 'PM'],
            ['name' => 'Saint Vincent and the Grenadines', 'code' => 'VC'],
            ['name' => 'Samoa', 'code' => 'WS'],
            ['name' => 'San Marino', 'code' => 'SM'],
            ['name' => 'Sao Tome and Principe', 'code' => 'ST'],
            ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'Senegal', 'code' => 'SN'],
            ['name' => 'Serbia', 'code' => 'RS'],
            ['name' => 'Seychelles', 'code' => 'SC'],
            ['name' => 'Sierra Leone', 'code' => 'SL'],
            ['name' => 'Singapore', 'code' => 'SG'],
            ['name' => 'Sint Maarten (Dutch part)', 'code' => 'SX'],
            ['name' => 'Slovakia', 'code' => 'SK'],
            ['name' => 'Slovenia', 'code' => 'SI'],
            ['name' => 'Solomon Islands', 'code' => 'SB'],
            ['name' => 'Somalia', 'code' => 'SO'],
            ['name' => 'South Africa', 'code' => 'ZA'],
            ['name' => 'South Georgia and the South Sandwich Islands', 'code' => 'GS'],
            ['name' => 'South Sudan', 'code' => 'SS'],
            ['name' => 'Spain', 'code' => 'ES'],
            ['name' => 'Sri Lanka', 'code' => 'LK'],
            ['name' => 'Sudan', 'code' => 'SD'],
            ['name' => 'Suriname', 'code' => 'SR'],
            ['name' => 'Svalbard and Jan Mayen', 'code' => 'SJ'],
            ['name' => 'Swaziland', 'code' => 'SZ'],
            ['name' => 'Sweden', 'code' => 'SE'],
            ['name' => 'Switzerland', 'code' => 'CH'],
            ['name' => 'Syrian Arab Republic', 'code' => 'SY'],
            ['name' => 'Taiwan', 'code' => 'TW'],
            ['name' => 'Tajikistan', 'code' => 'TJ'],
            ['name' => 'Tanzania, United Republic of', 'code' => 'TZ'],
            ['name' => 'Thailand', 'code' => 'TH'],
            ['name' => 'Timor-Leste', 'code' => 'TL'],
            ['name' => 'Togo', 'code' => 'TG'],
            ['name' => 'Tokelau', 'code' => 'TK'],
            ['name' => 'Tonga', 'code' => 'TO'],
            ['name' => 'Trinidad and Tobago', 'code' => 'TT'],
            ['name' => 'Tunisia', 'code' => 'TN'],
            ['name' => 'Turkey', 'code' => 'TR'],
            ['name' => 'Turkmenistan', 'code' => 'TM'],
            ['name' => 'Turks and Caicos Islands', 'code' => 'TC'],
            ['name' => 'Tuvalu', 'code' => 'TV'],
            ['name' => 'Uganda', 'code' => 'UG'],
            ['name' => 'Ukraine', 'code' => 'UA'],
            ['name' => 'United Arab Emirates', 'code' => 'AE'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United States Minor Outlying Islands', 'code' => 'UM'],
            ['name' => 'Uruguay', 'code' => 'UY'],
            ['name' => 'Uzbekistan', 'code' => 'UZ'],
            ['name' => 'Vanuatu', 'code' => 'VU'],
            ['name' => 'Venezuela, Bolivarian Republic of', 'code' => 'VE'],
            ['name' => 'Viet Nam', 'code' => 'VN'],
            ['name' => 'Virgin Islands, British', 'code' => 'VG'],
            ['name' => 'Virgin Islands, U.S.', 'code' => 'VI'],
            ['name' => 'Wallis and Futuna', 'code' => 'WF'],
            ['name' => 'Western Sahara', 'code' => 'EH'],
            ['name' => 'Yemen', 'code' => 'YE'],
            ['name' => 'Zambia', 'code' => 'ZM'],
            ['name' => 'Zimbabwe', 'code' => 'ZW'],
        ];
 
        DB::table('countries')->insert($countries);
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
            $table->string('type');
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
            $table->string('type');
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
            $table->string('first_name');
            $table->string('last_name');
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
            $table->string('text');
            $table->uuid('author_id');
            $table->uuid('cefr_id');
            $table->uuid('difficulty_id');
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
            $table->foreign('difficulty_id')
                  ->references('id')
                  ->on('difficulties')
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
        Schema::create('es_texts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('text');
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
            $table->string('text');
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

### Data restrictions

Add data restrictions:

#### countries

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

#### languages

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

#### cefrs

```php
Schema::create('cefrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level', 2)->unique;
            $table->timestamps();
        });
```

#### types

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

#### users

```php
public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name, 50');
            $table->string('last_name, 50');
            $table->string('profile_picture, 255');
            $table->string('username, 50');
            $table->string('email, 70')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password, 255');
            $table->boolean('is_admin, 1');
            $table->uuid('country_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
                  ->onDelete('cascade');
        });
    }
```

#### authors

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

#### texts

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

#### estexts

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

#### translations

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

### Regenerate secret after fresh

To avoid **attempt to read property "secret" on null** or **Personal access client not found. Please create one.** error when generating user token after seeding from fresh the database,

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

##

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
