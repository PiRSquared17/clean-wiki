
<? if ($wiki->showComments && $wiki->allowsComments && $wiki->hasCommentPermissions) { ?>
	<div class="comment">
		<h1>Add Comment</h1>
		<form method="get">
			<input type="hidden" name="action" value="comment"/>
			<input type="hidden" name="page" value="<?=$wiki->pageName?>" />
			<table border="0">
				<tr><td>Name:</td><td><input type="text" name="name" value="<?=$wiki->userName?>"/></td></tr>
				<tr><td>Email:</td><td><input type="text" name="email" value="<?=$wiki->userEmail?>"/></td></tr>
				<tr><td colspan="2"><textarea name="comment"></textarea></td></tr>
				<tr><td colspan="2" align="center"><input type="submit" value="Post Comment"/></td></tr>
			</table>
		</form>
	</div>
<? } ?>