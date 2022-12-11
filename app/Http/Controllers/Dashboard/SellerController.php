<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{

    /*public function __construct()
    {
        $this->middleware('permission:read_seller', ['only' => ['index','show']]);
        $this->middleware('permission:add_seller', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_seller', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_seller', ['only' => ['destroy']]);
    }*/

    public function index(Request $request)
    {

        $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
        ];

        $data['page_title'] = trans('admin.sellers');
        $sort_search = null;

        $sellers = Seller::whereHas("user", function ($q){
            $q->where('user_parent_id', null);
        })->orderBy('created_at', 'desc');

        if ($request->has('search')){

            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function($user) use ($sort_search){
                $user->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            })->pluck('id')->toArray();
              $sellers = $sellers->where(function($seller) use ($user_ids){
              $seller->whereIn('user_id', $user_ids);
            });
         }

           $data['sellers']= $sellers->paginate(15);

           return view('dashboard.sellers.index',$data);
     }

     public function create()
     {

          $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.sellers'), 'url' => route('sellers.index')],
               ['name' => trans('admin.create'), 'url' => null],
          ];

          $data['page_title'] = trans('admin.sellers');
          $data['data'] = new Seller;
          $data['cities'] = City::get();

           return view('dashboard.sellers.create',$data);
     }

    public function store(Request $request)
    {

           $this->validate($request, [

                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|unique:users,mobile',
                'password' => ['required', 'string', 'min:6', 'confirmed'],

             ]);

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->user_type = "seller";
            $user->password = Hash::make($request->password);
            $user->seller_type = "super_manager";

         if($user->save()){
            $user->givePermissionTo(['read_service','add_service','update_service','delete_service']);
            $user->syncRoles(['مزود خدمة']);
            $seller = new Seller;
            $seller->user_id = $user->id;
            $seller->number_of_branches = $request->number_of_branches;
            $seller->city_id = $request->city_id ;
            $seller->payment_status = $request->payment_status;
            $seller->save();


            return redirect()->route('sellers.index');
         }
    }

    public function show($id)
    {

        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.sellers'), 'url' => route('sellers.index')],
            ['name' => trans('admin.show'), 'url' => null]

        ];

        $data['page_title'] = trans('admin.sellers');

        $data['user'] = Seller::findOrFail($id);

        return view('dashboard.sellers.show',$data);

    }

    public function edit($id)
    {

         $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.sellers'), 'url' => route('sellers.index')],
            ['name' => trans('admin.edit'), 'url' => null]

        ];

        $data['page_title'] = trans('admin.sellers');
        $data['data'] = Seller::findOrFail($id);
        $data['cities'] = City::get();

        return view('dashboard.sellers.edit',$data);
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [

             'name' => 'required',
             'email' => 'required|email',

         ]);

        $seller = Seller::findOrFail($id);
        $seller->number_of_branches = $request->number_of_branches;
        $seller->city_id = $request->city_id ;
        if($seller->save()){
            $subUsersIds = User::where('user_parent_id', $seller->user_id)->pluck('id')->toArray();
            if(count($subUsersIds)){

                Seller::whereIn('user_id', $subUsersIds)->update(['payment_status' =>  $request->payment_status]);
            }
        }

        $user = $seller->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }

         if($user->save()){

             if($seller->save()){
                 return redirect()->route('sellers.index');
            }
        }
    }

    public function destroy($id)
    {

        $seller = Seller::findOrFail($id);
        // Product::where('user_id', $seller->user_id)->delete();
        Order::where('user_id', $seller->user_id)->delete();
        User::destroy($seller->user->id);
        if(Seller::destroy($id)){

            return back();
        }

        else {

            return back();
        }
    }

    public function login($id)
    {

        $seller = Seller::findOrFail(decrypt($id));

        $user  = $seller->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

}
