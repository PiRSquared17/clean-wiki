<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<style>

.toolbar
{
	padding: 0px;
	font-size: 8pt;
	font-family: arial;
}

.toolbar td
{
	padding: 2px 0px 0px 0px;
	vertical-align: top;
	border-left: 1px solid #CCC;
}

.toolbar img
{
	border: none;
	vertical-align: middle;
}

.toolbar_button,
.toolbar_selected,
.toolbar_disabled
{
	color: #555;
	margin: 1px;
	padding: 3px;
	cursor: pointer;
	text-decoration: none;
	line-height: 200%;
	
}

.toolbar_button:hover
{
	padding: 2px;
	text-decoration: none;
	border: 1px solid #79b4dc;
	background-color: #def0fa;
}

.toolbar_selected
{
	padding: 1px;
	text-decoration: none;
	border: 1px solid #79b4dc;
	background-color: #def0fa;
}

.toolbar_disabled
{
	color: #888;
	cursor: default;
}

.toolbar_disabled img
{
	opacity: 0.3;
	filter: alpha(opacity=30);
}


.icons,.icons_align_center,.icons_align_left,.icons_align_right,.icons_bold,.icons_bullets,.icons_clearformat,.icons_close,.icons_column_delete,.icons_copy,.icons_cut,.icons_fontcolor,.icons_fontsizedown,.icons_fontsizeup,.icons_highlight,.icons_image,.icons_indent,.icons_italic,.icons_justify,.icons_line,.icons_link,.icons_link_remove,.icons_numbering,.icons_paste,.icons_redo,.icons_row_delete,.icons_ruler,.icons_save,.icons_saveclose,.icons_search,.icons_source,.icons_specialchars,.icons_strikethrough,.icons_subscript,.icons_supperscript,.icons_table,.icons_table_above,.icons_table_below,.icons_table_delete,.icons_table_left,.icons_table_right,.icons_undent,.icons_underline,.icons_undo,.icons_user{background-image: url(icons.png); border: 0px; width: 16px; height: 16px}
.icons_align_center{ background-position-y: -0px }.icons_align_left{ background-position-y: -16px }.icons_align_right{ background-position-y: -32px }.icons_bold{ background-position-y: -48px }.icons_bullets{ background-position-y: -64px }.icons_clearformat{ background-position-y: -80px }.icons_close{ background-position-y: -96px }.icons_column_delete{ background-position-y: -112px }.icons_copy{ background-position-y: -128px }.icons_cut{ background-position-y: -144px }.icons_fontcolor{ background-position-y: -160px }.icons_fontsizedown{ background-position-y: -176px }.icons_fontsizeup{ background-position-y: -192px }.icons_highlight{ background-position-y: -208px }.icons_image{ background-position-y: -224px }.icons_indent{ background-position-y: -240px }.icons_italic{ background-position-y: -256px }.icons_justify{ background-position-y: -272px }.icons_line{ background-position-y: -288px }.icons_link{ background-position-y: -304px }.icons_link_remove{ background-position-y: -320px }.icons_numbering{ background-position-y: -336px }.icons_paste{ background-position-y: -352px }.icons_redo{ background-position-y: -368px }.icons_row_delete{ background-position-y: -384px }.icons_ruler{ background-position-y: -400px }.icons_save{ background-position-y: -416px }.icons_saveclose{ background-position-y: -432px }.icons_search{ background-position-y: -448px }.icons_source{ background-position-y: -461px }.icons_specialchars{ background-position-y: -477px }.icons_strikethrough{ background-position-y: -493px }.icons_subscript{ background-position-y: -509px }.icons_supperscript{ background-position-y: -525px }.icons_table{ background-position-y: -541px }.icons_table_above{ background-position-y: -557px }.icons_table_below{ background-position-y: -573px }.icons_table_delete{ background-position-y: -589px }.icons_table_left{ background-position-y: -605px }.icons_table_right{ background-position-y: -621px }.icons_undent{ background-position-y: -637px }.icons_underline{ background-position-y: -653px }.icons_undo{ background-position-y: -669px }.icons_user{ background-position-y: -685px }



.richtext
{
  width: 100%;
  height: 500px;
  border: 1px #888 solid;
}

.richtext_toolbar
{
	border-bottom: 1px #888 solid;
}

.richtext_frame
{
	width: 100%;
	height: 100%;
	frameborder: 0px;
}


		</style>
<script>

function RichText(oContainer)
{
	oContainer.innerHTML = "<table class='richtext' cellpadding='0' cellspacing='0'><thead><tr><td class='richtext_toolbar'><a href='' class='toolbar_button' style='float: right'><img class='icons_close' src='images/icons/empty.png'></a></td></td></thead></tr><tr><td></td></tr></table>";
	var self = oContainer.firstChild;

	var aIcons = new Array( 
	{type: 'button', name: 'save', icon: '', text: 'Save', tooltip: 'Save'},
	{type: 'newline'},
	{type: 'button', name: 'saveclose', icon: '', text: 'Save & Close', tooltip: 'Save and Close'},
	{type: 'newcell'},

	{type: 'button', name: 'undo', icon: 'undo', text: null, tooltip: 'Undo', state:'enable'},
	{type: 'button', name: 'redo', icon: 'redo', text: null, tooltip: 'Redo', state:'enable'},
	{type: 'newline'},
	{type: 'button', name: 'paste', icon: 'paste', text: null, tooltip: 'Paste', state: 'enable'},
	{type: 'button', name: 'copy', icon: 'copy', text: null, tooltip: 'Copy', state: 'enable'},
	{type: 'button', name: 'cut', icon: 'cut', text: null, tooltip: 'Cut', state: 'enable'},
	{type: 'newcell'},
	
	{type: 'button', name: 'fontfamily', icon: null, text: 'Font Family', tooltip: '', state: 'value'},
	{type: 'button', name: 'fontsize', icon: null, text: 'Font Size', tooltip: '', state: 'value'},
	{type: 'button', name: 'style', icon: null, text: 'Style', tooltip: '', state: 'value'},
	{type: 'newline'},
	{type: 'button', name: 'bold', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'italic', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'underline', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'strikethrough', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'subscript', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'supperscript', icon: '', text: null, tooltip: '', state: 'select'},
	{type: 'button', name: 'fontcolor', icon: '', text: null, tooltip: '', state: 'value'},
	{type: 'button', name: 'highlight', icon: '', text: null, tooltip: '', state: 'value'},
	{type: 'button', name: 'removeFormat', icon: 'clearformat', text: null, tooltip: '', state: 'enable'},
	{type: 'newcell'},

	
	{type: 'button', name: 'insertUnorderedList', icon: 'bullets', text: null, tooltip: 'Bullets'},
	{type: 'button', name: 'insertOrderedList', icon: 'numbering', text: null, tooltip: 'Numbering'},
	{type: 'button', name: 'outdent', icon: 'undent', text: null, tooltip: 'Decrease Indent', state: 'enable'},
	{type: 'button', name: 'indent', icon: '', text: null, tooltip: 'Increase Indent', state: 'enable'},
	{type: 'newline'},
	{type: 'button', name: 'justifyLeft', icon: 'align_left', text: null, tooltip: 'Align Text Left', state: 'select'},
	{type: 'button', name: 'justifyRight', icon: 'align_right', text: null, tooltip: 'Align Text Right', state: 'select'},
	{type: 'button', name: 'justifyCenter', icon: 'align_center', text: null, tooltip: 'Center', state: 'select'},
	{type: 'button', name: 'justify', icon: 'justify', text: null, tooltip: 'Justify', state: 'select'},
	{type: 'newcell'},

	{type: 'button', name: 'table_delete', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'link_remove', icon: '', text: null, tooltip: ''},
	{type: 'newline'},
	{type: 'button', name: 'table', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'link', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'image', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'specialchars', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'source', icon: '', text: null, tooltip: ''},
	{type: 'button', name: 'insertHorizontalRule', icon: 'line', text: null, tooltip: '', state: 'enable'},
	{type: 'newcell'},

	{type: 'button', name: 'column_delete', icon: '', text: null, tooltip: 'Delete Column', state: 'table'},
	{type: 'button', name: 'row_delete', icon: '', text: null, tooltip: 'Delete Row', state: 'table'},
	{type: 'newline'},
	{type: 'button', name: 'table_above', icon: '', text: null, tooltip: 'Incert Above', state: 'table'},
	{type: 'button', name: 'table_below', icon: '', text: null, tooltip: 'Incert Below', state: 'table'},
	{type: 'button', name: 'table_left', icon: '', text: null, tooltip: 'Insert Left', state: 'table'},
	{type: 'button', name: 'table_right', icon: '', text: null, tooltip: 'Insert Right.', state: 'table'});

	self.init = function()
	{
		self.toolbar = self.rows[0].cells[0].appendChild(document.createElement("TABLE"));
		self.rtContainer = self.rows[1].cells[0]

        self.rtContainer.innerHTML = "<iframe class='richtext_frame' frameborder='0'></iframe>";
        self.rtFrame = self.rtContainer.firstChild;

        self.rtDocument = (self.rtFrame.contentWindow ? self.rtFrame.contentWindow.document : self.rtFrame.document);
        self.rtDocument.designMode = "on";
        self.rtDocument.write("<html><body style='font-family:arial;font-size:12pt'></body></html>");
        self.rtDocument.close();
		self.rtDocument.onclick = self.updateToolbar;
		self.rtDocument.onkeyup = self.updateToolbar;

		self.buildToolbar(self.toolbar);
		self.updateToolbar();

		return self;
	};
	
	self.buildToolbar = function(oToolbar)
	{
		oToolbar.className = 'toolbar';
	
		var oRow = oToolbar.insertRow(-1);
		var oCell = oRow.insertCell(-1);
	
		for (var i = 0; i < aIcons.length; i++)
		{
			var oIcon = aIcons[i];
			if (oIcon.type == 'newcell') oCell = oRow.insertCell(-1);
			else if (oIcon.type == 'newline') oCell.appendChild(document.createElement('BR'));
			else if (oIcon.type == 'button') self.buildButton(oIcon, oCell);
		}
	};
	
	self.buildButton = function(oIcon, oCell)
	{
		var oButton = oCell.appendChild(document.createElement('A'));
		oButton.icon = oIcon;
		oButton.title = oIcon.tooltip;
		oButton.onclick = self.handEvent;
		oButton.className = 'toolbar_button';

		if (oIcon.icon != null)
		{
			var oImage = oButton.appendChild(document.createElement("IMG"));
			oImage.className = "icons_" + (oIcon.icon.length == 0 ? oIcon.name : oIcon.icon);
			oImage.src = "images/icons/empty.png";
		}

		if (oIcon.text != null) oButton.appendChild(document.createTextNode(oIcon.text));

		oButton.isSelected = false;
		oButton.setSelected = function(bSelected){ oButton.isSelected = bSelected; oButton.className = (bSelected ? 'toolbar_selected' : 'toolbar_button');  };

		oButton.isEnabled = true;
		oButton.setEnabled = function(bEnabled){ oButton.isEnabled = bEnabled; oButton.className = (bEnabled ? 'toolbar_button' : 'toolbar_disabled'); };

		oIcon.node = oButton;
	};
	
	self.updateToolbar = function()
	{
		for (var i = 0; i < aIcons.length; i++)
		{
			var oIcon = aIcons[i];
			
			if (oIcon.state == 'select')
			{
				oIcon.node.setSelected( self.rtDocument.queryCommandState(oIcon.name) );
			}
			else if (oIcon.state == 'enable')
			{
				oIcon.node.setEnabled( self.rtDocument.queryCommandEnabled(oIcon.name) );
			}
			else if (oIcon.state == 'table')
			{
				var oNode = self.rtDocument.getSelection().baseNode;
				var bInTable = self.hasParent(oNode, 'TD');
				oIcon.node.setEnabled(bInTable);
			}
		}
	};
	
	self.hasParent = function(oNode, sParentNodeName)
	{
		sParentNodeName = sParentNodeName.toUpperCase();
		for ( ; oNode != null; oNode = oNode.parentNode)
			if (oNode.nodeName.toUpperCase() == sParentNodeName) return true;

		return false;
	}
	
	self.getParent = function(oNode, sParentNodeName)
	{
		sParentNodeName = sParentNodeName.toUpperCase();
		for ( ; oNode != null; oNode = oNode.parentNode)
			if (oNode.nodeName.toUpperCase() == sParentNodeName) return oNode;

		return null;
	}

	self.handEvent = function()
	{
		var sEvent = this.icon.name;
		var sValue = null;

		self.rtFrame.contentWindow.focus();

		var oRange = self.rtDocument.createRange();

		if (sEvent == 'table')
		{
			sHtml = '<table border="1"><tr><td>test</td></tr></table>';
			self.rtDocument.execCommand('inserthtml', false, sHtml);
			self.rtDocument.execCommand('enableObjectResizing', false, "true");
			self.rtDocument.execCommand('enableInlineTableEditing', false, "true");
			
			
		}
		else if (sEvent == 'table_above')
		{
			var oNode = self.rtDocument.getSelection().baseNode;
			
			var oRow = self.getParent(oNode, 'tr');
			if (oRow != null)
			{
				var oNewRow = oRow.parentNode.insertRow(oRow.index-1);
				for (var i = 0; i < oRow.cells.length; i++)
				{	
					oNewRow.insertCell(-1);
				}
			}
		}
		else if (sEvent == 'table_left')
		{
			var oNode = self.rtDocument.getSelection().baseNode;
			
			var oColumn = self.getParent(oNode, 'td');
			var oTable = self.getParent(oColumn, 'table');
			if (oColumn != null && oTable != null)
			{
				for (var i = 0; i < oTable.rows.length; i++)
				{	
					oTable.rows[i].insertCell(oColumn.index-1);
				}
			}
		}
		else
		{
			self.rtDocument.execCommand(sEvent, false, sValue);
		}
	};
	
	return self.init();
}

var oRichText = null;

function bulidToolbar()
{
	oRichText = new RichText(document.body);
}

</script>
	</head>
	<body onload="bulidToolbar()"></body>
</html>