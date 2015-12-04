/**
 * Image preloading
 */
 
var casco_errors = [];
//var field_errors = new Array("cnp","judet","localitate","profesia","stare_civila","permis","permis_an","nr_auto","utilizat","frecventa","nr_km","nr_accidente","categoria","tip","marca","model","nr_inmatriculare","serie_sasiu","an_fab","data_inmatriculare","cc","putere","nr_locuri","masa","dotariSupl_1","alarma","gps","nou","valoare","durata","data_inceput","riscuri","fransiza_furt");
var field_errors = new Array("cnp","judet","localitate","profesia","stare_civila","permis","permis_an","nr_auto","utilizat","frecventa","nr_km","nr_accidente","categoria","tip","marca","model","nr_inmatriculare","serie_sasiu","an_fab","cc","putere","nr_locuri","masa","dotariSupl_1","alarma","gps","nr_chei","nr_telecomenzi","nou","valoare","durata","data_inceput","riscuri","fransiza_furt");

var varRate = -1;
var varFransiza = -1;

for (var i = 0; i < field_errors.length; i++){
	casco_errors[field_errors[i]] = 0;	
}

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
		formflag_ok = newImage("images/formflag_ok.gif");
		formflag_err = newImage("images/formflag_err.gif");
		preloadFlag = true;
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

function getSelectedSelectValue(selectField) {
	return selectField.options[selectField.selectedIndex].value;	
}

function validate(what) {
	//if (what == "an_fab" || what == "data_inmatriculare"){
	//	validateAnFab();
	//	return;
	//}
	if (what == "dotariSupl_1"){
		validateDotariSupl();
		return;
	}
	
	if (what == 'nr_chei' || what == 'nr_telecomenzi' || what == 'riscuri'){
		checkRiscuri();	
	}
	
	var value;
	var whatField = document.form_1[what];
	//alert(what);
	//alert(whatField);
	
	if (!whatField){
		return;	
	}
	
	if (whatField.options != undefined){
		// select	
		value = getSelectedSelectValue(whatField);
		
		if (value == 0){
			changeImages('flag_'+what, 'images/formflag_err.gif');
			casco_errors[what] = 0;
		}else{
			changeImages('flag_'+what, 'images/formflag_ok.gif');
			casco_errors[what] = 1;
		}
		return;	
	}else if (whatField.length != undefined && whatField.length > 0){
		// radio
		value = getSelectedRadioValue(whatField);	
		
		if (value == ""){
			changeImages('flag_'+what, 'images/formflag_err.gif');
			casco_errors[what] = 0;
		}else{
			changeImages('flag_'+what, 'images/formflag_ok.gif');
			casco_errors[what] = 1;
		}
		return;	
	}else{
		value = whatField.value;
	}
	
	var pars = 'validate=1&what='+what+'&value='+value;
	
	var myAjax = new Ajax.Request('casco_new_ajax.php', {method: 'get', parameters: pars, onComplete: function(o) { handleValidate(o, what); } });
}

function handleValidate(transport, what) {
	if (transport.responseText == '1'){
		changeImages('flag_'+what, 'images/formflag_ok.gif');
		casco_errors[what] = 1;
	}else{
		changeImages('flag_'+what, 'images/formflag_err.gif');
		casco_errors[what] = 0;
	}
}

function validateAllFields() {
	for (var i = 0; i < field_errors.length; i++){
		validate(field_errors[i]);	
	}
	
	if (getSelectedRadioValue(document.form_1.permis) == 2){
		document.form_1.permis_an.disabled = 1; casco_errors['permis_an'] = 1; changeImages('flag_permis_an', 'images/formflag_ok.gif');
	}
}

function validateAllData() {
	document.form_1['calculeaza'].disabled = true;
	
	validate('dotariSupl_1');
	validate('an_fab');
	
	var firstControl = true;
	for (var i = 0; i < field_errors.length; i++){
		value = field_errors[i];
		if (casco_errors[value] != 1){
			var whatField = document.form_1[value];
			if (whatField){
				if (value != 'dotariSupl_1' && value != 'data_inmatriculare'){
					changeImages('flag_'+value, 'images/formflag_err.gif');
				}
				if (firstControl){
					if (whatField.options != undefined){
						if (!whatField.disabled){
							whatField.focus();
						}
					}else if (whatField.length != undefined && whatField.length > 0){
						whatField[0].focus();
					}else{
						if (!whatField.disabled){
							whatField.focus();
						}
					}
					firstControl = false;
				}
			}
		}	
	}
	
	
	
	document.form_1['calculeaza'].disabled = false;
	return firstControl;
}

function changeValuta(cboValuta) {
	document.getElementById('valoare_').innerHTML = valutaArray[cboValuta.value];
	for (var i = 0; i <= 4; i++){
		var anElem = document.getElementById('valoare_'+i);
		if (anElem){
			anElem.innerHTML = valutaArray[cboValuta.value];
		}
	}
	document.getElementById('valoare_total').innerHTML = valutaArray[cboValuta.value];
}

function computeSupl(txtField) {
	var theValue = parseInt(txtField.value);
	if (isNaN(theValue)){
		theValue = 0;
	}
	txtField.value = theValue;	
	
	var sum = 0;
	for (var i = 0; i <= 4; i++){
		var anElem = document.getElementById('dotariSupl_'+i);
		
		if (anElem){
			theValue = parseInt(anElem.value);
			if (isNaN(theValue)){
				theValue = 0;
			}
	
			sum += theValue;
		}
	}
	
	anElem = document.getElementById('dotariSupl_total');
	if (anElem){
		anElem.value = sum;
	}
	
	anElem = document.getElementById('dotariSupl_total2');
	if (anElem){
		anElem.value = sum;	
	}
	
	validate('dotariSupl_1');
}

function validateModel () {
	validate('model');	
}

function validateMarca () {
	validate('marca');	
}

function validateLocalitate () {
	validate('localitate');	
}

function validateAnFab() {
	var pars = 'validate=1&what=data_inmatriculare&value='+document.form_1['data_inmatriculare'].value;
	
	var myAjax = new Ajax.Request('casco_new_ajax.php', {method: 'get', parameters: pars, onComplete: 
								  	function(transport) {  
										if (transport.responseText == '1' || document.form_1['an_fab'].selectedIndex > 0){
											changeImages('flag_an_fab', 'images/formflag_ok.gif');
											casco_errors['an_fab'] = 1;
											casco_errors['data_inmatriculare'] = 1;
										}else{
											changeImages('flag_an_fab', 'images/formflag_err.gif');
											casco_errors['an_fab'] = 0;
											casco_errors['data_inmatriculare'] = 0;
										}
									} });	
}

function validateDotariSupl() {
	//var pars = 'validate=1&what=dotariSupl&value='+document.form_1['dotariSupl_total2'].value+'&value1='+document.form_1['valoare'].value+'&value2='+getSelectedSelectValue(document.form_1['an_fab'])+'&value3='+document.form_1['data_inmatriculare'].value;
	//if(document.form_1['dotariSupl_total2'] && document.form_1['valoare'] && document.form_1['an_fab']) {
		var pars = 'validate=1&what=dotariSupl&value='+document.form_1['dotariSupl_total2'].value+'&value1='+document.form_1['valoare'].value+'&value2='+getSelectedSelectValue(document.form_1['an_fab'])+'&value3=';
	//}
	
	var myAjax = new Ajax.Request('casco_new_ajax.php', {method: 'get', parameters: pars, onComplete: 
								  	function(transport) {  
										if (transport.responseText == '1'){
											changeImages('flag_dotariSupl_total', 'images/formflag_ok.gif');
											casco_errors['dotariSupl_1'] = 1;
										}else{
											changeImages('flag_dotariSupl_total', 'images/formflag_err.gif');
											casco_errors['dotariSupl_1'] = 0;
										}
									} });	
}

function resetAllData() {
	var theForm = document.form_1;
	
	for (var i = 0; i < theForm.elements.length; i++){
		whatField = theForm.elements[i];
		
		if (whatField.options != undefined){
			// select	
			whatField.selectedIndex = 0;
		}else if (whatField.length != undefined && whatField.length > 0){
			// radio
			alert(1);
			for (var j = 0; j < whatField.length; j++) {
				whatField[j].checked = false;
			}
		}else if (whatField.type == 'text'){
			whatField.value = '';
		}else if (whatField.checked){
			whatField.checked = false;
			//alert(whatField.name);
		}
	}
	
	for (var i = 1; i < 7; i++){
		inputField = eval('theForm.dotariSupl_'+i);
		if (inputField){
			inputField.value='0';	
			computeSupl(inputField);
		}	
	}
	theForm.data_inceput.value = defaultDate;
	validateAllFields();
}

function checkSuplimentare(theValue) {
	var suplimentar = document.form_1['suplimentar[]'];
	var jj = 0;
	
	for (var i = 0; i < suplimentar.length; i++){
		if (suplimentar[i].value == 1 && theValue.value == 2 && theValue.checked){
			suplimentar[i].checked = false;	
		}
		if (suplimentar[i].value == 2 && theValue.value == 1 && theValue.checked){
			suplimentar[i].checked = false;	
		}
	}
	
}

function checkRiscuri() {
	var nr_chei = document.form_1['nr_chei'].value;
	var nr_telecomenzi = document.form_1['nr_telecomenzi'].value;
	
	if (nr_chei < 2 || nr_telecomenzi < 2){
		document.form_1['riscuri'][0].checked = false;
		document.form_1['riscuri'][1].checked = true;
		document.getElementById('riscuriMessage').style.display = 'block';
		changeImages('flag_riscuri', 'images/formflag_ok.gif');
		casco_errors['riscuri'] = 1;
		hideObj('ffTD1');	
		hideObj('ffTD2');
		removeByElement(field_errors, 'fransiza_furt');
		
	}else{
		document.getElementById('riscuriMessage').style.display = 'none';	
		showObj('ffTD1');	
		showObj('ffTD2');
		addByElement(field_errors, 'fransiza_furt');

	}
	//alert(field_errors.join(" "));
}

function submitRateFransiza(id) {
	if (varRate == -1 || varFransiza == -1){
		document.getElementById('rateMessage').innerHTML = '<span class="red">Selectati intai o optiune din tabelul de mai sus!</span>';	
	}else{
		var pars = 'setRate=1&id='+id+'&rate='+varRate+'&fransiza='+varFransiza;
		
		var myAjax = new Ajax.Request('casco_new_ajax.php', {method: 'post', parameters: pars, onComplete: 
										function(transport) {  
											document.getElementById('rateMessage').innerHTML = '<span class="green">Optiunea dumneavoastra a fost inregistrata cu succes!</span>';
										} });	
	}	
}

function setRateFransiza(chk) {
	varRateFransiza = chk.value.split('_');
	varRate = varRateFransiza[0];
	varFransiza = varRateFransiza[1];
}

function removeByElement(arrayName,arrayElement) {
	for(var i=0; i<arrayName.length;i++ ) { 
		if(arrayName[i]==arrayElement)
			arrayName.splice(i,1); 
	}
}

function addByElement(arrayName,arrayElement) {
	var inArray = 0;
	for(var i=0; i<arrayName.length;i++ ) { 
		if(arrayName[i]==arrayElement) { inArray = 1; }
	}
	if(!inArray) arrayName.push(arrayElement);
}


function showObj(id){ 
	if (document.getElementById){ 
		obj = document.getElementById(id); 
		obj.style.visibility = "visible"; 
		obj.style.display = "inline"; 
	} 
} 

function hideObj(id){ 
	if (document.getElementById){ 
		obj = document.getElementById(id); 
		obj.style.visibility = "hidden"; 
		obj.style.display = "none"; 
	} 
} 