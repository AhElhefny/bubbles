<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\HomePageSetting;
use App\Models\Photoes;
use Spatie\MediaLibrary\Models\Media;
use Auth;
use App\Models\Seller;
use App\Models\BranchesTax;

class BusinessController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:read_setting', ['only' => ['index','show']]);
        $this->middleware('permission:add_setting', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_setting', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_setting', ['only' => ['destroy']]);

    }

    public function main_settings()
    {

        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.general_settings'), 'url' => url('admin/settings/app')],
            ['name' => trans('admin.create'), 'url' => null],
        ];

        $data['page_title'] = trans('admin.general_settings');

        return view('dashboard.setting.main_setting',$data);
    }

    public function homepage_settings()
    {

         $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
             ['name' => trans('admin.home_page'), 'url' => url('admin/settings/home_page')],
             ['name' => trans('admin.create'), 'url' => null],
         ];

         $data['page_title'] = trans('admin.home_page');
         $data['gs'] = HomePageSetting::find(1);

         return view('dashboard.setting.home_page_settings',$data);
    }

    public function home_settings()
    {
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.home_settings'), 'url' => url('admin/settings/home')],
            ['name' => trans('admin.create'), 'url' => null],
        ];

        $data['page_title'] = trans('admin.home_settings');

        return view('dashboard.setting.home',$data);
    }

    public function order_settings()
    {

        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.order_settings'), 'url' => url('admin/settings/orders')],
            ['name' => trans('admin.create'), 'url' => null],
        ];

        $data['page_title'] = trans('admin.order_settings');

        return view('dashboard.setting.orders', $data);
    }

    public function worktimes_settings()
    {

        $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
             ['name' => trans('admin.worktimes_settings'), 'url' => url('admin/settings/worktimes')],
             ['name' => trans('admin.create'), 'url' => null],
         ];

         $data['page_title'] = trans('admin.worktimes_settings');
         $data['gs'] = HomePageSetting::find(1);

         return view('dashboard.setting.worktimes-settings', $data);
    }

    public function branchestax_settings()
    {

          $data['breadcrumb'] = [

              ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
              ['name' => trans('admin.branches_tax'), 'url' => url('vendor/settings/branches_taxes')],
          ];

          $data['page_title'] = trans('admin.branches_tax');
         //$data['data'] = BranchesTax::where('seller_id',Auth::user()->seller_id)->first();
          $getVenodr = Seller::where('user_id' ,Auth::user()->id)->first();

           BranchesTax::updateOrCreate([

              'seller_id' =>   $getVenodr->id ,
           ]);

         return view('dashboard.setting.branches-tax',$data);
    }

    public function store(Request $request)
    {


    }

    public function updatesetting(Request $request)
    {

        $update = HomePageSetting::find(1);
        $update->update($request->all());

       if($update)
        {

         if($request->hasFile('image')) {
               $update->addMedia($request->file('image'))
               ->withCustomProperties(['root' => 'user_prr'.uniqid()])
              ->toMediaCollection('homepage_images');
          }

         if($request->hasfile('photoes'))
         {

            $images = $request['photoes'];

            foreach($images as  $photo)
            {

                 $update->addMedia($photo)
                 ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                 ->toMediaCollection($update->photoesMediaCollection);
             }
           }

                Artisan::call('cache:clear');

                return  redirect()->back()->with('success','تم التعديل بنجاح');
             }

          else{

             Artisan::call('cache:clear');

             return back();
         }
     }

    public function updateBranchtax(Request $request,$id)
    {

        $getVenodr = Seller::where('user_id',$id)->first();
        $update = BranchesTax::where('seller_id', $getVenodr->id)->first();

        $update->update([

             'tax'=> $request->tax,
             'tax_type' => $request->tax_type,
             'seller_id' => $getVenodr->id ,
        ]);

         return  redirect()->back()->with('success','تم التعديل بنجاح');
    }

    public function update(Request $request)
    {

         foreach ($request->get('types', []) as $key => $type) {

            if($type == 'site_name'){

                $this->overWriteEnvFile('APP_NAME', $request[$type]);
            }

            if($type == 'timezone'){

                $this->overWriteEnvFile('APP_TIMEZONE', $request[$type]);
            }

            else {

            $lang = null;

            if(gettype($type) == 'array'){

                  $lang = array_key_first($type);
                  $type = $type[$lang];
                  $business_settings = Settings::where('key', $type)->first();

              }else{

                   $business_settings=Settings::where('key', $type)->first();
                }

                $requestValue = is_array($request[$type])?json_encode($request[$type]):$request[$type];

              if($business_settings!=null){

                  $business_settings->value = $requestValue;
                  $business_settings->save();
               }

             else{
                    $business_settings = new Settings;
                    $business_settings->key = $type;
                    $business_settings->value = $requestValue;
                    $business_settings->save();
               }
            }
         }

        Artisan::call('cache:clear');

        return  redirect()->back()->with('success','تم التعديل بنجاح');
    }

    public function deleteimages($id)
    {
         $delete = Media::find($id)->delete();

         return redirect()->back();
    }
}
