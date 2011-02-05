<?

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