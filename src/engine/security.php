<?
/**
 * Security information is stored in an xml file on the server. The security file contains
 * different secuirty groups and each group contains security users. There are 2 main security
 * groups: Guests and Administrators. They function as their names imply. As for users there is
 * one main user account called guest which is the default account used if the user is not logged on.
 * This security class does not contain any permission information it only houses security users
 * and groups. The permissoin information is stored independently within each file. This allows each
 * wiki page to manage it's own permissions and provides admins to specify exactly what groups
 * have what permissions. For simplicity purposes files can only specify group permissions and not
 * user permissions. This can change in the future to provide more granual functionality.
 */
class Security
{
	//Login
	public function login($sUser, $sPassword)
	{
	}
	
	public function logout()
	{
	}
	
	public function isLoggedIn()
	{
	}

	//User
	public function createUser($sUser)
	{
	}
	
	public function activateUser($sUser, $sCode)
	{
	}

	public function deleteUser($sUser)
	{
	}

	public function isMemberOf($sUser, $sGroup)
	{
	}

	//Group
	public function deleteGroup($sName)
	{
	}

	public function renameGroup($sOldName, $sNewName)
	{
	}

	public function addMember($sGroup, $sUser)
	{
	}

	public function removeMember($sGroup, $sUser)
	{
	}
}

?>