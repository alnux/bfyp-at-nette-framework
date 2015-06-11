<?php namespace App\Config;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Object;
/**
 * Set tables in just one place
 */
class TablesSettings extends Object
{	
	// proxyswhite table
	const T_WHITEPROXY = 'whiteproxy';
	// ipsblack table
	const T_BLACKIP = 'blackip';

	// privileges table
	const T_PRIVILEGE = 'privilege';
	// resources table
	const T_RESOURCE = 'resource';
	// roles table
	const T_ROLE = 'role';
	// permissions table
	const T_PERMISSION = 'permission';
	// users table
	const T_USER = 'user';
	// users_roles table
	const T_USER_ROLE = 'user_role';
	// languages table
	const T_LANGUAGE = 'language';
}