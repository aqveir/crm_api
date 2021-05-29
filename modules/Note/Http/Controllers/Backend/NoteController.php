<?php

namespace Modules\Note\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Note\Http\Requests\Backend\CreateNoteRequest;
use Modules\Note\Http\Requests\Backend\UpdateNoteRequest;
use Modules\Note\Http\Requests\Backend\DeleteNoteRequest;

use Modules\Note\Models\Note;
use Modules\Note\Services\NoteService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class NoteController extends ApiBaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Note::class, 'note');
    }


    /**
     * Create Note
     *
     * @param \Modules\Note\Http\Requests\Backend\CreateNoteRequest $request
     * @param \Modules\Note\Services\NoteService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/note",
     *     tags={"Note"},
     *     operationId="api.backend.note.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateNoteRequest $request, NoteService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $service->create($payload);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Note
     *
     * @param \Modules\Note\Http\Requests\Backend\UpdateNoteRequest $request
     * @param \Modules\Note\Services\NoteService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/note/{id}",
     *     tags={"Note"},
     *     operationId="api.backend.note.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateNoteRequest $request, NoteService $service, string $subdomain, Note $note)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update note
            $data = $service->update($payload, $note['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Note
     *
     * @param \Modules\Note\Http\Requests\Backend\DeleteNoteRequest $request
     * @param \Modules\Note\Services\NoteService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/note/{id}",
     *     tags={"Note"},
     *     operationId="api.backend.note.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteNoteRequest $request, NoteService $service, string $subdomain, Note $note)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete note
            $data = $service->delete($payload, $note['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
