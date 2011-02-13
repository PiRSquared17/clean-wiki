
<div class="history">
	<h1>Page History</h1>
	<ul>
		<? if ($aHistory = $wiki->firstHistory()) {?> <li><a href="<?=$aHistory['viewUrl']?>">[ View ]</a> <a href="<?=$aHistory['diffUrl']?>">[ Diff ]</a> <b>Created on</b> <i><?=$aHistory['datetime']?></i> <b>by</b> <i><?=$aHistory['user']?></i> (<?=$aHistory['length']?>)</li> <?}?>
		<? while ($aHistory = $wiki->nextHistory()) {?> <li><a href="<?=$aHistory['viewUrl']?>">[ View ]</a> <a href="<?=$aHistory['diffUrl']?>">[ Diff ]</a> <b>Updated on</b> <i><?=$aHistory['datetime']?></i> <b>by</b> <i><?=$aHistory['user']?></i> (<?=$aHistory['length']?>)</li> <?}?>
	</ul>
</div>