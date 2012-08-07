<?PHP
/*
 * Implementation of places.py in PHP
 */
require_once("api.php");
function getNicePlaces($lat, $lng, $radius, $count){

}
API(
	array(
		"get"=>array(
			"lat",
			"lng",
			"radius",
			"count"
		),
		getNicePlaces
	)
);
