<?

define('PAGES_DIR', getcwd() . '/pages/');
define('START_PAGE', 'Home');
define('WIKI_VERSION', '2.1');

include_once('page.php');
include_once('search.php');
include_once('security.php');

class Wiki
{
	public $page;
	public $security;
	
	public function __construct()
	{
		$this->page = new Page('home');
	}
	
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

?>