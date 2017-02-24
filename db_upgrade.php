<?php
/**
 * SX (AK) : Add DB upgrades here (Starting with V 2.3.0)
 * @var unknown_type
 *
 * DB connect is done before. The calls here use that db connection by default
 *
 * We introduce now a simple new one rec table to hold the schema info to avoid
 * an upgrade attempt during each server round trip !!
 * The schema info MUST correspond with a new schema version string in config.php
 *
 * NOTE : This works for mysql only currently and doesn't make use of the pear interface
 *
 * $Id
 */
function upgrade_database()
{
	global $config;


	// first check and create if necessary (only once ;-) ...)
	$sqlc = "CREATE TABLE IF NOT EXISTS `schema_info` ( " .
			" `id` int(11) NOT NULL AUTO_INCREMENT, " .
  			" `version` mediumtext NOT NULL, " .
  			" PRIMARY KEY (`id`) " .
			" ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	$sqlvi = "INSERT INTO `schema_info` (`id`, `version`) VALUES (1,'2.3.0')";
	$initial_upgrade=false;
	
	//print_r($sqlc);
	if(($res = mysql_query("show tables like 'schema_info'")) === false ) {
		if(($resc = mysql_query($sqlc)) === false ) {
			die("Can't upgrade database! ".mysql_error());
		} else {
			if(($resci = mysql_query($sqlvi)) === false ) {
				die("Can't insert initial schema info ".mysql_error());
			}
		}
		$initial_upgrade=true;
	} else {
		$resa = mysql_fetch_array($res);
		if(empty($resa)) {
			if(($resc = mysql_query($sqlc)) === false ) {
				die("Can't upgrade database! ".mysql_error());
			} else {
				if(($resci = mysql_query($sqlvi)) === false ) {
					die("Can't insert initial schema info ".mysql_error());
				}
			}	
			$initial_upgrade=true;		
		}
	}
	
	$sqlfv = "SELECT version from `schema_info` where id=1";
	if(($resfv = mysql_query($sqlfv)) === false ) {
		die("Can't fetch schema version ! ".mysql_error());
	}
	$res = mysql_fetch_array($resfv);

	$current_schema_version = $res['version'];
	
	$wanted_schema_version = $config->schema_version;
	
	/**
	 * Now a switch witout breaks : we enter at current schema version and walk
	 * up until wanted schema version is reached (end of switch)
	 */
	
	if($initial_upgrade || ($current_schema_version != $wanted_schema_version)) {
		/*
		 * Upgrade required
		 */
		
		mysql_query("SET NAMES 'utf8'");
			
		switch($current_schema_version) {
			case '2.3.0' :
				$newversion = '2.3.1';
				$sqlcheck = "SELECT id from `translate_en` where id=297";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(297, 'There are no projects with more than zero hours, that you are allowed to see.', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=297";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(297, 'Es gibt keine Projekte mit gebuchten Stunden, die Sie sehen d&uuml;rfen.', '0')";
				insert_record($sqlcheck, $sqlinsert);
				update_schema_info($newversion);
			case '2.3.1' :
				$newversion = '2.3.2';
				$sqlcheck = "SELECT id from `translate_en` where id=298";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(298, 'CAUTION: Project overbooked!', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=298";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(298, 'ACHTUNG: Projekt überbucht!', '0')";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=299";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(299, 'CAUTION: Only ', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=299";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(299, 'ACHTUNG: Nur ', '0')";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=300";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(300, 'hours left', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=300";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(300, 'Stunden übrig', '0')";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=301";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(301, 'Do you still want to book ?', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=301";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(301, 'Wollen Sie die Buchung trotzdem durchführen?', '0')";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=302";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(302, 'CANCEL : No booking!', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=302";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(302, 'Abrechen: Buchung wird nicht durchgeführt', '0')";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=303";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(303, 'OK : Booking will be done! (Project overbooked)', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=303";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(303, 'OK: Buchung wird durchgeführt, Projekt wird überbucht!', '0')";				
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_en` where id=304";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(304, 'Active projects', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=304";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(304, 'Aktive Projekte', '0')";				
				insert_record($sqlcheck, $sqlinsert);				
				$sqlcheck = "SELECT id from `translate_en` where id=305";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(305, 'Closed projects', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=305";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(305, 'Geschlossene Projekte', '0')";				
				insert_record($sqlcheck, $sqlinsert);				
				$sqlcheck = "SELECT id from `translate_en` where id=306";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(306, 'All projects', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=306";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(306, 'Alle Projekte', '0')";				
				insert_record($sqlcheck, $sqlinsert);				

				// we switch of this much too short english word as it garbles often the translation
				$sqlcheck = "SELECT id from `translate_en` where id=20";
				$sqlinsert = "UPDATE `translate_en` SET `string`='allX' WHERE `id`=20";
				update_record($sqlcheck, $sqlinsert);
				
				
				update_schema_info($newversion);
				
			case '2.3.2' :
				$newversion = '2.3.3';
				$sqlcheck = "SELECT id from `translate_en` where id=307";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
							"(307, 'Delete All Exports', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=307";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
							"(307, 'Alle Exports l&ouml;schen', '0')";
				insert_record($sqlcheck, $sqlinsert);

				$sqlcheck = "SELECT id from `translate_en` where id=308";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
						"(308, 'Are you sure you want to delete all exports?', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=308";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
						"(308, 'Wollen Sie wirklich alle Exports l&ouml;schen?', '0')";
				insert_record($sqlcheck, $sqlinsert);
				
				update_schema_info($newversion);
				
			case '2.3.3' :
				$newversion = '2.3.4';
				$sqlcheck = "SELECT id from `translate_en` where id=309";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
						"(309, 'Please note! This function is disabled in the demo version.', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=309";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
						"(309, 'Bitte beachten! Diese Funktion ist in der Demoversion deaktiviert.', '0')";
				insert_record($sqlcheck, $sqlinsert);

				update_schema_info($newversion);
				
		} // switch end
	}
	
	return;
}

/**
 * checks first and then does the insert 
 * 
 * @param string $sqlcheck
 * @param string $sqlinsert
 */
function insert_record($sqlcheck, $sqlinsert)
{
	
	if(($rescheck = mysql_query($sqlcheck)) === false ) {
		die("Can't get info : ".mysql_error());
	}
	$res = mysql_num_rows($rescheck);
	if(empty($res)) { 
		if(($res = mysql_query($sqlinsert)) === false ) {
			die("Upgrade failed : ".mysql_error());
		}
	}
}
/**
 * checks first and then does the update
 * 
 * @param string $sqlcheck
 * @param string $sqlinsert
 */
function update_record($sqlcheck, $sqlupdate)
{
	
	if(($rescheck = mysql_query($sqlcheck)) === false ) {
		die("Can't get info : ".mysql_error());
	}
	$res = mysql_num_rows($rescheck);
	if(!empty($res)) { 
		if(($res = mysql_query($sqlupdate)) === false ) {
			die("Upgrade failed : ".mysql_error());
		}
	}
}


/**
 * Upgrade schema version in db	
 */
function update_schema_info($schemaversion)
{
	$sqlupdate = "UPDATE `schema_info` SET `version`='".$schemaversion."' WHERE `id`=1";
	if(($res = mysql_query($sqlupdate)) === false ) {
		die("Upgrade failed : ".mysql_error());
	}	
}














?>