
<? if ($wiki->showComments && $wiki->hasComments) { ?>
	<div class="comments">
		<h1>Comments</h1>
		<? while ($aComment = $wiki->nextComment()) {?> 
			<h2><b>Posted by</b> <i><?=$aComment['user']?></i> <span class='comment_date'><?=Utility::getShortDate(strtotime($aComment['datetime']));?></span></h2>
			<div><?=$aComment['comment']?></div>
		<?}?>
	</div>
<? } ?>
