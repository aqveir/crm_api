<?php

namespace Modules\Boilerplate\Event;

use Modules\Boilerplate\Http\Response;

class ResponseIsMorphing
{
    /**
     * Response instance.
     *
     * @var \Modules\Boilerplate\Http\Response
     */
    public $response;

    /**
     * Response content.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new response is morphing event. Content is passed by reference
     * so that multiple listeners can modify content.
     *
     * @param \Modules\Boilerplate\Http\Response $response
     * @param string                   $content
     *
     * @return void
     */
    public function __construct(Response $response, &$content)
    {
        $this->response = $response;
        $this->content = &$content;
    }
}
