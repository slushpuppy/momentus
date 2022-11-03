<?php


namespace CoinTransaction\Model\Credit;





use Model\AccountCoin;
use Model\CoinTransactionLog;
use Model\Order;

class UnlockProfile extends CoinTransactionLog
{


    public function getAmount()
    {
        return 20;
    }


    public function getTransactionType()
    {
        return static::TRANS_CREDIT;
    }

}