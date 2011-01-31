<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<style>
			<?=include_once('styles.css'); ?>
		</style>
		<script>
			<?=include_once('dropdown.js'); ?>
			<?=include_once('dropdown_chars.js'); ?>
			<?=include_once('dropdown_color.js'); ?>
			<?=include_once('dropdown_table.js'); ?>
			<?=include_once('dropdown_link.js'); ?>
			<?=include_once('dropdown_image.js'); ?>
			<?=include_once('table.js'); ?>
			<?=include_once('toolbar.js'); ?>
			<?=include_once('utility.js'); ?>
			<?=include_once('richtext.js'); ?>

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
		<div id='contianer'></div>
	</body>
</html>