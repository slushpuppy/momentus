<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 6:29 PM
 */

namespace api\V1\Search;
use Api\V1\Model\Response;
use api\V1\Search\Model\MotorcycleModel;
use api\V1\Search\Model\PartModel;
use api\V1\Search\Model\ResponseMotorcycleSearch;
use api\V1\Search\Model\ResponsePartSearch;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Part extends \Api\V1\AbstractRestController
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
     *     path="/search/part",
     *     summary="search for bike part",
     *     description="Get Bike part Information",
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
     *     @OA\JsonContent(ref="#/components/schemas/ResponsePartSearch")
     * )
     * )
     * )
     *
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $db = \Lib\Core\Helper\Db\Conn::i()->get();
        $res = new ResponsePartSearch();
        try
        {
        if ($_GET['full_search']) {

                $stmt = $db->prepare(
                    ' select part_name, family_name, part_id, SUM(rel) as total_rel from
	  ((select p.id as part_id, p.name as part_name,f.name as family_name,(MATCH (f.name) AGAINST (?  IN BOOLEAN MODE)) as rel from motorcycle_part p left join motorcycle_part_family f on f.id=p.family_id where MATCH (f.name) AGAINST (?  IN BOOLEAN MODE)
)
  UNION ALL
	  	  (
		 	  (select p.id as part_id, p.name as part_name,f.name as family_name,(MATCH (p.name) AGAINST (?  IN BOOLEAN MODE)) as rel from motorcycle_part p left join motorcycle_part_family f on f.id=p.family_id where MATCH (p.name) AGAINST (?  IN BOOLEAN MODE) 
		  ))
) x_tbl group by part_id order by total_rel DESC LIMIT 10');
                $search_str = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $_GET['full_search']).'*';
                $stmt->bind_param('ssss', $search_str, $search_str, $search_str, $search_str);
                $stmt->execute();
                $row = [];
                $stmt->stmt_bind_assoc($row);
                while ($stmt->fetch())
                {
                    $veh = new PartModel();
                    $veh->id = $row['part_id'];
                    $veh->name = \str_replace("  ", " ", sprintf('%s %s',
                        $row['family_name'],
                        $row['part_name']));
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