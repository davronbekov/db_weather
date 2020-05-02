<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\Map;

Map::deleteItem(['map_id' => $_GET['map_id']]);

header("Location: /db_weather/user/admin/map/list.php");
exit();