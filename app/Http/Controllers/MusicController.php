<?php

namespace App\Http\Controllers;

use App\Models\DynamoMusic;
use App\Models\DynamoUser;
use Aws\DynamoDb\Marshaler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MusicController extends Controller
{

    
    public function subscription()
    {
        $user = Auth::user();
        $email = $user->email;

        $dynamoUser = DynamoUser::findByEmail($email);
        $subscriptions = $dynamoUser->subscriptions ?? [];

        return Inertia::render('Subscriptions', [
            'auth' => [
                'user' => ['user_name' => $user->user_name, 'email' => $user->email],
            ], 
            'subscriptions' => $subscriptions,
            'user' => $user->name ?? $user->email,
        ]);
    }

    public function subscribe(Request $request ){
        $success = DynamoUser::subscribe([
            'title' => $request->title,
            'album' => $request->album,
            'year' => $request->year,
            'artist' => $request->artist,
            'img_url' => $request->img_url,
        ]);
        return to_route('dashboard');

    }

    public function unsubscribe(Request $request ){
        $success = DynamoUser::unsubscribe($request->title, $request->album);

        return to_route('subscription');

    }

    public function query(Request $request){
    dd ($request);
    }
}
