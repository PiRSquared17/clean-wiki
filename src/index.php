<?

include_once('engine/wiki.php');

define('ON', 'on');
define('CREATED_BY', 'Created by');
define('LAST_MODIFIED_BY', 'Last modified by');

$wiki = new Wiki();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<style>
			<?=Utility::getStyles('styles.css');?>
		</style>
	</head>
	<body>
		<table class="header">
			<tr>
				<td rowspan="2" valign="top">
					<a class='<?=($wiki->isFixed ? 'page_name_fixed' : 'page_name')?>' href='<?=$wiki->pageUrl?>'><?=$wiki->pageName?></a>
					<? if ($wiki->hasCreator){?> <div class='created_by'><b>Created by:</b> <i><?=$wiki->createdBy?></i> <b>on</b> <i><?=$wiki->createdOn?></i></div> <?}?>
					<? if ($wiki->isModified){?> <div class='modified_by'><b>Last modified by:</b> <i><?=$wiki->lastModifiedBy?></i> <b>on</b> <i><?=$wiki->lastModifiedOn?></i></div> <?}?>
				</td>
				<td valign="top" align="right">
					<div class="menu">
						<? if ($wiki->hasSecurityPermissions){?> <a href="<?=$wiki->securityUrl?>">Security</a> <?}?>
						<? if ($wiki->canCreateAccount){?> <a href="<?=$wiki->createAccountUrl?>">Creat Account</a> <?}?>
						<? if ($wiki->isLoggedIn){?> <a href="<?=$wiki->logoutUrl?>">Log out</a> <?}?>
						<? if (!$wiki->isLoggedIn){?> <a href="<?=$wiki->loginUrl?>">Log in</a> <?}?>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="bottom" align="right">
					<form name="search" method="get" action="">
						<input type="hidden" name="action" value="search"/>
						<table class="search" cellpadding="0" cellspacing="0">
							<tr>
								<td><input type="text" name="search" value="<?=$wiki->searchString?>" tabindex="1"/></td>
								<td><button type="submit" accesskey="q" alt="Search"><img src="images/icons/search.png" alt="Search"></button></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
		<div class="menu_bar">
			<a href="<?=$wiki->homeUrl?>">Home</a>
			<a href="<?=$wiki->recentChangesUrl?>">Recent Changes</a>
			<div style="float:right">
				<? if ($wiki->isEditable){?> <a href="<?=$wiki->editUrl?>">Edit</a> <?}?>
				<? if ($wiki->hasHistory){?> <a href="<?=$wiki->historyUrl?>">History</a> <?}?>
			</div>
		</div>
		<div class="content">
			<!-- Recent Changes, Search Result, Log In, Create Account, Security -->
			<!-- View Mode, Editable Mode (Permissions, Allow Comments, Rich Text), History Mode, Version Mode, Version Diff mode. -->

			<? if ($wiki->action == 'recentchanges') { ?>

				<table class="recentchanges">
					<? while ($aPage = $wiki->nextPage()) {?> 
						<tr>
							<td><a href="<?=$aPage['url']?>"><?=$aPage['name']?></a></td>
							<td><b><?=($aPage['isNew'] ? 'Created by' : 'Updated by')?></b></td>
							<td><i><?=$aPage['user']?></i></td>
							<td><b>on</b></td>
							<td><i><?=$aPage['datetime']?></i></td>
							<td><b>about</b></td>
							<td><i><?=$aPage['shortDate']?></i></td>
						</tr>
					<?}?>
				</table>
			<? } else if ($wiki->action == 'edit') { ?>
			
			
			<? } else if ($wiki->action == 'history') { ?>
			
				<div class="history">
					<h1>Page History</h1>
					<ul>
						<? if ($aHistory = $wiki->firstHistory()) {?> <li><a href="<?=$aHistory['viewUrl']?>">[ View ]</a> <a href="<?=$aHistory['diffUrl']?>">[ Diff ]</a> <b>Created on</b> <i><?=$aHistory['datetime']?></i> <b>by</b> <i><?=$aHistory['user']?></i> (<?=$aHistory['length']?>)</li> <?}?>
						<? while ($aHistory = $wiki->nextHistory()) {?> <li><a href="<?=$aHistory['viewUrl']?>">[ View ]</a> <a href="<?=$aHistory['diffUrl']?>">[ Diff ]</a> <b>Updated on</b> <i><?=$aHistory['datetime']?></i> <b>by</b> <i><?=$aHistory['user']?></i> (<?=$aHistory['length']?>)</li> <?}?>
					</ul>
				</div>
				
			<? } else if ($wiki->action == 'version') { ?>
			
				<? $aVersion = $wiki->getVersion($wiki->index) ?>

				<div class="version">
					<h1>Page Version</h1>
					<? if ($aVersion['index'] == 0) {?> <b>Created on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>)
					<? } else {?>                       <b>Updated on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>) <?}?>
					<hr/>
				</div>

				<?=$aVersion['content']?>

			<? } else if ($wiki->action == 'diff') { ?>
			
				<? $aVersion = $wiki->getVersionDiff($wiki->index) ?>

				<div class="version">
					<h1>Version Difference</h1>
					<? if ($aVersion['index'] == 0) {?> <b>Created on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>)
					<? } else {?>                       <b>Updated on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>) <?}?>
					<hr/>
				</div>

				<?=$aVersion['content']?>

			<? } else { ?>

				<?=$wiki->pageContent?>

			<? } ?>
		</div>
		
		<? if ($wiki->showComments) { ?>
			<? if ($wiki->hasComments) { ?>
				<div class="comments">
					<h1>Comments</h1>
					<? while ($aComment = $wiki->nextComment()) {?> 
						<h2><b>Posted by</b> <i><?=$aComment['user']?></i> <span class='comment_date'><?=Utility::getShortDate(strtotime($aComment['datetime']));?></span></h2>
						<div><?=$aComment['comment']?></div>
					<?}?>
				</div>
			<? } ?>

			<? if ($wiki->allowsComments && $wiki->hasCommentPermissions) { ?>
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
		<? } ?>

	</body>
</html>