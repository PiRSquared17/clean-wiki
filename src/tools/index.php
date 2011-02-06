<?
include('mergeicons.php');

//header('content-type: image/png', false);
//$oMergeIcons = new MergeIcons();
//$oMergeIcons->load('icons/');
//$oMergeIcons->merge();
//$oMergeIcons->save('rt_icons.png');
//echo $oMergeIcons->generateCSS('rt_icons', 'rt_icons.png', true, 'icons.css');
echo base64_encode(file_get_contents('icons/search.png'));

?>