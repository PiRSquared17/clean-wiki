<?

class History
{
	private $oPage;
	
	public function __construct($oPage)
	{
		$this->oPage = $oPage;
	}

	public function isEmpty()
	{
		//Get the comments element form the header.
		$oHistory = $this->oPage->get('//page/history');

		//Finally check if it has any children.
		return !$oHistory->hasChildNodes();
	}

	public function getAll()
	{
		//Ensure the user has permissions to modify the page.
		if ($this->oPage->permissions->has($this->oPage->user, PERMISSION_HISTORY_GET))
		{
			//Get the version element to add the new version to.
			$oHistory = $this->oPage->get('//page/history');
			$aHistory = array();

			//Travers all the version and write them to the array.
			for ($oVersion = $oHistory->firstChild, $iIndex = 0; $oVersion != null; $oVersion = $oVersion->nextSibling, $iIndex++)
			{
				//Return this versions information.
				$aHistory[] = array('index'=>$iIndex,
									'datetime'=>$oVersion->getAttribute('datetime'),
									'user'=>$oVersion->getAttribute('user'),
									'length'=>$oVersion->getAttribute('length'),
									'content'=>Utility::decode($oVersion));
			}

			return $aHistory;
		}
		else return false;
	}

	public function add($sOldContent, $sNewContent)
	{
		$oUser = $this->oPage->user;
	
		//Ensure the user has permissions to modify the page.
		if ($this->oPage->permissions->has($oUser, PERMISSION_PAGE_MODIFY))
		{
			//Get the version element to add the new version to.
			$oHistory = $this->oPage->get('//page/history');

			//Create a new version node and append it to the history parent element.
			$oVersion = $oHistory->appendChild($oHistory->ownerDocument->createElement('version'));

			//Set all the attributes for this version. Attributes include: datetime, user, format.
			Utility::setAttributes($oVersion, 'datetime', date('Y/m/d H:i:s'), 'user', $oUser->getId(), 'length', strlen($sOldContent), 'format', 'base64/gz');

			//Set the version's text.
			$oVersion->nodeValue = base64_encode(gzcompress($sOldContent));

			//Make sure we update the modified field so that we can check for changes later and save if there are any.
			return $this->oPage->modified();
		}
		else return false;
	}

	public function get($oUser, $iIndex)
	{
		//Check to ensure that the user has permissions to fetch history versions.
		if ($this->oPage->hasPermission($oUser, PERMISSION_VERSION_GET))
		{
			//Get the history element so we can access all the versions within this page.
			$oHistory = $this->oPage->get('//page/history');

			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of versions.
			if ($iIndex >= 0 && $iIndex < $oHistory->childNodes->length)
			{
				//Get the version at the specified index.
				$oVersion = $oHistory->childNodes->item($iIndex);

				//Return this versions information.
				return array('index'=>$iIndex,
							 'datetime'=>$oVersion->getAttribute('datetime'),
							 'user'=>$oVersion->getAttribute('user'),
							 'content'=>Utility::decode($oVersion));
			}
			else return false;
		}
		else return false;
	}

	public function getDiff($iIndex)
	{
		//Check to ensure that the user has permissions to fetch history versions.
		if ($this->oPage->hasPermission($oUser, PERMISSION_VERSION_GET))
		{
			//Get the history element so we can access all the versions within this page.
			$oHistory = $this->oPage->get('//page/history');

			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of versions.
			if ($iIndex >= 0 && $iIndex < $oHistory->childNodes->length)
			{
				//Get the version at the specified index.
				$oVersion = $oHistory->childNodes->item($iIndex);
				$aChanges = array();
				
				//We need to compare it to the previous version. If the index is 0,
				//then this is the first node and therefore there is no history.
				if ($iIndex == 0)
				{
//TODO: not sure about this.
					$aChanges = Utility::decode($oVersion);
				}
				else
				{
					//Get the previous version.
					$oPreviousVersion = $oHistory->childNodes->item($iIndex-1);

//TODO: strip out the HTML, maybe!!
					$sCurrent = Utility::decode($oVersion);
					$sPrevious = Utility::decode($oPreviousVersion);

					//Compare the 2 and add the changes to the returned array.
					$aChanges = Utility::diff($sPrevious, $sCurrent);
				}

				//Return this versions information.
				return array('index'=>$iIndex,
							 'datetime'=>$oVersion->getAttribute('datetime'),
							 'user'=>$oVersion->getAttribute('user'),
							 'changes'=>aChanges);
			}
			else return false;
		}
		else return false;
	}
}

?>