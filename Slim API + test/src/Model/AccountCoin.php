<?php


namespace Model;


use ActiveRecord\Model;

class AccountCoin extends Model
{
    public static $table_name='account_coin';



    public function validate()
    {
        return $this->coin_balance > 0;
    }
}