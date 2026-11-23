<?php

return [
    // Optional
    'controllerNamespace' => 'App\Http\Controllers\Admin', // App\Http\Controllers\Admin
    'controllerPath' => 'Http/Controllers/Admin', // Http/Controllers/Admin

    // Required
    'modelName' => 'Post', // Product
    'modelNamePlural' => 'Posts', // Products
    'modelInstance' => 'post', // product
    'modelInstancePlural' => 'posts', // products
    'resourceName' => 'Posts', // Products
    'resourceNameSingular' => 'Post', // Product
    'viewPath' => 'admin/posts', // admin/products
    'route' => 'admin.posts', // admin.products

    // Data to return from JSON
    'data' => [
        [
            'JSONName' => 'title',
            'databaseColumn' => 'title',
            'tableName' => 'Post title',
        ],
        [
            'JSONName' => 'status',
            'databaseColumn' => 'status',
            'tableName' => 'Status',
        ],
        [
            'JSONName' => 'slug',
            'databaseColumn' => 'slug',
            'tableName' => 'Slug',
        ],
    ],

    // Fields
    'fields' => [
        [
            'name' => 'post_title',
            'label' => 'Post title',
            'type' => 'text',
            'create_validation' => ['required', 'string'],
            'edit_validation' => ['required', 'string'],
        ],
        [
            'name' => 'post_content',
            'label' => 'Post content',
            'type' => 'textarea',
            'create_validation' => ['max:60000'],
            'edit_validation' => ['max:60000'],
        ],
        [
            'name' => 'post_excerpt',
            'label' => 'Meta Description',
            'type' => 'textarea',
            'create_validation' => ['max:500'],
            'edit_validation' => ['max:500'],
        ],
        [
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'create_validation' => ['required', 'string'],
            'edit_validation' => ['required', 'string'],
        ],
    ],
];