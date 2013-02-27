<?php

function statusStripNumeric($str) {
	return preg_replace("/[^0-9.]/", "", $str);
}

function statusPriceMonthly($str) {
	$price = intval(statusStripNumeric($str));
	
	if($price != 0) {
		if(strpos($str, "month") !== false) {
			return $price;
		} else if(strpos($str, "year") !== false || strpos($str, "year") !== false) {
			return $price / 12;
		} else if(strpos($str, "quarter") !== false) {
			return $price / 4;
		}
	}
	
	return 0;
}

//returns array of overall stats (key: name of stat; value: stat value)
function statusOverview() {
	$status = array();
	
	//total accounts
	$result = mysql_query("SELECT COUNT(*) FROM accounts");
	$row = mysql_fetch_array($result);
	$status['Total accounts'] = $row[0];
	
	//total services
	$result = mysql_query("SELECT COUNT(*) FROM services");
	$row = mysql_fetch_array($result);
	$status['Total services'] = $row[0];
	
	//price-based
	$result = mysql_query("SELECT a.v, b.v FROM service_params AS a LEFT JOIN service_params AS b ON a.service_id = b.service_id AND b.k = 'due' WHERE a.k = 'price'");
	$status['Total monthly revenue'] = 0;
	
	while($row = mysql_fetch_array($result)) {
		$price = $row[0];
		$due = $row[1];
		
		$monthPrice = statusPriceMonthly($price);
		$status['Total monthly revenue'] += $monthPrice;
	}
	
	if($status['Total accounts'] != 0) {
		$status['Average customer monthly revenue'] = round($status['Total monthly revenue'] / $status['Total accounts'], 4);
	}
	
	return $status;
}

//returns due services
function statusDue($overdue = false) {
	$result = mysql_query("SELECT service_id, v FROM service_params WHERE k = 'due'");
	$dueArray = array();
	
	while($row = mysql_fetch_array($result)) {
		if(empty($row[1]) || $row[1] == "N/A") {
			continue;
		}
		
		$service_id = escape($row[0]);
		$due = strtotime($row[1]);
		
		if(($overdue && time() > $due) || (!$overdue && time() <= $due && time() > $due - 3600 * 24 * 12)) {
			$inner_result = mysql_query("SELECT services.account_id, services.name, accounts.email, accounts.name FROM services LEFT JOIN accounts ON accounts.id = services.account_id WHERE services.id = '{$service_id}'");
			
			if($inner_row = mysql_fetch_array($inner_result)) {
				$price = getServiceParam($service_id, 'price');
				
				if($price === false) {
					$price = "Unknown";
				}
				
				$dueArray[] = array('due' => $due, 'service_id' => $service_id, 'account_id' => $inner_row[0], 'service' => $inner_row[1], 'email' => $inner_row[2], 'name' => $inner_row[3], 'price' => $price);
			}
		}
	}
	
	usort($dueArray, "statusDueCompare");
	return $dueArray;
}

function statusDueCompare($a, $b) {
	return $a['due'] - $b['due'];
}

?>
