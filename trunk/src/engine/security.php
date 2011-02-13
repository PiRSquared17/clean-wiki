<?
/**
 * Security information is stored in an xml file on the server. The security file contains
 * different secuirty groups and each group contains security users. There are 2 main security
 * groups: Guests and Administrators. They function as their names emply. As for users, there is
 * one main user account called guest which is the default account used if the user is not logged on.
 * This security class does not contain any permission information it only houses security users
 * and groups. The permissoin information is stored independently within each file. This allows each
 * wiki page to manage it's own permissions and allows admins to specify exactly what groups
 * have what permissions. For simplicity purposes files can only specify group permissions and not
 * user permissions. This can change in the future to provide more granual functionality.
 */
class Security
{	
	//Login
	public function login($sUser, $sPassword)
	{
		$oUser = false;
		$sUser = strtolower($sUser);
		$oUserElement = Utility::getPath($this->oDocument, "security/users/user[@name='$sUser']");

		if ($oUserElement != null && $oUserElement->getAttributE('password') == md5($sPassword))
		{
			$sDisplayName = $oUserElement->getAttribute('displayName');

			$aMemberOf = array();
			$aMembers = Utility::getPaths($oGroups, "//member[@name='$sUser']");
			foreach ($aMembers as $oMember) $aMemberOf[] = $oMember->parentNode->getAttribute('name');

			$oUser = new User($sUser, $sDisplayName, $aMemberOf);
		}

		return $oUser;
	}

	//User
	public function deleteUser($sUser)
	{
	}

	public function createUser($sUser, $sPassword)
	{
	}

	//Group
	public function deleteGroup($sName)
	{
	}

	public function createGroup($sName)
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