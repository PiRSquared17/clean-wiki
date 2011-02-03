
function RichTextPopup_Colours(oValues, fEventHandler)
{
	var self = new RichTextPopup(null, null);

	self.initColor = function()
	{
		self.innerHTML = "<table class='richtext_popup_table' cellpadding='0px' cellspacing='2px'>" + 
						 "<thead><tr><td align='center'>Color Swatch</td></tr></thread>" + 
						 "<tbody><tr><td><table style='border: 0px' cellpadding='0px' cellspacing='1px'></table></td></tr></tbody></table>";

		var aValues = oValues.value;
		var oTable = self.firstChild.rows[1].cells[0].firstChild;
		var iIndex = 0;
		for (var y = 0; y < 6; y++)
		{
			var oRow = oTable.insertRow(-1);
			for (var x = 0; x < 8; x++)
			{
				var oCell = oRow.insertCell(-1);
				oCell.rtObject = {name: oValues.name, value: aValues[iIndex]};
				oCell.onclick = fEventHandler;
				oCell.onmouseout = function() { this.className = "richtext_colour_item"; };
				oCell.onmouseover = function() { this.className = "richtext_colour_item_hover"; };
				oCell.className = 'richtext_colour_item';
				oCell.style.background = aValues[iIndex++];
			}
		}

		return self;
	};
	
	return self.initColor();
}
