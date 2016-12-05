<?php

return [
    'iblog.categories' => [
        'index' => 'iblog::categories.list',
        'create' => 'iblog::categories.create',
        'edit' => 'iblog::categories.edit',
        'destroy' => 'iblog::categories.destroy',
    ],
    'iblog.posts' => [
        'index' => 'iblog::post.list',
        'create' => 'iblog::post.create',
        'edit' => 'iblog::post.edit',
        'destroy' => 'iblog::post.destroy',
    ],
    'iblog.tags' => [
        'index' => 'iblog::tag.list',
        'create' => 'iblog::tag.create',
        'edit' => 'iblog::tag.edit',
        'destroy' => 'iblog::tag.destroy',
    ],

];
