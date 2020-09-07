<?php

namespace App\Exceptions;

use App\Exceptions\ResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($request->input("devtools") !== null || env("DEBUG") === true) dd($exception);

        $error = [];
        $statusCode = method_exists($exception, "getStatusCode") ? $exception->getStatusCode() : (method_exists($exception, "getCode") && $exception->getCode() > 99 ? $exception->getCode() : 500);
        $description = method_exists($exception, "getDescription") ? $exception->getDescription() : null;
        $action = $request->getPathInfo();


        switch($statusCode){
            case 405:
                $message = "Method Not Allowed (".$request->getMethod()."). Only allow: ".$exception->getHeaders()["Allow"];
            break;
            case 404:
                $message = "The action you are looking for (".$action.") is not found.";
            break;
            default:
                $message = $exception->getMessage();
        }

        if(env("APP_ENV") == "production"){
            $message = "Internal Server Error";
        }

        $error["message"] = $message;
        if($description) $error["description"] = $description;
        if(env("APP_ENV") != "production")
            $error["file"] = $exception->getFile().":".$exception->getLine();
        $error["code"] = $statusCode;

        $response = new ResponseException("error", $error);
        return response()->json($response, $statusCode < 100 || $statusCode > 600 ? 500 : $statusCode);
    }
}
