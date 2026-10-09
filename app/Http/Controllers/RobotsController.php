<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController
{
    public function __invoke(): Response
    {
        return response(
            "User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }
}
