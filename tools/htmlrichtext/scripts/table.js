
function RichTextTable(oDocument)
{
	var self = this;

	self.insertTable = function(oRange, iRows, iColumns)
	{
		sHtml = '';
		for (var i = 0; i < iRows; i++)
		{
			var sRow = '';
			for (var j = 0; j < iColumns; j++) sRow += '<td></td>';
			sHtml += '<td>' + sRow + '</tr>';
		}

		sHtml = '<table border="1">' + sHtml + '</table>';

		if (oDocument.createRange) oDocument.execCommand('inserthtml', false, sHtml);
		else oRange.pasteHTML(sHtml);
	};
	
	self.insertRow = function(iOffset)
	{
		var oNode = oDocument.getSelection().baseNode;
		var oRow = Utility.getParent(oNode, 'tr');
		if (oRow != null)
		{
			var oNewRow = oRow.parentNode.insertRow(oRow.rowIndex+iOffset);
			for (var i = 0; i < oRow.cells.length; i++) oNewRow.insertCell(-1);
		}
	};

	self.insertColumn = function(iOffset)
	{
		var oNode = oDocument.getSelection().baseNode;
		var oColumn = Utility.getParent(oNode, 'td');
		var oTable = Utility.getParent(oColumn, 'table');
		if (oColumn != null && oTable != null)
		{
			for (var i = 0; i < oTable.rows.length; i++) oTable.rows[i].insertCell(oColumn.cellIndex + iOffset);
		}
	};

	self.deleteTable = function()
	{
		var oNode = oDocument.getSelection().baseNode;
		if (oNode != null)
		{
			var oTable = Utility.getParent(oNode, 'table');
			if (oTable != null)	oTable.parentNode.removeChild(oTable);
		}
	};

	self.deleteRow = function()
	{
		var oNode = oDocument.getSelection().baseNode;
		if (oNode != null)
		{
			var oRow = Utility.getParent(oNode, 'tr');
			if (oRow != null) oRow.parentNode.removeChild(oRow);
		}
	};

	self.deleteColumn = function()
	{
		var oNode = oDocument.getSelection().baseNode;
		if (oNode != null)
		{
			var oColumn = Utility.getParent(oNode, 'td');
			var oTable = Utility.getParent(oColumn, 'table');
			if (oColumn != null && oTable != null)
			{
				var iCellIndex = oColumn.cellIndex;
				for (var i = 0; i < oTable.rows.length; i++) oTable.rows[i].deleteCell(iCellIndex);
			}
		}
	};
}
