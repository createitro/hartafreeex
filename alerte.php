<?php
	require_once('includes/config.php');	

	//Process Send Document Form
	$addAlertaSubmitOK = 1;	
	$formErr = array();

	if(strlen($_POST['btn_adauga_alerte_submit'])) {


		//Sesizare Titlu
		$nume = filter_var($_POST['nume'], FILTER_SANITIZE_STRING);
		if(!strlen($nume)) { $formErr['nume'] = 'Va rugam completati numele!'; $addAlertaSubmitOK = 0;	}		
		
		//Sesizare Descriere
		$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
		if(!strlen($email)) { $formErr['email'] = 'Va rugam completati adresa de e-mail!'; $addAlertaSubmitOK = 0;	}
			
		//Sesizare Categorii
		$selected_categs = array();
		foreach ($_POST as $key => $value) {
		  //just checkboxes
		  if(begins_with($key,'cb')) { $selected_categs[] = $value;  }
	    }		
		if(!count($selected_categs)) { $formErr['selected_categs'] = 'Va rugam selectati cel putin o categorie!'; $addAlertaSubmitOK = 0; }
		
						
		//Process Insert Data
		if ($addAlertaSubmitOK):
			//check if mail exists => overwrite categs
			$_USER_CHECK = getQueryInArray("SELECT alerta_id FROM alerte WHERE alerta_email = :alerta_email LIMIT 1", array('alerta_email' => $email));
			
			if(count($_USER_CHECK)) {
				$alertaId = $_USER_CHECK[0]['alerta_id'];
				//delete old alerte categories (new ones will overwrite)
				deleteFromTable("DELETE FROM mm_categs_alerte WHERE alerta_id = :alerta_id ", array("alerta_id" => $alertaId ));
			} else {			
				//Insert Alerta							
				insertIntoTable("INSERT INTO alerte SET 
								 alerta_nume = :alerta_nume, 
								 alerta_email = :alerta_email,
								 alerta_added_at = :alerta_added_at
								 ", array(
									"alerta_nume" => $nume,
									"alerta_email" => $email,
									"alerta_added_at" => date("Y-m-d H:i:s"),		
									//"alerta_added_by_ip" => getIP()												
								 ));
				$alertaId = $config['dbConnection']->lastInsertId();
			}
						
			//insert $selected_categs in alerte
			for($i=0;$i<count($selected_categs);$i++) {
				insertIntoTable("INSERT INTO mm_categs_alerte SET alerta_id = :alerta_id, categ_id = :categ_id ", 
				array("alerta_id" => $alertaId, "categ_id" => intval($selected_categs[$i]) ));
			}//endfor $selected_categs							
			
		
										
			//succes: refresh and message
			redirect(add_querystring_var($_SERVER['REQUEST_URI'],'m','106'));			
			
			
			
			
		endif;								
	} else {
		//not submited
		$addAlertaSubmitOK = 0;
		$selected_categs = array();
	}

	$config['currentMenuSection'] = 'alerte';
	pageHeader();
?>       
        
<form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" id="add_alert_form" class="noEnterSubmit">        
<div class="container_12">
	<div class="grid_6">
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="categs">CATEGORII</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput">
            	<?php
					generateCategoriesCheckboxes($selected_categs);
				?>
            </div>
        </div>                
        
    </div>
    <div class="grid_6">
    
		<div class="formBox">
        	<div class="formBoxTitle"><label for="nume">NUME si PRENUME</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput"><input name="nume" type="text" value="<?php echo(htmlspecialchars($nume)); ?>" /></div>
        </div>        
         
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="email">E-MAIL</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput"><input name="email" type="text" value="<?php echo(htmlspecialchars($email)); ?>" /></div>
        </div>        
                               
		
        <div class="formExtraInfo">
            <?php echo(strip_tags(getPageText(4),'<br>')); ?>
        </div>
        
        <div class="btnAdaugaWrapper">
        	 <button class="btnCauta" name="btn_adauga_alerte" id="btn_adauga_alerte">TRIMITE</button>
             <input type="hidden" name="btn_adauga_alerte_submit" value="1" />
             <div class="clear"></div>
        </div>
		
        <div class="error floatRight blink" id="add_alert_errors">
        	<?php
				foreach($formErr as $err) { print $err.'<br />';	}
			?>
        </div>	
        
        
    </div>
</div>
</form>

<?php

	pageFooter();

?>
