<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *     @OA\Server(
 *          url=SWAGGER_CONST_HOST
 *     ),
 *     @OA\Info(
 *          version="3.0.0",
 *          title="CRM API Documentation"
 *     )
 * )
 */

 /**
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
 */
class CoreController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('core::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('core::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('core::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('core::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
