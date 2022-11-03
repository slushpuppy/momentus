<?php


namespace Controller\CoinTransaction\Model;


class TransactionModel
{
    public ?int $orderId = null;
    public ?int $createdBy = null;
    public ?int $updatedBy = null;
    public ?int $expires = null;
    public string $remarks="";
    public string $type;
    public float $amount;
}