<?php

return [
    [
        'title' => '数据库',
        'key' => 'database',
        'icon' => 'layui-icon-template-1',
        'weight' => 1000,
        'type' => 0,
        'children' => [
            [
                'title' => '所有表',
                'key' => 'plugin\\admin\\app\\controller\\TableController',
                'href' => '/app/admin/table/index',
                'type' => 1,
                'weight' => 800,
            ]
        ]
    ],
    [
        'title' => '权限管理',
        'key' => 'auth',
        'icon' => 'layui-icon-vercode',
        'weight' => 900,
        'type' => 0,
        'children' => [
            [
                'title' => '账户管理',
                'key' => 'plugin\\admin\\app\\controller\\AdminController',
                'href' => '/app/admin/admin/index',
                'type' => 1,
                'weight' => 1000,
            ],
            [
                'title' => '角色管理',
                'key' => 'plugin\\admin\\app\\controller\\AdminRoleController',
                'href' => '/app/admin/admin-role/index',
                'type' => 1,
                'weight' => 900,
            ],
            [
                'title' => '菜单管理',
                'key' => 'plugin\\admin\\app\\controller\\AdminRuleController',
                'href' => '/app/admin/admin-rule/index',
                'type' => 1,
                'weight' => 800,
            ],
        ]
    ],
    [
        'title' => '会员管理',
        'key' => 'user',
        'icon' => 'layui-icon-username',
        'weight' => 800,
        'type' => 0,
        'children' => [
            [
                'title' => '用户',
                'key' => 'plugin\\admin\\app\\controller\\UserController',
                'href' => '/app/admin/user/index',
                'type' => 1,
                'weight' => 800,
            ]
        ]
    ],
    [
        'title' => '通用设置',
        'key' => 'common',
        'icon' => 'layui-icon-set',
        'weight' => 700,
        'type' => 0,
        'children' => [
            [
                'title' => '个人资料',
                'key' => 'plugin\\admin\\app\\controller\\AccountController',
                'href' => '/app/admin/account/index',
                'type' => 1,
                'weight' => 800,
            ],
            [
                'title' => '系统设置',
                'key' => 'plugin\\admin\\app\\controller\\ConfigController',
                'href' => '/app/admin/config/index',
                'type' => 1,
                'weight' => 700,
            ],
            [
                'title' => '附件管理',
                'key' => 'plugin\\admin\\app\\controller\\UploadController',
                'href' => '/app/admin/upload/index',
                'type' => 1,
                'weight' => 600,
            ],
            [
                'title' => '字典设置',
                'key' => 'plugin\\admin\\app\\controller\\DictController',
                'href' => '/app/admin/dict/index',
                'type' => 1,
                'weight' => 500,
            ],
        ]
    ],
    [
        'title' => '插件管理',
        'key' => 'plugin',
        'icon' => 'layui-icon-app',
        'weight' => 600,
        'type' => 0,
        'children' => [
            [
                'title' => '应用插件',
                'key' => 'plugin\\admin\\app\\controller\\PluginController',
                'href' => '/app/admin/plugin/index',
                'weight' => 800,
                'type' => 1,
            ]
        ]
    ],
    [
        'title' => '开发辅助',
        'key' => 'dev',
        'icon' => 'layui-icon-fonts-code',
        'weight' => 500,
        'type' => 0,
        'children' => [
            [
                'title' => '表单构建',
                'key' => 'plugin\\admin\\app\\controller\\DevController',
                'href' => '/app/admin/dev/form-build',
                'weight' => 800,
                'type' => 1,
            ]
        ]
    ],
];
