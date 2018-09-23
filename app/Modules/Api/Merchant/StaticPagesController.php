<?php
namespace App\Modules\Api\Merchant;

use Auth;

class StaticPagesController extends MerchantApiController
{

    public function aboutUs(){
        return $this->respondSuccess([
            'title'         => __('About Us'),
            'html'          =>'<div>ايجي باي هي شركة مساهمة مصرية تاسست لتقديم خدمات الدفع الالكتروني والتجارة الالكترونية في مصر.
<br>
تقوم ايجي باي بتقديم خدماتها عن طريق تطبيق الهاتف المحمول الخاص بها (ايجي باي) والذي استخدمت في تطويره احدث التقنيات بحيث يستطيع عملاء شركة ايجي باي والمستخدمين للتطبيق والحلول الخاصة بها من شراء احتياجاتهم اليومية من العديد من المحلات والتجار المتعاقدة مع الشركة. كما سيكون بامكانهم تنفيذ عديد من عمليات الدفع مثل دفع فواتير الهاتف المحمول وشحن الرصيد وفواتير العديد من الخدمات الاخرى . كما يقدم تطبيق ايجي باي خدمة التجارة الالكترونية لكل من المستخدمين والتجار بشكل حديث ومتطور <br>
بالاضافة الى ما سبق، تقدم شركة ايجي باي خدمات البرمجة المختلفة</div>'
        ]);
    }



    public function checkversion(){
        return $this->respondSuccess(true);
    }


    public function apk(){
        return response()->download(storage_path('app/public/app.apk'),'latest-apk.apk');
    }



}