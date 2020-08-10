<?php

namespace Modules\Document\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Document\Http\Requests\Backend\CreateDocumentRequest;
use Modules\Document\Http\Requests\Backend\UpdateDocumentRequest;
use Modules\Document\Http\Requests\Backend\DeleteDocumentRequest;

use Modules\Document\Services\DocumentService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class DocumentController extends ApiBaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('note::index');
    } //Function ends


    /**
     * Create Document
     *
     * @param \Modules\Document\Http\Requests\Backend\CreateDocumentRequest $request
     * @param \Modules\Document\Services\DocumentService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/document",
     *     tags={"Document"},
     *     operationId="api.backend.document.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateDocumentRequest $request, DocumentService $service)
    {
        try {
            //Create document
            $data = $service->create($request);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('note::show');
    } //Function ends


    /**
     * Update Document
     *
     * @param \Modules\Document\Http\Requests\Backend\UpdateDocumentRequest $request
     * @param \Modules\Document\Services\DocumentService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/document/{id}",
     *     tags={"Document"},
     *     operationId="api.backend.document.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateDocumentRequest $request, DocumentService $service, $id)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Update document
            $data = $service->update($payload, $id);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Document
     *
     * @param \Modules\Document\Http\Requests\Backend\DeleteDocumentRequest $request
     * @param \Modules\Document\Services\DocumentService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/document/{id}",
     *     tags={"Document"},
     *     operationId="api.backend.document.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteDocumentRequest $request, DocumentService $service, $id)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Delete document
            $data = $service->delete($payload, $id);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
