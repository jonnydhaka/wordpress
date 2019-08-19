<?php
namespace smartimageoverlay\process;

require 'siooptions.php';
class curlprocess
{
    use siooptionlist {
        siooptionlist::__construct as private __tConstruct;
    }
    public function __construct()
    {
        $this->__tConstruct();
    }

    public function smartimageoverlay_file_get_contents_curl($sio_url)
    {
        $sio_ch = curl_init();
        curl_setopt($sio_ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($sio_ch, CURLOPT_HEADER, 1);
        curl_setopt($sio_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sio_ch, CURLOPT_URL, $sio_url);
        curl_setopt($sio_ch, CURLOPT_FOLLOWLOCATION, true);
        $sio_response = curl_exec($sio_ch);
        $sio_header_size = curl_getinfo($sio_ch, CURLINFO_HEADER_SIZE);
        curl_close($sio_ch);
        $sio_header = substr($sio_response, 0, $sio_header_size);
        $sio_header = explode(':', $sio_header, 2);
        $sio_headerarray = explode("\n", $sio_header[1]);
        $sio_response_array = array();
        $sio_response_array['header'] = $this->smartimageoverlay_build_header_array($sio_headerarray);
        $sio_response_array['body'] = json_decode(substr($sio_response, $sio_header_size));
        return $sio_response_array;
    }

    private function smartimageoverlay_build_header_array($sio_array)
    {
        $sio_return_array = array();
        array_shift($sio_array);
        foreach ($sio_array as $sio_key) {
            $sio_explode_val = explode(':', $sio_key);
            if (trim($sio_explode_val[0]) != '') {
                $sio_return_array[$sio_explode_val[0]] = $sio_explode_val[1];
            }
        }
        return $sio_return_array;
    }

}
