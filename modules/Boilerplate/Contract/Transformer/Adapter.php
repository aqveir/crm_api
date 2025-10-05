<?php

namespace Modules\Boilerplate\Contract\Transformer;

use Modules\Boilerplate\Http\Request;
use Modules\Boilerplate\Transformer\Binding;

interface Adapter
{
    /**
     * Transform a response with a transformer.
     *
     * @param mixed                          $response
     * @param object                         $transformer
     * @param \Modules\Boilerplate\Transformer\Binding $binding
     * @param \Modules\Boilerplate\Http\Request        $request
     *
     * @return array
     */
    public function transform($response, $transformer, Binding $binding, Request $request);
}
