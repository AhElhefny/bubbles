<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Http\service\push;
use App\Models\NotificationSubscription;
use Illuminate\Http\Request;
use App\Models\Appnotification;
use App\User;
use App\Models\UserNotification;
use App\Models\CityUser;
use App\Models\City;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Validation\Rule;

class AppNotificationController extends Controller
{
use push;
      public function index()
      {
          $data['breadcrumb'] = [

                ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
                ['name' => trans('admin.app_notifications'), 'url' => null],
           ];

           $data['page_title'] = trans('admin.app_notifications');
           $data['apps'] = Appnotification::where('group', '!=', 'private')->orderBy('created_at','desc')->get();

           return view('dashboard.appnotification.index', $data);
      }

      public function create()
      {

          $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.app_notifications'), 'url' => route('notification-messages.index')],
               ['name' => trans('admin.create'), 'url' => null],
          ];

          $data['page_title'] = trans('admin.app_notifications');
          $data['data'] = new Appnotification;

          return view('dashboard.appnotification.create_edit', $data);
      }

      public function store(Request $request)
      {
          $rules = [
            'title' => ['required', 'min:3','max:255'],
            'content' => ['required','min:10'],
            'group' => ['required'],
            'users' => [Rule::requiredIf($request->group == "select_user")]
          ];
          $validator = Validator::make($request->all(),$rules);
          if($validator->fails()){
              return back()->withErrors($validator->errors());
          }
          $Appnotification = new Appnotification;
          $Appnotification->title= $request->title;
          $Appnotification->content = $request->get('content');
          $Appnotification->status = 1;
          $Appnotification->group = $request->group;
          $Appnotification->save();

          if($request->group == "all"){
              $usersToken = NotificationSubscription::whereNotNull('player_id')->pluck('player_id')->toArray();
              $usersIds = NotificationSubscription::whereNotNull(['player_id','user_id'])->pluck('user_id')->toArray();

          }elseif ($request->group == "auth_users"){
              $usersToken = NotificationSubscription::whereNotNull('user_id')->pluck('player_id')->toArray();
              $usersIds = NotificationSubscription::whereNotNull(['player_id','user_id'])->pluck('user_id')->toArray();

          }elseif ($request->group == "guests"){
              $usersToken = NotificationSubscription::whereNull('user_id')->pluck('player_id')->toArray();
          }else{
              $usersToken = NotificationSubscription::whereIn('user_id',$request->users)->pluck('player_id')->toArray();
              $usersIds = $request->users;
          }
            foreach($usersIds as $id)
            {
               $insert = new  UserNotification;
               $insert->user_id= $id;
               $insert->notification_id = $Appnotification->id;
               $insert->save();
            }
         if ($request->hasFile('image')) {
            $Appnotification->addMedia($request->file('image'))
                ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                ->toMediaCollection('images');
           }
         $result = $this->send_notification($Appnotification->title,$Appnotification->content,$Appnotification,$usersToken);
          return  redirect()->route('notification-messages.index')->with('success','تم التعديل بنجاح');
       }

      public function showusers()
      {

          $data['breadcrumb'] = [

               ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
               ['name' => trans('admin.users'), 'url' => route('notification-messages.index')],
               ['name' => trans('admin.users'), 'url' => null],
          ];

          $data['page_title'] = trans('admin.users');
          $data['users'] =  UserNotification::all();

          return view('dashboard.appnotification.show', $data);
     }

     public function getusers()
     {

         $users = User::where('user_type','=','customer')->get()->pluck('mobile','id');
         return response()->json($users);
     }

//     public function edit($id){
//         $data['breadcrumb'] = [
//
//             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
//             ['name' => trans('admin.app_notifications'), 'url' => route('notification-messages.index')],
//             ['name' => trans('admin.edit'), 'url' => null],
//         ];
//
//         $data['page_title'] = trans('admin.app_notifications');
//          $app = Appnotification::find($id);
//          if(!$app){
//              return back();
//          }
//          $data['data'] = $app;
//          return view('dashboard.appnotification.create_edit',$data);
//     }

    public function destroy($id){
        $app = Appnotification::find($id);
        if(!$app){
            return back();
        }
        $app->delete();
        return back()->with(['message'=>'تم الحذف بنجاح']);
    }
}
