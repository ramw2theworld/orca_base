<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Modules\Core\Traits\RespondsWithJson;


/**
 * @OA\Info(
 *    title="Swagger with Foundation",
 *    version="1.0.0",
 * )

 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, RespondsWithJson;
}
