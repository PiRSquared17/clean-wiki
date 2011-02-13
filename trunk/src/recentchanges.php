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