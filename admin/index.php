<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }
	
	pageHeader();
	
?>	
    
                <div class="subTable">
                	<h1>Harta FreeEx Admin</h1>
                    <div class="container" style="">
                    	
                        <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] >= 0) { 
                        		echo ('<strong>'.$_SESSION['username'].'</strong>, navigheaza folosind meniul de mai sus.<br />');


                        	 } else { ?>
	                    	Va rugam sa va autentificati folosind formularul de mai sus.  <br /><br />
                            <ul style="line-height:25px;">
                            	<li>&bull; &nbsp;<a href="forgot_pass.php?noheader=1" rel="lyteframe" rev="width: 600px; height: 320px; scrolling: auto;" title="Recuperare parola">Recuperare parola</a></li>
                            </ul>                      
                        <?php } //endif logged in ?>
                    </div>
                    
                </div>         

<?php
	pageFooter();
?>
