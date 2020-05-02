<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\Color_mapping;

Color_mapping::deleteItem(['map_id' => $_GET['map_id'], 'value' => $_GET['value']]);

header("Location: /db_weather/user/admin/colors/list.php");
exit();