<?

class User
{
	public $sEmail;
	public $sDisplayName;

	public $aMemberOf;
	
	public function __construct($sEmail, $sDisplayName)
	{
		$this->sEmail = $sEmail;
		$this->sDisplayName = $sDisplayName;
	}

	public function getId()
	{
		return $this->sEmail;
	}
}

?>