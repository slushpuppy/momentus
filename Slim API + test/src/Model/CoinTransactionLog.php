<?php


namespace Model;


use ActiveRecord\Model;
use Ext\Exception\AppErrorException;
use Module\CoinTransaction\Loader;

class CoinTransactionLog extends Model
{
    static $table_name='coin_transaction_log';


    public const TRANS_CREDIT='credit';
    public const TRANS_DEBIT='debit';

    protected Order $order;

    /**
     *
    public function __construct(int $accountId,$data)
    {
    //orderId=123&createdBy=1&updatedBy=1&expires='.$expire.'&remarks=&type=&amount=
    $this->data["account_id"] = $accountId;
    $this->data["created_by"] = $data["createdBy"] ?? '0';
    $this->data["created"] = $data["created"] ?? '0';
    $this->data["updated"] = $data["updated"] ?? 'NOW()';
    $this->data["updated_by"] = $data["updatedBy"] ?? '0';
    $this->data["expires"] = 'FROM_UNIXTIME('. ($data["expires"] ?? time()) . ')';
    $this->data["remarks"] = $data["remarks"] ?? '';

    $this->amount = $data["amount"];


    }
     */

    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true)
    {
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    }

    public function getTransactionType()
    {
        return null;
    }
    public function setOrder(Order $order)
    {
        $this->order = $order;
        $this->set_attributes([
            'account_id' => $order->getAccountId(),
            'created_by' => $order->getAccountId(),
            'created' => $order->getAccountId(),
            'updated_by' => $order->getAccountId(),
            'updated' => $order->getAccountId(),
            'remarks' => $order->getRemarks(),
            'order_id' => $order->getOrderId(),
        ]);
        return $this;
    }

    public function setCreditAccountId(int $accountId)
    {
        $this->set_attributes([
            'account_id' => $accountId,
            'order_id' => 0,
            'created_by' => $accountId,
            'created' => $accountId,
            'updated_by' => $accountId,
            'updated' => $accountId,
        ]);
    }


    public function setType($type)
    {
        $this->type = $type;

    }

    protected function getAmount()
    {
        return null;
    }


    public function adjustAccountCoinBalance(float $current)
    {
        if ($this->validate())
        {
            switch($this->getTransactionType())
            {
                case static::TRANS_DEBIT:
                    return $current + $this->getAmount();
                    break;
                case static::TRANS_CREDIT:
                    return $current - $this->getAmount();
                    break;
                default:
                    throw new AppErrorException("Invalid transaction type");
            }
        }
        throw new AppErrorException("transaction failed");

    }

    static $validates_presence_of = [
        ['account_id'],
        ['order_id'],
    ];


    public function validate()
    {

        switch($this->getTransactionType())
        {
            case static::TRANS_DEBIT:
            case static::TRANS_CREDIT:
                if ($this->getAmount() <= 0)
                {
                    return false;
                }
                break;
            default:
                throw new AppErrorException("Invalid transaction type");
        }

        return true;
    }
}