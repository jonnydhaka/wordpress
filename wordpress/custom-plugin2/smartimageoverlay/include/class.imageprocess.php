<?php
namespace smartimageoverlay\process;

require 'class.curl.php';

use smartimageoverlay\process\curlprocess as curl;

class imageprocess
{
    private $sio_count = 0;

    private $sio_media_array = array();

    public function __construct()
    {
    }

    public function smartimageoverlay_get_media()
    {
        $curl = new curl();
        $sio_url = get_home_url() . '/wp-json/wp/v2/media?per_page=100&media_type=image';
        $sio_get_content = $curl->smartimageoverlay_file_get_contents_curl($sio_url);
        $this->smartimageoverlay_build_medias_array($sio_get_content['body']);
        if (isset($sio_get_content['header']['X-WP-TotalPages']) && $sio_get_content['header']['X-WP-TotalPages'] > 1) {
            for ($i = 2; $i <= $sio_get_content['header']['X-WP-TotalPages']; $i++) {
                $sio_url = get_home_url() . '/wp-json/wp/v2/media?per_page=100&page=' . $i;
                $sio_get_content = $curl->smartimageoverlay_file_get_contents_curl($sio_url);
                $this->smartimageoverlay_build_medias_array($sio_get_content['body']);
            }
        }
        return $this->sio_media_array;
    }

    private function smartimageoverlay_build_medias_array($sio_arrays)
    {
        if (empty($sio_arrays)) {
            return false;
        }
        foreach ($sio_arrays as $sio_array) {
            if ($sio_array->media_type == 'image') {
                if (isset($sio_array->media_details->sizes) && !empty((array) $sio_array->media_details->sizes)) {
                    /*foreach ($sio_array->media_details->sizes as $key => $sio_imagesize) {
                    $this->sio_media_array[$this->sio_count][$key]['url'] = $sio_imagesize->source_url;
                    $this->sio_media_array[$this->sio_count][$key]['width'] = $sio_imagesize->width;
                    $this->sio_media_array[$this->sio_count][$key]['height'] = $sio_imagesize->height;
                    }*/
                    $this->sio_media_array[$this->sio_count]['url'] = $sio_array->media_details->sizes->full->source_url;
                    $this->sio_media_array[$this->sio_count]['width'] = $sio_array->media_details->sizes->full->width;
                    $this->sio_media_array[$this->sio_count]['height'] = $sio_array->media_details->sizes->full->height;
                } else {
                    $this->sio_media_array[$this->sio_count]['url'] = $sio_array->source_url;
                    $this->sio_media_array[$this->sio_count]['width'] = $sio_array->media_details->width;
                    $this->sio_media_array[$this->sio_count]['height'] = $sio_array->media_details->height;
                }
                $this->sio_count++;
            }
        }
    }

}
