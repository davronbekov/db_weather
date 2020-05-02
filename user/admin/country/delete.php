<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\Country;

Country::deleteItem(['country_name' => $_GET['country_name']]);

header("Location: /db_weather/user/admin/country/list.php");
exit();