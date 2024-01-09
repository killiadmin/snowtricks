<?php

namespace App\Service;

class SlugService
{
    /**
     * Generates a slug from the given title.
     *
     * @param string $title The title to generate the slug from.
     * @return string The generated slug.
     */
    public function generateSlug($title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);
        $title = strtolower($title);

        $id = uniqid('', true);

        if (empty($title)) {
            return 'n-a';
        }

        return $title . '-' . $id;
    }
}