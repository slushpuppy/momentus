<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:25 PM
 */

namespace Module\OAuth;


class Scope
{
    public const PROFILE='profile';
    public const SOCIAL_MEDIA='social';
    public const GARAGE='garage';


    public const Indexes = [
        'profile' => 1,
        'social' => 2,
        'garage' => 3,
    ];
}