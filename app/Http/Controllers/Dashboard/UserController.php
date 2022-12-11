<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Hash;
use Alert;


class UserController extends Controller
{
   /* public function __construct()
    {
        $this->middleware('permission:read_user', ['only' => ['index','show']]);
        $this->middleware('permission:add_user', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    }
    */
    public function index()
    {

        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
        ];

        $data['page_title'] = trans('admin.users_staf');
        $data['users']= User::whereNotIn('user_type',['customer','seller'])->where('id','!=',1)->orderBy('created_at','desc')->paginate(20);

        return view('dashboard.users.index', $data);
    }

    public function create()
    {

        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.users_staf'), 'url' => route('users.index')],
            ['name' => trans('admin.create'), 'url' => null],
        ];

        $data['page_title'] = trans('admin.users_staf');
        $data['data'] = new User;
        $data['roles'] = Role::whereNotIn('id',[1,2])->get();

        return view('dashboard.users.create_edit', $data);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), (new User)->rules());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
//        if($request->role_id == 1){
//
//           $user->user_type = "admin";
//
//        }
//        elseif($request->role_id == 2){
//
//            $user->user_type = "seller";
//        }
//        else{
//
//            $user->user_type = "staff";
//        }
        $user->user_type = 'admin';
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole($request->role_id);

        return  redirect()->route('users.index')->with('success','تم الانشاء بنجاح');

    }

    public function show($id)
    {

          $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.users_staf'), 'url' => route('users.index')],
               ['name' => trans('admin.show'), 'url' => null]
          ];

          $data['page_title'] = trans('admin.users_staf');
          $data['user']= User::findOrFail($id);

          return view('dashboard.users.show', $data);
    }

     public function edit($id)
     {

         $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
             ['name' => trans('admin.users_staf'), 'url' => route('users.index')],
             ['name' => trans('admin.edit'), 'url' => null]

         ];

        $data['page_title'] = trans('admin.users_staf');
        $data['data']= User::findOrFail($id);
        $data['roles'] = Role::whereNotIn('id',[1,2])->get();

        $data['role_id']=$data['data']->roles->count()>0?$data['data']->roles[0]->id:null;
         return view('dashboard.users.create_edit', $data);

    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(),
         [
            'name' => 'required|string',
            'password' => 'nullable|min:6|confirmed',

         ]);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $update = User::find($id);
        $update->name = $request->name;
        $update->email = $request->email;

        if($request->role_id == 1){

            $update->user_type = "admin";
         }

         elseif($request->role_id == 2){

            $update->user_type = "seller";
          }

         else{

            $update->user_type = "staff";
         }

        if($request->password)
        {

            $update->password = Hash::make($request->password);
        }

        $update->save();
        $update->syncRoles([$request->role_id]);

        if($update)
        {
            return  redirect()->route('users.index')->with('success','تم التعديل بنجاح');
        }

    }

    public function destroy($id)
    {

        $delete = User::destroy($id);

        return redirect()->route('users.index');
    }


    public function editProfile($id)
    {

         $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
             ['name' => trans('admin.edit'), 'url' => null]

          ];

         $data['page_title'] =trans('admin.editprofile');
         $data['data']= User::findOrFail($id);

         return view('dashboard.users.edit_profile', $data);

   }

   public function updateProfile(Request $request, $id)
   {

       $request->validate([

            'name' => 'required',
           'email' => 'required',

       ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::find($id);
        $user->update($input);

        if($user){

        return  redirect()->route('users.index')->with('success','تم التعديل بنجاح');

        }
    }


    public function getusers(Request $request){

        $search = $request->search;

        if($search == ''){

           $users = User::where('user_type','=','customer')->select('id','mobile','name')->get()->take(500);

        }else{
           $users = User::where('user_type','=','customer')->select('id','mobile','name')->where('name', 'like', '%' .$search . '%')->get();
        }

        $response = array();

        foreach($users as $user){

            $response[] = array(

                "id"=>$user->id,
                "text"=>$user->name
           );
        }

        return response()->json($response);
    }

}
