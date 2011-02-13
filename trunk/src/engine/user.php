<?

class User
{
	public $sEmail;
	public $sDisplayName;
	public $aMemberOf;
	
	public $bIsLoggedIn = false;
	
	public function __construct($sEmail, $sDisplayName, $aMemberOf, $bIsLoggedIn = false)
	{
		$this->sEmail = $sEmail;
		$this->aMemberOf = $aMemberOf;
		$this->sDisplayName = $sDisplayName;
		$this->bIsLoggedIn = $bIsLoggedIn;
	}

	public function getId()
	{
		return $this->sEmail;
	}
	
	public function isMemberOf($sGroup)
	{
	}
}

?>