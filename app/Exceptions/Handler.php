<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\MissingScopeException;
use phpseclib3\File\ASN1\Maps\AccessDescription;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(['error' => 'Resource not found'], 404);
        });
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json(['error' => 'Method not allowed!'], 405);
        });
        $this->renderable(function (MissingScopeException $e, $request) {
            return response()->json(['error' => 'Invalid role!'], 403);
        });
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json(['error' => 'Invalid role!'], 403);
        });
    }
}
