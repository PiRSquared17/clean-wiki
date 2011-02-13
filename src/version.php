
<? $aVersion = $wiki->getVersion($wiki->index) ?>

<div class="version">
	<h1>Page Version</h1>
	<? if ($aVersion['index'] == 0) {?> <b>Created on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>)
	<? } else {?>                       <b>Updated on</b> <i><?=$aVersion['datetime']?></i> <b>by</b> <i><?=$aVersion['user']?></i> (<?=$aVersion['length']?>) <?}?>
	<hr/>
</div>

<?=$aVersion['content']?>