<?php

namespace Modules\Core\Repositories\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

use App\Contracts\Common\ExternalRepositoryContract;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ExternalRepository.
 */
abstract class ExternalRepository //implements ExternalRepositoryContract
{
    /**
     * The Base Uri
     *
     * @var \string
     */
    protected $baseUri;


    /**
     * Timeout
     *
     * @var \int
     */
    protected $timeout;


    /**
     * Generate the Guzzle
     */
    public function getClient()
    {
        return new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->baseUri,

            // Set Timeout
            'timeout'  => $this->timeout,

            // HTTP Headers
            'headers' => [
                'Content-Type' => 'application/json', 
                'Accept' => 'application/json', 
                //'Authorization' => "Basic " . $base64
            ],
    
            // HTTP Errors
            'http_errors' => false,

            'verify' => false
        ]);
    } //Function ends


    /**
     * Get Data from the external URL
     */
    public function get(string $url)
    {
        $objReturnValue = null;

        try {
            //Get client
            $client = $this->getClient();

            //Get the server response
            $response = $client->get($url);
            if ($response->getStatusCode() != 200) {
                throw new BadRequestHttpException();
            } //End if

            $objReturnValue = json_decode($response->getBody(), TRUE);

        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    public function post(string $url, $data)
    {
        $objReturnValue = null;

        try {
            //Get client
            $client = $this->getClient();

            //Get the server response
            $objReturnValue = $client->post($url, ['json'=>$data], ['debug'=>true]);
        } catch(Exception $e) {
            throw new Exception();
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    public function put(string $url)
    {
        $objReturnValue = null;

        try {
            //Get client
            $client = $this->getClient();

            //Get the server response
            $objReturnValue = $client->put($url, ['http_errors' => false]);
        } catch(Exception $e) {
            throw new Exception();
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends
    
} //Class ends