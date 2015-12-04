/**
 * Image preloading
 */

function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function changeImages() {
	if (document.images && (preloadFlag == true)) {
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}
}

var preloadFlag = false;
function preloadImages() {
	if (document.images) {
		m_ro_prod_over = newImage("images/ro/m_prod-over.jpg");
		m_ro_claims_over = newImage("images/ro/m_claims-over.jpg");
		m_ro_about_over = newImage("images/ro/m_about-over.jpg");
		m_ro_net_over = newImage("images/ro/m_net-over.jpg");
		
		m_en_prod_over = newImage("images/en/m_prod-over.jpg");
		m_en_claims_over = newImage("images/en/m_claims-over.jpg");
		m_en_about_over = newImage("images/en/m_about-over.jpg");
		m_en_net_over = newImage("images/en/m_net-over.jpg");
		
		smb_over = newImage("images/smb_1.gif");
		ib_over = newImage("images/ib_1.gif");
		preloadFlag = true;
	}
}

function resizeTable() {
	var mainDiv = document.getElementById("mainDiv");
	var submenuTable = document.getElementById("submenuTable");
	
	if (mainDiv && submenuTable){
		var divHeight = mainDiv.offsetHeight;
		if (mainDiv.offsetHeight){ 
			 divHeight=mainDiv.offsetHeight; 
		}else if(mainDiv.style.pixelHeight){ 
			 divHeight=mainDiv.style.pixelHeight; 
		} 
		
		submenuTable.style.height = (divHeight + 26) + 'px';
	}	
}


function getSelectedRadio(buttonGroup) {
   // returns the array number of the selected radio button or -1 if no button is selected
   if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
      for (var i=0; i<buttonGroup.length; i++) {
         if (buttonGroup[i].checked) {
            return i
         }
      }
   } else {
      if (buttonGroup.checked) { return 0; } // if the one button is checked, return zero
   }
   // if we get to this point, no radio button is selected
   return -1;
} // Ends the "getSelectedRadio" function

function getSelectedRadioValue(buttonGroup) {
   // returns the value of the selected radio button or "" if no button is selected
   var i = getSelectedRadio(buttonGroup);
   if (i == -1) {
      return "";
   } else {
      if (buttonGroup[i]) { // Make sure the button group is an array (not just one button)
         return buttonGroup[i].value;
      } else { // The button group is just the one button, and it is checked
         return buttonGroup.value;
      }
   }
} // Ends the "getSelectedRadioValue" function



