<?php

use ActiveRecord\Config;
use ActiveRecord\Connection;
use GuzzleHttp\Client;
use Model\AccountCoin;
use Model\CoinTransactionLog;

require __DIR__.'/src/autoload.php';

class Init extends \Ext\DesignPattern\Singleton {
    protected static $instance;
    protected \GuzzleHttp\Client $client;

    protected function __construct()
    {
        // The expensive process (e.g.,db connection) goes here.
        parent::__construct();
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://majestic.localhost.local',
            // You can set any number of default request options.
            'timeout'  => 5.0,
            'verify' => false
        ]);


        Config::initialize(
            function($cfg)
            {
                $cfg->set_model_directory(__DIR__ . '/src/Model');
                $cfg->set_connections(array('development' => 'mysql://'.\Config\Database::USER_NAME.':'.\Config\Database::PASS.'@'.\Config\Database::HOST_NAME.'/'.\Config\Database::DB_NAME));
                Connection::$datetime_format = 'Y-m-d H:i:s';


            }
        );

        $row = AccountCoin::delete_all([
            'conditions' => ['account_id' => [1,2]]
        ]);
        echo $row;
        CoinTransactionLog::delete_all([
            'conditions' => ['account_id' => [1,2]]
        ]);


        $values = [
            [
                1,19.99
            ],
            [
                2,20
            ],
        ];

        foreach($values as $val)
        {
            $ac = AccountCoin::create(['account_id' => $val[0],'coin_balance' => $val[1]]);
            $ac->save();

        }
    }
    public function client()
    {
        return $this->client;
    }
}