<?php
/**
 * Always call subdirectory autoload first
 */
require_once(__DIR__.'/../autoload.php');

use \Lib\Core\Helper\Db;
use \Lib\Core\Helper\Http\Scraper;

$conn = Db\Conn::i()->get();

$years = [];
$start = 1930;
$end = \intval(\date('Y')) + 1;

for (;$start <= $end; $start++) {
    $years[] = $start;
}
/**
 * types
 */
$types = ['Street' => 1,'Dirt' => 4];
//$types = ['Dirt' => 4];

/**
 * Motorbike manufacturer list URL: https://www.revzilla.com/api/v0/vehicle-makes?type=1&year=2018
 */
foreach ($years as $year) {
    foreach($types as $name => $type_id) {
        $tmp = @Scraper::i()->fetch('https://www.revzilla.com/api/v0/vehicle-makes?type='.$type_id.'&year='.$year);
        if ($tmp)
        $manufacturers[$type_id][$year] = json_decode($tmp);
    }


    sleep(1);
}
/**
 * Motorbike mode list URL: https://www.revzilla.com/api/v0/vehicle-models?make=22&type=4&year=1969
 */
$stmt_manufacturer = $conn->prepare('insert ignore into manufacturer(`name`) VALUES(?)');
$stmt_family = $conn->prepare('insert ignore into motorcycle_family(`name`,`manufacturer_id`) VALUES(?,(select id from manufacturer where name LIKE ?))');
$stmt_model = $conn->prepare('insert into motorcycle_model(`name`,`year`,`motorcycle_family_id`,`motorcycle_type_id`) VALUES(?,?,(select id from motorcycle_family where name LIKE ? AND manufacturer_id=(select id from manufacturer where name LIKE ?)),(select id from motorcycle_type where name LIKE ?))  ON DUPLICATE KEY UPDATE name=VALUES(name),year=VALUES(year),motorcycle_family_id=VALUES(motorcycle_family_id),motorcycle_type_id=VALUES(motorcycle_type_id)');
$stmt_type =  $conn->prepare('insert ignore into motorcycle_type(`name`) VALUES(?)');

foreach($types as $name =>$id) {
    $stmt_type->bind_param('s',$name);
    $stmt_type->execute();
}
$stmt_type->close();
foreach ($manufacturers as $type => $years) {
    $type_name = array_search($type,$types);
    var_dump($type_name);
    foreach ($years as $year => $brands) {
        foreach ($brands as $brand) {
            $stmt_manufacturer->bind_param('s',$brand->text);
            $stmt_manufacturer->execute();
            $tmp = @Scraper::i()->fetch('https://www.revzilla.com/api/v0/vehicle-models?make='.$brand->value.'&type='.$type.'&year='.$year);

            if (\defined("DEVELOPMENT_MODE"))
            {
                echo 'https://www.revzilla.com/api/v0/vehicle-models?make='.$brand->value.'&type='.$type.'&year='.$year."\r\n";
            }
            if ($tmp) {
                $tmp = json_decode($tmp);
                foreach ($tmp as $model) {
                    $category = $model->category."";
                    $stmt_family->bind_param('ss',$category,$brand->text);
                    $family_res = $stmt_family->execute();



                    $stmt_model->bind_param('sssss',$model->text,$year,$category,$brand->text,$type_name);
                    $model_res = $stmt_model->execute();
                    if (\defined("DEVELOPMENT_MODE"))
                    {
                        if (!$family_res)
                        {
                           echo 'Error Inserting Family:'.$stmt_family->error."\r\n";
                        }
                        if (!$model_res)
                        {
                            echo 'Error Inserting Model:'.$stmt_model->error."\r\n";
                        }
                        echo  sprintf("Inserting: %s, Family: %s Model: %s Year: %s\r\n",$brand->text,$model->category,$model->text,$year);
                    }
                }
            }
            sleep(1);
        }
    }

}
//$getYears = Scraper::i()->fetch()