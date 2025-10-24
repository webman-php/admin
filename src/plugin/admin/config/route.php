<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use plugin\admin\app\controller\AccountController;
use plugin\admin\app\controller\DictController;
use Webman\Route;
use support\Request;

Route::any('/app/admin/account/captcha/{type}', [AccountController::class, 'captcha']);

Route::any('/app/admin/dict/get/{name}', [DictController::class, 'get']);

/**
 * @deprecated 用于兼容旧版本
 */
Route::any('/app/admin/admin/js/{file}', function (Request $request, $file) {
    return response()->withFile(base_path() . '/plugin/admin/public/js/' . $file);
});
Route::any('/app/admin/admin/css/pages/{file}', function (Request $request, $file) {
    return response()->withFile(base_path() . '/plugin/admin/public/css/' . $file);
});

Route::fallback(function (Request $request) {
    return response($request->uri() . ' not found' , 404);
}, 'admin');

