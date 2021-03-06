<script type="text/javascript">
$(function() {
	$('input[type="submit"]').attr('disabled','disabled');
	pwdOK = false;
	pseudoOK = false;
	$('#pseudo').keyup(function() {
	    var pseudoVal = $('#pseudo').val();
	    if(pseudoVal.length > 3) {
		    $.ajax({
	            type: 'GET',
	            url: '../modele/listeUtil.php',
	            timeout: 3000,
	            dataType:"json",
	            success: function(data) {
					if($.inArray(pseudoVal, data) == -1){
				  		$("#validePseudo").html("Identifiant valide !");
				  		pseudoOK = true;
				  		if(pwdOK == true){
				  			$('input[type="submit"]').removeAttr('disabled');
				  		}
				  		else {
				  			$('input[type="submit"]').attr('disabled','disabled');
				  		}
					}
					else {
						$("#validePseudo").html("Identifiant déjà utilisé !");
						$('input[type="submit"]').attr('disabled','disabled');
						pseudoOK = false;
					}	
		  		},
	            error: function() {
	              alert('The query doesn\'t work'); //Affiche la popup en boucle quand on veut s'inscrire avec un pseudo qui n'existe pas encore
	          	}
	      	});
      	}
      	else {
      		$("#validePseudo").html("Veuillez rentrer un identifiant de plus de 3 caratères");
      		$('input[type="submit"]').attr('disabled','disabled');
      	}
	});
	$('#password2').keyup(function() {
		var pwd = $('#password').val();
		var pwd2 = $('#password2').val();
		if(pwd != pwd2){
			$('#resultmdp').html("Veuillez rentrer deux mots de passes identiques.");
			$('input[type="submit"]').attr('disabled','disabled');
			pwdOK = false;
		}
		else {
			$('#resultmdp').html("Mots de passes identiques.");
			pwdOK = true;
			if(pseudoOK == true){
	  			$('input[type="submit"]').removeAttr('disabled');
	  		}
	  		else {
	  			$('input[type="submit"]').attr('disabled','disabled');
	  		}
		}
	});
});
</script>

<form method="post" action="../controleur/controlInscription.php">
	<fieldset> 
		<div class="submitdiv"><p><label for="pseudo">Identifiant</label> :
		<input type="text" name="pseudo" id="pseudo" required/>
		<div id="validePseudo" ></div>
		</p>
		<p><label for="password">Mot de passe</label> :
		<input type="password" name="password" id="password" required/></p>
		<p><label for="password2">Mot de passe de vérification</label> :
		<input type="password" name="password2" id="password2" required /></p>
		<div id="resultmdp"></div>
		<p><label for="email">Email</label> :
		<input type="email" name="email" id="email" required/></p>
		<p><input type="submit" id="submit" value="S'inscrire" /></p></div>
	</fieldset>
</form>