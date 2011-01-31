
function RichTextDropdown(oValues, fEventHandler)
{
	var self = document.createElement('DIV');

	self.init = function()
	{
		if (Utility.isArray(oValues)) self.create(oValues);

		self.isVisible = false;
		self.className = "popup";
		//self.onkeydown = Utility.cancelBubble;
		//self.onmousedown = Utility.cancelBubble;
		return self;
	};

    self.create = function(aValues)
    {
        var sDropdown = "";
        for (var i = 0; i < aValues.length; i++) self.appendChild(self.createButton(aValues[i]));
    };

	self.createButton = function(oRtButton)
	{
		var oButton = document.createElement('A');
		oButton.rtEvent = oRtButton;
		oButton.onclick = fEventHandler;
		oButton.className = 'menu_item';
		oButton.innerHTML = oRtButton.text;

		return (oRtButton.node = oButton);
	};

    self.show = function(oRelativeTo, iOffsetX, iOffsetY, fCallback)
    {
		if (self.isVisible) return self.hide();

        var oPosition = Utility.position(oRelativeTo);

        self.style.display = "block";
        self.style.top = (oPosition.y + iOffsetY) + "px";
        self.style.left = (oPosition.x + iOffsetX) + "px";
        self.onSelect = fCallback;
		self.isVisible = true;

        if (self.onShow) self.onShow();
    };

    self.hide = function()
    {
		self.isVisible = false;
        self.style.display = "none";
    };

	return self.init();
}
