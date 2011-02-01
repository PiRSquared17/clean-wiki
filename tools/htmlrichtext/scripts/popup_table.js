
function RichTextPopup_Table(oValues, fEventHandler)
{
	var self = new RichTextPopup();

	self.initTable = function()
	{
		self.innerHTML = "<table class='menu_table' cellpadding='0px' cellspacing='2px'>" + 
						 "<thead><tr><td align='center'>(10x10)</td></tr></thread>" + 
						 "<tbody><tr><td><table cellpadding='0px' cellspacing='0px'></table></td></tr></tbody></table>";

		self.tableSize = self.firstChild.rows[0].cells[0];
		self.tableGrid = self.firstChild.rows[1].cells[0].firstChild;
		self.onmouseout = self.clear;

		var iIndex = 0;
		for (var y = 0; y < oValues.value.height; y++)
		{
			var oRow = self.tableGrid.insertRow(-1);
			for (var x = 0; x < oValues.value.width; x++)
			{
				var oCell = oRow.insertCell(-1);
				oCell.rtEvent = {name: oValues.name, value: {x: (x + 1), y: (y + 1)} };
				oCell.onclick = fEventHandler;
				oCell.className = 'menu_item_cell';
				oCell.onmouseout = Utility.cancelBubble;
				oCell.onmouseover = self.selectCells;
			}
		}

		return self;
	};
	
	self.selectCells = function()
	{
		var oCell = this;
		var iRows = oCell.parentNode.rowIndex;
		var iColumns = oCell.cellIndex;

		self.tableSize.innerHTML = (iRows + 1) + "x" + (iColumns + 1) + " Table";
		for (var y = 0; y < self.tableGrid.rows.length; y++)
		{
			var oRow = self.tableGrid.rows[y];
			for (var x = 0; x < oRow.cells.length; x++)
				oRow.cells[x].className = (x <= iColumns && y <= iRows ? 'menu_item_cell_over' : 'menu_item_cell');
		}
	};

	self.clear = function()
	{
		self.tableSize.innerHTML = "Insert Table"; 
		for (var y = 0; y < self.tableGrid.rows.length; y++)
		{
			var oRow = self.tableGrid.rows[y];
			for (var x = 0; x < oRow.cells.length; x++) oRow.cells[x].className = 'menu_item_cell';
		}
	};

	self.supperShow = self.show;

	self.show = function(oRelativeTo, iOffsetX, iOffsetY, fCallback)
	{
		self.clear();
		self.supperShow(oRelativeTo, iOffsetX, iOffsetY, fCallback); 
	};

	return self.initTable();
}
