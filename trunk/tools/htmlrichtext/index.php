<?

function jsClean($sFilePath, $bDebug = false)
{
	$sContent = file_get_contents($sFilePath);
	$sContent = str_replace(array("\n","\r", "\t"), array("", "", ""), $sContent);

	return $sContent;
}

function jsCleanAll($aFiles, $bDebug = false)
{
	$sContents = '';
	foreach ($aFiles as $sFile) $sContents .= jsClean($sFile, $bDebug);

	return $sContents;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<style>
			<?=jsCleanAll(array('styles/richtext.css',
								'styles/toolbar.css',
								'styles/popup.css',
								'styles/icons.css')); ?>
		</style>
		<script>
			<?=jsCleanAll(array('scripts/popup.js', 
								'scripts/popup_link.js',
								'scripts/popup_menu.js',
								'scripts/popup_image.js',
								'scripts/popup_table.js',
								'scripts/popup_colours.js',
								'scripts/popup_specialchars.js',
								
								'scripts/table.js',
								'scripts/toolbar.js',
								'scripts/utility.js',
								'scripts/richtext.js')); ?>

			var oRichText = null;

			function handleEvents(oEvent)
			{
				if (oEvent.name == 'discard') return discard();
				return true;
			}

			function discard()
			{
				if (confirm("Close window?"))
				{
					self.close();
				}
				return false;
			}

			function save(fClose)
			{
			}

			function load()
			{
				oRichText = new RichText(document.getElementById('contianer'), handleEvents);
			}

		</script>
	</head>
	<body onload="load()">
		<div id='contianer' style="width:100%; height:600px"></div>
	</body>
</html>