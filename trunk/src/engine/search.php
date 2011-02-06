<?

class Search
{
	protected $aTree = array(0=>0, 1=>array(), 2=>null, 3=>0);
	protected $sContent = '';

	public function build($sContent)
	{
	}

	public function load($sTree)
	{
		$this->aTree = unserialize($sTree);
	}

	public function serialize()
	{
		return serialize($this->aTree);
	}
	
	//Searching
	public function find($sString)
	{
		$aFound = array();

		//Split the search string into words
		$aWords = $this->split($sString);

		//For each word each for it and add the ranks together and return the rank.
		foreach ($aWords as $sWord)
		{
			//If word was found then add it to list.
			if ($aWord = $this->findWord( strtolower(trim($sWord)) )) $aFound[] = $aWord;
		}

		return $aFound;
	}
	
	//Tree management.
	protected function addWord($sWord)
	{
		$aTree = &$this->aTree;
		
		if (strlen($sWord) == 0) return false;

		//Split the word into letters or characters.
		$aLetters = str_split($sWord);

		//For each character in the word 
		foreach ($aLetters as $cChar) 
		{
			//If character already exists in the current branch then set it as current letter.
			if (!array_key_exists($cChar, $aTree[1])) $aTree[1][$cChar] = array(0=>$cChar, 1=>array(), 2=>null, 3=>0);
			$aTree = &$aTree[1][$cChar]; //Get the array content of this character.
		}

		//Once the tree path for this word has been built. If word did not exist then add it.
		if ($aTree[2] == null) $aTree[2] = $sWord;
		
		//Finally incroment the rank of the word by one. (If already exists then incs rank otherise sets rank to 1 from 0.
		$aTree[3] += 1;
	}

	protected function findWord($sWord)
	{
		$aTree = $this->aTree;
		$iFound = 0;
		$iLength = strlen($sWord);

		//If word is empty then return false.
		if ($iLength == 0) return false;

		//Split the word into letters.
		$aLetters = str_split($sWord);

		//For each character travers the tree until we find the word or not.
		foreach ($aLetters as $cChar)
		{
			if (!array_key_exists($cChar, $aTree[1])) break;

			$iFound++;
			$aTree = $aTree[1][$cChar];
		}

		//This might be no word at the end of this node if only have of the full word was requested.
		return ($aTree[2] == null ? array($sWord, $iFound/$iLength) :  array($aTree[2], $aTree[3]));
	}
}

?>