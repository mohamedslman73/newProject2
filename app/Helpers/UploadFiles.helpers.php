<?php

/**
 * @param $files
 * @param $titles
 * @param $path
 * @param $obj
 * @return array of uploadImage class
 */
function UploadRequestFiles($files, $titles, $path, \App\Models\MerchantProduct $obj){
    if(is_array($files)) {
        $images = [];
        foreach ($files as $key=>$val) {
            $images[] = UploadRequestFiles($val,$titles[$key],$path,$obj);
        }
        return $images;
    }

    return $obj->uploadmodel()->create([
        'path' => $files->store($path),
        'title' => $titles,
    ]);
}