<?php

use plugin\queue\app\controller\redis\DelayController;
use plugin\queue\app\controller\redis\FailedController;
use plugin\queue\app\controller\redis\NormalController;

return [
    [
        'title' => 'database',
        'name' => 'database',
        'path' => '/database',
        'icon' => 'ant-design:database-filled',
        'children' => [
            [
                'title' => 'all tables',
                'name' => 'plugin\\admin\\app\\controller\\database\\TableController',
                'path' => 'table',
                'component' => '/database/table/index'
            ],
            [
                'title' => 'Table Details',
                'name' => 'tableview',
                'path' => 'table/view/:id',
                'component' => '/database/table/View',
                'hide_menu' => 1,
            ],
        ]
    ],
    [
        'title' => 'authority management',
        'name' => 'auth',
        'path' => '/auth',
        'icon' => 'ant-design:setting-filled',
        'children' => [
            [
                'title' => 'Account Management',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminController',
                'path' => 'admin',
                'component' => '/auth/admin/index'
            ],
            [
                'title' => 'role management',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminRoleController',
                'path' => 'admin-role',
                'component' => '/auth/admin-role/index',
            ],
            [
                'title' => 'Menu management',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminRuleController',
                'path' => 'admin-rule',
                'component' => '/auth/admin-rule/index',
            ],
        ]
    ],
    [
        'title' => 'Membership Management',
        'name' => 'user',
        'path' => '/user',
        'icon' => 'ant-design:smile-filled',
        'children' => [
            [
                'title' => 'user',
                'name' => 'plugin\\admin\\app\\controller\\user\\UserController',
                'path' => 'user',
                'component' => '/user/user/index'
            ]
        ]
    ],
    [
        'title' => 'General Settings',
        'name' => 'common',
        'path' => '/common',
        'icon' => 'ant-design:setting-filled',
        'children' => [
            [
                'title' => 'personal information',
                'name' => 'plugin\\admin\\app\\controller\\user\\AccountController',
                'path' => 'account',
                'component' => '/common/account/index'
            ]
        ]
    ],
    [
        'title' => 'Plugin management',
        'name' => 'plugin',
        'path' => '/plugin',
        'icon' => 'ant-design:appstore-filled',
        'children' => [
            [
                'title' => 'Apply Plugins',
                'name' => 'plugin\\admin\\app\\controller\\plugin\\AppController',
                'path' => 'app',
                'component' => '/plugin/App'
            ]
        ]
    ],
];
