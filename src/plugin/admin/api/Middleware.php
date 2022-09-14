<?php
namespace plugin\admin\api;

use plugin\admin\api\Auth;
use plugin\admin\app\model\Option;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 对外提供的中间件
 */
class Middleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';
        if (!Auth::canAccess($controller, $action, $code, $msg)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'message' => $msg, 'type' => 'error']);
            } else {
                $response = \response($msg, $code);
            }
        } else {
            $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        }
        return $response;
    }

}