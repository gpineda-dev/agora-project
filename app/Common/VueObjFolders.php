<?php
////	AFFICHE CHAQUE DOSSIERS DE LA VUE
foreach($foldersList as $tmpFolder)
{
	echo $tmpFolder->divContainer($objContentCenterClass).$tmpFolder->contextMenu();
	echo "<div class='objContent objFolders'>
				<div class='objIcon'><a href=\"".$tmpFolder->getUrl()."\"><img src=\"".$tmpFolder->iconPath()."\" title=\"".Txt::formatTooltip($tmpFolder->description)."\"></a></div>
				<div class='objLabel'><a href=\"".$tmpFolder->getUrl()."\">".Txt::reduce($tmpFolder->name,70)."</a></div>
				<div class='objDetails'>".$tmpFolder->folderOtherDetails()." &nbsp;&nbsp; ".$tmpFolder->folderContentDescription()."</div>
				<div class='objAutorDate'>".$tmpFolder->displayAutorDate()."</div>
			</div>
		</div>";
}
?>