
function RichTextPopup_Menu(aValues, fEventHandler)
{
	var self = new RichTextPopup();

	self.initMenu = function()
	{
		self.addItems(aValues);
		return self;
	};

	self.addItems = function(aValues)
	{
		for (var i = 0; i < aValues.length; i++) self.appendChild(self.addItem(aValues[i]));
	};

	self.addItem = function(oItem)
	{
		var oMenuItem = document.createElement('A');
		oMenuItem.rtObject = oItem;
		oMenuItem.onclick = fEventHandler;
		oMenuItem.className = 'menu_item';
		oMenuItem.innerHTML = oItem.text;

		return (oItem.node = oMenuItem);
	};

	return self.initMenu();
}
