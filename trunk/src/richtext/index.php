<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<? include('packager.php'); ?>
		<?=Packager::package(array('scripts/popup.js', 
							'scripts/popup_link.js',
							'scripts/popup_menu.js',
							'scripts/popup_image.js',
							'scripts/popup_table.js',
							'scripts/popup_colours.js',
							'scripts/popup_specialchars.js',
							
							'scripts/table.js',
							'scripts/toolbar.js',
							'scripts/utility.js',
							'scripts/richtext.js'),
							
							array('styles/richtext.css',
							'styles/toolbar.css',
							'styles/popup.css',
							'styles/icons.css'), 

							'richtext.php'); ?>
		<script>
			var oRichText = null;

			function load()
			{
				oRichText = new RichText(document.getElementById('contianer'), handleEvents);
			}

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

		</script>
	</head>
	<body onload="load()">
		<div id='contianer' style="width:100%; height:600px"></div>
	</body>
</html>