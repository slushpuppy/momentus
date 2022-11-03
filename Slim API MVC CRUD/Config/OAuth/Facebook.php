<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 31/7/2018
 * Time: 8:10 PM
 */

namespace Config\OAuth;


class Facebook
{
    const APP_ID = '226106838094482';
    const SECRET_KEY = '0a9ed2c7b3d493ffc97ddaaa40e0014c';
 //   const PROFILE_FIELDS = 'id,address,age_range,birthday,email,gender,name,first_name,picture';
    const PROFILE_FIELDS = 'email,id,gender,first_name,last_name,picture';
    const TEMP_EMAIL_DOMAIN = '@facebook-validating.rust.bike';
}