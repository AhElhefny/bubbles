<?php


namespace App\Classes;

use App\Models\Notification as NotificationModel;
use App\Models\NotificationSubscription;
use App\Models\Template;
use App\Models\UserNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Notification
{

    public static function replaceNotificationsTemplateVariables($content, $user = null, $order = null)
    {
        if ($order && (strpos($content, '{order_number}') !== false || strpos($content, '{order_status}'))) {
            $content = str_replace('{order_number}', $order->order_number, $content);
            $content = str_replace('{order_status}', $order->order_number, $content);

        }
        if ($user && strpos($content, '{customer_name}') !== false) {
            $content = str_replace('{customer_name}', $user->name, $content);
        }
        $driver = $order->driver;
        if ($order && $driver && (strpos($content, '{driver_name}') !== false || strpos($content, '{driver_number}') !== false)) {
            $content = str_replace('{driver_name}', $driver->name, $content);
            $content = str_replace('{driver_number}', $driver->mobile, $content);
        }

        return $content;
        //order_number, customer_name, driver_name, driver_number, order_status
    }

    public static function sendOneNotificationByStatus($status, $user, $order = null, $product = null){
        $template = Template::where('slug', $status)->first();
        if(!$template){
            return false;
        }
        if(!$template->title || !$template->content){
            return false;
        }
        $title = self::replaceNotificationsTemplateVariables($template->title, $user, $order);
        $content = self::replaceNotificationsTemplateVariables($template->content, $user, $order);
        $notificationSubscription = NotificationSubscription::where('user_id', $user->id)->first();
        $usersIds = [];
        if($notificationSubscription){
            $usersIds[] = $notificationSubscription->player_id;
            $notification = NotificationModel::create([
                'group' => 'private',
                'title' => json_encode(['ar' => $title]),
                'content' => json_encode(['ar' => $content]),
                'status' => 0,
                'send_at' => Carbon::now()
            ]);

            UserNotification::create(['user_id' => $user->id, 'notification_id' => $notification->id, 'status' => 1]);
        }

           return self::sendNewNotification($content, $title, $usersIds);
    }

    public static function sendMultiNotification($notificationData){
        $playerIds = [];
        $usersIds = [];
        $sentUsersIds = $notificationData->users()->where('pivot_status', 1)->pluck('users.id')->toArray();
        if ($notificationData->group == 'auth_users'){
            $usersIds = User::whereNotIn('id', $sentUsersIds)->take(100)->pluck('id')->toArray();
            $playerIds = NotificationSubscription::whereIn('user_id', $usersIds)->pluck('player_id')->toArray();
        }elseif ($notificationData->group == 'select_user'){
            $usersIds = $notificationData->users()->where('pivot_status', 0)->take(100)->pluck('users.id')->toArray();
            $playerIds = NotificationSubscription::whereIn('user_id', $usersIds)->pluck('player_id')->toArray();
        }elseif ($notificationData->group == 'guests'){
            $playerIds = NotificationSubscription::whereNotIn('user_id', $sentUsersIds)->pluck('player_id')->toArray();
        }else{
            $usersIds = User::whereNotIn('id', $sentUsersIds)->take(100)->pluck('id')->toArray();
            $playerIds = NotificationSubscription::whereIn('user_id', $usersIds)->pluck('player_id')->toArray();
        }

        if(count($playerIds) <= 0){
            $notificationData->status = 1;
            $notificationData->save();
        }

        $syncUsers = [];
        foreach ($playerIds as $userId){
            $syncUsers[$userId] = [
                'status' => 1,
            ];
        }

        $notificationData->users()->sync($syncUsers);

        return self::sendNewNotification($notificationData->content, $notificationData->title, $playerIds);
    }

    public static function sendNewNotification($content, $title = null, $usersIds = [], $sendToAll = false)
    {
        if ($sendToAll) {
            \OneSignal::sendNotificationToAll($content);
            return true;
        }

        if (!empty($usersIds)) {
            $params = [];
            $params['include_player_ids'] = $usersIds;
            $contents = [
                "en" => $content,
            ];
            $params['contents'] = $contents;

            \OneSignal::sendNotificationCustom($params);
            return true;
        }
        return false;
    }

}
