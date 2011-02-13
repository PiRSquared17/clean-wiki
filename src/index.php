<?
define('PAGES_DIR', getcwd() . '/pages/');
define('START_PAGE', 'Home');

include_once('engine/page.php');
include_once('engine/user.php');
include_once('engine/search.php');
include_once('engine/utility.php');
include_once('engine/history.php');
include_once('engine/comments.php');
include_once('engine/security.php');
include_once('engine/permissions.php');
include_once('engine/wiki.php');

$wiki = new Wiki();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<? if ($wiki->editPage) { ?>
		<link rel="StyleSheet" href="richtext/styles/richtext.css" type="text/css"/>
		<link rel="StyleSheet" href="richtext/styles/toolbar.css" type="text/css"/>
		<link rel="StyleSheet" href="richtext/styles/popup.css" type="text/css"/>
		<link rel="StyleSheet" href="richtext/styles/icons.css" type="text/css"/>
	<? } ?>

		<style>
			<?=Utility::getStyles('styles/styles.css');?>
		</style>
	</head>
	<body>
		<table class="header" border="0">
			<tr>
				<td rowspan="2" valign="top">
				<? if ($wiki->editTitle) { ?>
					<input id="editPageName" class="page_name_edit" type="text" value="<?=$wiki->pageName?>"/>
				<? } else {?>
					<a class='<?=($wiki->isFixed ? 'page_name_fixed' : 'page_name')?>' href='<?=$wiki->pageUrl?>'><?=$wiki->pageName?></a>
					<? if ($wiki->hasCreator){?> <div class='created_by'><b>Created by:</b> <i><?=$wiki->createdBy?></i> <b>on</b> <i><?=$wiki->createdOn?></i></div> <?}?>
					<? if ($wiki->isModified){?> <div class='modified_by'><b>Last modified by:</b> <i><?=$wiki->lastModifiedBy?></i> <b>on</b> <i><?=$wiki->lastModifiedOn?></i></div> <?}?>
				<? } ?>
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
								<td><button type="submit" accesskey="q" alt="Search"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAANCAAAAAC4QtCeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAJ0Uk5TAP9bkSK1AAAAaklEQVQI12P4///u3NrauXf/AwHD//05YLAfxLmbl7/n48c9+Xn3gZxJYDGg/BQgpyznC4jzJacMmVOMrgxowMFPnw5CDIAZnbMLxAFaWl09txrEY/gPAe87gTwY5//PmTmr4Jz/f699BgAmIHmp1XxJagAAAABJRU5ErkJggg==" alt="Search"></button></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="menu_bar">
					<a href="<?=$wiki->homeUrl?>">Home</a>
					<a href="<?=$wiki->recentChangesUrl?>">Recent Changes</a>
					<div style="float:right">
						<? if (!$wiki->newPage){?> <a href="<?=$wiki->newUrl?>">New</a> <?}?>
						<? if ($wiki->isEditable){?> <a href="<?=$wiki->editUrl?>">Edit</a> <?}?>
						<? if ($wiki->hasHistory){?> <a href="<?=$wiki->historyUrl?>">History</a> <?}?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="content">

				<!-- Recent Changes, Search Result, Log In, Create Account, Security -->
				<!-- View Mode, Editable Mode (Permissions, Allow Comments, Rich Text), History Mode, Version Mode, Version Diff mode. -->

				<? if ($wiki->action == 'recentchanges') { ?>

					<? include_once('recentchanges.php'); ?>

				<? } else if ($wiki->editPage) { ?>

					<? include_once('edit.php'); ?>

				<? } else if ($wiki->action == 'history') { ?>

					<? include_once('history.php'); ?>
					
				<? } else if ($wiki->action == 'version') { ?>

					<? include_once('version.php'); ?>

				<? } else if ($wiki->action == 'diff') { ?>

					<? include_once('version.php'); ?>

				<? } else if ($wiki->action == 'login') { ?>

					<? include_once('login.php'); ?>

				<? } else { ?>

					<?=$wiki->pageContent?>

				<? } ?>

				</td>
			</tr>
			<tr>
				<td colspan="2">
					<? include_once('comments.php'); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<? include_once('addcomment.php'); ?>
				</td>
			</tr>
		</table>
	</body>
</html>