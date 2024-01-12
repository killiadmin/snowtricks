<?php

/**
 * A service for generating slugs and extracting YouTube video IDs from URLs.
 */

namespace App\Service;

class UtilsService
{
    /**
     * Generates a slug from the given title.
     *
     * @param string $title The title to generate the slug from.
     * @return string The generated slug.
     */
    public function generateSlug(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);
        $title = strtolower($title);

        if (empty($title)) {
            return 'n-a';
        }

        return $title;
    }

    /**
     * Retrieves the video ID from a given YouTube URL.
     *
     * @param string $url The YouTube URL from which to extract the video ID.
     * @return string|null The video ID if found, null otherwise.
     */
    public function getIdsVideos(string $url): ?string
    {
        $parts = parse_url($url);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $qs);

            if (isset($qs['v'])) {
                return $qs['v'];
            }
        }

        return null;
    }
}
