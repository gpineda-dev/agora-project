<!doctype html>
<html lang="<?= Txt::trad("HEADER_HTTP") ?>">
	<head>
		<!-- AGORA-PROJECT :: UNDER THE GENERAL PUBLIC LICENSE V2 :: http://www.gnu.org -->
		<meta charset="UTF-8">
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta http-equiv="content-language" content="<?= Txt::trad("HEADER_HTTP") ?>">
		<title><?= !empty(Ctrl::$agora->name) ? Ctrl::$agora->name : "Omnispace.fr - Agora-Project" ?></title>
		<meta name="Description" content="<?= !empty(Ctrl::$agora->description) ? Ctrl::$agora->description : "Omnispace.fr - Agora-Project" ?>">
		<meta name="application-name" content="Agora-Project">
		<meta name="application-url" content="https://www.agora-project.net">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"><!--mode compatibilité IE-->
		<link rel="icon" type="image/gif" href="app/img/favicon.png" />
		<!-- JQUERY & JQUERY-UI -->
		<script src="app/js/jquery-3.3.1.min.js"></script>
		<script src="app/js/jquery-ui/jquery-ui.min.js"></script>
		<script src="app/js/jquery-ui/datepicker-<?= Txt::trad("DATEPICKER") ?>.js"></script><!--lang du jquery-ui datepicker-->
		<link rel="stylesheet" href="app/js/jquery-ui/<?= $skinCss=="white"?"smoothness":"ui-darkness" ?>/jquery-ui.css"><!--skin du jquery-ui-->
		<!-- JQUERY PLUGINS -->
		<link  href="app/js/fancybox/dist/jquery.fancybox.css" rel="stylesheet">
		<script src="app/js/fancybox/dist/jquery.fancybox.min.js"></script>
		<script type="text/javascript" src="app/js/tooltipster/tooltipster.bundle.min.js"></script>
		<link rel="stylesheet" type="text/css" href="app/js/tooltipster/tooltipster.bundle.min.css">
		<link rel="stylesheet" type="text/css" href="app/js/tooltipster/themes/tooltipster-sideTip-shadow.min.css">
		<script type="text/javascript" src="app/js/toastmessage/jquery.toastmessage.js"></script>
		<link rel="stylesheet" type="text/css" href="app/js/toastmessage/toastmessage.css">
		<script src="app/js/timepicker/jquery.timepicker.min.js"></script>
		<link rel="stylesheet" type="text/css" href="app/js/timepicker/jquery.timepicker.css">
		<!-- JS & CSS DE L'AGORA -->
		<script src="app/js/common-3.6.3.js"></script><!--toujours après Jquery & plugins Jquery !!-->
		<link href="app/css/common-3.6.3.css" rel="stylesheet" type="text/css">
		<link href="app/css/<?= $skinCss ?>.css?v<?= VERSION_AGORA ?>" rel="stylesheet" type="text/css">

		<script>
		//Alerte pour MSIE
		if(/msie/i.test(navigator.userAgent) || /trident/i.test(navigator.userAgent))  {alert("<?= Txt::trad("ieObsolete") ?>");}
		//Divers params
		isMainPage=<?= Ctrl::$isMainPage==true ? "true" : "false" ?>;
		windowParent=(isMainPage==true) ? window : window.parent;//Si l'espace est intégré dans un Iframe (cf. redirection "invisible" de domaine)
		confirmCloseForm=false;//Confirmation de fermeture de page (exple: lightbox d'édition)
		//Divers labels de "common.js"
		labelConfirmCloseForm="<?= Txt::trad("confirmCloseForm") ?>";
		labelSpecifyLoginPassword="<?= Txt::trad("specifyLoginPassword") ?>";
		labelUploadMaxFilesize="<?= File::uploadMaxFilesize("error") ?>";
		valueUploadMaxFilesize=<?= File::uploadMaxFilesize() ?>;
		labelConfirmDelete="<?= Txt::trad("confirmDelete") ?>";
		labelDateBeginEndControl="<?= Txt::trad("beginEndError") ?>";
		labelEvtConfirm="<?= Txt::trad("CALENDAR_evtIntegrate") ?>";
		labelEvtConfirmNot="<?= Txt::trad("CALENDAR_evtNotIntegrate") ?>";

		////	Au chargement de la page
		$(function(){
			<?php
			////	Fermeture de lightbox ($msgNotif en parametre?)
			if(Ctrl::$lightboxClose==true)  {echo "lightboxClose(null,\"".Ctrl::$lightboxCloseParams."\");";}
			////	Affiche si besoin des notifications (messages à traduire?)
			foreach(Ctrl::$msgNotif as $tmpNotif){
				if(Txt::isTrad($tmpNotif["message"]))  {$tmpNotif["message"]=Txt::trad($tmpNotif["message"]);}
				echo "notify(\"".$tmpNotif["message"]."\", \"".$tmpNotif["type"]."\");";
			}
			?>
		});
		</script>

		<?php
		////	Footer javascript du Host ?
		if(Ctrl::isHost())  {Host::footerJs();}
		?>
		
		<style>
		<?php if(Ctrl::$isMainPage==true && Req::isMobile()){ ?>
		html	{height:100%; background:url(app/img/logoMobileBg.png) <?= (is_object(Ctrl::$agora) && Ctrl::$agora->skin=="black") ?"#333":"#f2f2f2" ?>; background-repeat:no-repeat; background-position:center 95%;}/*100% pour un affichage toute la hauteur (tester sur Forum)*/
		body	{background-color:transparent!important;}/*réinitialise les valeurs par défaut (cf. black/white.css) pour afficher le background "html' ci-dessus */
		<?php } ?>
		#backgroundImg		{position:fixed; z-index:-10; left:0px; top:0px; width:100%; height:100%;}
		#pageFooterHtml, #pageFooterIcon	{position:fixed; bottom:0px; z-index:20; display:inline-block; font-weight:normal;}/*pas de margin*/
		#pageFooterHtml		{left:0px; padding:5px; padding-right:10px; color:#eee; text-shadow:0px 0px 9px #000;}
		#pageFooterIcon		{right:2px; bottom:3px;}
		#pageFooterIcon img	{max-height:50px; max-width:200px;}
		#pageFooterSpecial	{display:inline-block; margin:0px 0px -7px -7px; background-color:rgba(0,0,0,0.7); border-radius:5px; padding:8px; color:#c00; font-weight:bold;}/*host*/
		/*RESPONSIVE*/
		@media screen and (max-width:1023px){
			#pageFooterHtml, #pageFooterIcon	{display:none!important;}
		}
		/*IMPRESSION*/
		@media print{
			[id^='pageFooter']	{display:none!important;}
		}
		</style>
	</head>

	<body>
		<?php
		////	PAGE PRINCIPALE : WALLPAPER & CONTENU DES LIGHTBOX & MENU RESPONSIVE
		if(Ctrl::$isMainPage==true)
		{
			//Image du background
			if(Req::isMobile()==false)  {echo "<img src=\"".$pathWallpaper."\" id='backgroundImg' class='noPrint'>";}
			//Menu responsive (le menu responsive peux fusionner 2 menu principaux. exple : menu des modules)
			echo "<div id='respMenuBg'></div>
				  <div id='respMenuMain'>
					<div id='respMenuClose'><img src='app/img/closeResp.png'></div>
					<div id='respMenuContent'><div id='respMenuContentOne'></div><hr id='respMenuHrSeparator'><div id='respMenuContentTwo'></div></div>
				  </div>";
		}

		////	MENU PRINCIPAL (HEADER) & CORPS DE LA PAGE
		echo $headerMenu.$mainContent.$messengerLivecounter;

		////	PAGE PRINCIPALE : FOOTER PERSONNALISE (script de stats, etc)  &  ICONE DE L'ESPACE
		if(Ctrl::$isMainPage==true && is_object(Ctrl::$agora))
		{
			echo "<div id='pageFooterHtml'>".Ctrl::$agora->footerHtml."</div>
				  <div id='pageFooterIcon'><a href=\"".$pathLogoUrl."\" target='_blank' title=\"".$pathLogoTitle."\"><img src=\"".Ctrl::$agora->pathLogoFooter()."\"></a></div>";
		}
		?>
	</body>
</html>