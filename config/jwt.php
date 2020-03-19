<?php


return [
    'secret' => env('JWT_SECRET'),
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'),
        'private' => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],
    'ttl' => null,
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    'algo' => env('JWT_ALGO', 'HS256'),
    'required_claims' => [
        'iss',
        'iat',
        'nbf',
        'sub',
        'jti',
    ],

    'persistent_claims' => [
        // 'foo',
        // 'bar',
    ],

    'lock_subject' => true,
    'leeway' => env('JWT_LEEWAY', 0),
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),
    'decrypt_cookies' => false,

    'providers' => [
        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,

    ],

];




//return [
//
//    /*
//    |--------------------------------------------------------------------------
//    | JWT Authentication Secret
//    |--------------------------------------------------------------------------
//    |
//    | Don't forget to set this, as it will be used to sign your tokens.
//    | A helper command is provided for this: `php artisan jwt:generate`
//    |
//    */
//
//    'secret' => env('JWT_SECRET', 'tUGxmRZpYGfm8EDIU5ko5UJLv2p3aQT4'),
//
//    /*
//    |--------------------------------------------------------------------------
//    | JWT time to live
//    |--------------------------------------------------------------------------
//    |
//    | Specify the length of time (in minutes) that the token will be valid for.
//    | Defaults to 1 hour
//    |
//    */
//
//    'ttl' => 60,
//
//    /*
//    |--------------------------------------------------------------------------
//    | Refresh time to live
//    |--------------------------------------------------------------------------
//    |
//    | Specify the length of time (in minutes) that the token can be refreshed
//    | within. I.E. The user can refresh their token within a 2 week window of
//    | the original token being created until they must re-authenticate.
//    | Defaults to 2 weeks
//    |
//    */
//
//    'refresh_ttl' => 20160,
//
//    /*
//    |--------------------------------------------------------------------------
//    | JWT hashing algorithm
//    |--------------------------------------------------------------------------
//    |
//    | Specify the hashing algorithm that will be used to sign the token.
//    |
//    | See here: https://github.com/namshi/jose/tree/2.2.0/src/Namshi/JOSE/Signer
//    | for possible values
//    |
//    */
//
//    'algo' => 'HS256',
//
//    /*
//    |--------------------------------------------------------------------------
//    | User Model namespace
//    |--------------------------------------------------------------------------
//    |
//    | Specify the full namespace to your User model.
//    | e.g. 'Acme\Entities\User'
//    |
//    */
//
//    'user' => 'App\User',
//
//    /*
//    |--------------------------------------------------------------------------
//    | User identifier
//    |--------------------------------------------------------------------------
//    |
//    | Specify a unique property of the user that will be added as the 'sub'
//    | claim of the token payload.
//    |
//    */
//
//    'identifier' => 'id',
//
//    /*
//    |--------------------------------------------------------------------------
//    | Required Claims
//    |--------------------------------------------------------------------------
//    |
//    | Specify the required claims that must exist in any token.
//    | A TokenInvalidException will be thrown if any of these claims are not
//    | present in the payload.
//    |
//    */
//
//    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],
//
//    /*
//    |--------------------------------------------------------------------------
//    | Blacklist Enabled
//    |--------------------------------------------------------------------------
//    |
//    | In order to invalidate tokens, you must have the the blacklist enabled.
//    | If you do not want or need this functionality, then set this to false.
//    |
//    */
//
//    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
//
//    /*
//    |--------------------------------------------------------------------------
//    | Providers
//    |--------------------------------------------------------------------------
//    |
//    | Specify the various providers used throughout the package.
//    |
//    */
//
//
//
//    'providers' => [
//        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
//        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
//        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
//
//    ],
//
//];
