<?
define('PAGE_MODE_VIEW', 'VIEW');
define('PAGE_MODE_EDIT', 'EDIT');

class Page
{
	private $sPath;
	private $sName;
	private $sMode;
	private $bExists = false;

	private $oHeader;
	private $sHeader;
	private $sContent;

	private $aHistory = null;
	private $aComments = null;
	private $aPermissions = null;

	private $oSearchTree;

	/**
	 * Attempts to load the page with the specified name. If the page does not
	 * exist then loads the page in edit mode to allow users to create the page.
	 *
	 * @param sName string name of the page to load. If does not exists loads in edit mode.
	 */
	public function __construct($sName = 'Unnamed', $sMode = PAGE_MODE_VIEW)
	{
		//If the name is empty or is null then throw exception.
		if ($sName == null || strlen($sName) == 0) throw new Exception('Invalid page name.');
	
		//Store the page path to facilitate and simplify access so we don't have to 
		//keep typing the full path every time. Also store the name in this class.
		$this->sName = $sName;
		$this->sPath = PAGES_DIR . $sName . '.html';

		//Set the page mode the the specified mode. This can change later depending whether 
		//the file exists or if it corrupt.
		$this->$sMode = $sMode;
		
		//Create a new XML header Document while will later be populated by the page header data.
		$this->oHeader = new DomDocument('1.0');
		
		//Check if the page exists. If so then get it's content.
		if (file_exists($this->sPath))
		{
			//Get the text content of the page from the file using the page path.
			$sContent = file_get_contents($this->sPath);
			
			//Since the header and the content is separated by "\r\n\r\n" then find the first position of 
			//this string and split the page into it's header and content.
			//Note: the page content can contain "\r\n\r\n" but this does not matter because we 
			//know that the header will never contain this string and we always look for the 
			//first occurrence of this string.
			$iIndex = strpos($sContent, "\r\n\r\n");

			//If the string was found then split the file data into the header and content and 
			//parse the XML header into the head document.
			if ($iIndex && $iIndex > 0)
			{
				//Set exists to true because we accturally found it.
				$this->bExists = true;

				//Read the header from the file data.
				$this->sHeader = substr($sContent, 0, $iIndex);

				//Read the page content from the file data skipping the separator token "\r\n\r\n".
				//We are skipping 4 because there are 4 characters in "\r\n\r\n";
				$this->sContent = substr($sContent, $iIndex + 4);

				//Parse the XML header to get the DOM Document.
				//If the header parsed successfully then check the integrity of the header to see if all
				//required elements are there. If not then add whatever is missing.
				if ($this->oHeader->loadXML($this->sHeader)) $this->ensureIntegrity();

				//If an error occurred while attempting to parse the XML header data then assume 
				//that the file is corrupt. create a new empty header and use the entire file 
				//data as the page content and set the page in edit mode.
				else $this->create($sName, $sContent);
			}
			//If this string was not found or it is at position 0 then we know something is wrong.
			//In this case we'll assume that the page is corrupt and create a new empty header while
			//using the entire file data as the page content to allow the user to fix it.
			else $this->create($sName, $sContent);
		}
		//If page does not exist then set the page in edit mode to allow the user to create it.
		//Use the helper function to create a new empty page.
		else $this->create($sName, '');
		
		$this->ensureIntegrity();
		$this->ensureIntegrity();
		
		//Notice in the code above we handled the success code file and branched of to the error case
		//later. This is generally the code style I'll use through all my code.
	}
	
	private function create($sName, $sContent)
	{
		//Set the page mode to edit.
		$this->sMode = PAGE_MODE_EDIT;

		//Set the standard XML header content for a new page. This will include the permissions, 
		//history, search (tree, and text content), and finally comments. Notic that only guest permissions
		//is required because adminitrators will always have access to the file whether it is given
		//or not.
		$this->sHeader = "<page><permissions><group name='guest' permission='VIEW|COMMENT'/></permissions><history/><search><tree/><content/></search><comments/></page>";

		//Parse the XML header document to be accessed later.
		$this->oHeader->loadXML($this->sHeader);

		//Set the content of the page to the specified content.
		$this->sContent = $sContent;
	}
	
	/**
	 * This function check if this page's header contains all the required basic elements. If the element
	 * are missing then they are added. We do not need to worry about attribute because whenever attributes
	 * are required a default value is always provided if they do not exist.
	 *
	 * The reason we are doing this is; first to ensure the header is correct and not cause any errors
	 * when things are requested that do not exist, and second, if in the future we added new elements this
	 * will automatically add them to existing file of older version.
	 *
	 * For this version the following elements are required: 
	 * <permissions/><history/><search><tree/><content/></search><comments/>
	 * The search element has both a tree index and a plain text content (with no HTML). The rest of
	 * the element are self explanatory.
	 */
	private function ensureIntegrity()
	{
		//This array contains a list of all required elements. If there are sub elements 
		//the parent must occure alone first to ensure it gets greated first so that children can be 
		//created later. See search for example.
		$aRequiredElements = array('//page', '//page/permissions', '//page/history', '//page/search', '//page/search/tree', '//page/search/content', '//page/comments');

		//We are going to be using xpath to find required elements. So reate the xpath object first.
		$oXPath = new DOMXpath($this->oHeader);

		//For each required element check if it exists if not then add it.
		foreach ($aRequiredElements as $sRequiredElement)
		{
			//Query the header for the required element.
			$oElements = $oXPath->query ($sRequiredElement);

			//If element was not found then create it. This is done by getting the element's parent 
			//and adding the new element to it as a child.
			if (!$oElements || $oElements->length == 0)
			{
				//Split the required element in to the parent and the child element.
				$iIndex = strrpos($sRequiredElement, '/');
				$sChild = substr($sRequiredElement, $iIndex+1);
				$sParent = substr($sRequiredElement, 0, $iIndex);

				//Query for the parent. The parent must exist because we ordered our required
				//element array from parent element to child element.
				$oParent = $oXPath->query($sParent)->item(0);

				//Create the child element and add it to the parent.
				$oParent->appendChild($this->oHeader->createElement($sChild));
			}
		}
	}

	//General
	public function getName()
	{
		return $this->sName;
	}
	
	public function getPath()
	{
		return $this->sPath;
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

	public function isHistoryMode()
	{
	}

	public function isVersionMode()
	{
	}

	public function isDiffMode()
	{
	}

	public function inEditMode()
	{
	}
	
	public function exists()
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

	public function allowsComments()
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

?>