<?php

namespace App\Http\Controllers\Dashboard;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Excel;
use App\Exports\OrdersExport;
use App\Models\Cart;
use App\User;
use App\Exports\ReturnedOrdersExport;
use App\Exports\EmailSmsExport;
use App\Exports\PromoCodesExport;
use App\Exports\OfferExport;
use App\Models\Compansations;
use App\Imports\UsersImport;
use App\Models\City;
use App\Models\UserBalance;
use App\Models\PromocodeCity;
use App\Models\PromoCodeuser;
use App\Models\Appnotification;
use App\Exports\CommonExport;
use App\Exports\CampaignResultsExport;
use Carbon\Carbon;


class MaatwebsiteDemoController extends Controller {


    public function exportpromocodes(Request $request)
    {
        
        return Excel::download(new PromoCodesExport, 'الخصومات.xlsx');
    }

    public function export_campaign_resultToExcel(Request $request ,$id)
    {
        
            $result  =PromoCodeUser::where('promocode_id',$id)->where('referenceable_type','orders')->count();
            $data['promocode_orders'] = PromoCodeUser::where('promocode_id',$id)->where('referenceable_type','orders')->pluck('referenceable_id');
            $promocode_results  =  Order::whereIN('id',$data['promocode_orders'])->sum('total');
            $data['promocodes'] = PromoCodeUser::where('promocode_id',$id)->pluck('user_id');
            $data_users = User::whereIn('id',$data['promocodes'])->get();
            $data_cities = PromoCodeCity::where('promocode_id',$id)->get();
        
            $users = [];
            $cities = [];
            $sales_results=[];
            $sales_results [] =  array('عدد الطلبات'=> $result > 0 ? $result :0 ,'اجمالى المبيعات' => $promocode_results > 0 ? $promocode_results : 0);

    
           foreach($data_users as $key => $value) {

                $users[] = array('رقم العميل'=> $value->id ,'اسم العميل' => $value->name ,'رقم الجوال'=>$value->mobile );
        
            }

            foreach($data_cities as $key => $city) {

                   $cities[]= array('اسم المدينة' =>  $city->city->name,
               );
          }
    
           $arrays = [$sales_results , $users , $cities];
 
           return Excel::download(new CampaignResultsExport($arrays), 'نتائج الحملة.xlsx');
     }

    public function ImportUsers(Request $request)
    {
        
        $data['insert'] = Excel::toArray(new UsersImport,request()->file('users_file'));
        $data['breadcrumb'] = [
      
            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.app_notifications'), 'url' => route('notification-messages.index')],
            ['name' => trans('admin.create'), 'url' => null],
       
        ];
    
        $data['page_title'] = trans('admin.app_notifications');
        $data['data'] = new Appnotification;
        $data['cities'] = City::all();

        return view('dashboard.appnotification.importusers',$data);

    }

    public function exportcancelledToExcel(Request $request)
    {
        
        $orders = $this->searchResult($request,  Order::query()->where('status','=','5'))->orderBy('created_at', 'desc')->get();
        $data['rows'] = [];

        foreach ($orders as $key =>$value){
           
             $data['rows'][] = 
             array('id'=>++$key ,'name' =>!is_null($value->user) ?$value->user->name: null ,'phone' => !is_null($value->user) ?$value->user->mobile: null,'deliver_date'=>$value->delivery_date,'total'=>$value->total ,'payment'=>$value->PaymentMethod ,'cancel'=>$value->cancelled_reason , 'status'=>show_order_status($value->status));
        }

        $data['headings'] = [

            '#',
            'اسم العميل',
            'رقم الهاتف',
            'تاريخ التوصيل',
            'الاجمالى',
            'وسيلة الدفع',
            'سبب الالغاء',
            'الحالة'
        ];

          return \Excel::download(new CommonExport($data), 'الطلبات الملغية.xlsx');
    }

    public function exportcartordersToExcel(Request $request)
    {
        
        $data['carts'] = Cart::whereNotNull('user_id')->select('id','user_id','updated_at')->orderBy('created_at','desc')->pluck('user_id');
        $orders = $this->searchCartResult($request, Order::query()->whereIN('user_id', $data['carts']))->orderBy('created_at','desc')->paginate(15)->withQueryString();

        $data['rows'] = [];

        foreach ($orders as $key =>$value){
           
             $data['rows'][] = 
            
             array('id'=>$value->id ,'name' =>!is_null($value->user) ?$value->user->name: null ,'phone' => !is_null($value->user) ?$value->user->mobile: null,'deliver_date'=>$value->delivery_date,'total'=>$value->total ,'payment'=>$value->PaymentMethod , 'status'=>show_order_status($value->status));
        }

        $data['headings'] = [

            'ID',
            'اسم العميل',
            'رقم الهاتف',
            'تاريخ التوصيل',
            'الاجمالى',
            'وسيلة الدفع',
            'الحالة'

        ];

        return \Excel::download(new CommonExport($data), 'السلات التى تحولت الى طلبات.xlsx');
    }


    public function exportcashbackToExcel(Request $request)
    {
        $customers= $this->searchBalancesResult($request, UserBalance::query()->where([['type', '=', 'cashback'],  ['operation_type', '=', 'plus'],['expiry_date','>=',date('Y-m-d' ,strtotime(Carbon::now()))]]))->orderBy('created_at','desc')->get();

        $data['rows'] = [];

        foreach($customers as $key => $value) {

          $sumOrdersTotal = $value->user->orders()->where('orders.status', 8)->sum('total');
          $countDeliveredOrders = $value->user->orders()->where('orders.status', 8)->count();
          $cartaverage = ($countDeliveredOrders > 0 && $sumOrdersTotal > 0)? round($sumOrdersTotal/$countDeliveredOrders) :0;

          $data['rows'][] = array('id'=> ++ $key ,'name' => $value->user->name ,'phone' => $value->user->mobile,'city'=>$value->user->city_name ,'cart_rate'=>$cartaverage , 'cashback_value'=>$value->value,
          
           'reason'=>$value->reason ,'expiry_date'=>$value->expiry_date);
        }

        $data['headings'] = [

             '#',
            'اسم العميل',
            'رقم الهاتف',
            'المدينة',
            'متوسط السلة',
            'مقدار الكاش باك',
            'السبب',
            'فترة انتهاء الصلاحية',

        ];

        return \Excel::download(new CommonExport($data), 'تقارير الكاش باك.xlsx');
    }

    public function exportcartToExcel(Request $request)
    {
       
        $customers =  $this->searchCartResult($request, Cart::query()->whereNotNull('user_id'))->select('id','user_id','updated_at')->orderBy('created_at','desc')->get();
        $data['rows'] = [];

        foreach($customers as $key => $value) {

        $data['rows'][] = array('id'=> ++ $key ,'name' => $value->user->name ,'email' => $value->user->email ,'phone' => $value->user->mobile, 'value'=>$value->amount(),
        
            'updated_at'=>$value->updated_at);
        }

        $data['headings'] = [

            '#',
            'اسم العميل',
            'الايميل',
            'رقم الهاتف',
            'قيمة السلة',
            'اخر تحدي للسلة ',
        ];

         return \Excel::download(new CommonExport($data), 'السلات المتروكة.xlsx');
    }

    public function exportnewcustomersToExcel(Request $request)
    {
        
        $data['getorders'] = Order::pluck('user_id');

        $customers = $this->searchCustomersResult($request, User::query()->where('user_type','customer')->whereNotIN('id', $data['getorders']))->orderBy('id', 'desc')->paginate(15);     
      
        $data['rows'] = [];

        foreach($customers as $key => $value) {

          $city = $value->city;

          $data['rows'][] = array('id'=> ++ $key ,'name' => $value->name ,'city'=>$city ? $city->name : null ,'phone' => $value->mobile, 'orders_count'=>0,
          
             'balance'=>0 ,'status'=> show_status($value->status));
        }

        $data['headings'] = [

             '#',
            'اسم العميل',
            'المدينة',
            'رقم الهاتف',
            'عدد الطلبات',
            'قيمة المحفظة',
            'الحالة',

        ];

          return \Excel::download(new CommonExport($data), 'السلات المتروكة.xlsx');
    }


    public function exportcampaignsalesToExcel(Request $request ,$id)
    {
       
        $result = PromoCodeUser::where('promocode_id',$id)->where('referenceable_type','orders')->count();
        $promocode_orders = PromoCodeUser::where('promocode_id',$id)->where('referenceable_type','orders')->pluck('referenceable_id');
        $promocode_results  = Order::whereIN('id' , $promocode_orders)->sum('total');
       
        $data['rows'][] =  array('count'=> $result > 0 ? $result :0 ,'sales' => $promocode_results > 0 ? $promocode_results : 0);

        $data['headings'] = [

             'عدد الطلبات',
            'اجمالى المبيعات',
            
        ];

          return \Excel::download(new CommonExport($data), 'نتائج الحملة.xlsx');
    }

    protected function searchCustomersResult($request, $customers){

        if($searchWord = $request->get('search_word')){
            $customers = $customers->where('name', $searchWord)->orWhere('mobile', $searchWord)->orWhere('email', $searchWord);
        }

        if(!is_null($request->status)){
            $customers = $customers->where('status', $request->status);
        }

        if($createdAt= $request->created_at){
            $array = explode(' >> ', $createdAt);
            $customers = $customers->where('created_at', '>=', $array[0])
                ->where('created_at', '<=', $array[1]);
        }

        if ($request->mobile || $request->name){
            $customers = $customers->where('mobile', $request->mobile)
                    ->orWhere('name', $request->name);
        }

        if ($request->city){
            $customers = $customers->whereHas('addresses', function ($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        
        }

         if(!is_null($request->cart_rate)){

            $customers = $customers->where('cart_rate', $request->cart_rate);
        }

        return $customers;
    }

    protected function searchCartResult($request, $orders){

        if($searchWord = $request->get('search_word')){
               $orders = $orders->where(function ($q) use ($searchWord) {
                     $q->whereHas('user', function ($q) use ($searchWord) {
                         $q->where('name', $searchWord)
                           ->orWhere('mobile', $searchWord);
                     });
               })
               ->orWhere('id', substr($searchWord, 2));
           }
   
           if($createdAt= $request->created_at){
               $array = explode(' >> ', $createdAt);
               $orders = $orders->where('created_at', '>=', $array[0])
               ->where('created_at', '<=', $array[1]);
           }
   
    
           if ($request->mobile || $request->name){
                 $orders = $orders->whereHas('user', function ($q) use ($request) {
                   $q->where('mobile', $request->mobile)
                     ->orWhere('name', $request->name);
               });
           }
   
               return $orders;
        }

    protected function searchResult($request, $orders){
        
     if($searchWord = $request->get('search_word')){
            $orders = $orders->where(function ($q) use ($searchWord) {
                  $q->whereHas('user', function ($q) use ($searchWord) {
                      $q->where('name', $searchWord)
                        ->orWhere('mobile', $searchWord);
                  });
            })

            ->orWhere('id', substr($searchWord, 2));
        }

        if($orderNumber = $request->order_number){
            $orders = $orders->where('id', substr($orderNumber, 2));
        }

        if(!is_null($request->status)){
            $orders = $orders->where('status', $request->status);
        }

        if($createdAt= $request->created_at){
            $array = explode(' >> ', $createdAt);
            $orders = $orders->where('created_at', '>=', $array[0])
            ->where('created_at', '<=', $array[1]);
        }

        if($delivery_date= $request->delivery_date){
            $array = explode(' >> ', $createdAt);
            $orders = $orders->where('delivery_date', '>=', $array[0])
                ->where('delivery_date', '<=', $array[1]);
        }

        if ($request->mobile || $request->name){
            $orders = $orders->whereHas('user', function ($q) use ($request) {
                $q->where('mobile', $request->mobile)
                  ->orWhere('name', $request->name);
            });
        }

        if ($request->city){
            $orders = $orders->whereHas('address', function ($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        }

        if ($request->payment_method){
            $orders = $orders->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_method_id', (int) $request->payment_method);

            });
        }

        return $orders;
    }

 protected function searchCompansationResult($request, $orders){
        
    if($searchWord = $request->get('search_word')){
         $orders = $orders->whereHas('order', function ($result) use ($searchWord) {
            $result->whereHas('user', function ($q) use ($searchWord) {
             $q->where('mobile', $searchWord )
               ->orWhere('name', $searchWord );

             });
          });

        }

       if($orderNumber = $request->order_number){
           $orders = $orders->whereHas('order', function ($q) use ($request) {
            $q->where('id', $request->order_number);
              
        });
       }

       if(!is_null($request->status)){
             $orders = $orders->whereHas('order', function ($q) use ($request) {
                $q->where('status', $request->status);
             
          });
          }

       if($createdAt= $request->created_at){
             $array = explode(' >> ', $createdAt);
             $orders = $orders->where('created_at', '>=', $array[0])
             ->where('created_at', '<=', $array[1]);
       }

    
       if ($request->mobile || $request->name){
             $orders = $orders->whereHas('order', function ($result) use ($request) {
               $result->whereHas('user', function ($q) use ($request) {
                $q->where('mobile', $request->mobile)
                  ->orWhere('name', $request->name);

               });
           });
       }

       return $orders;
    }


 protected function searchBalancesResult($request, $customers){

    if($searchWord = $request->get('search_word')){
        $customers = $customers->where(function ($q) use ($searchWord) {
              $q->whereHas('user', function ($q) use ($searchWord) {
                  $q->where('name', $searchWord)
                    ->orWhere('mobile', $searchWord);
              });
        })
            ->orWhere('id', substr($searchWord, 2));
    }

    if($createdAt= $request->created_at){
        $array = explode(' >> ', $createdAt);
        $customers = $customers->where('created_at', '>=', $array[0])
            ->where('created_at', '<=', $array[1]);
    }

    if ($request->mobile || $request->name){
        $customers = $customers->whereHas('user', function ($q) use ($request) {
            $q->where('mobile', $request->mobile)
              ->orWhere('name', $request->name);
        });

    }

    if ($request->city){
        $customers = $customers->whereHas('user', function ($q) use ($request) {
            $q->where('city_id', $request->city);
            
        });

    }

    return $customers;
 }

}
