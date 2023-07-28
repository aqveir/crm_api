<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="aQveir API Documentation",
 *     version="0.1"
 * )
 * 
 * @OA\Parameter(
 *      parameter="organization_key",
 *      in="query",
 *      name="key",
 *      required=true,
 *      description="Provide the organization key, defaults to *0*",
 *      @OA\Schema(type="string", default=0)
 * )
 * 
 * @OA\Parameter(
 *      parameter="organization_secret",
 *      in="query",
 *      name="secret",
 *      required=true,
 *      description="Provide the organization secret",
 *      @OA\Schema(type="string")
 * )
 *
 * @OA\Parameter(
 *      parameter="source_key",
 *      in="query",
 *      name="source",
 *      required=false,
 *      description="Provide the request source key, defaults to *web*",
 *      @OA\Schema(type="string", default="web")
 * )
 *
 * @OA\Parameter(
 *      parameter="language_code",
 *      in="query",
 *      name="lang",
 *      required=false,
 *      description="Provide the language code, defaults to *en_US*",
 *      @OA\Schema(type="string", default="en_US")
 * )
 * 
 * @OA\Parameter(
 *      parameter="hash_identifier",
 *      in="path",
 *      name="hash",
 *      required=true,
 *      description="Provide the hash identifier",
 *      @OA\Schema(type="string")
 * )
 * 
 * @OA\Parameter(
 *      parameter="identifier",
 *      in="path",
 *      name="id",
 *      required=true,
 *      description="Provide the identifier",
 *      @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *      parameter="slug_identifier",
 *      in="path",
 *      name="slug",
 *      required=true,
 *      description="Provide the identifier",
 *      @OA\Schema(type="string")
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Security Scheme for API Access Token",
 *     scheme="bearer",
 *     securityScheme="omni_token",
 * )
 */
class CoreController extends BaseController
{

}
