<?php
include("../config.php");


class Trajet {

	//List of the variables
	private $idTrajet;
	private $typeTrajet;
	private $villeDepart;
	private $villeArrivee;
	private $prix;
	private $description;
	private $date;
	private $heure;
	private $duree;


	public function __construct ($typeTrajet, $villeDepart, $villeArrivee, $prix, $description, $date, $heure, $duree){
		$this->typeTrajet = $typeTrajet;
		$this->villeDepart = $villeDepart;
		$this->villeArrivee = $villeArrivee;
		$this->prix = $prix;
		$this->description = $description;
		$this->date = $date;
		$this->heure = $heure;
		$this->duree = $duree;
		$this->idTrajet = null;
	}

	public function getidTrajet() {
		return $this->idTrajet;
	}

	public function setidTrajet($id) {
		$this->idTrajet = $id;
	}

	public function gettypeTrajet() {
		return $this->typeTrajet;
	}

	public function getvilleDepart() {
		return $this->villeDepart;
	}

	public function getvilleArrivee() {
		return $this->villeArrivee;
	}

	public function getprix() {
		return $this->prix;
	}

	public function getdescription() {
		return $this->description;
	}

	public function getdate() {
		return $this->date;
	}

	public function getheure() {
		return $this->heure;
	}

	public function getduree() {
		return $this->duree;
	}

	public static function createTrajet($typeTrajet, $villeDepart, $villeArrivee, $prix, $nbpers, $duree, $description, $date, $heure, $tabescale, $tabflag, $idUser, $lienGoogle){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM trajet") or die("ERROR0");
		$req = $mysqli->query("INSERT INTO trajet (typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure) VALUES ('$typeTrajet','$villeDepart', '$villeArrivee', '$prix', '$nbpers','$duree','$description','$date','$heure')") or die($mysqli->error);
		$req = $mysqli->query("SELECT idTrajet FROM trajet WHERE typeTrajet='$typeTrajet' AND villedep='$villeDepart' AND dateTrajet='$date' AND heure='$heure'") or die ("ERROR2");
		$tupleTrajet = $req->fetch_array();
		$idTrajet = $tupleTrajet['idTrajet'];
		
		$req = $mysqli->query("INSERT INTO usertrajetcreator (idUser,idTrajet) VALUES ('$idUser','$idTrajet')") or die ("ERROR3");

		if($typeTrajet == 1 || $typeTrajet == 2 || $typeTrajet == 6 || $typeTrajet == 7 || $typeTrajet == 8 || $typeTrajet == 9){

			$req = $mysqli->query("SELECT * FROM escale WHERE ville = '$villeDepart'") or die("ERROR2.5");
			$tuplevilleDep = $req->fetch_array();
			if($tuplevilleDep == null){
				$req = $mysqli->query("INSERT INTO escale (ville) VALUES ('$villeDepart')") or die ("ERROR2.5.2");
			}
			$req = $mysqli->query("SELECT * FROM escale WHERE ville = '$villeArrivee'") or die("ERROR2.5.3");
			$tuplevilleArr = $req->fetch_array();
			if($tuplevilleArr == null){
				$req = $mysqli->query("INSERT INTO escale (ville) VALUES ('$villeArrivee')") or die ("ERROR2.5.4");
			}

			if ($tabescale != null) {
				foreach ($tabescale as $key => $value) {
					$req = $mysqli->query("SELECT idVille FROM escale WHERE ville='$value'") or die ("ERROR4");
					$tuple = $req->fetch_array();
					if($tuple == null){
						$req = $mysqli->query("INSERT INTO escale (ville) VALUES ('$value')") or die ("ERROR5");
						$req = $mysqli->query("SELECT * FROM escale WHERE ville='$value'") or die("ERROR6");
						$tupleNewVille = $req->fetch_array();
						$idVille=$tupleNewVille['idVille'];
						$req = $mysqli->query("INSERT INTO trajetescale(idTrajet,idVille,ordre) VALUES ('$idTrajet','$idVille', '$key')") or die ("ERROR7");
					}
					else {
						$req = $mysqli->query("INSERT INTO trajetescale(idTrajet,idVille,ordre) VALUES ('$idTrajet','$value', '$key')") or die ("ERROR8");
					}
				}
			}
		}
		else {

			$req = $mysqli->query("SELECT * FROM lieu WHERE lieu = '$villeDepart'") or die("ERROR2.5");
			$tuplelieuDep = $req->fetch_array();
			if($tuplelieuDep == null){
				$req = $mysqli->query("INSERT INTO lieu (lieu) VALUES ('$villeDepart')") or die ("ERROR2.5.2");
			}
			$req = $mysqli->query("SELECT * FROM lieu WHERE lieu = '$villeArrivee'") or die("ERROR2.5.3");
			$tuplelieuArr = $req->fetch_array();
			if($tuplelieuArr == null){
				$req = $mysqli->query("INSERT INTO lieu (lieu) VALUES ('$villeArrivee')") or die ("ERROR2.5.4");
			}
			if ($tabescale != null) {
				foreach ($tabescale as $key => $value) {
					$req = $mysqli->query("SELECT idLieu FROM lieu WHERE lieu='$value'") or die ("ERROR9");
					$tuple = $req->fetch_array();
					if($tuple == null){
						$req = $mysqli->query("INSERT INTO lieu (lieu) VALUES ('$value')") or die ("ERROR10");
						$req = $mysqli->query("SELECT * FROM lieu WHERE lieu='$value'") or die("ERROR11");
						$tupleNewLieu = $req->fetch_array();
						$idLieu=$tupleNewLieu['idLieu'];
						$req = $mysqli->query("INSERT INTO trajetlieu(idTrajet,idLieu,ordre) VALUES ('$idTrajet','$idLieu', '$key')") or die ("ERROR12");
					}
					else {
						$req = $mysqli->query("INSERT INTO trajetlieu(idTrajet,idLieu,ordre) VALUES ('$idTrajet','$value', '$key')") or die ("ERROR13");
					}
				}
			}
		}
		if ($tabflag != null) {
			foreach ($tabflag as $key => $value) {
				$req = $mysqli->query("INSERT INTO trajetflag (idTrajet,idFlag) VALUES ('$idTrajet','$value')") or die ("ERROR14");
			}
		}
		$req = $mysqli->query("INSERT INTO usertrajetgoogle (idTrajet, lienGoogle) VALUES ('$idTrajet','$lienGoogle')") or die ("ERROR15");
		return $req;
	}

	public static function modifyTrajet($idTrajet, $typeTrajet, $villeDepart, $villeArrivee, $prix, $duree, $description, $date, $heure, $tabescale, $tabflag, $lienGoogle){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM trajet WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$tupleTrajet =  $req->fetche_array();
		if($tupleTrajet['typeTrajet'] != $typeTrajet){
			$req = $mysqli->query("UPDATE trajet SET typeTrajet='$typeTrajet' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['typeTrajet'] != $typeTrajet){
			$req = $mysqli->query("UPDATE trajet SET typeTrajet='$typeTrajet' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['villedep'] != $villeDepart){
			$req = $mysqli->query("UPDATE trajet SET villede='$villeDepart' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['villearr'] != $villeArrivee){
			$req = $mysqli->query("UPDATE trajet SET villearr='$villeArrivee' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['prix'] != $prix){
			$req = $mysqli->query("UPDATE trajet SET prix='$prix' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['duree'] != $duree){
			$req = $mysqli->query("UPDATE trajet SET duree='$duree' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['description'] != $description){
			$req = $mysqli->query("UPDATE trajet SET description='$description' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['dateTrajet'] != $date){
			$req = $mysqli->query("UPDATE trajet SET dateTrajet='$date' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		if($tupleTrajet['heure'] != $heure){
			$req = $mysqli->query("UPDATE trajet SET heure='$heure' WHERE idTrajet = '$idTrajet'") or die("ERROR");
		}
		$req = $mysqli->query("DELETE FROM trajetescale WHERE idTrajet='$idTrajet'") or die("ERROR");
		foreach ($tabescale as $key => $value) {
			$req = $mysqli->query("SELECT id FROM trajetescale WHERE ville='$value'") or die ("ERROR");
			$tuple = $req->fetch_array();
			if($tuple == null){
				$req = $mysqli->query("INSERT INTO escale (ville) VALUES('$value')") or die ("ERROR");
				$req = $mysqli->query("SELECT * FROM escale WHERE ville='$value'") or die("ERROR");
				$tupleNewVille = $req->fetch_array();
				$nomVille=$tupleNewVille['ville'];
				$req = $mysqli->query("INSERT INTO trajetescale(idTrajet,idVille,ordre) VALUES ('$idTrajet','$nomVille', '$key')") or die ("ERROR");
			}
			else {
				$req = $mysqli->query("INSERT INTO trajetescale(idTrajet,idVille,ordre) VALUES ('$idTrajet','$value', '$key')") or die ("ERROR");
			}
		}
		$req = $mysqli->query("DELETE FROM trajetflag WHERE idTrajet='$idTrajet'") or die("ERROR");
		if ($tabflag != null) {
			foreach ($tabflag as $key => $value) {
				$req = $mysqli->query("INSERT INTO trajetflag (idTrajet,idFlag) VALUES ('$idTrajet','$value')") or die ("ERROR");
			}
		}
		$req = $mysqli->query("INSERT INTO usertrajetgoogle (idTrajet, lienGoogle) VALUES ('$idTrajet','$lienGoogle')") or die ("ERROR");
		return $req;
	}

	public static function deleteTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("DELETE FROM trajet WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$req = $mysqli->query("DELETE FROM usertrajetcreator WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$req = $mysqli->query("DELETE FROM trajetescale WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$req = $mysqli->query("DELETE FROM trajetflag WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$req = $mysqli->query("DELETE FROM usertrajetgoogle WHERE idTrajet='$idTrajet'") or die ("ERROR");
		$req = $mysqli->query("DELETE FROM usertrajetpassager WHERE idTrajet='$idTrajet'") or die("ERROR");
		return $req;
	}

	public static function subscribeTrajet($idTrajet, $idUser) {
		global $mysqli;
		$boolfalse = 0;
		$req = $mysqli->query("INSERT INTO usertrajetpassager (idTrajet,idUser,accepted) VALUES ('$idTrajet','$idUser','$boolfalse')") or die("ERROR");
		return $req;
	}

	public static function accepteSub($idTrajet, $idUser) {
		global $mysqli;
		$booltrue = 1;
		$req = $mysqli->query("UPDATE usertrajetpassager SET accepted='$booltrue' WHERE idTrajet='$idTrajet' AND idUser='$idUser'") or die("ERROR");
		return $req;
	}

	public static function postMessage($idTrajet,$pseudo, $message, $date, $heure){
		global $mysqli;
		$req = $mysqli->query("INSERT INTO message (auteur,message,datePost,heurePost) VALUES ('$pseudo','$message','$date','$heure')") or die("ERROR");
		$req = $mysqli->query("SELECT idMessage FROM message WHERE auteur='$pseudo' AND datePost='$date' AND heurePost='$heure' ") or die("ERROR");
		$tuple = $req->fetch_array();
		$idMessage = $tuple['idMessage'];
		$req = $mysqli->query("INSERT INTO trajetmessage (idTrajet,idMessage) VALUES ('$idTrajet','$idMessage') ") or die("ERROR");
		return $req;
	}

	public static function getAllEscale() {
		global $mysqli;
		$reqVille = $mysqli->query("SELECT * FROM escale") or die("ERROR");
		$i = 0;
		while($tupleville = $reqVille->fetch_array()){
			$tableauVille[$i] = $tupleville;
			$i++;
		}
		if($i == 0) {
			return null;
		}
		else {
			return $tableauVille;
		}
	}

	public static function getVilleByName($name){
		global $mysqli;
		$reqVille = $mysqli->query("SELECT * FROM escale WHERE ville LIKE '$name'") or die("ERROR");
		$i = 0;
		while($tupleville = $reqVille->fetch_array()){
			$tableauVille[$i] = ucfirst($tupleville['ville']);
			$i++;
		}
		if($i == 0) {
			return null;
		}
		else {
			return $tableauVille;
		}
	}

	public static function getAllLieu() {
		global $mysqli;
		$reqLieu = $mysqli->query("SELECT * FROM lieu") or die("ERROR");
		$i = 0;
		while($tuplelieu = $reqLieu->fetch_array()){
			$tableauLieu[$i] = ucfirst($tuplelieu['lieu']);
			$i++;
		}
		if($i == 0) {
			return null;
		}
		else {
			return $tableauLieu;
		}
	}

	public static function getLieuByName($name){
		global $mysqli;
		$reqLieu = $mysqli->query("SELECT * FROM lieu WHERE lieu LIKE '$name'") or die("ERROR");
		while($tuplelieu = $reqLieu->fetch_array()){
			$tableauLieu[$i] = ucfirst($tuplelieu['lieu']);
			$i++;
		}
		if($i == 0) {
			return null;
		}
		else {
			return $tableauLieu;
		}
	}

	public static function getTrajetByUser($idUser){
		global $mysqli;
		$req = $mysqli->query("SELECT idTrajet FROM usertrajetcreator WHERE idUser = '$idUser'") or die("ERROR");
		$i=0;
		while ($tuple = $req->fetch_array()) {
			$idTrajet = $tuple['idTrajet'];
			$req2 = $mysqli->query("SELECT * FROM trajet WHERE idTrajet = '$idTrajet' ORDER BY dateTrajet ASC") or die("ERROR");
			$j = 0;
			while ($tupleTrajet = $req2->fetch_array()) {
				$listeTrajet[$j] = $tupleTrajet;
				$j++;
			}
			$i++;
		}
		return $listeTrajet;
	}

	public static function getTrajetsByUser($idUser){
		global $mysqli;
		$dateToday = Date("Y-m-d");
		$req = $mysqli->query("SELECT * FROM trajet, (SELECT idTrajet
														FROM usertrajetcreator
														WHERE idUser = '$idUser') AS idT
								WHERE trajet.idTrajet = idT.idTrajet AND trajet.dateTrajet > '$dateToday'") or die("ERROR");
		$i=0;
		$listeTrajets = [];
		while ($tuple = $req->fetch_array()) {
			$listeTrajets[$i] = new Trajet($tuple['typeTrajet'], $tuple['villedep'], $tuple['villearr'], $tuple['prix'], $tuple['description'], $tuple['dateTrajet'], $tuple['heure'], $tuple['duree']);
			$listeTrajets[$i]->setidTrajet($tuple['idTrajet']);
			$i++;
		}
		return $listeTrajets;
	}

	public static function getTrajetsByPassager($idUser){
		global $mysqli;
		$dateToday = Date("Y-m-d");
		$req = $mysqli->query("SELECT * FROM trajet, (SELECT idTrajet
														FROM usertrajetpassager
														WHERE idUser = '$idUser') AS idT
								WHERE trajet.idTrajet = idT.idTrajet AND trajet.dateTrajet > '$dateToday'") or die("ERROR");
		$i=0;
		$listeTrajets = [];
		while ($tuple = $req->fetch_array()) {
			$listeTrajets[$i] = new Trajet($tuple['typeTrajet'], $tuple['villedep'], $tuple['villearr'], $tuple['prix'], $tuple['description'], $tuple['dateTrajet'], $tuple['heure'], $tuple['duree']);
			$listeTrajets[$i]->setidTrajet($tuple['idTrajet']);
			$i++;
		}
		return $listeTrajets;
	}

	public static function getTrajetByType($typeTrajet){
		global $mysqli;
		$dateToday = Date("Y-m-d");
		$date2 = "2015-06-01";
		$date3 = "2016-01-01";
		$req = $mysqli->query("SELECT * FROM trajet WHERE typeTrajet = '$typeTrajet' AND dateTrajet > '$dateToday' ORDER BY dateTrajet ASC") or die ("ERROR");
		$i = 0;
		$listeTrajet = [];
		while($tuple = $req->fetch_array()){
			$listeTrajet[$i]=$tuple;
			$i++;
		}
		return $listeTrajet;
	}

	public static function getTrajetByTypeAndVille($typeTrajet, $ville){
		global $mysqli;
		if($typeTrajet == 1){
			$req = $mysqli->query("SELECT * FROM trajet WHERE typeTrajet = '$typeTrajet' AND villearr='$ville'") or die ("ERROR");
			$i = 0;
			while($tuple = $req->fetch_array()){
				$listeTrajet[$i]=$tuple;
				$i++;
			}
		}
		else {
			$req = $mysqli->query("SELECT * FROM trajet WHERE typeTrajet = '$typeTrajet' AND villedep='$ville'") or die ("ERROR");
			$i = 0;
			while($tuple = $req->fetch_array()){
				$listeTrajet[$i]=$tuple;
				$i++;
			}
		}
		return $listeTrajet;
	}

	public static function getAllEscaleByTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM trajetescale WHERE idTrajet='$idTrajet' ORDER BY ordre ASC") or die("ERROR");
		$i = 0;
		$listeEscale = [];
		while ($tuple = $req->fetch_array()){
			$listeEscale[$i] = $tuple;
			$i++;
		}
		if($i == 0){
			return null;
		}
		else {
			return $listeEscale;
		}
	}

	public static function getPassagersNb($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM usertrajetpassager WHERE idTrajet = '$idTrajet' AND accepted='1'") or die ("ERROR");
		$i=0;
		while($tuple = $req->fetch_array()){
			$i++;
		}
		return $i;
	}

	public static function getTrajetById($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM trajet WHERE idTrajet='$idTrajet'") or die("ERROR");
		$tuple = $req->fetch_array();
		return $tuple;
	}

	public static function getCreatorByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM usertrajetcreator WHERE idTrajet = '$idTrajet'") or die ("ERROR");
		$tuple = $req->fetch_array();
		return $tuple['idUser'];
	}

	public static function getPassagerByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM usertrajetpassager WHERE idTrajet = '$idTrajet' AND accepted='1'") or die ("ERROR");
		$i=0;
		$listePassager = [];
		while($tuple = $req->fetch_array()){
			$listePassager[$i] = $tuple['idUser'];
			$i++;
		}
		if($i == 0){
			return $i;
		}
		else {
			return $listePassager;
		}
	}

	public static function getTousLesPassagersByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT *
								FROM user, (SELECT *
											FROM usertrajetpassager
												WHERE idTrajet = '$idTrajet') p
								WHERE user.id = p.idUser") or die ("ERROR");
		$i=0;
		$listePassager = [];
		while($tuple = $req->fetch_array()){
			$listePassager[$i] = new User($tuple['mail'], $tuple['password'], $tuple['pseudo']);
			$listePassager[$i]->setID($tuple['id']);
			$i++;
		}
		return $listePassager;
	}

	public static function getTousLesPassagersEnAttenteByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT *
								FROM user, (SELECT *
											FROM usertrajetpassager
												WHERE idTrajet = '$idTrajet'
												AND accepted = '0') p
								WHERE user.id = p.idUser") or die ("ERROR");
		$i=0;
		$listePassager = [];
		while($tuple = $req->fetch_array()){
			$listePassager[$i] = new User($tuple['mail'], $tuple['password'], $tuple['pseudo']);
			$listePassager[$i]->setID($tuple['id']);
			$i++;
		}
		return $listePassager;
	}

	public static function getTousLesPassagersAcceptesByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT *
								FROM user, (SELECT *
											FROM usertrajetpassager
												WHERE idTrajet = '$idTrajet'
												AND accepted = '1') p
								WHERE user.id = p.idUser") or die ("ERROR");
		$i=0;
		$listePassager = [];
		while($tuple = $req->fetch_array()){
			$listePassager[$i] = new User($tuple['mail'], $tuple['password'], $tuple['pseudo']);
			$listePassager[$i]->setID($tuple['id']);
			$i++;
		}
		return $listePassager;
	}

	public static function getFlagsByIdTrajet($idTrajet){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM trajetflag WHERE idTrajet = '$idTrajet'") or die("ERROR");
		$i = 0;
		$listeFlag = [];
		while($tuple = $req->fetch_array()){
			$listeFlag[$i] = $tuple;
			$i++;
		}
		if($i == 0){
			return $i;
		}
		else {
			return $listeFlag;
		}

	}

	public static function getAllFlags(){
		global $mysqli;
		$req = $mysqli->query("SELECT * FROM flags") or die("ERROR");
		$i = 0;
		$listeFlag = [];
		while($tuple = $req->fetch_array()){
			$listeFlag[$i] = $tuple;
			$i++;
		}
		if($i == 0){
			return $i;
		}
		else {
			return $listeFlag;
		}
	}

	public static function getTrajetByIdCreator($idUser){
		global $mysqli;
		$req = $mysqli->query("SELECT idTrajet FROM usertrajetcreator WHERE idUser = '$idUser' ") or die ("ERROR");
		$i = 0;
		$listeTrajet = [];
		while($tuple = $req->fetch_array()){
			$idTrajet = $tuple['idTrajet'];
			$reqTrajet = $mysqli->query("SELECT * FROM trajet WHERE idTrajet='$idTrajet'") or die("ERROR");
			$tupleTrajet = $reqTrajet->fetch_array();
			$listeTrajet[$i] = $tupleTrajet;
			$i++;
		}
		if($i == 0){
			return 0;
		}
		else {
			return $listeTrajet;
		}

	}

	public function getEtatByUser($idUser) {
		global $mysqli;
		$req = $mysqli->query("SELECT accepted FROM usertrajetpassager WHERE idUser = '$idUser' AND idTrajet = '$this->idTrajet'") or die ("ERROR");
		$tuple = $req->fetch_array();
		$ret = $tuple['accepted'];
		return $ret;
	}

	public function getMailCreator() {
		global $mysqli;
		$req = $mysqli->query("SELECT mail
								FROM user, (SELECT idUser
											FROM usertrajetcreator
											WHERE idTrajet = '5') createur

								WHERE user.id = createur.idUser");
		$tuple = $req->fetch_array();
		$ret = $tuple['mail'];
		return $ret;
	}

	public static function getTrajetsByTypeDepartArriveeDateHeure($type, $depart, $arrivee, $date, $heure) {
		global $mysqli;
		$req = $mysqli->query("SELECT DISTINCT idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM ((SELECT swag.idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM trajet, (SELECT DISTINCT trajets.idTrajet FROM
			(SELECT idVille FROM escale
			WHERE ville LIKE '%$arrivee%') escaleLol,
			(SELECT * FROM trajet WHERE typeTrajet = '$type') trajets,
			trajetescale
		WHERE escaleLol.idVille = trajetescale.idVille
		AND trajetescale.idTrajet = trajets.idTrajet) swag
WHERE trajet.idTrajet = swag.idTrajet)
UNION
(SELECT idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM (SELECT * FROM trajet WHERE typeTrajet = '$type') voyagesType
			WHERE villedep LIKE '%$depart%'
			AND villearr LIKE '%$arrivee%'
			AND dateTrajet LIKE '%$date%'
			AND heure LIKE '%$heure%')) inswag
WHERE (idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure) NOT IN
(SELECT idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM (SELECT * FROM trajet WHERE typeTrajet = '$type') voyagesType
			WHERE villedep NOT LIKE '%$depart%'
			OR villearr NOT LIKE '%$arrivee%'
			OR dateTrajet NOT LIKE '%$date%'
			OR heure NOT LIKE '%$heure%')") or die ("ERROR");

		/*$req = $mysqli->query("(SELECT swag.idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM trajet, (SELECT DISTINCT trajets.idTrajet FROM
			(SELECT idVille FROM escale
			WHERE ville LIKE '%$arrivee%') escaleLol,
			(SELECT * FROM trajet WHERE typeTrajet = '$type') trajets,
			trajetescale
		WHERE escaleLol.idVille = trajetescale.idVille
		AND trajetescale.idTrajet = trajets.idTrajet
		AND trajets.idTrajet IN
		(SELECT idTrajet FROM (SELECT * FROM trajet WHERE typeTrajet = '$type') voyagesType
			WHERE villedep LIKE '%$depart%'
			AND villearr LIKE '%$arrivee%'
			AND dateTrajet LIKE '%$date%'
			AND heure LIKE '%$heure%')) swag
WHERE trajet.idTrajet = swag.idTrajet)
UNION
(SELECT * FROM (SELECT * FROM trajet WHERE typeTrajet = '$type') voyagesType
			WHERE villedep LIKE '%$depart%'
			AND villearr LIKE '%$arrivee%'
			AND dateTrajet LIKE '%$date%'
			AND heure LIKE '%$heure%')") or die ("ERROR");*/


		/*$req = $mysqli->query("(SELECT trajets.idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM
			(SELECT idVille FROM escale
			WHERE ville LIKE '%$arrivee%') escaleLol,
			(SELECT * FROM trajet WHERE typeTrajet = '$type') trajets,
			trajetescale
		WHERE escaleLol.idVille = trajetescale.idVille
		AND trajetescale.idTrajet = trajets.idTrajet)
		UNION
		(SELECT * FROM (SELECT * FROM trajet WHERE typeTrajet = '$type') voyagesType
			WHERE villedep LIKE '%$depart%'
			AND villearr LIKE '%$arrivee%'
			AND dateTrajet LIKE '%$date%'
			AND heure LIKE '%$heure%') 
		ORDER BY dateTrajet ASC") or die ("ERROR");*/
		$i = 0;
		$listeTrajet = [];
		while($tuple = $req->fetch_array()){
			$listeTrajet[$i]=$tuple;
			$i++;
		}
		return $listeTrajet;		
	}


/*(SELECT swag.idTrajet, typeTrajet, villedep, villearr, prix, nbpers, duree, description, dateTrajet, heure FROM trajet, (SELECT DISTINCT trajets.idTrajet FROM
			(SELECT idVille FROM escale
			WHERE ville LIKE '%%') escaleLol,
			(SELECT * FROM trajet WHERE typeTrajet = '2') trajets,
			trajetescale
		WHERE escaleLol.idVille = trajetescale.idVille
		AND trajetescale.idTrajet = trajets.idTrajet
		AND trajets.idTrajet IN
		(SELECT idTrajet FROM (SELECT * FROM trajet WHERE typeTrajet = '2') voyagesType
			WHERE villedep LIKE '%%'
			AND villearr LIKE '%%'
			AND dateTrajet LIKE '%%'
			AND heure LIKE '%%')) swag
WHERE trajet.idTrajet = swag.idTrajet)
UNION
(SELECT * FROM (SELECT * FROM trajet WHERE typeTrajet = '2') voyagesType
			WHERE villedep LIKE '%%'
			AND villearr LIKE '%%'
			AND dateTrajet LIKE '%%'
			AND heure LIKE '%%')*/

}



 
?>