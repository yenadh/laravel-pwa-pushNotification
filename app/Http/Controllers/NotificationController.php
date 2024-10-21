<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class NotificationController extends Controller
{
    public function saveSubscription(Request $request)
    {
        $items = new Notification();
        $items->subscriptions = json_decode($request->sub);
        $items->save();

        return response()->json(['message' => "Added successfully"], 200);
    }

    public function sendNotification(Request $request)
    {
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:yenadh@mail.com',
                'publicKey' => 'BNSR0cVgFTexMi-vsaPqYIYW2wIDzeFmwYF9PYSD1G8T6d9G6ZhoxzseDRU2RPg81Jbf7CtQTzPypLyVcUdbO68',
                'privateKey' => 'uMYPJMcH4C6bfagWqCM5eyLvdJuTn99SKkfVN0xTvK4',
            ],
        ];

        $webPush = new WebPush($auth);

        $payload = json_encode([
            'title' => $request->title,
            'message' => $request->message,
            'url' => $request->url
        ]);

        $notifications = Notification::all();
        foreach ($notifications as $notification) {
            $webPush->sendOneNotification(
                Subscription::create($notification->subscriptions),
                $payload,
                ['TTL' => 5000]
            );
        }
    }
}
