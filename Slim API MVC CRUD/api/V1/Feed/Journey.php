<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 6:29 PM
 */

namespace api\V1\Search;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Journey extends \Api\V1\AbstractRestController
{
    private static $_i;

    private function __construct()
    {
    }

    public static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $db = \Lib\Core\Helper\Db\Conn::i()->get();
        $return = [];
        if ($_GET['full_search']) {
            $stmt = $db->prepare(
                'SELECT f.id as family_id,f.name as family_name,mr.id as manufacturer_id,mr.name as manufacturer_name,mm.id as model_id,mm.name as model_name,mm.year as model_year FROM `motorcycle_model` mm left join 
motorcycle_family f on mm.motorcycle_family_id=f.id left join 
manufacturer mr on f.manufacturer_id=mr.id where 
MATCH (mr.name) AGAINST (? IN NATURAL LANGUAGE MODE) OR
 MATCH (f.name) AGAINST (? IN NATURAL LANGUAGE MODE) OR
  MATCH (mm.name) AGAINST (? IN NATURAL LANGUAGE MODE) order by mm.year desc limit 10');
            $stmt->bind_param('sss',$_GET['full_search'],$_GET['full_search'],$_GET['full_search']);
            $stmt->execute();
            $row = [];
            $stmt->stmt_bind_assoc($row);
            while ($stmt->fetch()) {
                $return[] = [
                    'id' => $row['model_id'],
                    'bike' => sprintf('%s, %s %s (%s)',
                        $row['manufacturer_name'],
                        $row['family_name'],
                        $row['model_name'],
                        $row['model_year'])
                ];
            }
            return $response->withJson($return);
        }


    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement post() method.
    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement patch() method.
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}