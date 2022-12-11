<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\PromoCodeService;
use App\Models\CouponCategory;
use App\Models\PromocodeCity;
use App\Models\Service;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Validation\Rule;

class PromocodesController extends Controller
{
    
    /* public function __construct()
        {
            $this->middleware('permission:read_promocode', ['only' => ['index','show']]);
            $this->middleware('permission:add_promocode', ['only' => ['create', 'store']]);
            $this->middleware('permission:update_promocode', ['only' => ['edit', 'update']]);
            $this->middleware('permission:delete_promocode', ['only' => ['destroy']]);
       }*/

       public function index()
       {
           
           $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
           ];
 
           $data['page_title'] = trans('admin.promocodes');
           $data['promocodes']  = PromoCode::orderBy('id', 'DESC')->get();

           return view('dashboard.promocode.index', $data);
       }

       public function create()
       {
         
            $data['breadcrumb'] = [

                ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
                ['name' => trans('admin.promocodes'), 'url' => route('promocodes.index')],
                ['name' => trans('admin.create'), 'url' => null],
            ];
    
            $data['page_title'] = trans('admin.promocodes');
            $data['data'] = new PromoCode;
            $data['services'] = Service::all();

            return view('dashboard.promocode.create_edit', $data);
       }

       public function store(Request $request)
       {
            
             $validator = Validator::make($request->all(),
              [  
                  'code' => 'required|unique:promocodes',
                  'start_at' => 'required',
                  'expires_at' => 'required',
                 'min_value' => 'required|numeric',
                  'services' =>[Rule::requiredIf($request->product_type==2)]


             ]);

          if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
           }

           $collect = array(

               "percent" => $request->settings
           );

          $settings = json_encode($collect);
          $promocode= new PromoCode;
          $promocode->code= $request->code;
          $promocode->type = $request->type;
          $promocode->description = $request->description;
          $promocode->product_type = $request->product_type;
          $promocode->amount = $request->amount;
          $promocode->settings =$settings;
          $promocode->start_at=$request->start_at;
          $promocode->expires_at= $request->expires_at;
          $promocode->status  =$request->status;
          $promocode->min_value = $request->min_value;
          $promocode->max_value = $request->max_value?:null;
          $promocode->num_of_use =$request->num_of_use;
          $promocode->save();
          $products= Service::all();

        if($request->product_type == 1)
        {

         foreach($products as $product)
         {

              $insert = new PromocodeService;
              $insert->service_id = $product->id;
              $insert->promocode_id = $promocode->id;
              $insert->save(); 
         }
       }

       elseif($request->product_type == 2) 
       {

            $selected = $request['services'];
          
            foreach($selected as $product)
            {
            
                $insert = new PromocodeService;
                $insert->service_id = $product;
                $insert->promocode_id = $promocode->id;
                $insert->save();

            }
          }
               
           return redirect()->route('promocodes.index');
     }

     public function edit($id)
     {
         
          $data['breadcrumb'] = [
 
               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.promocodes'), 'url' => route('promocodes.index')],
               ['name' => trans('admin.edit'), 'url' => null]
          ];

          $data['page_title'] = trans('admin.promocodes');
          $data['data'] = PromoCode::findOrFail($id);
          $data['cities'] = City::all();
          $data['services'] = Service::all();
          $data['selected_products_ids'] = PromocodeService::where('promocode_id',$id)->pluck('service_id')->toArray();

          return view('dashboard.promocode.edit', $data);
     }

     public function update(Request $request, $id)
     {

        $validator = Validator::make($request->all(),
         [
             'code' => 'required',
             'start_at' => 'required',
             'expires_at' => 'required',
           'min_value' => 'required|numeric',
            'services' =>[Rule::requiredIf($request->product_type==2)]


         ]);

         if($validator->fails()) {

               return redirect()->back()->withErrors($validator)->withInput();
          }

           $collect = array(

               "percent" => $request->settings
           );

            $settings = json_encode($collect);
            $promocode = PromoCode::find($id);
            $promocode->code= $request->code;
            $promocode->type = $request->type;
            $promocode->description = $request->description;
            $promocode->product_type = $request->product_type;
            $promocode->amount = $request->amount;
            $promocode->settings=$settings;
            $promocode->start_at=$request->start_at;
            $promocode->expires_at= $request->expires_at;
            $promocode->status  =$request->status;
            $promocode->min_value = $request->min_value;
            $promocode->max_value = $request->max_value?:null;
            $promocode->num_of_use =$request->num_of_use;
            $promocode->save();
            $products= Service::all();

            if($request->product_type == 1)
            {

              foreach($products as $product)
              {
                  $insert = new PromocodeService;
                  $insert->service_id = $product->id;
                  $insert->promocode_id = $promocode->id;
                  $insert->save();

               }
            }

             elseif($request->product_type == 2 )
             {
            
                $delete = PromocodeService::where('promocode_id',$id)->delete();
                $selected = $request['services'];

             foreach($selected as $product)
             {

                    $insert = new PromocodeService;
                    $insert->service_id = $product;
                    $insert->promocode_id = $promocode->id;
                    $insert->save();

             }
         }

             return redirect()->route('promocodes.index');
       }

       public function destroy($id)
       {
          
           $delete = PromoCode::destroy($id);

           return redirect()->route('promocodes.index');
       }
}
