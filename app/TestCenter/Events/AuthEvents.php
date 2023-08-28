<?php

namespace App\TestCenter\Events;

use App\Models\User;
use App\Models\SystemUser;
use App\Models\Category;
use App\Models\Role;
use App\Models\Verification;
use App\TemaFirst\Services\Sms;
use App\TemaFirst\Services\OneSignal;
use App\TemaFirst\Services\Twilio;
use App\TemaFirst\Utilities\Constants;
use Illuminate\Support\Facades\Log;

class AuthEvents
{
	public static function userHasLoggedIn(User $user)
	{

	}

	public static function userHasRegistered(User $user)
	{

	}
}
