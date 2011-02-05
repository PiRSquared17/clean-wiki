<?

class Permissions
{
	private $oPage;

	public function __construct($oPage)
	{
		$this->oPage = $oPage;
	}

	public function get()
	{
	}

	public function clear()
	{
	}

	public function add($sGroup, $sPermission)
	{
	}

	public function has($oUser, $sPermission)
	{
		return true;
	}
}

?>