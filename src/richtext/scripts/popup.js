
function RichTextPopup()
{
	var self = document.createElement('DIV');

	self.init = function()
	{
		self.isPopup = true;
		self.isVisible = false;
		self.className = "richtext_popup";
		return self;
	};

	self.show = function(oRelativeTo, iOffsetX, iOffsetY)
	{
		if (self.isVisible) return self.hide();

		iOffsetX = Utility.defaultValue(iOffsetX, 0);
		iOffsetY = Utility.defaultValue(iOffsetY, 0);
		var oPosition = (Utility.isDefined(oRelativeTo) ? Utility.position(oRelativeTo) : {x: event.x, y: event.y});

		self.style.display = "block";
		self.style.top = (oPosition.y + iOffsetY) + "px";
		self.style.left = (oPosition.x + iOffsetX) + "px";
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
