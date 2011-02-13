
var Utility = {

	hasParent: function(oNode, sParentNodeName)
	{
		sParentNodeName = sParentNodeName.toUpperCase();
		for ( ; oNode != null; oNode = oNode.parentNode)
			if (oNode.nodeName.toUpperCase() == sParentNodeName) return true;

		return false;
	},

	getParent: function(oNode, sParentNodeName)
	{
		sParentNodeName = sParentNodeName.toUpperCase();
		for ( ; oNode != null; oNode = oNode.parentNode)
			if (oNode.nodeName.toUpperCase() == sParentNodeName) return oNode;

		return null;
	},
	
	inPopup: function(oNode)
	{
		while (oNode != null)
		{
			if (oNode.isPopup) return true;
			oNode = oNode.parentNode;
		}
		
		return false;
	},

	isDefined: function(oObject)
	{
		return (typeof(oObject) != 'undefined');
	},

	defaultValue: function(oValue, oDefault)
	{
		return (Utility.isDefined(oValue) ? oValue : oDefault);
	},

	clearXML: function(oElement)
	{
		while(oElement.firstChild != null) oElement.removeChild(oElement.lastChild);
	},

	position: function(oObject)
	{
		var oPosition = {x: 0, y: 0, width: oObject.offsetWidth, height: oObject.offsetHeight};
		do
		{
			oPosition.y += oObject.offsetTop;
			oPosition.x += oObject.offsetLeft;
			oObject = oObject.offsetParent;
		}while(oObject != null);

		return oPosition;
	},

	cancelBubble: function(oEvent)
	{
		oEvent = (oEvent ? oEvent : event); 
		oEvent.cancelBubble = true; 
		return false;
	},

	isArray: function(oObject)
	{
		return !(oObject.constructor.toString().indexOf("Array") == -1);
	},

	addEventListener: function(oObject, sEvent, fListener)
	{
		if (oObject.attachEvent) oObject.attachEvent("on" + sEvent, fListener);
		else oObject.addEventListener(sEvent, fListener, false);
	}
};