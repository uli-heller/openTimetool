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
		switch($current_schema_version) {
			case '2.3.0' :
				$newversion = '2.3.0';
				$sqlcheck = "SELECT id from `translate_en` where id=297";
				$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
	 							"(297, 'There are no projects with more than zero hours, that you are allowed to see.', 0)";
				insert_record($sqlcheck, $sqlinsert);
				$sqlcheck = "SELECT id from `translate_de` where id=297";
				$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
	 							"(297, 'Es gibt keine Projekte mit gebuchten Stunden, die Sie sehen d&uuml;rfen.', '0')";
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