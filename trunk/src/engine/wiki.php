<?
define('WIKI_VERSION', '0.1');

class Wiki
{
	protected $user = null;
	protected $page = null;
	protected $security = null;

//Request information

	//Holds the request action.
	public $action = '';

	//Holds the History or Version request index.
	public $index = 0;

//Default Links

	//Points the home page.
	public $homeUrl = '?page=Home';
	
	//Url to create a new page.
	public $newUrl = '?action=new';

	//Points the Recent Changes Page.
	public $recentChangesUrl = '?action=recentchanges';

	//Points the page history. 
	public $editUrl = '';
	public $historyUrl = '';

	public $loginUrl = '?action=login';
	public $logoutUrl = '?action=logout';
	public $securityUrl = '?aciton=security';
	public $createAccountUrl = '?action=createaccount';

//General

	//Indicates if the page is a fixed page such as Recent Changes, Search, History of a page, etc.
	public $isFixed = false;
	
	//Indicates if the page is a wiki page.
	//This can be true while the fixed is true as well; in the case when viewing History if a page.
	public $isWikiPage = false;

//Page

	//Holds the current page's URL
	public $pageUrl = '';

	//Indicates that it is displaying a page.
	public $isPage = false;

	//Indicates if the current does not exist and is a new page to be created.
	public $newPage = false;
	
	//Indicates whether the page is in edit mode.
	public $editPage = false;
	
	//Indicates whether the title is in edit mode. This is also dependent on editPage.
	public $editTitle = false;

	//Holds the current page name, which could also be a fixed page such as Recent Changes.
	public $pageName = '';

	//This is only populated if the page is a wiki page and not a fixed page.
	public $pageContent = '';

	//Indicates if page is editable. Page may be editbale but user does not have permission.
	public $isEditable = false; 
	public $hasEditPermissions = false;
	
//Comments

	public $showComments = false;

	//Indicates if page has comments to be displayed.
	public $hasComments = false;

	//Indicates if comments are allowed within this page.
	public $allowsComments = false;

	//Indicates if user has permissions to add comments. If not allowed this it does not matter if has permissions.
	public $hasCommentPermissions = false;

	//Array for all comments: user, datetime, comment, deleted
	protected $comments = array();
	protected $commentCount = 0;
	protected $commentIndex = 0;

//History

	//If is wiki page and page has history.
	public $hasHistory = false;

	//If is wiki page and user has permissions to access history.
	public $hasHistoryPermissions = false;

	//Array page history with attributes: user, datetime, length in bytes.
	protected $history = array();
	protected $historyCount = 0;
	protected $historyIndex = 0;

//History extras, only if hasHistory 

	//Indicates if page has creator. Only true when isWikiPage and has history.
	public $hasCreator = false;
	public $createdBy = '';
	public $createdOn = '';

	//Indicates if page has been modified. Only true when isWikiPage and has more than on history.	
	public $isModified = false;
	public $lastModifiedBy = '';
	public $lastModifiedOn = '';

//Login & Security

	//True if the user is logged on.
	public $isLoggedIn = false;

	//True if this wiki allows users to create accounts and user is not logged on.
	public $canCreateAccount = false;

	//Indicates if user is an administartor, to show 
	public $hasSecurityPermissions = false;
	
//User

	public $userName = '';
	public $userEmail = '';
	

//Recent Changes

	public $isRecentChanges = false;

	//Array of all pages.
	protected $pages = array();
	protected $pageCount = 0;
	protected $pageIndex = 0;
	
//Search

	//Indicates that we are in the fixed search page.
	public $isSearch = false;

	//Holds the search string if user is searching.
	public $sSearchString = '';
	
	protected $search = array();
	protected $searchCount = 0;
	protected $searchIndex = 0;
	
//Constructor

	public function __construct()
	{
//TODO: Load from session.
		$this->user = new User('Guest', 'Guest', array());

		$this->index = Utility::getRequest('index', 0);
		$this->action = Utility::getRequest('action', 'view');
		$this->pageName = Utility::getRequest('page', 'Home');
		$this->searchString = Utility::getRequest('search', '');

		if ($this->action == 'search')
		{
			$this->loadSearchResults();
		}
		else if ($this->action == 'login')
		{
		}
		else if ($this->action == 'logout')
		{
		}
		else if ($this->action == 'createaccount')
		{
		}
		else if ($this->action == 'security')
		{
		}
		else if ($this->action == 'recentchanges')
		{
			$this->loadRecentChanges();
		}
		else
		{
			if ($this->action == 'new') $this->pageName = Utility::getRequest('page', '');
			
			$oPage = new Page($this->user, $this->pageName);

			if ($this->action == 'comment')
			{
				//TODO: check the conditions to which this can occure.
				$oPage->comments->add($this->user, Utility::getRequest('comment', ''));
				$this->action = '';
			}
			else if ($this->action == 'edit')
			{
				$this->editPage = true;
				$this->editTitle = ($this->pageName != 'Home');
			}
			else if ($this->action == 'comment_modify')
			{
			}
			else if ($this->action == 'comment_delete')
			{
			}
			else if ($this->action == 'save' || $this->action == 'save&close')
			{
				$oPage->save($this->user, Utility::getRequest('content', ''));
				$this->action = ($this->action == 'save' ? 'edit' : '');
			}

			$this->loadPage($oPage);

			if ($oPage->exists()) $oPage->write();
		}
	}

	public function loadPage($oPage)
	{
		$this->page = $oPage;
		
		if ($this->page->exists())
		{
			//Page
			$this->isPage = true;
			$this->pageUrl = "?page={$this->page->getName()}";
			$this->pageName = $this->page->getName();
			$this->editUrl = "?page={$this->pageName}&action=edit";
			$this->isEditable = $this->page->isEditable();
			$this->pageContent = $this->page->getContent();

			//Permissions
			$this->hasEditPermissions = $this->page->permissions->has($this->user, PERMISSION_HISTORY_GET);
			$this->hasHistoryPermissions = $this->page->permissions->has($this->user, PERMISSION_HISTORY_GET);
			$this->hasCommentPermissions = $this->page->permissions->has($this->user, PERMISSION_COMMENT_ADD);

			//History
			$this->history = $this->page->history->getAll();
			$this->historyUrl = "?page={$this->pageName}&action=history";
			$this->historyCount = count($this->history);

			//History extras
			if ($this->historyCount > 0)
			{
				$this->hasHistory = true;
				$this->hasCreator = true;
				$this->createdBy = $this->history[0]['user'];
				$this->createdOn = $this->history[0]['datetime'];

				if ($this->historyCount > 1)
				{
					$this->isModified = true;
					$this->lastModifiedBy = $this->history[$this->historyCount-1]['user'];
					$this->lastModifiedOn = $this->history[$this->historyCount-1]['datetime'];
				}
			}

			//Comments
			$this->showComments = ($this->action == 'view');
			$this->comments = $this->page->comments->get();
			$this->hasComments = !$this->page->comments->isEmpty();
			$this->commentCount = count($this->comments);
			$this->allowsComments = $this->page->comments->isAllowed();
		}
		else
		{
			$this->newPage = true;
			$this->editPage = true;
			$this->editTitle = ($this->pageName != 'Home');
		}
	}

//Load search result

	public function loadSearchResults()
	{
		//TODO: get from language file.
		$this->pageName = 'Search';
		$this->isFixed = true;
		$this->isSearch = true;
	}

//Resent Changes
	
	public function loadRecentChanges()
	{
		//TODO: get from language file.
		$this->pageName = 'Recent Changes';
		$this->isFixed = true;
		$this->isRecentChanges = true;

		//Get all pages and set counters.
		$aPages = $this->getAllPages();
		foreach ($aPages as $iIndex=>$oPage) $this->pages[] = $this->populatePage($oPage);

		//Set Counters. 
		$this->pageIndex = 0;
		$this->pageCount = count($this->pages);

		//Sort them according to last modified.
		usort($this->pages, function($aPage1, $aPage2){ return ($aPage1['datetime'] < $aPage2['datetime']); });
	}

	public function firstPage()
	{
		$this->pageIndex = 1;
		return ($this->pageCount > 0 ? $this->pages[0] : false);
	}

	public function nextPage()
	{
		return ($this->pageIndex < $this->pageCount ? $this->pages[$this->pageIndex++] : false);
	}

	public function populatePage($oPage)
	{
		$sUser = 'UNKNOWN';
		$bIsNew = true;
		$sDateTime = date('Y/m/d H:i:s', filemtime($oPage->getPath()));
		$sShortDate = '';

		$aHistory = $oPage->history->getAll();
		$iHistoryCount = count($aHistory);
		if ($iHistoryCount > 0)
		{
			$sUser = $aHistory[$iHistoryCount-1]['user'];
			$sDateTime = $aHistory[$iHistoryCount-1]['datetime'];
			if ($iHistoryCount > 1) $bIsNew = false;
		}

		$sShortDate = Utility::getShortDate(strtotime($sDateTime));
		return array('name'=>$oPage->getName(), 'url'=>'?page='.$oPage->getName(), 'datetime'=>$sDateTime, 'shortDate'=>$sShortDate, 'user'=>$sUser, 'isNew'=>$bIsNew);
	}

//History methods.

	public function firstHistory()
	{
		$this->historyIndex = 1;
		return ($this->hasHistory ? $this->populateHistory($this->history[0]) : false);
	}

	public function nextHistory()
	{
		return ($this->historyIndex < $this->historyCount ? $this->populateHistory($this->history[$this->historyIndex++]) : false);
	}

	public function getVersion($iIndex)
	{
		return ($iIndex < $this->historyCount ? $this->history[$iIndex] : false);
	}

	public function getVersionDiff($iIndex)
	{
		return ($iIndex < $this->historyCount ? $this->history[$iIndex] : false);
	}

	protected function populateHistory($aHistory)
	{
		$aHistory['diffUrl'] = "?page={$this->pageName}&action=diff&index={$aHistory['index']}";
		$aHistory['viewUrl'] = "?page={$this->pageName}&action=version&index={$aHistory['index']}";
		return $aHistory;
	}

//Comments

	public function firstComment()
	{
		$this->commentIndex = 1;
		return ($this->hasComments ? $this->comments[0] : false);
	}

	public function nextComment()
	{
		return ($this->commentIndex < $this->commentCount ? $this->comments[$this->commentIndex++] : false);
	}

//Helper functions

	protected function getAllPages()
	{
		$aPages = array();

		//Get all pages within page direcotry/
		$oDirectory = opendir(PAGES_DIR);
		while ($sFile = readdir($oDirectory))
		{
			//Split the file and the extension, check if the extension if html.
			if (is_file(PAGES_DIR . $sFile) && $iIndex = strrpos($sFile, '.'))
			{
				$sName = substr($sFile, 0, $iIndex);
				$sExtension = substr($sFile, $iIndex + 1);
				if ($sExtension == 'html') $aPages[] = new Page($this->user, $sName);
			}
		}
		
		return $aPages;
	}

	
	
}


?>