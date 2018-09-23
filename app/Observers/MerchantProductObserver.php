<?php
namespace App\Observers;
use App\Models\MerchantProduct;
use App\Models\Upload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MerchantProductObserver {


    public function updated(MerchantProduct $merchantproduct){
        $this->SyncUpload($merchantproduct);
    }

    public function updating(MerchantProduct $merchantproduct){
        $this->SyncUpload($merchantproduct);
    }


    public function saved(MerchantProduct $merchantproduct){
        $this->SyncUpload($merchantproduct);
    }


    function SyncUpload($merchantproduct){
        /*
        foreach($merchantproduct->uploadmodel()->get() as $oneupload){
            if(!in_array($oneupload->path,request('oldimage'))){
                if($oneupload->delete())
                    Storage::delete($oneupload->path);
            }
        }

        $uploads = new Collection();
        $newimgs = [];
        if(is_array(request('image'))) {
            $images = request('image');
            $titles = request('title');
            foreach ($images as $key => $image) {
                $uploads->push(new Upload(['path' => $image->store(request('merchant_id') . '/product'),
                    'title' => $titles[$key], 'uploadmodel_id' => $merchantproduct->id]));
            }

            $merchantproduct->uploadmodel()->saveMany($uploads);
        }
        */
    }
}
