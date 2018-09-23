<?php

namespace App\Modules\System;

use App\Libs\WalletData;
use App\Models\Advertisement;
use App\Models\Bus;


use App\Models\ClientOrders;
use App\Models\Department;

use App\Models\Item;

use App\Models\ItemCategories;
use App\Models\Project;

use App\Models\MerchantStaffGroup;
use App\Models\PaymentSDKGroup;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use App\Models\Supplier;
use App\Models\SupplierOrders;
use App\Models\Upload;

use Illuminate\Http\Request;

use App\Models\Staff;

use App\Models\PermissionGroup;

use App\Models\Client;

use Illuminate\Support\Facades\DB;

class AjaxController extends SystemController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function get(Request $request){

        switch ($request->type) {
            case 'user':
                $word = $request->word;
                $data = User::where('id', '=', $word)
                    ->orwhere('firstname', 'LIKE', '%' . $word . '%')
                    ->orwhere('lastname', 'LIKE', '%' . $word . '%')
                    ->orWhere(DB::raw("CONCAT(firstname,'+',lastname)"), 'LIKE', '%' . $word . '%')
                    ->orwhere('email', 'LIKE', '%' . $word . '%')
                    ->orwhere('mobile', 'LIKE', '%' . $word . '%')
                    ->orwhere('national_id', '=', $word)
                    ->get(['id', 'firstname', 'lastname']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->firstname . ' ' . $value->lastname . ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;

                return $return;
                break;

            case 'client':
                $word = $request->word;
                $data = Client::where('name', 'LIKE', '%' . $word . '%')
                    ->where('status','active')
                    ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name', 'mobile']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }
                return $return;
                break;

            case 'ItemCategory':
                $word = $request->word;
                $data = ItemCategories::where('name', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name']);
                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;


            case 'project':
                $word = $request->word;
                $data = Project::where('name', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name']);
                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;

            case 'department':
                $word = $request->word;
                $data = Department::where('name', 'LIKE', '%' . $word . '%')

                    ->get(['id', 'name']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;


            case 'client':
                $word = $request->word;
                $data = Client::where('name', 'LIKE', '%' . $word . '%')
                    ->where('status','active')
                    ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name', 'mobile']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;


            case 'bus':
                $word = $request->word;
                $data = Bus::where('bus_number', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'bus_number']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->bus_number. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;

            case 'supplier':
                $word = $request->word;
                $data = Supplier::where('name', 'LIKE', '%' . $word . '%')
                    ->where('status','activie')
                    ->orWhere('email', 'LIKE', '%' . $word . '%')
                    ->orWhere('mobile1', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name', 'mobile1']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;

            case 'item':
                $word = $request->word;
                $data = Item::where('name', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;
                
            case 'staff':
                $word = $request->word;
                $data = Staff::where('firstname', 'LIKE', '%' . $word . '%')
                    ->orWhere('lastname', 'LIKE', '%' . $word . '%')
                    ->orWhere('email', 'LIKE', '%' . $word . '%')
                    ->orWhere(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $word . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                    ->where('status','=','active')->get(['id', 'firstname', 'lastname']);

                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->firstname . ' ' . $value->lastname . ' #ID:' . $value->id
                    ];
                }

                return $return;
                break;


            case 'item_category':
                $word = $request->word;
                $data = ItemCategories::where('name', 'LIKE', '%' . $word . '%')
                    ->get(['id', 'name']);
                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' => $value->name. ' #ID:' . $value->id
                    ];
                }
                return $return;
                break;
            case 'supplier_order_id':
                $word = $request->word;
                $data = SupplierOrders::where('id', '=', $word)
                    ->get(['id']);
                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' =>  $value->id
                    ];
                }

                return $return;
                break;
            case 'client_order_id':
                $word = $request->word;
                $data = ClientOrders::where('id', '=', $word)
                    ->get(['id']);
                $return = [];
                foreach ($data as $value) {
                    $return[] = [
                        'id' => $value->id,
                        'value' =>  $value->id
                    ];
                }

                return $return;
                break;

        }

    }

}
