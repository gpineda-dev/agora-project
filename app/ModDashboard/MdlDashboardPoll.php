<?php
/**
* This file is part of the Agora-Project Software package.
*
* @copyright (c) Agora-Project Limited <https://www.agora-project.net>
* @license GNU General Public License, version 2 (GPL-2.0)
*/


/*
 * Modele des actualites
 */
class MdlDashboardPoll extends MdlObject
{
	const moduleName="dashboard";
	const objectType="dashboardPoll";
	const dbTable="ap_dashboardPoll";
	const hasAccessRight=true;
	const hasNotifMail=true;
	const hasUsersLike=true;
	const dontHideMiscMenu=true;
	const hasUsersComment=true;
	public static $requiredFields=array("title");
	public static $searchFields=array("title","description");
	public static $sortFields=array("dateCrea@@desc","dateCrea@@asc","dateModif@@desc","dateModif@@asc","_idUser@@asc","_idUser@@desc","title@@asc","title@@desc","description@@asc","description@@desc");
	//Valeurs mises en cache
	private $_responseList=null;

	/*
	 * STATIC : Liste des sondages à afficher
	 */
	public static function getPolls($mode, $pollsOffsetCpt=0, $notVoted=false, $newsDisplay=false)
	{
		//Selection SQL : Sondages que l'on peut voir  && Uniquement ceux non votés ?  && Uniquement ceux affichés avec les news ?
		$sqlSelection=static::sqlDisplayedObjects();
		if($notVoted==true)		{$sqlSelection.=" AND _id NOT IN (select _idPoll as _id from ap_dashboardPollResponseVote where _idUser=".Ctrl::$curUser->_id.")";}
		if($newsDisplay==true)	{$sqlSelection.=" AND newsDisplay IS NOT NULL";}
		//Nombre de sondages  ||  Sondages en affichage normal (infinite scroll)  ||  Sondages en affichage "newsDisplay" ("GROUP BY" : affiche les + votés en premier!)
		if($mode=="count")			{return Db::getVal("SELECT count(*) FROM ".static::dbTable." WHERE ".$sqlSelection);}
		elseif($newsDisplay==false)	{return Db::getObjTab(static::objectType, "SELECT * FROM ".static::dbTable." WHERE ".$sqlSelection." ".static::sqlSort()." LIMIT 10 OFFSET ".((int)$pollsOffsetCpt * 10));}//$pollsOffsetCpt: "infinite scroll" par blocs de 10
		else{
			$T1Fields="T1._id, T1.title, T1.description, T1.dateEnd, T1.multipleResponses, T1.newsDisplay, T1.dateCrea, T1._idUser, T1.dateModif, T1._idUserModif";//Chaque champ doit être spécifié dans les "GROUP BY" (cf. "sql_mode=only_full_group_by" du "my.cnf")
			return Db::getObjTab(static::objectType, "SELECT T1.*, count(T2._idResponse) as nbVotes FROM ap_dashboardPoll T1  LEFT JOIN ap_dashboardPollResponseVote T2 ON T1._id=T2._idPoll  WHERE ".$sqlSelection."  GROUP BY ".$T1Fields."  ORDER BY nbVotes DESC, T1.dateCrea DESC  LIMIT 10 OFFSET 0");
		}
	}

	/*
	 * STATIC : Droit de créer un sondage
	 */
	public static function addRight()
	{
		return (Ctrl::$curUser->isAdminSpace() || (Ctrl::$curUser->isUser() && Ctrl::$curSpace->moduleOptionEnabled(self::moduleName,"adminAddPoll")==false));
	}

	/*
	 * Liste des réponses d'un sondage
	 */
	public function getResponses($orderByNbVotes=false)
	{
		//Réponses du sondage (trié par "rank"), avec pour chaque réponse : le nb de votes ("GROUP BY") et auquel cas le chemin du fichier
		if($this->_responseList===null)
		{
			$T1Fields="T1._id, T1._idPoll, T1.label, T1.rank, T1.fileName";//Chaque champ doit être spécifié dans les "GROUP BY" (cf. "sql_mode=only_full_group_by" du "my.cnf")
			$this->_responseList=Db::getTab("SELECT  T1.*, count(T2._idResponse) as nbVotes FROM ap_dashboardPollResponse T1  LEFT JOIN ap_dashboardPollResponseVote T2 ON T1._id=T2._idResponse  WHERE T1._idPoll=".$this->_id."  GROUP BY ".$T1Fields."  ORDER BY T1.rank");
			foreach($this->_responseList as $tmpKey=>$tmpResponse){
				if(!empty($tmpResponse["fileName"])){
					$this->_responseList[$tmpKey]["filePath"]=$this->responseFilePath($tmpResponse);
					$this->_responseList[$tmpKey]["fileUrlDownload"]="?ctrl=dashboard&action=ResponseDownloadFile&targetObjId=".$this->_targetObjId."&_idResponse=".$tmpResponse["_id"];
				}
			}
		}
		//Tri par défaut ("rank") OU par nombre de votes
		if($orderByNbVotes==false || function_exists("array_column")==false)	{return $this->_responseList;}
		else{
			$responseList=$this->_responseList;
			$columnToSort=array_column($this->_responseList,"nbVotes");//Colonne où porte le tri : php 5.5 minimum
			array_multisort($columnToSort,SORT_DESC,$responseList);//Tri le tableau
			return $responseList;
		}
	}

	/*
	 * Infos sur une réponse
	 */
	public function getResponse($_idResponse)
	{
		//Parcourt la liste des réponses et renvoi la réponse demandée
		foreach($this->getResponses() as $tmpResponse){
			if($tmpResponse["_id"]==$_idResponse)  {return $tmpResponse;}
		}
	}

	/*
	 * Nombre de votes pour le sondage
	 */
	public function votesNb($_idResponse=false, $curUser=false)
	{
		//Nombre de votes :  pour un réponse précise  /  pour l'user courant  /  Au total
		if(!empty($_idResponse))	{$sqlSelect="AND _idResponse=".Db::format($_idResponse);}
		elseif(!empty($curUser))	{$sqlSelect="AND _idUser=".Ctrl::$curUser->_id;}
		else						{$sqlSelect=null;}
		return Db::getVal("SELECT count(*) FROM ap_dashboardPollResponseVote WHERE _idPoll=".$this->_id." ".$sqlSelect);
	}

	/*
	 * Vérifie si le sondage est terminé
	 */
	public function isFinished()
	{
		return (!empty($this->dateEnd) && Txt::formatDate($this->dateEnd,"dbDate","time")<time());
	}

	/*
	 * L'user courant a déjà voté le sondage ?
	 */
	public function curUserHasVoted()
	{
		return ($this->votesNb(false,true)>0);
	}

	/*
	 * Récupère les résultats d'un sondage
	 */
	public function vuePollResult()
	{
		$vDatas["objPoll"]=$this;
		return Ctrl::getVue(Req::getCurModPath()."vuePollResult.php", $vDatas);
	}

	/*
	 * Récupère le formulaire de vote d'un sondage
	 */
	public function vuePollForm($newsDisplay=false)
	{
		$vDatas["objPoll"]=$this;
		$vDatas["newsDisplay"]=($newsDisplay==true)  ?  "newsDisplay"  :  null;
		return Ctrl::getVue(Req::getCurModPath()."vuePollForm.php", $vDatas);
	}

	/*
	 * SURCHARGE : Suppression d'un sondage
	 */
	public function delete()
	{
		//Supprime chaque reponses du sondage
		foreach($this->getResponses() as $tmpResponse)	{$this->deleteResponse($tmpResponse["_id"],true);}
		//Supprime l'objet lui-même
		parent::delete();
	}

	/*
	 * Supprime une réponse du sondage
	 * $forceDelete à false (édition du sondage) : ne supprime pas la réponse s'il y a deja des votes
	 */
	public function deleteResponse($_idResponse, $forceDelete=false)
	{
		//On supprime les votes de la réponse, Puis supprime la réponse elle-même
		if($this->editRight() && ($forceDelete==true || $this->votesNb($_idResponse)==0))
		{
			//Supprime en bdd
			Db::query("DELETE FROM ap_dashboardPollResponseVote WHERE _idPoll=".$this->_id." AND _idResponse=".Db::format($_idResponse));
			Db::query("DELETE FROM ap_dashboardPollResponse		WHERE _idPoll=".$this->_id." AND _id=".Db::format($_idResponse));
			//Supprime le fichier si besoin
			$this->deleteReponseFile($_idResponse);
		}
	}

	/*
	 * Path du fichier d'une réponse ($tmpResponse : "_id" + "fileName" nécessaires)
	 */
	public function responseFilePath($tmpResponse)
	{
		return PATH_MOD_DASHBOARD.$tmpResponse["_id"].".".File::extension($tmpResponse["fileName"]);
	}

	/*
	 * Affichage du fichier d'une réponse ($tmpResponse : "_id" + "fileName" + "fileUrlDownload" nécessaires)
	 */
	public function responseFileDiv($tmpResponse)
	{
		//Il y a un fichier ?
		if(!empty($tmpResponse["fileName"])){
			//Image avec un lien pour l'afficher OU Nom du fichier avec un lien de téléchargement
			if(File::controlType("imageBrowser",$tmpResponse["fileName"]))	{$responseFileDiv="<a href=\"".$this->responseFilePath($tmpResponse)."\" data-fancybox='images' title=\"".$tmpResponse["fileName"]."\"><img src=\"".$this->responseFilePath($tmpResponse)."\"></a>";}
			else															{$responseFileDiv="<a href=\"".$tmpResponse["fileUrlDownload"]."\" title=\"".Txt::trad("download")."\"><img src='app/img/attachment.png'> ".$tmpResponse["fileName"]."</a>";}
			return "<div class='vPollsResponseFile'>".$responseFileDiv."</div>";
		}
	}

	/*
	 * Supprime le fichier d'une réponse
	 */
	public function deleteReponseFile($_idResponse)
	{
		$tmpResponse=$this->getResponse($_idResponse);
		if(!empty($tmpResponse["fileName"]) && is_file($this->responseFilePath($tmpResponse))){
			$isDeleted=unlink($this->responseFilePath($tmpResponse));
			if($isDeleted==true)  {Db::query("UPDATE ap_dashboardPollResponse SET fileName=null WHERE _idPoll=".$this->_id." AND _id=".Db::format($_idResponse));}
			return $isDeleted;
		}
	}
}