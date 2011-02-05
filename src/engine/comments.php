<?

class Comments
{
	private $oPage;

	public function __construct($oPage)
	{
		$this->oPage = $oPage;
	}

	/**
	 * Checks if this page has any comments. A page can have comments even if allows comments is 
	 * set to false. This is possible if the flag was set after comments has been added.
	 * So do not use allowsComment to check if a page has comments but rather use hasComments.
	 *
	 * @return true if the page has comments or false if it does not.
	 */
	public function isEmpty()
	{
		//Get the comments element form the header.
		$oComments = $this->oPage->get('//page/comments');

		//Finally check if it has any children.
		return !$oComments->hasChildNodes();
	}

	/**
	 * Check if this page allows users to add comments. The comments element holds the
	 * allow attribute which contains this value. By default if the attribute is not set
	 * then it is true. This attribute can be toggled by calling allowComments method.
	 */
	public function isAllowed()
	{
		//Get the attribute allow within the comment's element and check it's value. The
		//default value is true, so if it is not set then it is allow by default.
		return ($this->oPage->get('//page/comments/@allow', 'true') == 'true');
	}
	
	/**
	 * Toggles the allow comment attribute. The allow comments attribute determines whether
	 * comments are allowed on this page or not. Even if a user has permissions to write
	 * a comment on this page the allow comment attribute overrides this permissions.
	 *
	 * @param bAllow boolean specifies whether to allow comments or not.
	 */
	public function allow($bAllow)
	{
//TODO: check if user has permissions to modified the allow field.

		//Get the comments attribute from the header.
		$oComments = $this->oPage->get('//page/comments');
		
		//Set the attribute to the specified parameter.
		$oComments->setAttribute('allow', ($bAllow ? 'true' : 'false'));

		//Indicate that this page has be modified so that it will be saved later.
		return $this->oPage->modified();
	}

	/**
	 * Gets all the comments within this page as an array of comment arrays. The array
	 * is order according the order attribute. Each comment holds the following information:
	 * datetime, user, comment.
	 */
	public function get()
	{
		//Start by initializing the comments array. This will hold all the comments.
		$aComments = array();

		//Get the comment element fromt he header.
		$oComments = $this->oPage->get('//page/comments');

		//Depending on the ordering required  start from the first or last element and move in
		//the required direction until there are no more comments to read.
		for ($oComment = $oComments->firstChild, $iIndex = 0; $oComment != null; $oComment = $oComment->nextSibling, $iIndex++)
		{
			//Add a new array to the comments array containing the current comment's datetime, user and comment text.
			$aComments[] = array('index'=>$iIndex,
								 'datetime'=>$oComment->getAttribute('datetime'), 
								 'user'=>$oComment->getAttribute('user'), 
								 'comment'=>Utility::decode($oComment),
								 'deleted'=>$oComment->getAttribute('deleted'),
								 'modified'=>$oComment->getAttribute('modified'),
								 'modifiedOn'=>$oComment->getAttribute('modifiedOn'),
								 'modifiedBy'=>$oComment->getAttribute('modifiedBy'));

//TODO: getting comment history?
		}

		//Finally return the comments array which has been populated with all the comments.
		return $aComments;

		//NOTE: The reason why we are not just returning the comments element is 
		//because we want to ensure that callers can not modify the header and 
		//because we want to controle what we are returning.
	}

//TODO: add an ID to the comment so that it can be deleted or modified later.

	/**
	 * Adds a new comment to this page. Two conditions must be met for comments to be added.
	 * 1. User must have permissions to add comments to this page.
	 * 2. The page must allow comments to be added.
	 *
	 * @param oUser an instance of the user object.
	 * @param sComment a string for the comment to be added.
	 */
	public function add($oUser, $sComment)
	{
//TODO: check if the comment is empty.
//TODO: check if the user already added the exact same comment.
	
	
		//Make sure that this page allows comments to be added.
		if ($this->isAllowed())
		{
			//Next check if the specified user has permissions to add comments to this page.
			if ($this->oPage->permissions->has($oUser, PERMISSION_COMMENT_ADD))
			{
				//Get the comment attributes. We don't need to check if it is null because we ensured that 
				//it existed when we created that page and ensured the header's integrity.
				$oComments = $this->oPage->get('//page/comments');

				//Create a new comment node and append it to the comments parent element.
				$oComment = $oComments->appendChild($oComments->ownerDocument->createElement('comment'));

				//Set all the attributes for the new comment node. Attributes include: datetime, user, format and length.
				Utility::setAttributes($oComment, 'datetime', date('Y/m/d H:i:s'), 'user', $oUser->getId(), 'format', 'base64', 'length', strlen($sComment));

				//Set the comment text as the new comment node value.
				$oComment->nodeValue = base64_encode($sComment);

				//Make sure we update the modified field so that we can check for changes later and save if there are any.
				return $this->oPage->modified();
			}
			//If the user does not have permissions to add comments to this page then return false.
			//We could have added this case to the allow comments error case but it is cleaner to keep them
			//separate just in case we wanted to return different error message in the future for each case.
			//Also separating them does not necessary slow down the process.
			else return false;
		}
		//If the page does not allow comments to be added then return false to indicate that the comment was not added.
		else return false;
	}

	public function delete($oUser, $iIndex, $bDelete = true)
	{
		//Check to ensure that the user has permissions to modify comments.
		if ($this->oPage->permissions->has($oUser, PERMISSION_COMMENT_DELETE))
		{
			//Get the comments element so we can access all the comments within this page.
			$oComments = $this->oPage->get('//page/comments');
			
			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of comments.
			if ($iIndex >= 0 && $iIndex < $oComments->childNodes->length)
			{
				//Get the comment at the specified index.
				$oComment = $oComments->childNodes->item($iIndex);

				//We don't delete comments we set the deleted flag, so it is up to the renderer to 
				//notice that this comment is deleted and not display it.
				$oComment->setAttribute('deleted', ($bDelete ? 'true' : 'false'));

				//Make sure we update the modified field so that we can check for changes later and save if there are any.
				return $this->oPage->modified();
			}
			//The specified index was out of bounds do nothing and return false.
			else return false;	
		}
		//If the user does not have permissions to modify comments on this page then return false.
		else return false;
	}

	public function modify($oUser, $iIndex, $sComment)
	{
		//Check to ensure that the user has permissions to modify comments.
		if ($this->oPage->permissions->has($oUser, PERMISSION_COMMENT_MODIFY))
		{
			//Get the comments element so we can access all the comments within this page.
			$oComments = $this->oPage->get('//page/comments');
		
			//If the index is invalid then do nothing and return false. An invalid index is one
			//that is less than 0 or greater than the total number of comments.
			if ($iIndex >= 0 && $iIndex < $oComments->childNodes->length)
			{
				//Get the comment at the specified index.
				$oComment = $oComments->childNodes->item($iIndex);

				//Store the current comment as history within the comment. This will help us
				//keep track of changes just incase we wanted to revert them.
				$oComment->appendChild($oComment->cloneNode(false));
//TODO: check if the node value is also being cloned.

				//Modify the comment and set a flag indicating that the comment has been modified.
				Utility::setAttributes($oComment, 'modified', 'true', 'modifiedOn', date('Y/m/d H:i:s'), 'modifiedBy', $oUser->getId(), 'format', 'base64', 'length', strlen($sComment));

				//Set the comment text as the new comment node value.
				$oComment->nodeValue = base64_encode($sComment);

				//Make sure we update the modified field so that we can check for changes later and save if there are any.
				//This also returns true since the modification was successfull.
				return $this->oPage->modified();
			}
			//The specified index was out of bounds do nothing and return false.
			else return false;		
		}
		//If the user does not have permissions to modify comments on this page then return false.
		else return false;
	}

}


?>