<?php

namespace Credicom\Log\Logging;

use Monolog\Logger;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Str;
use Monolog\Formatter\JsonFormatter as MonologJsonFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class JsonFormatterMonolog
{

    public function __invoke(array $config)
    {
        $logger = new Logger($config['driver']);
        $handler = new StreamHandler($config['path'], $config['level']);
        $handler->setFormatter(new MonologJsonFormatter());
        $logger->pushHandler($handler);
        $this->setExtra($handler);
        return $logger;
    }

    public function setExtra($handler){
        $handler->pushProcessor(function ($record) {
            $route = Route::current();
            $routeName = ($route) ? $route->getName() : null;
            $request = new Request();
            $uriCompleta = $request->fullUrl();
            $requestId = (string) Str::uuid();
            $record['extra']['transaction-id'] = isset($request->transactionId) ? $request->transactionId : null;
            $record['extra']['request-id'] = $requestId;
            $record['extra']['ip'] = $request->ip();
            $record['extra']['app-name'] = config('app.name');
            $record['extra']['url'] = $uriCompleta;
            $record['extra']['route-name'] = $routeName;
            $user = Auth::user();
            if ($user){
                $record['extra']['user-id'] = $user->id;
                $record['extra']['user-email'] = $user->email;
                $record['extra']['user-name'] = $user->name;
            }
            
            return $record;
        });
    }

}