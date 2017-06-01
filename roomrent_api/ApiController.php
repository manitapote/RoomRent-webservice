<?php

namespace Roomrent;

use App\Http\Controllers\Controller;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="/api",
 *     host="roomrent.dev",
 *     schemes={"http"},
 *     @SWG\Info(
 *         title="Roomrent API",
 *         version="1.0"
 *     ),
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */

/**
 *
 * @SWG\SecurityScheme(
 * 	securityDefinition="api_key",
 * 	type="apiKey",
 * 	in="header",
 * 	name="Authorization"
 * 	)
 */
class ApiController extends Controller
{
}