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
	private $oDocument = null;

	public function __construct($sSecuirtyXmlPath = 'engine/security.xml')
	{
		$this->oDocument = new DomDocument('1.0');
		$this->oDocument->load($sSecuirtyXmlPath);
	}

	//Login
	public function login($sUser, $sPassword)
	{
		$oUser = false;
		$sUser = strtolower($sUser);

		//Find the user element within the security users.
		$oUserElement = Utility::getPath($this->oDocument, "//security/users/user[@name='$sUser']");

		//If the user exists then check the password.
		if ($oUserElement != null && $oUserElement->getAttribute('password') == md5($sPassword))
		{
			//Load the user display name.
			$sDisplayName = $oUserElement->getAttribute('displayName');

			//Get all the groups that this user belongs to and add them to the members list.
			$aMemberOf = array();
			$aMembers = Utility::getPaths($this->oDocument, "//security/groups/*member[@name='$sUser']");
			foreach ($aMembers as $oMember) $aMemberOf[] = $oMember->parentNode->getAttribute('name');

			//Create the user with the name and display name and member groups.
			$oUser = new User($sUser, $sDisplayName, $aMemberOf, true);
		}

		//Return the user object which could be false if the user did not exist.
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