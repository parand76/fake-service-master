<?php

return [
    'errorReporting' => env('LOG_ERROR_REPORT', false),
    'excludes' => [
        "Illuminate\Validation\ValidationException",
        "Symfony\Component\HttpKernel\Exception\NotFoundHttpException",
        "Illuminate\Auth\AuthenticationException",
    ],
    'activeTypes' => env("LOG_ACTIVE_TYPES", "info,dd,dump,warning,error,debug,WSResError"),
];