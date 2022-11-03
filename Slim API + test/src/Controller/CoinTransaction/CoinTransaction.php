<?php


namespace Controller\CoinTransaction;

use ActiveRecord\Config;
use ActiveRecord\Connection;
use CoinTransaction\CoinTransactionFactory;
use Ext\Exception\ApiServerException;
use Ext\Exception\AppErrorException;
use Model\accountCoin;
use Model\coinTransactionLog;
use Model\Order;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Ext\Libs\BaseController;

class CoinTransaction extends BaseController
{

    public function __construct(ContainerInterface $container) {
        parent::__construct($container);

        Config::initialize(
            function($cfg)
            {
                $cfg->set_model_directory(__DIR__ . '/../../Model');
                $cfg->set_connections(array('development' => 'mysql://'.\Config\Database::USER_NAME.':'.\Config\Database::PASS.'@'.\Config\Database::HOST_NAME.'/'.\Config\Database::DB_NAME));
                Connection::$datetime_format = 'Y-m-d H:i:s';


            }
        );

    }

    function getBalance(Request $request, Response $response,$args)
    {
        try {
            $coinAcc = accountCoin::find([
                'account_id'=> $this->getLoggedInAccountId()
            ]);



            $dataResponse = [];

            $dataResponse["data"] = [];
            if ($coinAcc)
            {
                $dataResponse["data"][] = [
                    "accountId" => $coinAcc->account_id,
                    "coinBalance" => $coinAcc->coin_balance
                ];
            }


            $dataResponse["status"] = "ok";



        } catch (ApiServerException $e)
        {
            \error_log(print_r($e,TRUE),0);
            $dataResponse["status"] = "error";
        }


        return $this->withJson($response,$dataResponse);

    }

    public function deposit($request,$response,$args)
    {
        return $this->_transaction($request,$response,$args,'Debit');
    }
    public function credit($request,$response, $args)
    {
        return $this->_transaction($request,$response,$args,'Credit');
    }

    protected function _transaction($request, $response, $args,$transType)
    {
        try {
            $type = $args['type'];


            \parse_str($request->getBody()->getContents(),$req);

            $req = array_filter($req);

            if ($transType == 'Debit')
            {
                if (!isset($req,$req["order_id"]))
                {
                    throw new AppErrorException("Order ID not found");
                }


                $order = $this->getOrder($req["order_id"]);
                if (!isset($order))
                {
                    throw new AppErrorException("order not found");
                }

                if (!$this->isValidLoggedInUser($order->getAccountId()))
                {
                    throw new AppErrorException("no permission");

                }


                $transFactory = new CoinTransactionFactory($order);

            }
            else
            {
                $transFactory = new CoinTransactionFactory();
            }

            $model = $transFactory->runTransaction($type);


            if (!$model)
            {
                throw new AppErrorException($transType." type not found");
            }
            if ($transType == 'Credit')
            {
                $model->setCreditAccountId($this->getLoggedInAccountId());
            }


            if (!CoinTransactionLog::transaction(
                function () use ($model,$transFactory)
                {



                    if ($model->is_valid())
                    {

                        $model->save(true);

                        $coinAcc = AccountCoin::find([
                            'account_id'=> $this->getLoggedInAccountId()
                        ]);

                        $coinAcc->coin_balance = $model->adjustAccountCoinBalance($coinAcc->coin_balance);


                        //if ($coinAcc->is_valid()) //is_valid() bug not working for class reuse
                        if ($coinAcc->validate())
                        {
                            $coinAcc->save();
                        }
                        else throw new AppErrorException('Not enough coin');

                    }

                    return true;

                }
            ))
            {
                throw new ApiServerException("Mysql Error");
            }



            $dataResponse["status"] = "ok";
            $dataResponse["data"] = "updated";

        } catch (ApiServerException $e)
        {
            \error_log(print_r($e,TRUE),0);

            $dataResponse["status"] = "server error";
        }
        catch (AppErrorException | \Exception $e)
        {
            \error_log(print_r($e,TRUE),0);

            $dataResponse["status"] = "app_error";
            $dataResponse["data"] = $e->getMessage();
        }


        return $this->withJson($response,$dataResponse);

    }

    /**
     * Accessor method for current logged in account_id
     */
    protected function getLoggedInAccountId()
    {
        return 1;
    }

    /**
     * Accessor method for Order Model object
     * @return Order
     */
    protected function getOrder(int $orderId)
    {
        return Order::find(['id' => $orderId]);
    }

    protected function isValidLoggedInUser(int $accountId)
    {
        return $this->getLoggedInAccountId() === $accountId;
    }


}