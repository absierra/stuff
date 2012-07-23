<?php 

$treeCount = 1;

function makeTree($filename, $cities) {
	$data = array();
	$inside = &$data;
	$fp = @fopen($filename, 'r');
	while (!feof($fp)) {
		$row = fgetcsv($fp);
		$first = true;
 		foreach($row as $curr) {
 			if($first and !in_array($curr,$cities)) {
 				break;
 			}
 			if(empty($curr)) {
 				continue;
 			}
 			if(is_numeric($curr)) {
 				$inside[] = $curr;
 				$first = false;
 				continue;
 			}
 			if(!array_key_exists($curr, $inside)) {
 				$inside[$curr] = array();
 			}
 			$inside = &$inside[$curr];
 			$first = false;
 		}
 		$inside = &$data;
	}
	fclose($fp);
	return $data;
}


function getCategory($city, $metric, $data) {
	$path = explode(",", $city.','.$metric);
	$revPath = array_reverse($path);
	$prev = &$data;
	while(count($revPath) > 0) {
		$prev = &$prev[array_pop($revPath)];
	}
	return $prev;
}

function getCategories($cities, $metric, $data) {
	$result = array();
	foreach($cities as $city) {
		$result[$city] = array();
		$result[$city][$metric] = getCategory($city, $metric, $data);
	}
	return $result;
}

function prettyTree($map, &$str,$level,$path) {
	global $treeCount;
	if(is_numeric($map)) {
		$str .= $map;
		return;
	}
	$str .= '<ul>';
	++$level;
	//++$treeCount;
	foreach($map as $key=>$val) {
		if(empty($val)) {
			continue;
		}
		$temp = $key;
		$newPath = $path.$key.',';
		if(strpos($key, ',') !== false) {
			$newPath = $path.'\''.$key.'\',';
		}
		$check = '<input type="checkbox" name="category" id="BBB'.++$treeCount.'" value="'.$newPath.'" />';
		if(is_numeric($key)) {
			$temp = '';
			$check = '';
		}
		$isNum  = (is_numeric($val)) ? ' name="num"' : '';
		$str .= '<li class="'.$level.'"'.$isNum.' id="'.$newPath.'">'.$temp.$check;
		prettyTree($val, $str, $level,$newPath);
		$str .= '</li>';
	}
	$str .= '</ul>';
	//--$treeCount;
	--$level;
	return $str;
}

$cities = $_REQUEST['City']; 
//$metric = $_REQUEST['Metric']; 
//$dept = $_REQUEST['Dept']; 

$expenditures = makeTree('SCOData.csv', $cities);
$metrics = makeTree('metrics.csv', $cities);

$expendList = print_r($expenditures, true);
$metricList = print_r($metrics, true);

//$metricVals = getCategories($cities,$metric,$metrics);
//$expendVals = getCategories($cities,$dept,$expenditures);

//$text = '';
//for($i=0; $i < count($cities) ; $i++){
//	$text .= $cities[$i] . "<br/>";
//}

$str = '';
$expendList = prettyTree($expenditures, $str, 0,'');
$str = '';
$metricList = prettyTree($metrics, $str, 0);

//$out = $text.'<br/>'.$metric.'<br/>'.$dept.'<br/>'.print_r($metricVals, true).'<br/><br/>'.print_r($expendVals, true);
$buttonsExp = '<div id="buttonDiv1"></div>';
$expendDisplay = $buttonsExp.'<ul class="mktree" id="tree1"><li id="zero" name="zero">Expenditures'.$expendList.'</li></ul>';
$buttonsMet = '<div id="buttonDiv2"></div>';
$metricDisplay = $buttonsMet.'<ul class="mktree" id="tree2"><li id="zero1" name="zero">Metrics'.$metricList.'</li></ul>';

$out = "TEST";
?> 
