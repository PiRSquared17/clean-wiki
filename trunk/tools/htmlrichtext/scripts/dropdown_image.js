
function RichTextDropdown_Image(oValues, fEventHandler)
{
	var self = new RichTextDropdown(oValues, fEventHandler);

	self.initLink = function()
	{
        self.innerHTML = 
"<form name='RichTextInsertLink' onsubmit='return false;'>" + 
	"<table class='menu_table rt_dropdown_link'>" + 
		"<thead><tr><td align='center' colspan='2'>Insert Image</td></tr></thread>" + 
		"<tbody>" + 
		"<tr><td>Address:</td><td><input type='text' name='link' value='http://' style='width:300px'/></td></tr>" +
		"<tr><td align='center' colspan='2'><input type='submit' value='Insert'/></td></tr>" +
		"</tbody>" + 
	"</table>" + 
"</form>";

		self.form = self.firstChild;
		self.form.onsubmit = self.submit;

		return self;
	};

	self.submit = function()
	{
		fEventHandler({name: 'insertimage', value: self.form.link.value});
		return false;
	};

	self.supperShow = self.show;

	self.show = function(oRelativeTo, iOffsetX, iOffsetY, fCallback)
	{
		self.supperShow(oRelativeTo, iOffsetX, iOffsetY, fCallback); 
		self.makeInternal(true);
		self.form.link.focus();
	};

	return self.initLink();
}