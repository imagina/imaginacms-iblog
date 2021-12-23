<?php

return [
  //Extra field to crud categories
  'categories' => [
    'iconimage' => [
      'value' => (object)[],
      'name' => 'mediasSingle',
      'type' => 'media',
      'props' => [
        'label' => 'Icono Menú',
        'zone' => 'iconimage',
        'entity' => "Modules\Iblog\Entities\Category",
        'entityId' => null
      ]
    ],
    'breadcrumbimage' => [
      'value' => (object)[],
      'name' => 'mediasSingle',
      'type' => 'media',
      'props' => [
        'label' => 'Imagen Breadcrumb',
        'zone' => 'breadcrumbimage',
        'entity' => "Modules\Iblog\Entities\Category",
        'entityId' => null
      ]
    ]
  ],
  //Extra field to crud post
  'posts' => [
    'urlCoder' => [
      'value' => "categoryAndPostSlug",
      'isFakeField' => true,
      'name' => 'urlCoder',
      'type' => 'select',
      'props' => [
        'label' => 'iblog::common.crudFieldsLabels.urlCoder',
        'multiple' => false,
        'hint' => 'iblog::common.crudFieldsHints.urlCoder',
        'options' => [
          ['label' => 'slug-categoría/slug-entrada', 'value' => "categoryAndPostSlug"],
          ['label' => 'slug-entrada', 'value' => "onlyPost"]
        ]
      ]
    ],
    'breadcrumbimage' => [
      'value' => (object)[],
      'name' => 'mediasSingle',
      'type' => 'media',
      'props' => [
        'label' => 'Imagen Breadcrumb',
        'zone' => 'breadcrumbimage',
        'entity' => "Modules\Iblog\Entities\Post",
        'entityId' => null
      ]
    ],
    
  ]
];
