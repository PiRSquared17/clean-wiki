
function RichTextPopup_Chars(oValues, fEventHandler)
{
	var self = new RichTextPopup();

	self.initChars = function()
	{
		self.innerHTML = "<table class='menu_table' cellpadding='0px' cellspacing='2px'>" + 
						 "<thead><tr><td align='center'>Special Characters</td></tr></thread>" + 
						 "<tbody><tr><td><table style='border: 0px' cellpadding='0px' cellspacing='1px'></table></td></tr></tbody></table>";

		var oTable = self.firstChild.rows[1].cells[0].firstChild;

		var iColumn = 0;
		var oRow = oTable.insertRow(-1);
		for (var iIndex = 128; iIndex < 255; iIndex++)
		{
			if (iIndex != 129 && iIndex != 141 && iIndex != 143 && iIndex != 144 && iIndex != 157 && iIndex != 160)
			{
				if (iColumn == 16){ iColumn = 0; oRow = oTable.insertRow(-1); }

				iColumn++;
				var oCell = oRow.insertCell(-1);
				oCell.rtObject = {name: 'inserthtml', value:  "&#" + iIndex + ";"};
				oCell.innerHTML = "&#" + iIndex + ";";
				oCell.className = "menu_item_chars";
				oCell.onclick = fEventHandler;
				oCell.onmouseout = function() { this.className = "menu_item_chars"; };
				oCell.onmouseover = function() { this.className = "menu_item_chars_over"; };
			}
		}
		
		return self;
	};

	return self.initChars();
}
