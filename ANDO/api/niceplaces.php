<?PHP
/*
 * Implementation of places.py in PHP
 */
require_once("api.php");
function stripURL($url){
	//slice and dice
	$splitStr=str_split($url);
	$o=array_slice($splitStr, getLastIndex("/", $splitStr));
	if(count($o)>0){return implode("",$o);}else{return $url;}
}
function convLinkedURL($url){
	return str_replace(
		"http://environment.data.gov.uk/id/",
		"http://environment.data.gov.uk/doc/",
		$url
		) . ".json";
}
function getLastIndex($needle, $haystack){
	$cnum=0;
	for($i=0;$i<count($haystack);$i++){
		if($haystack[$i]==$needle){
			$cnum=$i;
		}
	}
	return $cnum+1;
}
function getNicePlaces($lat, $lng, $radius, $count){
	//Load the JSON from remote
	$minLat=$lat-$radius;
	$maxLat=$lat+$radius;
	$minLng=$lng-$radius;
	$maxLng=$lng+$radius;
	$ch=curl_init();
	curl_setopt(
		$ch,
		CURLOPT_URL,
		"http://environment.data.gov.uk/doc/bathing-water.json?min-samplingPoint.lat=$minLat&max-samplingPoint.lat=$maxLat&min-samplingPoint.long=$minLng&max-samplingPoint.long=$maxLng&_page=0&_pageSize=$count"
	);
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/json'
			)
		)
	);
	if(!($result=curl_exec($ch))){
		//remote connection error
		display_error(5, "Cannot connect to the Government Linked Data servers.");
		
	}
	else{
		curl_close($ch);
		$data=json_decode($result)->{"result"}->{"items"};
	}
	$niceData=array();
	foreach ($data as $place){
		$niceDatum=array(
			"magicId"=>$place->{"eubwidNotation"},
			"image"=>"http://environment.data.gov.uk/media/image/bathing-water-profile/".$place->{"eubwidNotation"}."_1-webres.jpg",
			"sediment"=>	stripURL($place->{"sedimentTypesPresent"}),
			"yearDesignated"=> stripURL($place->{"yearDesignated"}),
			"name"=>$place->{"name"}->{"_value"},
			"about"=>convLinkedURL($place->{"_about"}),
			"district"=>array(
				"about"=> $place->{"district"}[0]->{"_about"},
				"name"=>	$place->{"latestSampleAssessment"}->{"sampleClassification"}->{"name"}
			),

			"lastTest"=>array(
				"results"=>convLinkedURL($place->{"latestSampleAssessment"}->{"_about"}),
				"verdict"=>$place->{"latestSampleAssessment"}->{"sampleClassification"}->{"name"}->{"_value"},
			),

			"type"=>$place->{"type"},
			"samplePoint"=>array(
				"latitude"=>$place->{"samplingPoint"}->{"lat"},
				"longitude"=>$place->{"samplingPoint"}->{"long"},
				"OSGB"=>array(
					"northing"=>$place->{"samplingPoint"}->{"northing"},
					"easting"=>$place->{"samplingPoint"}->{"easting"}
				)
			)
		);
		$tempType=array();
		foreach($niceDatum["type"] as $type){
			$tempType[]=stripURL($type);
		}
		$niceDatum["type"]=$tempType;
		$niceData[]=$niceDatum;
	}
	return $niceData;
}
API(
	array(
		"get"=>array(
			array(
					"lat",
					"lng",
					"radius",
					"count"
			),
			'getNicePlaces'
		)
	)
);
