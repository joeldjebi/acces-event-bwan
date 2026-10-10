<?php

use App\Support\SiteContent;

if (! function_exists('site_text')) {
    /**
     * Texte du site public modifiable dans l'administration (voir config/site_content.php).
     *
     * @param  array<string, string|int>  $replace
     */
    function site_text(string $key, array $replace = []): string
    {
        return SiteContent::text($key, $replace);
    }
}

if (! function_exists('site_image')) {
    /**
     * Image du site public modifiable dans l'administration (voir config/site_content.php).
     */
    function site_image(string $key): string
    {
        return SiteContent::image($key);
    }
}
