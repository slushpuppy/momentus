<?php


namespace CoinTransaction\Model\Debit;


use Model\CoinTransactionLog;

class CashDeposit  extends  CoinTransactionLog
{
    public function getTransactionType()
    {
        return static::TRANS_DEBIT;
    }


    public function getAmount()
    {
        return $this->order->getAmount();
    }
}