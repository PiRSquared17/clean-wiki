<?
define('PAGE_MODE_VIEW', 'VIEW');
define('PAGE_MODE_EDIT', 'EDIT');
define('PAGE_MODE_HIST', 'HIST');
define('PAGE_MODE_VERS', 'VERS');
define('PAGE_MODE_DIFF', 'DIFF');

define('PERMISSION_COMMENT_ADD', 0);
define('PERMISSION_COMMENT_MODIFY', 1);
define('PERMISSION_COMMENT_DELETE', 1);

define('PERMISSION_HISTORY_GET', 1);
define('PERMISSION_VERSION_GET', 1);

define('PERMISSION_PAGE_MODIFY', 1);

class Page
{
	private $sPath;
	private $sName;

	private $bExists = false;
	private $bModified = false;
	private $bIsEditable = true;

	private $oHeader;
	private $sHeader;
	private $sContent;

	private $oSearchTree;

	public $user = null;
	public $history;
	public $comments;
	public $permissions;

	/**
	 * Attempts to load the page with the specified name. If the page does not
	 * exist then loads the page in edit mode to allow users to create the page.
	 *
	 * @param sName string name of the page to load. If does not exists loads in edit mode.
	 */
	public function __construct($oUser, $sName = 'Unnamed')
	{
		$this->user = $oUser;
		$this->Load($sName, PAGE_MODE_VIEW);
	}
	
	private function load($sName, $sMode)
	{
		//If the name is empty or is null then throw exception.
		//if ($sName == null || strlen($sName) == 0) throw new Exception('Invalid page name.');

		//Store the page path to facilitate and simplify access so we don't have to 
		//keep typing the full path every time. Also store the name in this class.
		$this->sName = $sName;
		$this->sPath = PAGES_DIR . $sName . '.html';

		//Set the page mode the the specified mode. This can change later depending whether 
		//the file exists or if it corrupt.
		$this->$sMode = $sMode;
		
		//Create a new XML header Document while will later be populated by the page header data.
		$this->oHeader = new DomDocument('1.0');
		
		//Create the helper classes.
		$this->comments = new Comments($this);
		$this->history = new History($this);
		$this->permissions = new Permissions($this);

		//Check if the page exists. If so then get it's content.
		if (!($sName == null || strlen($sName) == 0) && file_exists($this->sPath))
		{
			//Set exists to true because we accturally found it.
			$this->bExists = true;

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

		//Notice in the code above we handled the success code file and branched of to the error case
		//later. This is generally the code style I'll use through all my code.
	}
	
//Helper functions

	public function write()
	{
		//If it has been modified then write page back to disk.
		if ($this->bModified)
		{
			//Save the file back to disk
			$sContent = $this->oHeader->saveXml($this->oHeader);
			file_put_contents($this->sPath, trim($sContent) . "\r\n\r\n" . trim($this->sContent));
		}
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

	/**
	 * Get the element or attribute VALUE for the specified xpath using this page's header.
	 * This is an enternal helper function to assist in getting specific attributes or
	 * elements from the header. This is designed to get the first node of the query only
	 * which is mainly helpfull when getting root page elements and their attribute values or
	 * using an xpath that returns only one node.
	 *
	 * The default value is returned when the xpath does not return any values. This is helpful 
	 * mainly for getting attribute values when the attribute is not set the default value is 
	 * returned.
	 *
	 * @param sXPath an xpath string
	 * @param sDefault a default value if the specified xpath does not exist.
	 * @return a DomElement or attribute string value that the xpath points to. 
	 * or if xpath does not exist then returns the specified default value.
	 */
	public function get($sXPath, $sDefault = null)
	{
		//Create the xpath object using the page header to execute the xpath query.
		$oXPath = new DOMXpath($this->oHeader);

		//Execute the xpath query.
		$oNodeList = $oXPath->query($sXPath);

		//If there are nodes returned then get the first node.
		if ($oNodeList && $oNodeList->length > 0)
		{
			//Get the first node in the list. The reason is described in the comments above.
			$oNode = $oNodeList->item(0);

			//If node is attribute then return the value, otherwise return the node.
			if ($oNode instanceof DomAttr) return $oNode->value;
			else if ($oNode instanceOf DomElement) return $oNode;
		}

		//If no nodes found then return the default value.
		return $sDefault;
	}

	public function modified()
	{
		return ($this->bModified = true);
	}
	
	//General
	public function getName() { return $this->sName; }
	public function getPath() { return $this->sPath; }
	public function exists() { return $this->bExists; }
	public function getContent() { return $this->sContent; }
	public function isEditable() { return $this->bIsEditable; }

	public function getUnformatedContent()
	{
	}

	/**
	 * 
	 */
	public function save($oUser, $sContent)
	{
		$sContent = trim($sContent);

		//if the page content has changed then save it.
		if ($sContent != $this->sContent)
		{
			//TODO: Check user permissions first.

			//If the page was saved within the last 5 minutes by the same user then merge the saves into one.

			//Add a new history and add the old content to it.
			$this->history->add($this->sContent, $sContent);

			//Update the search content information.
			$this->updateSearchTree($sContent);

			//Set the new content of this file.
			$this->sContent = $sContent;

			//Indicate that the file has changed.
			$this->modified();
		}
	}

	//Search
	public function search($sSearchString)
	{
	}

	public function updateSearchTree($sContent)
	{
	/*
		$oSearch = new SearchTree();
		$oSearch->build($sContent);

		$oSearchTree = $this->get('//page/search/tree');
		$oSearchTree->setAttribute('format', 'base64/gz');
		$oSearchTree->nodeValue =  base64_encode(gzcompress($oSearch->save()));

		$oSearchContent = $this->get('//page/search/content');
		$oSearchContent->setAttribute('format', 'base64/gz');
		$oSearchContent->nodeValue =  base64_encode(gzcompress($oSearch->textContent));
	*/
	}
}

?>