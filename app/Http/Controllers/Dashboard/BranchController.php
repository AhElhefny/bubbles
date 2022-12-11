<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\Models\Media;

class BranchController extends Controller
{

    public function __construct()
    {
        // $this->middleware('permission:read_branch', ['only' => ['index','show']]);
        // $this->middleware('permission:add_branch', ['only' => ['create', 'store']]);
        // $this->middleware('permission:update_branch', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:delete_branch', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {

        $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
         ];

         $data['page_title'] = trans('admin.branches');
        if(Auth::user()->seller)
        {
         $data['branches'] =Branch::where('seller_id',Auth::user()->seller->id)->orderBy('created_at','desc')->paginate(20);
        }else{
            $data['branches'] =Branch::orderBy('created_at','desc')->paginate(20);
        }

         return view('dashboard.branches.index',$data);
     }

     public function create()
     {

          $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.branches'), 'url' => route('branches.index')],
               ['name' => trans('admin.create'), 'url' => null],
           ];

          $data['page_title'] = trans('admin.branches');
          $data['data']= new Branch;
          $data['cities'] = City::get();
          $data['categories']= Category::all();

           return view('dashboard.branches.create',$data);
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
        $user->seller_type ="branch_manager";

      if($user->save()){

           $branch = new Branch;
           $branch->user_id = $user->id;
           $branch->seller_id = auth()->user()->seller->id;
           $branch->city_id = $request->city_id ;
           $branch->address = $request->address;
           $branch->category_id = $request->category_id;
           $branch->latitude = $request->latitude;
           $branch->langitude = $request->langitude;
           $branch->range_price = $request->range_price;
           $branch->save();

        if($request->hasFile('img')) {

              $branch->addMedia($request->file('img'))
                ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                ->toMediaCollection($branch->mediaImageCollectionName);
         }

         if($request->hasFile('branch_slider')) {
            foreach($request->file('branch_slider') as $slider){
                $branch->addMedia($slider)
              ->withCustomProperties(['root' => 'user_prr'.uniqid()])
              ->toMediaCollection('branch_slider');
            }

       }
             return redirect()->route('branches.index');
        }
    }

    public function show($id)
    {


    }

    public function edit($id)
    {

         $data['breadcrumb'] = [

              ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
              ['name' => trans('admin.branches'), 'url' => route('branches.index')],
              ['name' => trans('admin.edit'), 'url' => null]
         ];

         $data['page_title'] = trans('admin.branches');
         $data['data'] = Branch::findOrFail($id);
         $images = $data['data']->getMedia('branch_slider');
         $slider=[];
         $ids=[];
         foreach($images as $slide){
            if(!empty($slide)){
                $slider[] = asset('media/'.$slide->id.'/'.$slide->file_name);
                $ids[]=$slide->id;
            }
         }
         $data['data']->slider = $slider;
         $data['data']->ids = $ids;
         $data['cities'] = City::get();
         $data['categories']= Category::all();

        return view('dashboard.branches.edit',$data);
    }

    public function update(Request $request, $id)
    {

           $this->validate($request, [

               'name' => 'required',
               'email' => 'required|email',

            ]);

            $branch = Branch::findOrFail($id);
            $branch->user_id = $branch->user_id ;
            $branch->seller_id = auth()->user()->seller->id;
            $branch->city_id = $request->city_id ;
            $branch->address = $request->address;
            $branch->category_id = $request->category_id;
            $branch->latitude = $request->latitude;
            $branch->langitude = $request->langitude;
            $branch->range_price = $request->range_price;
            $user = $branch->user;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;

            if(strlen($request->password) > 0){

               $user->password = Hash::make($request->password);
           }
        //    dd($request->all());
           if($user->save()){

              if($branch->save()){

                if ($request->get('img_remove') || $request->hasFile('img')) {
                       $branch->clearMediaCollection($branch->mediaImageCollectionName);
                  }

               if ($request->hasFile('img')) {
                   $branch->addMedia($request->file('img'))
                     ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                     ->toMediaCollection($branch->mediaImageCollectionName);
                }

                if($request->hasFile('branch_slider'))
                {
                    $oldslider = $request->oldslider;
                    foreach($request->file('branch_slider') as $key=>$slide){
                        if(!empty($slide)){
                            Media::where('id',$oldslider[$key])->delete();
                            $branch->addMedia($slide)->toMediaCollection('branch_slider');
                        }
                    }
                }
                 return redirect()->route('branches.index');
             }
         }

    }

    public function destroy($id)
    {

        $branch = Branch::findOrFail($id);
        Order::where('user_id', $branch->user_id)->delete();
        User::destroy($branch->user->id);
        if(Branch::destroy($id)){

            return back();
        }
        else {
             return back();
        }
    }

}
