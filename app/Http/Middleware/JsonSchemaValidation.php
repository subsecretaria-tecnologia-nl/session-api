<?php
namespace App\Http\Middleware;

use App\Schemas\SchemaValidator;
use Closure;

class JsonSchemaValidation
{
    public function handle($request, Closure $next)
    {
		$body = to_object($request->all());
		$path = str_replace("/", ".", substr($request->getPathInfo(), 1));
		$method = strtolower($request->getMethod());

        $schemaPathRequest = preg_replace("/\.\./", ".", app("path")."/Schemas/request/{$path}.{$method}.schema.json");
        $schemaResponse = (file_exists($schemaPathRequest)) ? file_get_contents($schemaPathRequest) : null;

        if($schemaResponse)
			SchemaValidator::validateJson($body, $schemaResponse);

		return $next($request);
    }
}