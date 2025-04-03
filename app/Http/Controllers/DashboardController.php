<?php

namespace App\Http\Controllers;

use App\Models\DynamoMusic;
use App\Models\DynamoUser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    public function index(){

        $user = auth()->user();

        $dynamoUser = DynamoUser::findByEmail($user->email);


        //dd($dynamoUser);

        return Inertia::render('Dashboard',[
            'user' => $user->user_name,
            'email' => $user->email,
            'subscriptions' => $user->subscriptions,
        ]);
    }
    public function showAllMusic()
    {
        $music = DynamoMusic::showAllMusic();

        return Inertia::render('Dashboard', [
            'music' => $music,
        ]);
    }

}
