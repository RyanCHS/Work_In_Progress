<?php

namespace App\Http\Controllers;

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use a bearer token to access this API",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */



abstract class Controller
{
    //
}
