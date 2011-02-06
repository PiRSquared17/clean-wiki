<?
/**
 * Merges all PNG files found in a specified folder into one image. This is helpful
 * to speed up website loading by reducing the number of HTTP requests to the server. 
 * Insted sending 30 requests for 30 images, the browser send only one requests for one
 * image containing all 30 and uses CSS to display the required icon with that one image.
 *
 * Along with the image, this class will also generate the CSS for each image using a
 * specified prefix followed by the image name (excluding the png extension).
 *
 * @author Charbel Choueiri
 * @date 2011-01-22
 */
class MergeIcons
{
	/**
	 * A list of all loaded images from the specified path. This is an array of arrays
	 * holding the following image information: image object, name, width, height.
	 */
	var $aIcons = array();
	
	/**
	 * Holds the max image width and height along with the total width and height.
	 * These are updated when the images are loaded.
	 */
	var $iMaxWidth = 0;
	var $iMaxHeight = 0;
	var $iTotalWidth = 0;
	var $iTotalHeight = 0;

	/**
	 * The final image containing the merge of all found images.
	 */
	var $oImage = null;
	
	/**
	 * Loads all png images within the specifed page. 
	 * Stores max width, max height, total width and total height for future processing.
	 */
	public function load($sPath)
	{
		$oDirectory = opendir($sPath);
		while ($sFile = readdir($oDirectory))
		{
			//If it s a file and is a PNG file then process it.
			$iIndex = strrpos($sFile, '.');
			if (is_file($sPath . $sFile) && strtoupper(substr($sFile, $iIndex+1)) == 'PNG')
			{
				//If image openned successfully then exract dimension information.
				if ($oImage = imagecreatefrompng($sPath. $sFile))
				{
					//Get image name and dimensions.
					$sName = strtolower(substr($sFile, 0, $iIndex));
					$iWidth = imagesx($oImage);
					$iHeight = imagesy($oImage);

					//Add the image information the the image list and update the total width and height.
					$this->aIcons[] = array('image'=>$oImage, 'name'=>$sName, 'width'=>$iWidth, 'height'=>$iHeight);
					$this->iTotalWidth += $iWidth;
					$this->iTotalHeight += $iHeight;

					//Update the max with and height if required.
					if ($iWidth > $this->iMaxWidth) $this->iMaxWidth = $iWidth;
					if ($iHeight > $this->iMaxHeight) $this->iMaxHeight = $iHeight;
				}
			}
		}
	}

	/**
	 * Merges all loaded images in the vertical position, one on top of the other.
	 * Preserves all transparency and alpha values.
	 */
	public function merge()
	{
		$iY = 0;
		$this->oImage = imagecreatetruecolor($this->iMaxWidth, $this->iTotalHeight);
		imagesavealpha($this->oImage, true);
		imagealphablending($this->oImage, false);
		imagefill($this->oImage, 0, 0, 0xFFFFFF00);

		foreach ($this->aIcons as $aImage)
		{
			imagecopy($this->oImage, $aImage['image'], 0, $iY, 0, 0, $aImage['width'], $aImage['height']);
			$iY += $aImage['height'];
		}
	}

	/**
	 * Generates a string of CSS classes for each image containing it's location.
	 */
	public function generateCSS($sPrefix, $sImageUrl, $bEmbedImage = false, $sOutputFile = null)
	{
		$iY = 0;
		$sStyle = "";
		$sGlobal = ".$sPrefix";

		foreach ($this->aIcons as $aImage)
		{
			$sName = "{$sPrefix}_{$aImage['name']}";
			$sStyle .= ".$sName{ background-position: 0px -{$iY}px }\n";
			$sGlobal .= ",.$sName";
			$iY += $aImage['height'];
		}
		
		if ($bEmbedImage) $sImageUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($sImageUrl));
		
		$sStyles = $sGlobal . "\n{background-image: url($sImageUrl); border: none}\n" . $sStyle;
		
		if ($sOutputFile != null) file_put_contents($sOutputFile, $sStyles);

		return $sStyles;
	}

	/**
	 * Save the current merged image into the specified path.
	 */
	public function save($sPath = null)
	{
		return imagepng($this->oImage, $sPath);
	}
}

?>