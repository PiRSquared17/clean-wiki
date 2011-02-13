<?

class User
{
	public $sEmail;
	public $sDisplayName;
	public $aMemberOf;
	
	public function __construct($sEmail, $sDisplayName, $aMemberOf)
	{
		$this->sEmail = $sEmail;
		$this->aMemberOf = $aMemberOf;
		$this->sDisplayName = $sDisplayName;
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