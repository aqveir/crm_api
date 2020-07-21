<?php

if (! function_exists('version')) {
    /**
     * Set the version to generate API URLs to.
     *
     * @param string $version
     *
     * @return \Modules\Boilerplate\Routing\UrlGenerator
     */
    function version($version)
    {
        return app('api.url')->version($version);
    }
}
