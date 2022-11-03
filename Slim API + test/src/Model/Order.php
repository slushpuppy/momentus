<?php


namespace Model;


use ActiveRecord\Model;

class Order extends Model
{
    public static $table_name='order';


    public function getAmount()
    {
        return $this->amount;
    }
    public function getAccountId()
    {

        return $this->account_id;
    }
    public function getOrderId()
    {
        return $this->id;
    }
    public function getOrderDate()
    {
        return $this->order_date;
    }

    public function getRemarks()
    {
        return 'remarks from order';
    }

}