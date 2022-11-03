<?php


namespace CoinTransaction;


use ActiveRecord\Model;
use CoinTransaction\Model\Credit\UnlockProfile;
use CoinTransaction\Model\Debit\CashDeposit;
use Model\CoinTransactionLog;
use Model\Order;

class CoinTransactionFactory
{
    protected CoinTransactionLog $transaction;
    protected ?Order $order;

    public function __construct(Order $order = null)
    {
        if ($order)
        $this->order = $order;
    }

    public function runTransaction(string $transactionType)
    {
        switch($transactionType)
        {
            case 'unlock-profile':
                $this->transaction = new UnlockProfile();
                $this->transaction->setType('unlock-profile');
                break;
            case 'cash-deposit':
                $this->transaction = new CashDeposit();
                $this->transaction->setOrder($this->order);
                $this->transaction->setType('cash-deposit');
                break;
            default:
                return null;
        }
        return $this->transaction;
    }

    public function getTransactionModel()
    {
        return $this->transaction;
    }

    public function getOrder()
    {
        return $this->order;
    }



}

