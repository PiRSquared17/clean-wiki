<?

class Utility
{
	public static function ensureKeys(&$aArray, $aKeys)
	{
		foreach ($aKeys as $sKey=>$sDefaultValue)
		{
			if (!array_key_exists($aArray, $sKey)) $aArray[$sKey] = $sDefaultValue;
		}
	}
	
	public static function appendChild($oParent, $sName)
	{
		//Get the total number of arguments and also get all the arguments in an array.
		$iArgCount = func_num_args();
		$aArguments = func_get_args();

		//Create the new element and append it to the parent.
		$oElement = $oParent->appendChild($oParent->ownerDocument->createElement(sName));

		//Set the attribute values for the element.
		for ($i = 2; $i+1 < $iArgCount; $i+=2) $oElement->setAttribute($aArguments[$i], $aArguments[$i+1]);

		//Return the element.
		return $oElement;
	}

	public static function setAttributes($oElement)
	{
		//Get the total number of arguments and also get all the arguments in an array.
		$iArgCount = func_num_args();
		$aArguments = func_get_args();

		//We are starting at 1 because we want to skip the first argument which is the Xml 
		//element itself. Next we are skipping 2 each iteration because we have an attribute
		//name and value pairs. This means we must have an odd number of parameters including
		//the first element. The +1 within teh condition ensures we are not left with a 
		//single argument within the array without it's pair.
		for ($i = 1; $i+1 < $iArgCount; $i+=2) $oElement->setAttribute($aArguments[$i], $aArguments[$i+1]);
	}
	
	public static function decode($oElement)
	{
		//Because this is a standard method, this means that the format attribute will 
		//contain encoding structure, and teh element's value contains the content to be decoded.
		//Therefore start by getting the format attribute and splitting it into the different encoding.
		$sFormat = $oElement->getAttribute('format');
		$aFormat = explode('/', $sFormat);
		
		//Get the element text and travers each encoding and decode it in the order it is found.
		$sContent = $oElement->nodeValue;
		foreach ($aFormat as $sFormat)
		{
			//If the current format is base64 encoding then decode it using base64.
			if ($sFormat == 'base64') $sContent = base64_decode($sContent);
			
			//If the current format is a 'gz' then uncompress it using gz.
			else if ($sFormat == 'gz') $sContent = gzuncompress($sContent);
		}

		//Finally return the decoded content.
		return $sContent;
	}
	
	public static function encode($oElement, $sFormat, $sContent)
	{
		//base64_encode(gzcompress($sOldContent));
		//'length', strlen($sOldContent), 'format', 
	}

	public static function diff($aOld, $aNew)
	{ 
		$omax = 0;
		$nmax = 0;
		$maxlen = 0;
		$matrix = array();

		//For each character in the old string.
		foreach($aOld as $oindex => $ovalue)
		{
			//Get all locations/indexes of the new array where the current character is found.
			$nkeys = array_keys($aNew, $ovalue); 

			//For every match character in the new string find the longest match.
			foreach($nkeys as $nindex)
			{ 
				//If consecutive characters where found then set current character as the incroment of the previous others set to first start match.
				$matrix[$oindex][$nindex] = (isset($matrix[$oindex - 1][$nindex - 1]) ? $matrix[$oindex - 1][$nindex - 1] + 1 : 1);

				//If the current match is the longest match then 
				if($matrix[$oindex][$nindex] > $maxlen)
				{ 
					//Update the logest match to the currently found.
					$maxlen = $matrix[$oindex][$nindex]; 

					//Also Update the start and end of the current max length to later extract it out and found the next.
					$omax = $oindex + 1 - $maxlen; 
					$nmax = $nindex + 1 - $maxlen; 
				} 
			}        
		}

		//If no match found then the array are completely different.
		if($maxlen == 0) return array(array('d'=>$aOld, 'i'=>$aNew)); 
		
		//Finally find the different of the first part.
		$aStartDiff = diff(array_slice($aOld, 0, $omax), array_slice($aNew, 0, $nmax));
		//Extract out the matched part.
		$aLongestMatch = array_slice($aNew, $nmax, $maxlen);
		//Find the different for the end part.
		$aEndDiff = diff(array_slice($aOld, $omax + $maxlen), array_slice($aNew, $nmax + $maxlen));

		//Stick all the part together.
		return array_merge($aStartDiff , $aLongestMatch, $aEndDiff); 
	}
	
	public static function getStyles($sFilePath)
	{
		$sStyles = file_get_contents($sFilePath);
		$sStyles = str_replace(array("\t", "\n", "\r", ': '), array('', '', '', ':'), $sStyles);
		return $sStyles;
	}

	public static function getRequest($sKey, $sDefault)
	{
		return (isset($_REQUEST[$sKey]) ? $_REQUEST[$sKey] : $sDefault);
	}
	
	public static function getShortDate($iTime)
	{
		$sDate = '';
		$oInterval = date_diff(new DateTime(date(DateTime::W3C, $iTime)), new DateTime());

		if      ($oInterval->y == 1) 	$sDate = 'a year ago';
		else if ($oInterval->y  > 1) 	$sDate = $oInterval->y . ' years ago';
		else if ($oInterval->m  > 1) 	$sDate = $oInterval->m . ' month ago';
		else if ($oInterval->d == 1)	$sDate = '1 day ago';
		else if ($oInterval->d  > 1) 	$sDate = $oInterval->d . ' days ago';
		else if ($oInterval->h == 1) 	$sDate = '1 hour ago';
		else if ($oInterval->h  > 1) 	$sDate = $oInterval->h . ' hours ago';
		else if ($oInterval->i == 1) 	$sDate = '1 minute ago';
		else if ($oInterval->i  > 1) 	$sDate = $oInterval->i . ' minutes ago';
		else if ($oInterval->s == 1) 	$sDate = '1 second ago';
		else if ($oInterval->s  > 1) 	$sDate = $oInterval->s . ' seconds ago';
		else 							$sDate = 'a second ago';

		return $sDate;
	}
	
	public static function getPath($oParent, $sXPath, $sDefault = null)
	{
		//Create the xpath object using the page header to execute the xpath query.
		$oXPath = new DOMXpath($oParent);

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
	
	public static function getPaths($oParent, $sXPath, $sDefault = null)
	{
		//Create the xpath object using the page header to execute the xpath query.
		$oXPath = new DOMXpath($oParent);

		//Execute the xpath query.
		$oNodeList = $oXPath->query($sXPath);

		//If there are nodes returned then get the first node.
		if ($oNodeList && $oNodeList->length > 0) return $oNodeList;

		//If no nodes found then return the default value.
		return $sDefault;
	}
	
}



?>