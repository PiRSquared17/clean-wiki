<?
define('USER_ROOT', 'root');

/**
 * 
 *
 * @author Charbel Choueiri, charbel.choueiri@live.ca
 */
class Permissions
{
	private $page;

	public function __construct($oPage)
	{
		$this->page = $oPage;
	}

	/**
	 * Returns all the groups with their permissions. There are 2 instances where permissions are required
	 * by the user: 1- Creating new page, the default permissions are sent. 2- Editing a page, you can edit
	 * a page but do not have permissions to modify the page's permissions.
	 *
	 * Returns an array of arrays containing: 'name', 'permissions'=array(permissions).
	 */
	public function get()
	{
		//Ensure the user has permissions to modify the page.
		if ($this->page->permissions->has(PERMISSION_PERMISSIONS_GET))
		{
			//Get the permissions element and create the array which will house the groups and thier permission.
			$oPermissions = $this->getPermissions();
			$aPermissions = array();

			//Travers all the permissions and write them to the array.
			for ($oGroup = $oPermission->firstChild; $oGroup != null; $oGroup = $oGroup->nextSibling)
			{
				//Return this versions information.
				$aPermissions[] = $this->toArray($oGroup);
			}

			//Return the permissions array containing the groups and thier permissions.
			return $aPermissions;
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * Set this page permissions. Any exsiting permission will be removed and replaced by the specified permissions.
	 * 
	 * @param aPermissions is an array of arrays containing ['name'=>'GROUP_NAME', 'permissions'=>'P1|P2|P3...']
	 */
	public function set($aPermissions)
	{
		//Ensure user has permission to do this operation.
		if ($this->page->permissions->has(PERMISSION_PERMISSIONS_SET))
		{
			//Get the permission element so we can remove existing and add the new one.
			$oPermissions = $this->getPermissions();

			//Remove existing permissions.
			while ($oPermissions->hasChildNodes ()) $oPermissions->removeChild($oPermissions->firstChild);
			
			//For each permission create the element and set the attributes.
			foreach ($aPermissions as $aPermission)
			{
				//If the specified permissions does not contain the required keys then skip it.
				if (!isset($aPermission['name']) || !isset($aPermission['permissions'])) continue;

				//Create and add a new permission element to the permissions
				Utility::appendChild($oPermissions, 'group', 'name', $aPermission['name'], 'permissions', $aPermission['permissions']);
			}
			
			//Indicate to the page that it has been modified.
			$this->page->modified();
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * Check if the logged on user has the specified permission for this page. If the user is
	 * "root" then regardless if page has permission it will always be true.
	 *
	 * @return true if the user has permissions otherwise returns false.
	 */
	public function has($sPermission)
	{
		//Get the logged on user.
		$oUser = $this->page->user;
		$oPermissions = $this->getPermissions();

		//If user is root then return true.
		if ($oUser->getId() == USER_ROOT) return true;

		//For each group within this page, check if this group has this permission. If so then check if user belongs to this group.
		//If so then return true.
		for ($oPermission = $oPermission->firstChild; $oPermission != null; $oPermission = $oPermission->nextSibling)
		{
			//Get the group name and group permissions.
			$sGroupName = $oPermission->getAttribute('name');
			$aPermissions = explode('|', $oGroup->getAttribute('permissions'));

			//If the group has the required permission then check if the user belongs to this group. If so then return true.
			if (in_array($aPermissions, $sPermission) && $oUser->isMemberOf($sGroupName)) return true;
		}

		//If no permission was matched then return false.
		return false;
	}

	/**
	 * A Helper function to fetch the history xml element from the page header.
	 */
	private function getPermissions()
	{
		return  $this->page->get('//page/permissions');
	}

	/**
	 * A helper function converts the xml element attributes into an array.
	 */
	private function toArray($oGroup)
	{
		return array('name'=>$oGroup->getAttribute('name'), 'permissions'=>explode('|', $oGroup->getAttribute('permissions')));
	}
}

?>