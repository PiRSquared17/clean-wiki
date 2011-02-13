
<div id="pagecontent" style="height:700px"><?=$wiki->pageContent?></div>
<form name="editForm" method="post" action=".">
	<input type="hidden" name="page" value="<?=$wiki->pageName?>"/>
	<input type="hidden" name="action" value=""/>
	<textarea name="content" style="display:none"></textarea>
</form>

<script src="richtext/scripts/popup.js"></script>
<script src="richtext/scripts/popup_link.js"></script>
<script src="richtext/scripts/popup_menu.js"></script>
<script src="richtext/scripts/popup_image.js"></script>
<script src="richtext/scripts/popup_table.js"></script>
<script src="richtext/scripts/popup_colours.js"></script>
<script src="richtext/scripts/popup_specialchars.js"></script>
<script src="richtext/scripts/table.js"></script>
<script src="richtext/scripts/toolbar.js"></script>
<script src="richtext/scripts/utility.js"></script>
<script src="richtext/scripts/richtext.js"></script>

<script>

	function RichTextHandler(sContainerId)
	{
		var self = this;

		self.init = function()
		{
			var oContainer = document.getElementById(sContainerId);
			var oContent = (oContainer.firstChild != null ? oContainer.removeChild(oContainer.firstChild) : null);
			self.richtext = new RichText(oContainer, self.handleEvents);

			if (oContent != null) self.richtext.setContent(oContent);
		};

		self.handleEvents = function(oEvent)
		{
			if (oEvent.name == 'discard') return self.discard();
			else if (oEvent.name == 'save') return self.save(false);
			else if (oEvent.name == 'saveclose') return self.save(true);
			return true;
		};

		self.discard = function()
		{
			if (self.richtext.hasChanged() && confirm("Close & discard changes?"))
			{
				window.location = "<?=$wiki->pageUrl?>";
			}
			return false;
		};

		self.save = function(bClose)
		{
			document.editForm.action.value = (bClose ? "save&close" : "save");
			document.editForm.content.value = self.richtext.getContent();
			document.editForm.submit();
			return false;
		};

		self.init();
	}
	
	var oRichTextHandler = new RichTextHandler("pagecontent");

</script>
