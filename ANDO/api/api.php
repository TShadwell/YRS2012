<?PHP
define("DEBUG", 0);
if(DEBUG==1){
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
}
//General API abstractionv2
//TODO:Change program function if documenting rather than using api
define("DOCUMENT_API", isset($_GET["document"]));
function xml_encode($mixed,$domElement=null,$DOMDocument=null){
	if(is_null($DOMDocument)){
		$DOMDocument=new DOMDocument;
		$DOMDocument->formatOutput=true;
		xml_encode($mixed,$DOMDocument,$DOMDocument);
		return $DOMDocument->saveXML();
	}
	else{
		if(is_array($mixed)){
			foreach($mixed as $index=>$mixedElement){
				if(is_int($index)){
					if($index==0){
						$node=$domElement;
					}
					else{
						$node=$DOMDocument->createElement($domElement->tagName);
						$domElement->parentNode->appendChild($node);
					}
				}
				else{
					$plural=$DOMDocument->createElement($index);
					$domElement->appendChild($plural);
					$node=$plural;
					if(rtrim($index,'s')!==$index && count($mixedElement)>1){
						$singular=$DOMDocument->createElement(rtrim($index,'s'));
						$plural->appendChild($singular);
						$node=$singular;
					}
				}
				xml_encode($mixedElement,$node,$DOMDocument);
			}
		}
		else{
			$domElement->appendChild(
				$DOMDocument->createTextNode(
					is_bool($mixed)?($mixed?"true":"false"):$mixed
				)
			);
		}
	}
}
function string_multiply($str, $count){
        $o="";
        for($i=0;$i<($count);$i++){
                $o.=$str;
        }
        return $o;
}
function plaintext_encode($obj, $count=null){
        if(is_null($count)){
                //First run; parse the root object and return it.
                return implode("\n", plaintext_encode($obj, 0));
        }
        else{
                if(is_array($obj)){
						//For each of the children of this object
						$lines=array();
                        foreach($obj as $index=>$element){
                                //Add a header of the same tab-level as the depth in the tree
                                $lines[]=string_multiply("\t",$count).$index.":";
                                //Do the same for all children of this object,
                                //incrementing the tree-depth by one
                                $child=plaintext_encode($element, $count+1);
                                //Append children
                                $lines=array_merge($lines, $child);
                        }
                        return (empty($lines)?array(string_multiply("\t",$count)."(empty)"):$lines);
 
                }
				elseif(is_object($obj)){
					return plaintext_encode((array)$obj, $count);
				}
				elseif(is_bool($obj)){
					return plaintext_encode($obj?"true":"false", $count);
				}
                else{
					$str=array();
					foreach(explode("\n", $obj) as $line){
						$str[]= string_multiply("\t",$count).$line;
					}
					return $str;
                }
        }
}
function html_plaintext_encode($obj, $count=null){
        if(is_null($count)){
                //First run; parse the root object and return it.
			return 	"<!DOCTYPE HTML><head><title>".
					"response</title><body>".
					implode("", plaintext_encode($obj, 0)) .
					"</body>";
        }
        else{
                if(is_array($obj)){
						//For each of the children of this object
						$lines=array();
						//Add the <section> wrapper for the section
						$lines[]="<section>";
						foreach($obj as $index=>$element){
                                //Add a header of the same tab-level as the depth in the tree
                                $lines[]="<h".($count+1).">".$index."</h".($count+1).">";
                                //Do the same for all children of this object,
                                //incrementing the tree-depth by one
                                $child=plaintext_encode($element, $count+1);
                                //Append children
                                $lines=array_merge($lines, $child);
                        }
						//Add the <section> wrapper for the section
						$lines[]="</section>";
                        return (empty($lines)?array(string_multiply("\t",$count)."(empty)"):$lines);
 
                }
				elseif(is_object($obj)){
					return plaintext_encode((array)$obj, $count);
				}
				elseif(is_bool($obj)){
					return plaintext_encode($obj?"true":"false", $count);
				}
				else{
					$obj=(string)$obj;
					return array("<span>".$obj."</span>");
                }
        }

}
$errors = array(
	0=>"A required parameter was not provided.",
	1=>"The action specified does not exist.",
	2=>"No action was provided.",
	3=>"The appropriate information does not exist.",
	4=>"An input was invalid.",
	5=>"There was an internal error.",
	6=>"The action object specified does not exist."
);
//In the future, this could hold several differnt types of success.
$success = array(
	0=>"Action completed successfully."
);
function verbose_list($arr, $connective, $before="", $after="", $noun=null){
	if (count($arr)==1){
		return ($noun!==null?$noun:""). $before .(string) $arr[0] . $after;
	}
	else{
		return ($noun!==null?$noun."s":"").
			$before.implode(", ", array_slice($arr, 0, ($len=count($arr)-1))).
			" " . $connective . " " .
			$arr[$len]
			.$after;
	}
									
}
function display($inp){
	//process XML JSON etc...
	if(!isset($_GET["format"])){
		header("Content-type: application/json");
		echo isset($_GET["pp"])?json_encode($inp, JSON_PRETTY_PRINT):json_encode($inp);
		exit;
	}
	switch(strtolower($_GET["format"])){
		case "xml":
			header("Content-type: application/xml");
			echo xml_encode(array("response"=>$inp));
		break;
		case "txt":
			header("Content-type: text/plain");
			echo plaintext_encode($inp);
			break;
		case "htm":
			//Functionally the same as .html`
		case "html":
			header("Content-type text/html");
			echo html_plaintext_encode($inp);
			break;
		case "jsonp":
			header("Content-type: text/javascript");
			echo (isset($_get["callback"])?$_get["callback"]:"getYodel")."(".json_encode($inp).")";
			break;
		case "jsonrpc":
			//Some logic may go here eventually
		default:
			header("Content-type: application/json");
			echo isset($_GET["pp"])?json_encode($inp, JSON_PRETTY_PRINT):json_encode($inp);
	}
	exit;
}
function display_error($err, $supp=null, $data=null){
	$val=
		$supp==null?
		array(
			"result"		=>	false,
			"code"			=>	$err,
			"message"		=>	$GLOBALS["errors"][$err]
		)
		:array(
			"result"		=>	false,
			"code"			=>	$err,
			"message"		=>	$GLOBALS["errors"][$err],
			"supplimentary"	=>	$supp
		);
	if($data!==null){
		$val["data"]=$data;
	}
	display($val);
}
function api_assert_auth(){
	//get auth token from headers, if it doesn't exist, throw an error
	if(isset($_SERVER["HTTP_AUTHORISATION"])){
		return $_SERVER["HTTP_AUTHORISATION"];
	}
	else{
		display_error(
			0,
			"This function expects HTTP_AUTHORIZATION headers."
		);
	}
	
}
function pt($inp){
	if(is_bool($inp)){
		$inp=$inp?"true":"false";
	}
	echo $inp;
	return $inp;
}
function facebook_auth_json($reqvalid=false, $authtoken=null){
	//Takes the token from header if $authtoken=null,
	//If reqvalid=true, will throw an error on facebook error
	$ch=curl_init();
	if(isset($_SERVER["HTTP_AUTHORISATION"])){
		$authtoken=$authtoken=null?$_SERVER["HTTP_AUTHORISATION"]:$authtoken;
	}
	else{
		display_error(
			0,
			"This function expects HTTP_AUTHORIZATION headers."
		);
	}
	curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?access_token={$authtoken}");

	curl_setopt_array(
		$ch,
		array(
			CURLOPT_RETURNTRANSFER	=>	true, //curl_exec() will return server response
			CURLOPT_HTTPHEADER 		=>	array(
				'Content-type: application/json'
			)
		)
	);

	if(!($result=curl_exec($ch))){
		//Remote connection error
		curl_close($ch);
		display_error(5, "Problem when contacting the facebook Open Graph API servers.");
	}
	else{
		curl_close($ch);
		$facebook_json=json_decode($result);
		if($reqvalid&&isset($facebook_json->error)){
			display_error(
				4,
				"Facebook returned error.",
				$facebook_json->error
			);
		}
		return $facebook_json;
	}
}
function display_success($data=null, $succ=0){
	display(
		$data==null?
		array(
			"result"		=>	true,
			"code"			=>	$succ,
			"message"		=>	$GLOBALS["success"][$succ]
		)
		:array(
			"result"		=>	true,
			"code"			=>	$succ,
			"message"		=>	$GLOBALS["success"][$succ],
			"data"			=>	$data
		)
	);
}
function API($api){
	if(DOCUMENT_API){
		foreach($api as $action => $arr){
			$arr[0] = array_slice($arr[0], (is_numeric($arr[0][0]) + is_array($arr[0][1])));
			//See if we use GET or POST
			$ac=(isset($arr[2]))?"get":"post";
			$args=array();
			foreach($arr[0] as $arg){
				if(!is_numeric($arg)){
					if(is_array($arg)){
						$args=array_merge($arg, $args);
					}
					elseif(is_string($arg)){
						$args[] = $arg;
					}
				}
			}
			//Undefined functions are converted to strings by PHP
			$func=is_string($arr[1])?$arr[1]:"anonymous";
			echo ("<div class='action' data-type='{$ac}'><span class='actionName'>{$action}</span> <span data-action='{$action}' class='args'><b>". implode("</b><b>",$args) . "</b></span><span class=\"goesTo\">{$func}</span></div>");
		}
	}
	else{
		//the HTTP get attr action should be set, or it's really not a valid query.
		if(
			//No set action
			(!isset($_GET["action"]))||
			//No bound action
			!isset($api[$_GET["action"]])||
			//
			(!($thisapi=$api[$_GET["action"]]))
		){
			//scream
			display_error(1, "This action is invalid, or does not exist; the action was ". (isset($_GET["action"])?"'".$_GET["action"]."'":"not set"));

		}		
		else{
			//If a separate store for arguments has been defined, use that.
			//Quick hack to allow JSON-rpc type requests
			if(isset($_GET["format"])&&$_GET["format"]=="jsonrpc"){
				$json = file_get_contents('php://input');

				$jobj = json_decode($json);

				$content=Array();

				foreach($jobj as $key => $value)
				{
				   $content[$key] = $value;
				}
				$action=$content;
				
			}
			else{
				$action=$_GET;
				if(isset($thisapi[2])){
					$action=$thisapi[2];
				}
			}
			//Handle variadic functions
			if(is_numeric($thisapi[0][0])){
				//Will hold the variables to pass on to the callback
				$arr = array();
				//If fb_token is one of the non-mandatory arguments
				if(in_array("fb_token", $thisapi[0])){
					$action=array_merge(array("fb_token"=>api_assert_auth()), $action);
				}
				elseif(is_array($thisapi[0][1])&&in_array("fb_token", $thisapi[0][1])){
					$action=array_merge(array("fb_token"=>api_assert_auth()), $action);
				}
				//If the second item in the arguments is an array, they'll be the required arguments.
				$params=($has_defaults = is_array($thisapi[0][1]))?array_slice($thisapi[0], 2):array_slice($thisapi[0], 1);
				if(!(
					//Shortcut if total number of given is lower
					count($action)>=$thisapi[0][0]&&
					//Check the number of valid values is correct
					count(($given_params=array_intersect($params, array_keys($action))))>=$thisapi[0][0]&&
					//If the second value of the arguments is an array
					($has_defaults&&
						//Then if the number of mandatory values in the set of given values is less than
						//the number of mandatory values
						count(array_intersect($thisapi[0][1], array_keys($action)))>=count($thisapi[0][1])
					)
				)){
					if($has_defaults){
						display_error(
							0,
							"This function requires the " . verbose_list($thisapi[0][1], "and", "\n", "\n", "parameter").
							" to be given, and at least " . count($thisapi[0][1]) . " of the ".
							verbose_list($params, "and", "\n", "\n", "parameter") . " to be given.", array("action"=>$action, "GET" => $_GET, "POST"=>$_POST)
						);
					}
					else{
						display_error(
							0,
							"This function requires at minimum " .
							$thisapi[2][0] .
							"of the parameters " .
							verbose_list($params, "and").
							" to be given.", array("action"=>$action, "GET" => $_GET, "POST"=>$_POST)
						);
					}
				}
				else{
					foreach($given_params as $given_param){
						$arr[$given_param]=$action[$given_param];
					}
					if($has_defaults){
						foreach($thisapi[0][1] as $mandatory){
							$arr[$mandatory] = $action[$mandatory];
						}
					}
					if(isset($action["fb_token"])){
						$arguments["facebook"]=facebook_auth_json(false,$action["fb_token"]);
					}
					if(is_bool(($val=$thisapi[1]($arr)))){
						display_success();
					}
					else{
						display_success($val);
					}
					
				}
			}
			else{
				//If fb_token is a required parameter, fill it with the token.
				if(in_array("fb_token", $thisapi[0])){
					$action["fb_token"]=api_assert_auth();
				}


				//We need to sort the array in the order of thisapi[1]
				//Die if needed value not given
				foreach($thisapi[0] as $arg){
					if(isset($action[$arg])&&($val=$action[$arg])){
						$arguments[]=$val;
					}
					else{
						display_error(0, "Missing argument '{$arg}'.", array("action"=>$action, "GET"=>$_GET, "POST"=>$_POST));
					}
				}
				if(isset($action["fb_token"])){
					$arguments[]=facebook_auth_json(false,$action["fb_token"]);
				}
			}
			//Send the callback function the values
			if(is_bool(($val=call_user_func_array($thisapi[1], $arguments)))){
				//Callback returned boolean
				display_success();
			}
			else{
				//Callback returned data
				display_success($val);
			}

		}
	}
}
