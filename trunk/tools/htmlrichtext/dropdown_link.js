
function RichTextDropdown_Link(oValues, fEventHandler)
{
	var self = new RichTextDropdown(oValues, fEventHandler);

	self.initLink = function()
	{
        self.innerHTML = 
"<form name='RichTextInsertLink' onsubmit='return false;'>" + 
	"<table class='menu_table rt_dropdown_link'>" + 
		"<thead><tr><td align='center' colspan='2'>Insert Link</td></tr></thread>" + 
		"<tbody>" + 
		"<tr><td></td><td>" + 
		"<input type='radio' name='linktype'/><span onclick='this.previousSibling.click()'>External Link</span>&nbsp;&nbsp;" + 
		"<input type='radio' name='linktype'/><span onclick='this.previousSibling.click()'>Internal Link</span></td></tr>" +
		"<tr><td>Address:</td><td><input type='text' name='link' value='http://' style='width:300px'/></td></tr>" +
		"<tr><td align='center' colspan='2'><input type='submit' value='Insert'/></td></tr>" +
		"</tbody>" + 
	"</table>" + 
"</form>";

		self.form = self.firstChild;
		self.form.onsubmit = self.submit;
		self.form.linktype[0].onclick = function(){ self.makeInternal(false); };
		self.form.linktype[1].onclick = function(){ self.makeInternal(true); };
		self.form.linktype[1].click();

		return self;
	};

	self.submit = function()
	{
		fEventHandler({name: 'createlink', value: self.form.link.value});
		return false;
	};

	self.makeInternal = function(bInternal)
	{
		self.form.link.value = (bInternal ? "?page=" : "http://");
		self.form.link.focus();
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
