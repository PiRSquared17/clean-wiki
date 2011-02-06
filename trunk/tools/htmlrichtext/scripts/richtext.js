
function RichText(oContainer, fEventHandler)
{
	oContainer.innerHTML =	"<table class='richtext' cellpadding='0' cellspacing='0'>" + 
							"<thead><tr><td class='richtext_bar'></td><td class='richtext_discard'></td></tr></thead>" +
							"<tbody><tr><td colspan='2' height='100%'></td></tr></tbody></table>";

	var self = oContainer.firstChild;
	var aIcons = aRichTextToolbarIcons;

	self.init = function()
	{
		self.buildToolbar();
		self.buildFrame();
		self.updateToolbar();
		self.rtTable = new RichTextTable(self.rtDocument);

		self.table = oContainer.appendChild(new RichTextPopup_Table(aRichTextTable, self.eventHandler));
		self.fontSize = oContainer.appendChild(new RichTextPopup_Menu(aRichTextFontSize, self.eventHandler));
		self.fontStyle = oContainer.appendChild(new RichTextPopup_Menu(aRichTextFontStyle, self.eventHandler));
		self.fontFamily = oContainer.appendChild(new RichTextPopup_Menu(aRichTextFontFamily, self.eventHandler));
		self.fontColor = oContainer.appendChild(new RichTextPopup_Colours(aRichTextFontColor, self.eventHandler));
		self.highlight = oContainer.appendChild(new RichTextPopup_Colours(aRichTextHighlight, self.eventHandler));
		self.specialChars = oContainer.appendChild(new RichTextPopup_Chars(aRichTextChars, self.eventHandler));
		self.linkPopup = oContainer.appendChild(new RichTextPopup_Link(new Array(), self.eventHandler));
		self.imagePopup = oContainer.appendChild(new RichTextPopup_Image(new Array(), self.eventHandler));

		Utility.addEventListener(document, "keydown", self.hideDropdowns);
		Utility.addEventListener(document, "mousedown", self.hideDropdowns);
		Utility.addEventListener(self.rtDocument, "keydown", self.hideDropdowns);
		Utility.addEventListener(self.rtDocument, "mousedown", self.hideDropdowns);

		return self;
	};

	self.buildToolbar = function()
	{
		self.toolbar = self.rows[0].cells[0].appendChild(new RichTextToolbar(aRichTextToolbarIcons, self.eventHandler));
		var oDiscard = self.rows[0].cells[1].appendChild(self.toolbar.createButton(aRichTextDiscardIcon, self.eventHandler), self.toolbar);
	};

	self.buildFrame = function()
	{
		self.rtContainer = self.rows[1].cells[0];
		self.rtContainer.innerHTML = "<iframe class='richtext_frame' frameborder='0'></iframe>";
		self.rtFrame = self.rtContainer.firstChild;
		self.rtDocument = (self.rtFrame.contentWindow ? self.rtFrame.contentWindow.document : self.rtFrame.document);
		self.rtDocument.designMode = "on";
		self.rtDocument.write("<html><head><style><?=Packager::get('styles/content.css', false);?></style></head><body class='richtext_content'></body></html>");

		self.rtDocument.close();
		/*self.rtDocument.onclick = self.updateToolbar;*/
		/*self.rtDocument.onkeyup = self.updateToolbar;*/

		self.execCommand('enableObjectResizing', false, "true");
		self.execCommand('enableInlineTableEditing', false, "true");
	};

	self.hidePopups = function()
	{
		self.table.hide();
		self.fontSize.hide();
		self.fontStyle.hide();
		self.fontFamily.hide();
		self.fontColor.hide();
		self.highlight.hide();
		self.specialChars.hide();
		self.linkPopup.hide();
		self.imagePopup.hide();
	};
	
	self.hideDropdowns = function(oEvent)
	{
		oEvent = (oEvent ? oEvent : event);
		if (!Utility.inPopup(oEvent.target)) self.hidePopups();
	};

	self.updateToolbar = function()
	{
	};

	self.execCommand = function(sCommand, bShowUI, sValue)
	{
		try
		{
			if (self.rtDocument.queryCommandSupported(sCommand)) self.rtDocument.execCommand(sCommand, bShowUI, sValue);
		}
		catch (e1)
		{
			try
			{
				self.rtDocument.execCommand(sCommand, bShowUI, sValue)
			}
			catch (e2)
			{
				alert('Your browser does not support this feature.');
			}
		}
	};
	
	self.getRange = function()
	{
		return (self.rtDocument.createRange ? self.rtDocument.createRange() : self.rtDocument.selection.createRange());
	};
	
	self.execEvent = function(oEvent)
	{
		var oRange = self.getRange();

		self.rtFrame.contentWindow.focus();
		if (oEvent.name == "inserthtml" && oRange.pasteHTML)
		{
			oRange.pasteHTML(oEvent.value);
			oRange.select();
		}
		else
		{
			if (oRange.select) oRange.select();
			else oRange.collapse(false);
			self.execCommand(oEvent.name, false, oEvent.value);
		}
	};

	self.eventHandler = function(oEvent)
	{
		oEvent = (this.rtEvent ? this.rtEvent : oEvent);

		if (fEventHandler && !fEventHandler(oEvent)) return;

		self.hidePopups();

		if (oEvent.name == 'table_insert') self.rtTable.insertTable(self.getRange(), oEvent.value.y, oEvent.value.x);
		else if (oEvent.name == 'table_left') self.rtTable.insertColumn(0);
		else if (oEvent.name == 'table_right') self.rtTable.insertColumn(+1);
		else if (oEvent.name == 'table_above') self.rtTable.insertRow(0);
		else if (oEvent.name == 'table_below') self.rtTable.insertRow(+1);
		else if (oEvent.name == 'table_delete') self.rtTable.deleteTable();
		else if (oEvent.name == 'row_delete') self.rtTable.deleteRow();
		else if (oEvent.name == 'column_delete') self.rtTable.deleteColumn();

		else if (oEvent.name == 'table') self.table.show(this, 0, 21);
		else if (oEvent.name == 'fontSize') self.fontSize.show(this, 0, 21);
		else if (oEvent.name == 'fontStyle') self.fontStyle.show(this, 0, 21);
		else if (oEvent.name == 'fontFamily') self.fontFamily.show(this, 0, 21);
		else if (oEvent.name == 'fontColor') self.fontColor.show(this, 0, 21);
		else if (oEvent.name == 'highlight') self.highlight.show(this, 0, 21);
		else if (oEvent.name == 'charInsert') self.specialChars.show(this, -250, 21);
		else if (oEvent.name == 'datetime') self.execEvent({name: 'inserthtml', value: new Date()});
		else if (oEvent.name == 'link') self.linkPopup.show(this, -250, 21);
		else if (oEvent.name == 'image') self.imagePopup.show(this, -250, 21);

		else self.execEvent(oEvent);
	};

	return self.init();
}

var aRichTextColors = new Array(
	"#ff0000", "#400000", "#800000", "#c00000", "#ff4040", "#ff8080", "#ffc0c0", "#000000", 
	"#ffff00", "#404000", "#808000", "#c0c000", "#ffff40", "#ffff80", "#ffffc0", "#202020",
	"#00ff00", "#004000", "#008000", "#00c000", "#40ff40", "#80ff80", "#c0ffc0", "#404040",
	"#00ffff", "#004040", "#008080", "#00c0c0", "#40ffff", "#80ffff", "#c0ffff", "#808080",
	"#0000ff", "#000040", "#000080", "#0000c0", "#4040ff", "#8080ff", "#c0c0ff", "#c0c0c0",
	"#ff00ff", "#400040", "#800080", "#c000c0", "#ff40ff", "#ff80ff", "#ffc0ff", "#ffffff");

var aRichTextTable = {type: 'table', name: 'table_insert', value: {width: 10, height: 10}};
var aRichTextChars = {type: 'chars', name: 'charInsert', value: null};
var aRichTextFontColor = {type: 'color', name: 'forecolor', value: aRichTextColors};
var aRichTextHighlight = {type: 'color', name: 'backcolor', value: aRichTextColors};

var aRichTextFontStyle = new Array(
	{type: 'button', name: 'formatblock', icon: null, text: "<h1>Heading 1</h1>", value: '<h1>'},
	{type: 'button', name: 'formatblock', icon: null, text: "<h2>Heading 2</h2>", value: '<h2>'},
	{type: 'button', name: 'formatblock', icon: null, text: "<h3>Heading 3</h3>", value: '<h3>'},
	{type: 'button', name: 'formatblock', icon: null, text: "<h4>Heading 4</h4>", value: '<h4>'},
	{type: 'button', name: 'formatblock', icon: null, text: "<h5>Heading 5</h5>", value: '<h5>'},
	{type: 'button', name: 'formatblock', icon: null, text: "<h6>Heading 6</h6>", value: '<h6>'},
	{type: 'button', name: 'formatblock', icon: null, text: "Formatted", value: '<pre>'});

var aRichTextFontSize = new Array(
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='1'>Extra Small</font>", value: '1'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='2'>Smaller</font>", value: '2'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='3'>Normal</font>", value: '3'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='4'>Large</font>", value: '4'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='5'>Larger</size>", value: '5'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='6'>Extra Large</font>", value: '6'},
	{type: 'button', name: 'fontsize', icon: null, text: "<font size='7'>Largest</font>", value: '7'});
								
var aRichTextFontFamily = new Array(
	{type: 'button', name: 'fontname', icon: null, text: "<font face='tahoma'>Tahoma</font>", value: 'tahoma'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='arial'>Arial</font>", value: 'arial'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='arial black'>Arial Black</font>", value: 'arial black'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='verdana'>Verdana</font>", value: 'verdana'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='times new roman'>Times New Roman</font>", value: 'times new roman'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='garamond'>Garamond</font>", value: 'garamond'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='lucida handwriting'>Lucida Handwriting</font>", value: 'lucida handwriting'},
	{type: 'button', name: 'fontname', icon: null, text: "<font face='courier new'>Courier New</font>", value: 'courier new'});

var aRichTextDiscardIcon = {type: 'button', name: 'discard', icon: 'close', text: '', tooltip: 'Discard changes'};
var aRichTextToolbarIcons = new Array( 
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
	{type: 'button', name: 'fontFamily', icon: 'dropdown', text: 'Font Family', tooltip: 'Font family', state: 'value'},
	{type: 'button', name: 'fontSize', icon: 'dropdown', text: 'Size', tooltip: 'Font size', state: 'value'},
	{type: 'button', name: 'fontStyle', icon: 'dropdown', text: 'Style', tooltip: 'Font style', state: 'value'},
	{type: 'newline'},
	{type: 'button', name: 'bold', icon: '', text: null, tooltip: 'Bold', state: 'select'},
	{type: 'button', name: 'italic', icon: '', text: null, tooltip: 'Italic', state: 'select'},
	{type: 'button', name: 'underline', icon: '', text: null, tooltip: 'Underline', state: 'select'},
	{type: 'button', name: 'strikethrough', icon: '', text: null, tooltip: 'Strikethrough', state: 'select'},
	{type: 'button', name: 'subscript', icon: '', text: null, tooltip: 'Subscript', state: 'select'},
	{type: 'button', name: 'supperscript', icon: '', text: null, tooltip: 'Supperscript', state: 'select'},
	{type: 'button', name: 'fontColor', icon: 'fontcolor', text: null, tooltip: 'Font Colour', state: 'value'},
	{type: 'button', name: 'highlight', icon: '', text: null, tooltip: 'Highlight', state: 'value'},
	{type: 'button', name: 'removeFormat', icon: 'clearformat', text: null, tooltip: 'Clear Formatting', state: 'enable'},
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
	{type: 'button', name: 'table_delete', icon: '', text: null, tooltip: 'Remove table'},
	{type: 'button', name: 'unlink', icon: 'link_remove', text: null, tooltip: 'Remove link'},
	{type: 'button', name: 'source', icon: '', text: null, tooltip: 'View source'},
	{type: 'newline'},
	{type: 'button', name: 'table', icon: 'table', text: null, tooltip: 'Insert table'},
	{type: 'button', name: 'link', icon: 'link', text: null, tooltip: 'Insert link'},
	{type: 'button', name: 'image', icon: 'image', text: null, tooltip: 'Insert image'},
	{type: 'button', name: 'charInsert', icon: 'specialchars', text: null, tooltip: 'Insert special characters'},
	{type: 'button', name: 'datetime', icon: 'datetime', text: null, tooltip: 'Insert Today\'s date & time'},
	{type: 'button', name: 'insertHorizontalRule', icon: 'line', text: null, tooltip: 'Horizontal line', state: 'enable'},
	{type: 'newcell'},
	{type: 'button', name: 'column_delete', icon: '', text: null, tooltip: 'Delete Column', state: 'table'},
	{type: 'button', name: 'row_delete', icon: '', text: null, tooltip: 'Delete Row', state: 'table'},
	{type: 'newline'},
	{type: 'button', name: 'table_above', icon: '', text: null, tooltip: 'Incert Above', state: 'table'},
	{type: 'button', name: 'table_below', icon: '', text: null, tooltip: 'Incert Below', state: 'table'},
	{type: 'button', name: 'table_left', icon: '', text: null, tooltip: 'Insert Left', state: 'table'},
	{type: 'button', name: 'table_right', icon: '', text: null, tooltip: 'Insert Right.', state: 'table'});
