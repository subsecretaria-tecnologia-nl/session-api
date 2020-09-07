<?php
namespace App\Http\Middleware;
use App\Exceptions\ResponseException;
use Closure;

class ResponseMiddleware
{
    public function handle($request, Closure $next)
    {
		$res = $next($request);

        if(isset($res->original->data) && $res->original->data == "error") $response = $res->original;
        else $response = new ResponseException("response", $res->original);
		return response()->json($response, $res->getStatusCode());
    }
}