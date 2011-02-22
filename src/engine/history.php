<?

/**
 * Manages the history and version of a page. History information is stored in the page 
 * header under the <history> tag. History tags contain versions ordered form olderst to most
 * recent. Each version element contains the following information:
 * <version index='INT' datetime='DATETIME' user='STRING' length='INT' format=''>CONTENT</version>
 *
 * @author Charbel Choueiri, charbel.choueiri@live.ca
 */
class History
{
	/**
	 * Points the page that this history belongs to.
	 */
	private $page;

	/**
	 * Creates a new history object belonging to the specified page. This object
	 * provides methods to access and manipulate the page history.
	 */
	public function __construct($page)
	{
		$this->page = $page;
	}

	/**
	 * If this page has never been nodified then it will contain no history and so
	 * returns true. Otherwise if the page has been nodified then returns false.
	 */
	public function isEmpty()
	{
		//Get the comments element form the header.
		$oHistory = $this->getHistory();

		//Finally check if it has any children.
		return !$oHistory->hasChildNodes();
	}

	/**
	 * Returns the specified version index. 
	 *
	 * @return An array containing the version information: index, datetime, user, content.
	 * @throws EXCEPTION_ACCESS_DENIED if user does not have permissions to modify page.
	 * @throws EXCEPTION_INVALID_PARAMETER if user specified an invalid index.
	 */
	public function get($iIndex)
	{
		//Check to ensure that the user has permissions to fetch history versions.
		if ($this->page->permissions->has(PERMISSION_VERSION_GET))
		{
			//Get the history element so we can access all the versions within this page.
			$oHistory = $this->getHistory();

			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of versions.
			if ($iIndex >= 0 && $iIndex < $oHistory->childNodes->length)
			{
				//Get the version at index iIndex.
				$oVersion = $oHistory->childNodes->item($iIndex);

				//Return this versions information.
				return $this->toArray($oVersion, $iIndex);
			}
			else throw new Exception(EXCEPTION_INVALID_PARAMETER);
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * Returns an array of all current page history/versions.
	 *
	 * @return An array of arrays containing: index, datetime, user, content.
	 * @throws EXCEPTION_ACCESS_DENIED if user does not have permissions to modify page.
	 */
	public function getAll()
	{
		//Ensure the user has permissions to modify the page.
		if ($this->page->permissions->has(PERMISSION_HISTORY_GET))
		{
			//Get the history element and create the array which will house the versions.
			$oHistory = $this->getHistory();
			$aHistory = array();

			//Travers all the version and write them to the array.
			for ($oVersion = $oHistory->firstChild, $iIndex = 0; $oVersion != null; $oVersion = $oVersion->nextSibling, $iIndex++)
			{
				//Return this versions information.
				$aHistory[] = $this->toArray($oVersion, $iIndex);
			}

			//Return the history array containing the versions.
			return $aHistory;
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * Adds a new version to the page history containing the old content.
	 *
	 * @return True if added successfully.
	 * @throws EXCEPTION_ACCESS_DENIED if user does not have permissions to modify page.
	 */
	public function add($sOldContent, $sNewContent)
	{
		//Ensure the user has permissions to modify the page.
		if ($this->page->permissions->has(PERMISSION_PAGE_MODIFY))
		{
			//Get the version element to add the new version to.
			$oHistory = $this->getHistory();

			//Create a new version node and append it to the history parent element.
			$oVersion = $oHistory->appendChild($oHistory->ownerDocument->createElement('version'));

			//Set all the attributes for this version: datetime, user, format, length.
			Utility::setAttributes($oVersion, 'datetime', date('Y/m/d H:i:s'), 'user', $this->page->user->getId());

			//Set the version's formated content.
			Utility::encode($oVersion, 'base64/gz', $sOldContent);

			//Make sure we update the modified field so that we can check for changes later and save if there are any.
			return $this->page->modified();
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * Calculates the difference between the specified index and the previous one and returns an array of differences
	 * 
	 * @return Version information where the content is an array of differences with the previous array. 
	 * @throws EXCEPTION_ACCESS_DENIED if user does not have permissions to modify page.
	 * @throws EXCEPTION_INVALID_PARAMETER if user specified an invalid index.
	 */
	public function getDiff($iIndex)
	{
		//Check to ensure that the user has permissions to fetch history versions.
		if ($this->page->permissions->has(PERMISSION_VERSION_GET))
		{
			//Get the history element so we can access all the versions within this page.
			$oHistory =  $this->getHistory();

			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of versions.
			if ($iIndex >= 0 && $iIndex < $oHistory->childNodes->length)
			{
				//Get the version at the specified index.
				$oVersion = $oHistory->childNodes->item($iIndex);
				$sContent = Utility::decode($oVersion);
				$aChanges = array();

				//We need to compare it to the previous version. 
				if ($iIndex > 0)
				{
					//Get the previous version.
					$oPrevious = $oHistory->childNodes->item($iIndex-1);
					$sPrevious = Utility::decode($oPrevious);

					//Compare the 2 and add the changes to the returned array.
					$aChanges = Utility::diff($sPrevious, $sContent);
				}
				//If the index is 0, then this is the first node and therefore there is no history.
				else $aChanges = $sContent;

				//Return this versions information.
				return $this->toArray($oVersion, $iIndex, $aChanges);
			}
			else throw new Exception(EXCEPTION_INVALID_PARAMETER);
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}
	
	/**
	 * Merges any history with a miniume time delta in seconds. Doing the merge after helps ensure that if there
	 * any issues during the saves that they are not merged and lost then. Allowing users to save their work
	 * as they compose it is important but can take up a lot of history space. So merging later helps keep space
	 * managed.
	 *
	 * @throws EXCEPTION_ACCESS_DENIED if user does not have permissions to modify page.
	 */
	public function clean($iMinTimeDelta, $aResult = null)
	{
		//Ensure that the user has the proper permissions.
		if ($this->page->permissions->has(PERMISSION_VERSION_MERGE))
		{
			//Standard result array.
			$aResultArray = array('count'=>0, 'length'=>0, 'compressedLength'=>0);
		
			//To help keep track of which has been modified the result array holds the total size saved and the number of removed version.
			//If we are doing a clean on mltiple files then it may help to use the same result array.
			if ($aResult == null) $aResult = $aResultArray;
			else Utility::ensureKeys($aResult, $aResultArray);

			//Get the history element so we can travers all version and check them.
			$oHistory =  $this->getHistory();

			//If there is no history or only one history then there is nothing to do. We do this one check here
			//to avoid doing a check within the loop below. This helps in performance.
			if ($oHistory->childNodes->length > 1)
			{
				//Travers all the version and remove from previous to current. This means we'll keep the most
				//recent version of a history. The main criterial to remove a version if edited by the same user and
				//has a time delta of MinTimeDelta.
				for ($oVersion = $oHistory->firstChild->nextSibling; $oVersion != null; $oVersion = $oVersion->nextSibling)
				{
					//Get the previous version and calculate the time delta.
					$oPrevious = $oVersion->previousSibling;
					$iTimeDelta = strtotime($oVersion->getAttribute('datetime')) - strtotime($oPrevious->getAttribute('datetime'));

					//If edited by the same user within MiniTimeDelte (in seconds) then remove the previous version since the current is the latest.
					//PERFORMANCE NOTE: we are doing the delta check first because if it failed we can save cycles by not having to 
					//get the user attributes and check them. Even though it is faster to check users first it is usually more likely that the same
					//user will update the same file.
					if ($iTimeDelta <= $iMinTimeDelta && $oVersion->getAttribute('user') == $oPrevious->getAttribute('user'))
					{
						//Remove the previous. Since we are remove the previous node the current node is not affected and we can continue
						//to travers the nodes.
						$History->removeChild($oPrevious);
					}
				}
			}

			//Finally return the cleanup results.
			return $aResult;
		}
		else throw new Exception(EXCEPTION_ACCESS_DENIED);
	}

	/**
	 * A Helper function to fetch the history xml element from the page header.
	 */
	private function getHistory()
	{
		return  $this->page->get('//page/history');
	}
	
	/**
	 * A helper function converts the xml element attributes and content into an array.
	 */
	private function toArray($oVersion, $iIndex, $sContent = null)
	{
		return array('index'=>$iIndex,
					 'datetime'=>$oVersion->getAttribute('datetime'),
					 'user'=>$oVersion->getAttribute('user'),
					 'content'=>($sContent == null ? Utility::decode($oVersion) : $sCntent));
	}	
}

?>