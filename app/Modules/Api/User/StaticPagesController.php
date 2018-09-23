<?php
namespace App\Modules\Api\User;

use Auth;

class StaticPagesController extends UserApiController
{

    public function aboutUs(){
        return $this->respondSuccess([
            'title'         => 'About us Page',
            'html'          =>'<div>Test Page Test Page Test Page Test Page Test Page </div>'
        ]);
    }



    public function checkversion(){
        return $this->respondSuccess(true);
    }

}