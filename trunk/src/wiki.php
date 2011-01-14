<?

class Wiki
{
	public $page;
	public $security;
	
	public function load()
	{
	}

	public function handle($sRequest)
	{
	}

	public function getRequest($sKey, $sDefault = null)
	{
	}
}

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

class Page
{
	private $sPath;

	private $oHeader;
	private $oContent;
	
	private $aHistory;
	private $aComments;
	private $aPermissions;

	private $oSearchTree;
	
	//General
	public function getName()
	{
	}

	public function getContent()
	{
	}
	
	public function getUnformatedContent()
	{
	}

	public function isPage()
	{
	}

	public function isHistory()
	{
	}

	public function isSearch()
	{
	}

	public function isChanges()
	{
	}

	public function isEditable()
	{
	}
	
	public function inEditMode()
	{
	}

	//History
	public function hasHistory()
	{
	}
	
	public function getHistory()
	{
	}
	
	public function addChange($sUser, $sOldContent, $sNewContent)
	{
	}
	
	public function getChange($iIndex)
	{
	}
	
	public function getChangeDiff($iIndex)
	{
	}

	//Comments
	public function hasComment()
	{
	}

	public function getComments()
	{
	}

	public function addComment($sUser, $sComment)
	{
	}

	public function deleteComment($iIndex)
	{
	}

	public function modifyComment($iIndex, $sUser, $sComment)
	{
	}

	//Search
	public function search($sSearchString)
	{
	}

	public function updateSearchTree($sContent)
	{
	}

	//Permissions
	public function allowComments($bAllow)
	{
	}

	public function getPermissions()
	{
	}

	public function clearPermissions()
	{
	}

	public function addPermission($sGroup, $iPermission)
	{
	}

	public function hasPermission($sUser, $iPermission)
	{
	}
}

class Search
{
	protected $aTree = array();
	protected $sContent = '';

	public function build($sContent)
	{
	}

	public function load($sTree)
	{
	}

	public function serialize()
	{
	}
	
	//Searching
	public function find($sString)
	{
	}
	
	//Tree management.
	protected function addWord($sWord)
	{
	}

	protected function findWord($sWord)
	{
	}
}

?>