<?php

return [
    'name' => 'Iblog',

    'middleware' => [],

    'imagesize' => ['width' => 1024, 'height' => 768, 'quality'=>80],
    'mediumthumbsize' => ['width' => 400, 'height' => 300, 'quality'=>80],
    'smallthumbsize' => ['width' => 100, 'height' => 80, 'quality'=>80],

    /*
     |--------------------------------------------------------------------------
     | Dynamic fields
     |--------------------------------------------------------------------------
     | Add fields that will be dynamically added to the Post entity based on Bcrud
     | https://laravel-backpack.readme.io/docs/crud-fields
     */
    'fields' => [
//        'image' => [
        // image
//            'label' => trans('iblog::common.image'),
//            'name' => "imgsecund",
//            'type' => 'image',
//            'upload' => true,
//            'crop' => true, // set to true to allow cropping, false to disable
//            'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
//            'fake' => true,
//            'store_in' => 'options',
//            'viewposition' => 'left',
//        ]
    ],
    /*
   |--------------------------------------------------------------------------
   | Dynamic relations
   |--------------------------------------------------------------------------
   | Add relations that will be dynamically added to the Post entity
   */
    'relations' => [
//        'extension' => function ($self) {
//            return $self->belongsTo(PageExtension::class, 'id', 'page_id')->first();
//        }
    ],

    /*
   |--------------------------------------------------------------------------
   | Iblog Locale Configuration
   |--------------------------------------------------------------------------
   |
   | The localetime determines the default locale that will be used in date formatting inside this Module with (setlocale function):
    http://php.net/setlocale

   |
   */

    'localetime' => 'es_CO.UTF-8',

    /*
  |--------------------------------------------------------------------------
  | Iblog timezone Configuration
  |--------------------------------------------------------------------------
  |
  | The application locale determines the default locale that will be used
  | by the translation service provider. You are free to set this value
  | to any of the locales which will be supported by the application.
  |
  */

    'datetimezone' => 'America/Bogota',

    /*
  |--------------------------------------------------------------------------
  | Iblog og:locale Configuration
  |--------------------------------------------------------------------------
  |
  | The application locale determines the default locale that will be used
  | by the translation service provider. You are free to set this value
  | to any of the locales which will be supported by the application.
  |
  */

    'oglocale' => 'es_LA',

    /*
    |--------------------------------------------------------------------------
    | Iblog Watermark Configuration
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'watermark' => [
        'activated' => false,
        'url' => 'modules/iblog/img/watermark/watermark.png',
        'position' => 'top-left', #top, top-right, left, center, right, bottom-left, bottom, bottom-right
        'x' => 10,
        'y' => 10,
    ],

    'dateformat'=>'%A, %B %d, %Y',

    /*
       |--------------------------------------------------------------------------
       | Iblog feed Configuration
       |--------------------------------------------------------------------------
       |Activates the import, combination and display of RSS and Atom feeds
       |
       |
       */

    'feed' => [
        'activated' => true,
        'posts_per_feed'=>20,
        'logo'=>''
    ],

];
