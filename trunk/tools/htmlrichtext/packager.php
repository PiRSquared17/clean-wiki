<?

class Packager
{
	public static $bDebug = false;

	public static function get($oFile, $bDebug = null)
	{
		return (is_array($oFile) ? Packager::getFiles($oFile) : Packager::getFile($oFile));
	}

	public static function getFile($sFile, $bDebug = null)
	{
		ob_start();
		include($sFile);
		$sContent = ob_get_contents();
		ob_end_clean();

		return str_replace(array("\n","\r", "\t"), array("", "", ""), $sContent);
	}
	
	public static function getFiles($aFiles, $bDebug = null)
	{
		$sContents = '';
		foreach ($aFiles as $sFile) $sContents .= packager::getFile($sFile);

		return $sContents;
	}
	
	public static function package($aScripts, $aStyles, $sOutputFile = null)
	{
		$sStyles = Packager::get($aStyles);
		$sScripts = Packager::get($aScripts);
		$sContent = "<style>$sStyles</style>\n<script>$sScripts</script>";

		if ($sOutputFile != null) file_put_contents($sOutputFile, $sContent);

		return $sContent;
	}
}

?>