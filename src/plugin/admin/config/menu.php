<?php

use plugin\queue\app\controller\redis\DelayController;
use plugin\queue\app\controller\redis\FailedController;
use plugin\queue\app\controller\redis\NormalController;

return [
    [
        'title' => '数据库',
        'name' => 'database',
        'path' => '/database',
        'icon' => 'ant-design:database-filled',
        'children' => [
            [
                'title' => '所有表',
                'name' => 'plugin\\admin\\app\\controller\\database\\TableController',
                'path' => 'table',
                'component' => '/database/table/index'
            ],
            [
                'title' => '表详情',
                'name' => 'tableview',
                'path' => 'table/view/:id',
                'component' => '/database/table/View',
                'hide_menu' => 1,
            ],
        ]
    ],
    [
        'title' => '权限管理',
        'name' => 'auth',
        'path' => '/auth',
        'icon' => 'ant-design:setting-filled',
        'children' => [
            [
                'title' => '账户管理',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminController',
                'path' => 'admin',
                'component' => '/auth/admin/index'
            ],
            [
                'title' => '角色管理',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminRoleController',
                'path' => 'admin-role',
                'component' => '/auth/admin-role/index',
            ],
            [
                'title' => '菜单管理',
                'name' => 'plugin\\admin\\app\\controller\\auth\\AdminRuleController',
                'path' => 'admin-rule',
                'component' => '/auth/admin-rule/index',
            ],
        ]
    ],
    [
        'title' => '会员管理',
        'name' => 'user',
        'path' => '/user',
        'icon' => 'ant-design:smile-filled',
        'children' => [
            [
                'title' => '用户',
                'name' => 'plugin\\admin\\app\\controller\\user\\UserController',
                'path' => 'user',
                'component' => '/user/user/index'
            ]
        ]
    ],
    [
        'title' => '通用设置',
        'name' => 'common',
        'path' => '/common',
        'icon' => 'ant-design:setting-filled',
        'children' => [
            [
                'title' => '个人资料',
                'name' => 'plugin\\admin\\app\\controller\\user\\AccountController',
                'path' => 'account',
                'component' => '/common/account/index'
            ]
        ]
    ],
    [
        'title' => '插件管理',
        'name' => 'plugin',
        'path' => '/plugin',
        'icon' => 'ant-design:appstore-filled',
        'children' => [
            [
                'title' => '应用插件',
                'name' => 'plugin\\admin\\app\\controller\\plugin\\AppController',
                'path' => 'app',
                'component' => '/plugin/App'
            ]
        ]
    ],
];
