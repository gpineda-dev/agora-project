<script>
////	Resize
lightboxSetWidth(450);
</script>

<div class="lightboxContent objVueBg">
	<div class="lightboxTitle">
		<?php
		if($curObj->editRight())  {echo "<a href=\"javascript:lightboxOpen('".$curObj->getUrl("edit")."')\" class='lightboxTitleEdit' title=\"".Txt::trad("modify")."\"><img src='app/img/edit.png'></a>";}
		echo $curObj->getLabel("all");
		?>
	</div>

	<div class="personLabelImg"><?= $curObj->getImg() ?></div>
	<div class="personVueFields"><?= $curObj->getFieldsValues("profile") ?></div>
	<?php
	//Groupes d'utilisateurs
	$userGroups=MdlUserGroup::getGroups(null,$curObj);
	if(!empty($userGroups)){
		$groupsLabel=null;
		foreach($userGroups as $tmpGroup)  {$groupsLabel.="<img src='app/img/arrowRight.png'> ".$tmpGroup->title."<br>";}
		echo "<div class='objField'><div class='fieldLabel'><img src='app/img/user/userGroup.png'> ".Txt::trad("USER_userGroups")."</div><div>".$groupsLabel."</div></div>";
	}
	?>
</div>