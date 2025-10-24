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
                'key' => 'plugin\\admin\\app\\controller\\RoleController',
                'href' => '/app/admin/role/index',
                'type' => 1,
                'weight' => 900,
            ],
            [
                'title' => '菜单管理',
                'key' => 'plugin\\admin\\app\\controller\\RuleController',
                'href' => '/app/admin/rule/index',
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
                'title' => '附件管理',
                'key' => 'plugin\\admin\\app\\controller\\UploadController',
                'href' => '/app/admin/upload/index',
                'type' => 1,
                'weight' => 700,
            ],
            [
                'title' => '字典设置',
                'key' => 'plugin\\admin\\app\\controller\\DictController',
                'href' => '/app/admin/dict/index',
                'type' => 1,
                'weight' => 600,
            ],
            [
                'title' => '系统设置',
                'key' => 'plugin\\admin\\app\\controller\\ConfigController',
                'href' => '/app/admin/config/index',
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
            ],
        ]
    ],
    [
        'title' => '示例页面',
        'key' => 'demos',
        'icon' => 'layui-icon-templeate-1',
        'weight' => 400,
        'type' => 0,
        'children' => [
            [
                'title' => '工作空间',
                'key' => 'demo1',
                'icon' => 'layui-icon-console',
                'weight' => 1000,
                'type' => 0,
                'children' => [
                    [
                        'title' => '分析页',
                        'key' => 'demo10',
                        'href' => '/app/admin/view/analysis/index.html',
                        'type' => 1,
                        'open_type' => '_component', // _component _layer _iframe
                        'weight' => 1000,
                    ],
                    [
                        'title' => '工作台',
                        'key' => 'demo11',
                        'href' => '/app/admin/view/console/index.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 900,
                    ]
                ]
            ],
            [
                'title' => '列表页面',
                'key' => 'demo9',
                'icon' => 'layui-icon-auz',
                'weight' => 900,
                'type' => 0,
                'children' => [
                    [
                        'title' => '查询表格',
                        'key' => 'demo91',
                        'href' => '/app/admin/view/listing/table.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 1000,
                    ]
                ]
            ],
            [
                'title' => '扩展组件',
                'key' => 'demo2',
                'icon' => 'layui-icon-auz',
                'weight' => 800,
                'type' => 0,
                'children' => [
                    [
                        'title' => '核心方法',
                        'key' => 'demo21',
                        'href' => '/app/admin/view/component/admin.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 1000,
                    ],
                    [
                        'title' => '高级栅格',
                        'key' => 'demo23',
                        'href' => '/app/admin/view/component/grid.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 900,
                    ],
                    [
                        'title' => '消息提示',
                        'key' => 'demo24',
                        'href' => '/app/admin/view/component/toast.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 800,
                    ],
                    [
                        'title' => '路由过渡',
                        'key' => 'demo25',
                        'href' => '/app/admin/view/component/nprogress.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 700,
                    ]
                ]
            ],
            [
                'title' => '结果页面',
                'key' => 'demo666',
                'icon' => 'layui-icon-auz',
                'weight' => 700,
                'type' => 0,
                'children' => [
                    [
                        'title' => '成功页面',
                        'key' => 'demo667',
                        'href' => '/app/admin/view/result/success.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 1000,
                    ],
                    [
                        'title' => '失败页面',
                        'key' => 'demo668',
                        'href' => '/app/admin/view/result/error.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 900,
                    ]
                ]
            ],
            [
                'title' => '异常页面',
                'key' => 'demo-error',
                'icon' => 'layui-icon-auz',
                'weight' => 600,
                'type' => 0,
                'children' => [
                    [
                        'title' => '403',
                        'key' => 'demo403',
                        'href' => '/app/admin/view/exception/403.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 1000,
                    ],
                    [
                        'title' => '404',
                        'key' => 'demo404',
                        'href' => '/app/admin/view/exception/404.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 900,
                    ],
                    [
                        'title' => '500',
                        'key' => 'demo500',
                        'href' => '/app/admin/view/exception/500.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 800,
                    ]
                ]
            ],
            [
                'title' => '菜单模式',
                'key' => 'demo-open',
                'icon' => 'layui-icon-auz',
                'weight' => 500,
                'type' => 0,
                'children' => [
                    [
                        'title' => '普通路由',
                        'key' => 'demo-a',
                        'href' => '/app/admin/view/result/success.html',
                        'type' => 1,
                        'open_type' => '_component',
                        'weight' => 1000,
                    ],
                    [
                        'title' => '嵌套网页',
                        'key' => 'demo-b',
                        'href' => 'http://www.layui-vue.com',
                        'type' => 1,
                        'open_type' => '_iframe',
                        'weight' => 900,
                    ],
                    [
                        'title' => '新建标签',
                        'key' => 'demo-c',
                        'href' => 'http://www.layui-vue.com',
                        'type' => 1,
                        'open_type' => '_blank',
                        'weight' => 800,
                    ],
                    [
                        'title' => '弹窗网页',
                        'key' => 'demo-d',
                        'href' => 'http://www.layui-vue.com',
                        'type' => 1,
                        'open_type' => '_layer',
                        'weight' => 700,
                    ]
                ]
            ],
            [
                'title' => '深度测试',
                'key' => 'demo-deep',
                'icon' => 'layui-icon-auz',
                'weight' => 400,
                'type' => 0,
                'children' => [
                    [
                        'title' => '二级菜单',
                        'key' => 'demo-deep1-1',
                        'href' => '/app/admin/view/result/success.html',
                        'type' => 0,
                        'weight' => 1000,
                        'children' => [
                            [
                                'title' => '三级菜单',
                                'key' => 'demo-deep1-1-1',
                                'href' => '/app/admin/view/result/success.html',
                                'type' => 0,
                                'weight' => 1000,
                                'children' => [
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-1-1-1',
                                        'href' => '/app/admin/view/result/success.html',
                                        'type' => 1,
                                        'open_type' => '_component',
                                        'weight' => 1000,
                                    ],
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-1-1-2',
                                        'href' => 'http://www.layui-vue.com',
                                        'type' => 1,
                                        'open_type' => '_blank',
                                        'weight' => 900,
                                    ]
                                ]
                            ],
                            [
                                'title' => '三级菜单',
                                'key' => 'demo-deep1-1-2',
                                'href' => 'http://www.layui-vue.com',
                                'type' => 0,
                                'weight' => 900,
                                'children' => [
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-1-2-1',
                                        'href' => '/app/admin/view/result/success.html',
                                        'type' => 1,
                                        'open_type' => '_component',
                                        'weight' => 1000,
                                    ],
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-1-2-2',
                                        'href' => 'http://www.layui-vue.com',
                                        'type' => 1,
                                        'open_type' => '_blank',
                                        'weight' => 900,
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => '二级菜单',
                        'key' => 'demo-deep1-2',
                        'href' => 'http://www.layui-vue.com',
                        'type' => 0,
                        'weight' => 900,
                        'children' => [
                            [
                                'title' => '三级菜单',
                                'key' => 'demo-deep1-2-1',
                                'href' => '/app/admin/view/result/success.html',
                                'type' => 0,
                                'weight' => 1000,
                                'children' => [
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-2-1-1',
                                        'href' => '/app/admin/view/result/success.html',
                                        'type' => 1,
                                        'open_type' => '_component',
                                        'weight' => 1000,
                                    ],
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-2-1-2',
                                        'href' => 'http://www.layui-vue.com',
                                        'type' => 1,
                                        'open_type' => '_blank',
                                        'weight' => 900,
                                    ]
                                ]
                            ],
                            [
                                'title' => '三级菜单',
                                'key' => 'demo-deep1-2-2',
                                'href' => 'http://www.layui-vue.com',
                                'type' => 0,
                                'weight' => 900,
                                'children' => [
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-2-2-1',
                                        'href' => '/app/admin/view/result/success.html',
                                        'type' => 1,
                                        'open_type' => '_component',
                                        'weight' => 1000,
                                    ],
                                    [
                                        'title' => '四级菜单',
                                        'key' => 'demo-deep1-2-2-2',
                                        'href' => 'http://www.layui-vue.com',
                                        'type' => 1,
                                        'open_type' => '_blank',
                                        'weight' => 900,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
