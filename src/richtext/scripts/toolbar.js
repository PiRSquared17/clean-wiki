
function RichTextToolbar(aIcons, fEventHandler)
{
	var self = document.createElement("TABLE");

	self.init = function()
	{
		self.className = 'richtext_toolbar';
		self.build(aIcons);

		return self;
	};

	self.build = function(aIcons)
	{
		self.icons = aIcons;
		var oRow = self.insertRow(-1);
		var oCell = oRow.insertCell(-1);

		for (var i = 0; i < aIcons.length; i++)
		{
			var oIcon = aIcons[i];
			if (oIcon.type == 'newcell') oCell = oRow.insertCell(-1);
			else if (oIcon.type == 'newline') oCell.appendChild(document.createElement('BR'));
			else if (oIcon.type == 'button') oCell.appendChild(self.createButton(oIcon, fEventHandler));
		}
	};

	self.createButton = function(oIcon, fEventHandler)
	{
		var oButton = document.createElement('A');
		oButton.rtEvent = oIcon;
		oButton.title = oIcon.tooltip;
		oButton.onclick = fEventHandler;
		oButton.className = 'richtext_toolbar_button';

		if (oIcon.icon != null)
		{
			var oImage = oButton.appendChild(document.createElement("IMG"));
			oImage.className = "rt_icons_" + (oIcon.icon.length == 0 ? oIcon.name : oIcon.icon);
			oImage.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sCBBYpODVgeswAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAAADUlEQVQI12NgYGBgAAAABQABXvMqOgAAAABJRU5ErkJggg==";
		}

		if (oIcon.text != null) oButton.appendChild(document.createTextNode(oIcon.text));

		oButton.isEnabled = true;
		oButton.isSelected = false;
		oButton.setEnabled = function(bEnabled){ oButton.isEnabled = bEnabled; oButton.className = (bEnabled ? 'richtext_toolbar_button' : 'richtext_toolbar_button_disabled'); };
		oButton.setSelected = function(bSelected){ oButton.isSelected = bSelected; oButton.className = (bSelected ? 'richtext_toolbar_button_selected' : 'richtext_toolbar_button');  };		

		return (oIcon.node = oButton);
	};

	return self.init();
}
