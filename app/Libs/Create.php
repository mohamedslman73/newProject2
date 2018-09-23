<?php

namespace App\Libs;

use App\Models\Contacts;
use App\Models\Staff;
use App\Models\Merchant;
use App\Models\MerchantPlan;
use App\Models\MerchantStaffPermission;
use App\Models\TempData;
use App\Models\Upload;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
use App\Libs\SMS;

class Create
{
    public static function Staff(Array $staffArray){
        $staff = false;
        DB::transaction(function() use ($staffArray,&$staff) {
            $staff = Staff::create([
                'firstname'=> $staffArray['firstname'],
                'lastname'=> $staffArray['lastname'],
                'national_id'=> $staffArray['national_id'],
                'email'=> $staffArray['email'],
                'mobile'=> $staffArray['mobile'],
                'avatar'=> @$staffArray['avatar'],
                'gender'=> $staffArray['gender'],
                'birthdate'=> $staffArray['birthdate'],
                'address'=> $staffArray['address'],
                'password'=> bcrypt($staffArray['password']),
                'description'=> $staffArray['description'],
                'job_title'=> $staffArray['job_title'],
                'status'=> $staffArray['status'],
                'permission_group_id'=> $staffArray['permission_group_id'],
                'supervisor_id'=> $staffArray['supervisor_id']
            ]);


            $staff->wallet()->create([
                'type'=> 'payment'
            ]);

        });

        return $staff;
    }

    public static function User(Array $userArray){
        $user = false;
        DB::transaction(function () use ($userArray,&$user) {
            $user = User::create([
                'user_name' => $userArray['user_name'],
                'firstname' => $userArray['firstname'],
                'middlename' => $userArray['middlename'],
                'lastname' => $userArray['lastname'],
                'email' => $userArray['email'],
                'mobile' => $userArray['mobile'],
                'password' => $userArray['password'],
                'image' => $userArray['image'],
                'gender' => $userArray['gender'],
                'national_id' => $userArray['national_id'],
                'birthdate' => $userArray['birthdate'],
                'address' => $userArray['address'],
                'status' => $userArray['status'],
                'parent_id' => $userArray['parent_id'],
                'nationality_id' => $userArray['nationality_id'],
                'national_id_image' => $userArray['national_id_image']
            ]);

            $user->wallet()->createMany([
                ['type'=> 'e-commerce'],
                ['type'=> 'loyalty']
            ]);

        });

        return $user;
    }

    public static function Merchant(Array $merchantArray,$contactArray,Array $branchArray,Array $staffArray,Array $contractArray,$contractFilesArray,$contractTitleArray,$alreadyUploadedContractFiles,$tempData,$merchantImages){
        $merchant = false;
        DB::transaction(function() use($contractTitleArray, $contractFilesArray, $branchArray, $contractArray, $staffArray, $merchantArray,$contactArray,$alreadyUploadedContractFiles,&$merchant,$tempData,$merchantImages) {



            // Add Merchant
            $merchant = Merchant::create([
                'is_reseller'=> $merchantArray['is_reseller'],
                'area_id'=> $merchantArray['area_id'],
                'name_ar'=> $merchantArray['name_ar'],
                'name_en'=> $merchantArray['name_en'],
                'description_ar'=> $merchantArray['description_ar'],
                'description_en'=> $merchantArray['description_en'],
                'address'=> $merchantArray['address'],
                'logo'=> $merchantArray['logo'],
                'merchant_contract_id'=> null,
                'merchant_category_id'=> $merchantArray['merchant_category_id'],
                'status'=> $merchantArray['status'],
                'staff_id'=> $merchantArray['staff_id'],
                'parent_id'=> $merchantArray['parent_id']
            ]);
            

            /*
             * Merchant Images
             */
            if(is_array($merchantImages) && count($merchantImages)){
                $uploadImages = new Collection();
                foreach($merchantImages as $title=>$file){
                    $uploadImages->push(new Upload([
                        'path'      => $file->store(MediaFiles('merchant_images',$merchant->id).'/'.$merchant->id),
                        'title'     => $title,
                        'model_id'  => $merchant->id,
                        'model_type'  => get_class($merchant)
                    ]));
                }

                $merchant->MerchantImages()->saveMany($uploadImages);
            }

            // Add Contacts
            if(!empty($contactArray)){
                $contactInfo = new Collection();
                foreach ($contactArray as $key => $value) {
                    foreach ($value as $contact) {
                        $contactInfo->push(new Contacts(['model_id' => $merchant->id, 'type' => $key, 'value' => $contact]));
                    }
                }
                $merchant->contact()->saveMany($contactInfo);
            }


            // Add Wallets
            $merchant->wallet()->createMany([
                ['type'=> 'payment'],
                ['type'=> 'e-commerce'],
                ['type'=> 'loyalty']
            ]);

            // Add Contracts

            $merchantPlans = MerchantPlan::find($contractArray['plan_id']);
            $contractArray['start_date'] = Carbon::now();
            $contractArray['end_date']   = (Carbon::now())->addMonths($merchantPlans->months);

            $MerchantContracts = $merchant->MerchantContracts()->create([
                'plan_id'=> $contractArray['plan_id'],
                'description'=> $contractArray['description'],
                'price'=> $contractArray['price'],
                'start_date'=> $contractArray['start_date'],
                'end_date'=> $contractArray['end_date'],
                'admin_name'=> $contractArray['admin_name'],
                'admin_job_title'=> $contractArray['admin_job_title'],
                'staff_id'=> $merchant->staff_id
            ]);

            $uploads = new Collection();
            /*
             * Already uploaded files
             */
             if(is_array($alreadyUploadedContractFiles) && count($alreadyUploadedContractFiles)){
                 foreach($alreadyUploadedContractFiles as $oneFile){
                     $File = new File($oneFile['path']);
                     $Moved = $File->move('storage/contract/'.$merchant->id.'/');
                     $uploads->push(new Upload([
                         'path'      => (($Moved)?$Moved->getPath().'/'.$Moved->getBasename():$oneFile['path']),
                         'title'     => $oneFile['title'],
                         'model_id'  => $MerchantContracts->id,
                         'model_type'  => get_class($MerchantContracts)
                     ]));
                 }
             }

            if($contractFilesArray){
                foreach ($contractFilesArray as $key => $value){
                    $uploads->push(new Upload([
                        'path'      => 'storage/'.$value->store('contract/'.$merchant->id),
                        'title'     => @$contractTitleArray[$key],
                        'model_id'  => $MerchantContracts->id,
                        'model_type'  => get_class($MerchantContracts)
                    ]));
                }
            }

            if(count($uploads)){
                $MerchantContracts->upload()->saveMany($uploads);
            }

            // Add Staff Group
            $merchantStaffGroups = self::MerchantStaffGroup($merchant,['title'=>__('Administrators')],false);

            // Add Staff
            $staffEmailUserName = $staffArray['national_id'].rand().'@merchant.egpay.com';
            $merchantStaff = $merchantStaffGroups->staff()->create([
                'firstname' => $staffArray['firstname'],
                'lastname'  => $staffArray['lastname'],
                'username'  => $staffEmailUserName,
                'national_id'=> $staffArray['national_id'],
                'address'=> $staffArray['address'],
                'birthdate'=> $staffArray['birthdate'],
                'email'=> $staffEmailUserName,
                'password'=> bcrypt($staffArray['password']),
                'mobile'=> $staffArray['mobile'],
                'status'=> $merchant->status
            ]);

            // Add Branch
            $branch = $merchant->merchant_branch()->create([
                'name_ar'=> $branchArray['name_ar'],
                'name_en'=> $branchArray['name_ar'],
                'address_ar'=> $branchArray['address_ar'],
                'address_en'=> $branchArray['address_en'],
                'description_ar'=> $branchArray['description_ar'],
                'description_en'=> $branchArray['description_en'],
                'latitude'=> $branchArray['latitude'],
                'longitude'=> $branchArray['longitude'],
                'status'=> $merchant->status,
                'area_id'=> $merchant->area_id,
                'staff_id'=> $merchant->staff_id,
                'merchant_staff_id'=> $merchantStaff->id
            ]);

            $merchantStaff->update([
                'branches'=> $branch->id
            ]);

            $merchant->update([
                'merchant_contract_id'=> $MerchantContracts->id
            ]);

            if(!is_null($tempData)){
                TempData::where('id',$tempData)
                    ->first()
                    ->update(['reviewed_id'=>Auth::id(),'reviewed_at'=>Carbon::now()]);
            }


            $SMS = new SMS();
            $SMS->Send($merchantStaff->mobile,str_replace(['{1}','{2}','{3}'],[$merchant->id,$merchantStaff->id,$staffArray['password']],setting('sms_on_merchant_create')));
            
//            \Mail::to($staffEmailUserName)
//                ->send(
//                    new \App\Mail\SendClearEmail(setting('merchant_welcome_message_subject'),setting('merchant_welcome_message'))
//                );

        });
        
        


        return $merchant;

    }

    public static function MerchantStaffGroup($merchantModel,Array $staffGroupArray,$permissionsArray = false){

        $permissions = \Illuminate\Support\Facades\File::getRequire(app_path().'/Modules/Merchant/Permissions.php');

        $merchantStaffGroups = false;
        DB::transaction(function() use($permissionsArray, $permissions, $merchantModel,$staffGroupArray,&$merchantStaffGroups){

            $merchantStaffGroups = $merchantModel->merchant_staff_group()->create([
                'title'=> $staffGroupArray['title']
            ]);

            // Permission

            if($permissionsArray === false){
                $permissionRoutes = [];
                foreach ($permissions as $key => $value){
                    $flatten = array_flatten($value['permissions']);
                    if(is_array($flatten) && !empty($flatten)){
                        foreach ($flatten as $kf => $vf){
                            $permissionRoutes[] = [
                                'route_name'=> $vf
                            ];
                        }
                    }
                }
            }else{
                $permissionRoutes = new Collection();
                foreach ($permissions as $key => $value){
                    if($permissionsArray === false){
                        foreach ($value as $sKey => $sValue){
                            $permissionRoutes->push(new MerchantStaffPermission(['route_name'=>$sValue,'merchant_staff_group_id'=>$merchantStaffGroups->id]));
                        }
                    }elseif(is_array($permissionsArray)){
                        if(in_array($key,$permissionsArray)){
                            foreach ($value as $sKey => $sValue){
                                $permissionRoutes->push(new MerchantStaffPermission(['route_name'=>$sValue,'merchant_staff_group_id'=>$merchantStaffGroups->id]));
                            }
                        }
                    }
                }
            }


            $merchantStaffGroups
                ->merchant_staff_permission()
                ->createMany($permissionRoutes);

        });

        return $merchantStaffGroups;

    }

}