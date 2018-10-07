<?php
/**
 * Created by PhpStorm.
 * User: Demon Warlock
 * Date: 5/29/2018
 * Time: 10:06 PM
 */


if (!function_exists('human_file_size')) {
    /**
     * @param $bytes
     * @param int $precision
     * @return string
     *
     */
    function human_file_size($bytes, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }
}

if (!function_exists('is_image')) {
    /**
     * Is the mime type an image
     *
     * @param $mimeType
     * @return bool
     *
     */
    function is_image($mimeType)
    {
        return starts_with($mimeType, 'image/');
    }
}

if (!function_exists('is_video')) {
    /**
     * Is the mime type an image
     *
     * @param $mimeType
     * @return bool
     *
     */
    function is_video($mimeType)
    {
        return starts_with($mimeType, 'video/');
    }
}

if (!function_exists('get_file_by_size')) {
    /**
     * @param $url
     * @param $size
     * @return mixed
     *
     */
    function get_file_by_size($url, $size)
    {
        return str_replace(File::name($url), File::name($url) . '-' . $size, $url);
    }
}

if (!function_exists('get_object_image')) {
    /**
     * @param $image
     * @param null $size
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function get_object_image($image, $size = null)
    {
        if (!empty($image)) {
            if (empty($size) || $image == '__value__') {
                return url($image);
            }
            return url(get_file_by_size($image, $size));
        } else {
            return url(get_file_by_size(config('media.default-img'), config('media.thumb-size')));
        }
    }
}