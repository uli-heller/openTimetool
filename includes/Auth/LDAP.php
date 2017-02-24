<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>             |
// | Changes: "DIRK KONRAD" <dirkvanderwalt@webmail.co.za>   (AA)         |
// +----------------------------------------------------------------------+
//
// $Log: LDAP.php,v $
// Revision 1.3  2003/03/11 12:57:56  wk
// *** empty log message ***
//
// Revision 1.5  2002/09/14 15:58:24  mccain
// - added extension-check (Dirk Konrad)
//
// Revision 1.4  2002/09/14 15:46:07  mccain
// - changes by Rony Moreas, see inline comments for diffs
//
// Revision 1.3  2002/08/26 09:54:02  mccain
// - added changes by DIRK KONRAD
//
// Revision 1.2  2002/06/19 21:41:56  mccain
// - check for loaded extension
//
// Revision 1.1  2002/06/19 21:12:09  mccain
// - new container classes, thanks to Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
//

/*
 *   2002/09/11 16:57:44  rmoreas
 *   Added scope parameter and removed 'username:password@" to be
 *   conform to the RFC 2255 URL.
 *
 *   2002/09/10 15:17:24  rmoreas
 *   fixed bug in parseLDAP
 *
 *   2002/09/10 13:51:44  rmoreas
 *   changed URL parsing allowing the URL format:
 *   ldap://username:password@host:port host2:port2/basedn?attribute?scope?filter
 *
 *   2002/06/12 19:51:52  rmoreas
 *   Authentication classes adapted from Auth classes (version 1.1.1)
 *   at http://sourceforge.net/projects/auth/. Original author is
 *   Wolfram Kriesing. Added NIS and LDAP drivers and fixed
 *   GLOBALS bug in common.php.
 *
 *
 */

require_once("Auth/common.php");
require_once("PEAR.php");
require_once("DB.php");


/**
 *
 *
 *   @package    Auth
 */

class Auth_LDAP extends Auth_common
{

	/**
	 *
	 *
	 *   @var    string  conn_id
	 */
	var $conn_id;

	var $_dsn = array(
                    'basedn'=>      false
	,'userattr'=>   'uid'
	,'scope'=>      'sub'
	,'filter'=>     '(objectClass=*)'
	);

	/**
	 *
	 *   create LDAP authentication driver for the given RFC 2255 URL
	 *   Format of the URL is:
	 *
	 *       ldap://host:port/basedn?userattr?scope?filter
	 *
	 *   @access public
	 *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
	 *   @param  string  $domain
	 */
	function xxx_Auth_LDAP( $url )
	{
		$this->Auth_common();
		// set defaults
		$this->options['protocol'] = 'ldap';
		$this->options['host']     = 'localhost';
		$this->options['port']     = '389';
		$this->options['basedn']   = false;
		$this->options['host2']    = false;
		$this->options['port2']    = '389';
		$this->options['userattr'] = "uid";
		$this->options['scope'] = "sub";
		$this->options['filter'] = "(objectClass=*)";
		$this->conn_id = "";
	}


	/**
	 *
	 *
	 *   @access public
	 *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
	 */
	function setup( $url )
	{
		if( !extension_loaded('ldap') )
		{
			if( !@dl('ldap') )
			return new PEAR_Error('LDAP extension could not be found or loaded!');
		}

		if ($parsed = Auth::parseDSN($url)) {
			$this->_dsn = array_merge($this->_dsn,$parsed);
			$this->_dsn['basedn'] = $this->_dsn['database'];
		}

		if (Auth::isError($this->_connect())) {
			return new PEAR_Error('Connect failed!');
		}

		return parent::setup($this->_dsn);

		/*
		 if ( ($parsed = Auth_LDAP::parseURL($url)) == false ) {
		 return new Pear_Error("LDAP URL format is incorrect!", 41, PEAR_ERROR_DIE);
		 }
		 $this->options['protocol']  = $parsed['protocol'];
		 $this->options['host']   = $parsed['host'];
		 $this->options['port']   = $parsed['port'];
		 $this->options['host2']   = $parsed['host2'];
		 $this->options['port2']   = $parsed['port2'];
		 $this->options['basedn'] = $parsed['basedn'];
		 $this->options['userattr'] = $parsed['userattr'];
		 $this->options['scope'] = $parsed['scope'];
		 $this->options['filter'] = $parsed['filter'];

		 if ($this->options['basedn'] != false) {
		 $this->_connect( );
		 } else {
		 return new Pear_Error("No LDAP Search Base specified!", 41, PEAR_ERROR_DIE);
		 }

		 return parent::setup( $this->dbh->dsn );
		 */
	}

	/**
	 *   Parse RFC 2255 URL. Returns array with components of the URL: host, port, basedn,
	 *   userattr, scope and filter. Returns false if format incorrect. The syntax of
	 *   the URL is:
	 *
	 *       ldap://host:port/basedn?userattr?scope?filter
	 *
	 *   ldap               - For regular ldap, use the string ldap. For secure LDAP,
	 *                        use ldaps instead.
	 *   host:port          - The name/port of the ldap server (defaults to localhost:389
	 *                        for ldap, and localhost:636 for ldaps).
	 *                        To specify two redundant LDAP servers, just list both
	 *                        servers, separated by a space.
	 *   basedn             - The DN of the branch of the directory where all searches should
	 *                        start from. At the very least, this must be the top of your
	 *                        directory tree, but could also specify a subtree in the directory.
	 *   userattr           - The attribute to search for. If no attributes are provided,
	 *                        the default is to use uid. It's a good idea to choose an
	 *                        attribute that will be unique across all entries in the subtree
	 *                        you will be using.
	 *   scope              - Scope used for searching. This should be 'sub' or 'one'. The
	 *                        default is 'sub', i.e. the whole subtree is searched. The scope
	 *                        'base' is not supported.
	 *   filter             - A valid LDAP search filter. If not provided, defaults to
	 *                        (objectClass=*), which will search for all objects in the tree.
	 *
	 *   When doing searches, the attribute, filter and username passed by the user are combined
	 *   to create a search filter that looks like (&(filter)(userattr=username)).
	 *   For example, consider an URL of ldap://ldap.yourcompany.com/o=yourcompany?cn?(posixid=*).
	 *   When a client attempts to connect using a username of Babs Jenson, the resulting search
	 *   filter will be (&(posixid=*)(cn=Babs Jenson)).
	 *
	 *   @access public
	 *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
	 */
	function xxx_parseURL( $url ) {

		// Get defaults
		$parsed = array (
         'protocol'  => $this->options['protocol'],
         'host'      => $this->options['host'],
         'port'      => $this->options['port'],
         'host2'     => $this->options['host2'],
         'port2'     => $this->options['port2'],
         'basedn'    => $this->options['basedn'],
         'userattr'  => $this->options['userattr'],
         'scope'     => $this->options['scope'],
         'filter'    => $this->options['filter']
		);

		// find protocol
		if (($pos = strpos($url, '://')) !== false) {
			$parsed['protocol'] = substr($url, 0, $pos);
			$url = substr($url, $pos + 3);
			if ( $parsed['protocol'] == "ldap" ) {
				$parsed['port'] = 389;
				$parsed['port2'] = 389;
			} else if ( $parsed['protocol'] == "ldaps" ) {
				$parsed['port'] = 636;
				$parsed['port2'] = 636;
			}
		}

		// find host and port
		if (($pos = strpos($url, '/')) !== false) {
			$str = substr($url, 0, $pos);
			$url = substr($url, $pos + 1);
		} else {
			$str = $url;
			$url = "";
		}
		$hosts = explode (" ",$str);
		if ( sizeof($hosts) > 0 ) {
			if (($pos = strpos($hosts[0], ':')) !== false) {
				$parsed['host'] = substr($hosts[0], 0, $pos);
				$parsed['port'] = substr($hosts[0], $pos + 1);
			} else {
				$parsed['host'] = $hosts[0];
			}
		}
		if ( sizeof($hosts) > 1 ) {
			if (($pos = strpos($hosts[1], ':')) !== false) {
				$parsed['host2'] = substr($hosts[1], 0, $pos);
				$parsed['port2'] = substr($hosts[1], $pos + 1);
			} else {
				$parsed['host2'] = $hosts[1];
			}
		}

		// find basedn
		if ( $url != "" ) {
			if ( ($pos = strpos($url, '?')) != false ) {
				$parsed['basedn'] = substr($url, 0, $pos);
				$url = substr($url, $pos + 1);
			} else {
				$parsed['basedn'] = $url;
				$url = "";
			}
		}

		// find userattr, scope and filter options
		if ( $url != "" ) {
			$opts = explode("?",$url);
			if ($opts[0] != "") {
				$parsed['userattr'] = $opts[0];
			}
			if ($opts[1] == 'sub' || $opts[1] == 'one') {
				$parsed['scope'] = $opts[1];
			}
			if ($opts[2] != "") {
				$parsed['filter'] = $opts[2];
			}
		}

		return $parsed;
	}

	/**
	 *   Connect to LDAP server given by the 'host' option. If the connection fails, the
	 *   optional backup server is tried
	 *
	 *   @access private
	 *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
	 */
	function _connect( )
	{
		global $config;  // from config.php
		 
		/*
		 if (($this->conn_id = @ldap_connect($this->options['host'], $this->options['port'])) == false) {
		 if ( $this->options['host2'] != "" ) {
		 // try to connect to backup server instead
		 if (($this->conn_id = @ldap_connect($this->options['host2'], $this->options['port2'])) == false) {
		 return new PEAR_Error("Error connecting to LDAP.", 41, PEAR_ERROR_DIE);
		 }
		 } else {
		 return new PEAR_Error("Error connecting to LDAP.", 41, PEAR_ERROR_DIE);
		 }
		 }
		 // bind anonymously for searching
		 if ((@ldap_bind($this->conn_id)) == false) {
		 return new PEAR_Error("Error binding anonymously to LDAP.", 41, PEAR_ERROR_DIE);
		 }
		 */

		$this->conn_id = @ldap_connect($this->_dsn['hostspec'], $this->_dsn['port']);
		if (!$this->conn_id) {
			return new PEAR_Error('Error connecting to LDAP.', 41, PEAR_ERROR_DIE);
		}

		if(!empty($config->auth->ldap_adminid)) {
			// bind with credentials from config.php
			if ((@ldap_bind($this->conn_id,$config->auth->ldap_adminid,$config->auth->ldap_adminpwd)) == false) {
				return new PEAR_Error('Error binding with \''.$config->auth->ldap_adminid.'\' to LDAP.', 41, PEAR_ERROR_DIE);
			}
		} else {
			// bind anonymously for searching
			if ((@ldap_bind($this->conn_id)) == false) {
				return new PEAR_Error('Error binding anonymously to LDAP.', 41, PEAR_ERROR_DIE);
			}
		}
	}


	/**
	 *   try to authenticate the user, comparing username and password
	 *   with the given source, does the work for login
	 *
	 *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
	 *   @param  string  $username
	 *   @param  string  $password
	 *   @param  boolean true on success
	 */
	function _login( $username , $password )
	{
		global $db;
		global $user;
		 
		$filter = "(& {$this->_dsn['filter']} ({$this->_dsn['userattr']}=$username))";
		// search
		if ($this->_dsn['scope'] == 'sub') {
			$result_id = @ldap_search($this->conn_id, $this->_dsn['basedn'], $filter);
		} else {
			// scope is one
			$result_id = @ldap_list($this->conn_id, $this->_dsn['basedn'], $filter);
		}
		if (!$result_id) {
			return new PEAR_Error("Error searching LDAP.", 41, PEAR_ERROR_DIE);
		}

		// did we get just one entry?
		if (ldap_count_entries($this->conn_id, $result_id) == 1) {

			// then get the user dn
			$entry_id = ldap_first_entry($this->conn_id, $result_id);
			$user_dn  = ldap_get_dn($this->conn_id, $entry_id);

			// and try binding as this user with the supplied password
			if (@ldap_bind($this->conn_id, $user_dn, $password)) {
				// auth successful, fetch all data
				if ($this->_dsn['scope'] == 'sub') {
					$result_id = @ldap_search($this->conn_id, $this->_dsn['basedn'], $filter);
				} else {
					// scope is one
					$result_id = @ldap_list($this->conn_id, $this->_dsn['basedn'], $filter);
				}
				if (($entry_id = @ldap_first_entry($this->conn_id, $result_id)) == false) {
					return new PEAR_Error("Error reading user data.", 41, PEAR_ERROR_DIE);
				}
				$info = @ldap_get_attributes($this->conn_id, $entry_id);
				foreach ($info as $k=>$v) {
					// remove those strange results that it returns, which only contains
					// i.e. [1]=>'sn' ... i dont need this
					if (!is_string($k)) {
						unset($info[$k]);
						continue;
					}
					// remove the 'count' index, since it is useless
					unset($info[$k]['count']);
					// if the array contains just one element set it directly, we
					// dont need an array in this case
					if (sizeof($info[$k]) == 1) {
						$info[$k] = $v[0];
					}
				}
				$info['username'] = $username;
				$info['password'] = $password;

				/**
				 * SX : user is authenticated agains LDAP!
				 * BUT he doesn't exist as oTt-user yet -> create him
				 * He isn't assigned to any project anyway. So no harm done
				 */

				$query = sprintf("SELECT * FROM %s WHERE %s=%s", TABLE_USER, 'login', $db->quote($username));
				if (DB::isError($res = $db->getRow($query))) {
					return $this->raiseError(AUTH_ERROR_DB_READ_FAILED, null, null, null, DB::errorMessage($res));
				}
				unset($res['password']);
				if (sizeof($res)) {
					return $res;
				} else {
					// user is no oTt user yet !!
					if(empty($info['sn'])) $info['sn'] = $username;  // sanity
					if(empty($info['givenName'])) $info['givenName'] = $username; // sanity
					if(empty($info['mail'])) return AUTH_FAILED; // this is absolutely required !
						
					$ret = $user->add_ldap_user_passthrough($info['givenName'],$info['sn'],$username,$info['mail']);
					if($ret === false) return AUTH_FAILED;

					// I am lazy ... Just read the current entry again and return the data
					$query = sprintf("SELECT * FROM %s WHERE %s=%s", TABLE_USER, 'login', $db->quote($username));
					if (DB::isError($res = $db->getRow($query))) {
						return $this->raiseError(AUTH_ERROR_DB_READ_FAILED, null, null, null, DB::errorMessage($res));
					}
					unset($res['password']);
					if (sizeof($res)) {
						return $res;
					}
					return AUTH_FAILED;

				}
			} else {
				// wrong password
				return AUTH_FAILED;
			}
		}
		// user does not exists in LDAP !
		//return -98;
		/**
		 * SX : we might have users which are NO LDAP users but stored in DB
		 * These are usually guest or so which should be able to authenticate against DB data
		 *
		 * In principle a copy from Auth/DB.php
		 */

		$pwd = $this->digest($username,$password);
		$username = $db->quote($username);
		$pwd = $db->quote($pwd);
		$query = sprintf("SELECT * FROM %s WHERE %s=%s AND %s=%s",
		TABLE_USER,
                            'login',$username,
                            'password',$pwd
		);



		if( DB::isError( $res = $db->getRow($query)) )
		{
			return $this->raiseError(AUTH_ERROR_DB_READ_FAILED, null, null,
			null, DB::errorMessage($res) );
		}


		unset($res[$db->options['passwordColumn']]);  // erase the password from the data, so it wont be visible in the session data
		if( sizeof($res) )                          // if the query returns data return them back
		return $res;

		return -98;                         // no data found
		// neither LDAP nor DB !!!
	}

	/**
	 * Is given user in LDAP ?
	 */
	function is_LDAP_user($username)
	{
		 
		$filter = "(& {$this->_dsn['filter']} ({$this->_dsn['userattr']}=$username))";
		// search
		if ($this->_dsn['scope'] == 'sub') {
			$result_id = @ldap_search($this->conn_id, $this->_dsn['basedn'], $filter);
		} else {
			// scope is one
			$result_id = @ldap_list($this->conn_id, $this->_dsn['basedn'], $filter);
		}
		if (!$result_id) {
			return new PEAR_Error("Error searching LDAP.", 41, PEAR_ERROR_DIE);
		}

		// did we get just one entry?
		if (ldap_count_entries($this->conn_id, $result_id) == 1) {
			// user is an ldap user
			return true;
		}
		return false;
	}
}
?>
