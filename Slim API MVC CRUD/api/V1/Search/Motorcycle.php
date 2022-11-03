<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 6:29 PM
 */

namespace api\V1\Search;
use api\V1\Garage\Model\Vehicle;
use Api\V1\Model\Response;
use api\V1\Search\Model\MotorcycleModel;
use api\V1\Search\Model\ResponseMotorcycleSearch;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Motorcycle extends \Api\V1\AbstractRestController
{
    private static $_i;
    //protected static $permissionScope = Scope::PROFILE;

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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Get(
     *     path="/search/motorcycle",
     *     summary="search for bike model",
     *     description="Get Bike Information",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="full_search",
     *     required=true,
     * @OA\Schema(type="string"),
     *     in="path"
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Response after Creating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseMotorcycleSearch")
     * )
     * )
     * )
     *
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $db = \Lib\Core\Helper\Db\Conn::i()->get();
        $res = new ResponseMotorcycleSearch();
        try
        {
        if ($_GET['full_search']) {

                $stmt = $db->prepare(
                    '  select family_name, manufacturer_name, model_id, model_name,model_year, SUM(rel) as total_rel from
	  ((SELECT f.id as family_id,f.name as family_name,mr.id as manufacturer_id,mr.name as manufacturer_name,mm.id as model_id,mm.name as model_name,mm.year as model_year,MATCH (mr.name) AGAINST (?  IN BOOLEAN MODE) as rel
 FROM motorcycle_manufacturer mr left join motorcycle_family f on f.manufacturer_id=mr.id
  left join `motorcycle_model` mm on f.id=mm.motorcycle_family_id where  MATCH (mr.name) AGAINST (?  IN BOOLEAN MODE))
  UNION ALL
	  	  (SELECT f.id as family_id,f.name as family_name,mr.id as manufacturer_id,mr.name as manufacturer_name,mm.id as model_id,mm.name as model_name,mm.year as model_year,MATCH (f.name) AGAINST (?  IN BOOLEAN MODE) as rel
 FROM motorcycle_family f 
 left join motorcycle_manufacturer mr on
 f.manufacturer_id=mr.id 
 left join 
 `motorcycle_model` mm on f.id=mm.motorcycle_family_id where  MATCH (f.name) AGAINST (?  IN BOOLEAN MODE))
  UNION ALL
  	  (SELECT f.id as family_id,f.name as family_name,mr.id as manufacturer_id,mr.name as manufacturer_name,mm.id as model_id,mm.name as model_name,mm.year as model_year,(MATCH (mm.name) AGAINST (?  IN BOOLEAN MODE) + (CAST(? LIKE concat(\'%\',year,\'%\') AS SIGNED INTEGER)*5))  as rel
 FROM `motorcycle_model` mm  
 left join motorcycle_family f on f.id=mm.motorcycle_family_id
 left join  motorcycle_manufacturer mr on f.manufacturer_id=mr.id 
 where  MATCH (mm.name) AGAINST (?  IN BOOLEAN MODE))) as x group by model_id order by total_rel DESC LIMIT 10');
                $search_str = $_GET['full_search'];//\str_replace(" ",",",$_GET['full_search']);
                $stmt->bind_param('sssssss', $search_str, $search_str, $search_str, $search_str, $search_str, $search_str, $search_str);
                $stmt->execute();
                $row = [];
                $stmt->stmt_bind_assoc($row);
                while ($stmt->fetch())
                {
                    $veh = new MotorcycleModel();
                    $veh->id = $row['model_id'];
                    $veh->name = \str_replace("  ", " ", sprintf('%s, %s %s (%s)',
                        $row['manufacturer_name'],
                        $row['family_name'],
                        $row['model_name'],
                        $row['model_year']));
                    $res->data[] = $veh;
                }
            }

        } catch (\Throwable $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
        } finally {
            return $response->withJson($res);
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