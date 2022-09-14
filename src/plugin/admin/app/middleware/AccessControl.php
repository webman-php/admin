<?php
namespace plugin\admin\app\middleware;

use plugin\admin\api\Auth;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AccessControl implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';
        if (!Auth::canAccess($controller, $action, $code, $msg)) {
            $response = json(['code' => $code, 'message' => $msg, 'type' => 'error']);
        } else {
            $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        }

        return $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Headers' => '*',
        ]);

    }

}