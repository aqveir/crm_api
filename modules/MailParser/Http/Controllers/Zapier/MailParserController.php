<?php

namespace Modules\MailParser\Http\Controllers\Zapier;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\MailParser\Http\Controllers\BaseMailParserController;
use Modules\MailParser\Services\MailParserService;
use Modules\MailParser\Http\Requests\Zapier\MailParserRequest;
use Modules\MailParser\Transformers\MailParserResource as ZapierMailParserResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MailParserController extends BaseMailParserController
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
     * Mail Parser - Zapier
     *
     * @param \Modules\MailParser\Http\Requests\Zapier\VoiceCallbackRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/mailparser/zapier",
     *     tags={"MailParser"},
     *     operationId="api.mailparser.zapier.create",
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(MailParserRequest $request, MailParserService $service, string $subdomain)
    {
        try {
            //Provider switchcase to transform request
            $payload = null;
            $payload = new ZapierMailParserResource($request, 'zapier');

            //Create payload
            $payload = collect($payload);

            //Process mail parsed data
            return parent::processMailData($request, $service, $subdomain, 'zapier', $payload);
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
