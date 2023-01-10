<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Branch;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\LogActivity as LogActivityModel;

class DashboardController extends Controller
{

    public function index()
    {
         // $sellers_users=User::where('user_type','seller')->get();
         // foreach($sellers_users as $user)
         // {
         //       $user->givePermissionTo(['read_service','add_service','update_service','delete_service']);
         // }
         $data['orders'] = Order::where('status',0)->OrderBy('created_at','desc')->get()->take(5);
         $data['washers'] =Branch::where('category_id',1)->get();
         $data['center_service'] =Branch::where('category_id',2)->get();
         $data['customers'] =User::where('user_type','=','customer')->get();

         return view('dashboard.index',$data);
    }

    public function sellerDashboard()
    {
          $data['orders']=[];
          $data['branches']=[];
          if (auth()->user()->seller) {
               // code...
//               $data['orders'] = Order::where('seller_id',auth()->user()->seller->id)->OrderBy('created_at','desc')->get()->take(5);
               $data['orders'] = Order::where('seller_id',auth()->user()->seller->id)->OrderBy('created_at','desc')->get();
               $data['branches']= Branch::where('seller_id',auth()->user()->seller->id)->get();
          }

          return view('dashboard.sellers_index',$data);
    }

}
