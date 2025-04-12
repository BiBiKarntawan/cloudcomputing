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

    public function index(){

        $user = auth()->user();

        $dynamoUser = DynamoUser::findByEmail($user->email);
        $music = DynamoMusic::showAllMusic();
        $subscriptions = $dynamoUser->subscriptions ?? [];
        
        return Inertia::render('Dashboard',[
            'auth' => [
                'user' => ['user_name' => $user->user_name, 'email' => $user->email],
            ],            
            'user' => $user->user_name,
            'email' => $user->email,
            'music' => [],
            'subscriptions_list' => $subscriptions,
        ]);
    }

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
        $title = $request->title;
        $album = $request->album;
        $artist = $request->artist;
        $year = $request->year;

        $formattedTitle = $title ? ucfirst($title) : null;
        $formattedAlbum = $album ? ucfirst($album) : null;
        $formattedArtist = $artist ? ucfirst($artist) : null;

        $results = DynamoMusic::query($formattedTitle, $formattedAlbum, $formattedArtist, $year) ?? [];

        $user = Auth::user();
        $email = $user->email;
        $dynamoUser = DynamoUser::findByEmail($email);
        $subscriptions = $dynamoUser->subscriptions ?? [];

        foreach ($results as &$item) {
            $isSubscribed = false;
            foreach ($subscriptions as $sub) {
                if (isset($sub['title']) && isset($sub['album']) && 
                    $sub['title'] === $item['title'] && $sub['album'] === $item['album']) {
                    $isSubscribed = true;
                    break;
                }
            }
            
            $item['is_subscribed'] = $isSubscribed;
        }

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => ['user_name' => $user->user_name, 'email' => $user->email],
            ],
            'music' => $results,
            'search_params' => [
                'title' => $formattedTitle ?? '',
                'album' => $formattedAlbum ?? '',
                'artist' => $formattedArtist ?? '',
                'year' => $year ?? '',
            ],
            // 'subscriptions' => $subscriptions,
            'subscriptions_list' => $subscriptions,
            'user' => $user->name ?? $user->email,
        ]);
    }
}
