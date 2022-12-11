<?php

namespace App\Http\Controllers\Api\V3;
use App\Http\Controllers\Controller;
use App\Http\Resources\V3\NotificationResource;
use App\Models\Notification;
use App\Models\NotificationSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    function index(Request $request)
    {
       
        $notificationsIds = [];
        
        $lastTimeUnreadNotification = Carbon::now()->toDateString();
        
        if(auth()->check()){

            $authUser = auth()->user();
            $lastTimeUnreadNotification = $authUser->notifications_read_at?:Carbon::now()->toDateString();
            if($request->get('opened')){
                $authUser->update(['notifications_read_at' => Carbon::now()]);
            }
            $notificationsIds = array_merge($notificationsIds, Notification::whereHas('users', function ($q){
                $q->where('user_id', auth('api')->id());
            })->pluck('id')->toArray());

            if(auth()->check()){

              $notificationsIds = array_merge($notificationsIds, Notification::where('group', 'auth_users')->pluck('id')->toArray());
            
            }else{
 
               $notificationsIds = array_merge($notificationsIds,Notification::where('group', 'guests')->pluck('id')->toArray());
          }

          }else{

             $notificationsIds = array_merge($notificationsIds,Notification::where('group', 'not_auth')->pluck('id')->toArray());
         }

         $notificationsList = Notification::orderBy('id', 'desc')->whereIn('id', $notificationsIds)->orWhere('group', 'all')->paginate(10);
         $unreadNotificationsCount = $notificationsList->where('created_at', '>', $lastTimeUnreadNotification)->count();
         $data = NotificationResource::collection($notificationsList);
        
         return $this->sendResponse(['unread' => (int)$unreadNotificationsCount, 'last_notifications_read_at' => $lastTimeUnreadNotification,'list' => $data], trans('messages.get_data_success'));
     }

    function subscribe(Request $request)
    {
      
        $validator = Validator::make($request->all(), [

            'player_id' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            
            return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
        }

        
        $authUserId = auth('api')->user()?auth('api')->user()->id:null;
        
        $data = $request->all();
        $data['user_id'] = $authUserId;
        NotificationSubscription::updateOrCreate(['player_id' => $request->player_id], ['user_id' => $data['user_id']]);
        // dd($request->headers->get('Authorization'),$authUserId);
        
        return $this->sendResponse($data, trans('messages.subscribed_successfully'));
    }

}
