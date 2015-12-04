$(function() {

	//DISPLAY MORE SESIZARI BTN
	$('#sesizari_more').click(function() {
		$(".sesizare_group_hidden:first").slideDown().removeClass('sesizare_group_hidden');
		if( $(".sesizare_group_hidden").length == 0) { $(this).hide(); }
	});


	//CONTACT SELECT ALL (INPUTS) 
	$(".select_embed").click(function () {
	   $(this).select();
	});	  
	  
	  
  	  // Apparently click is better than change? Cuz IE?
      $('input[type="checkbox"]').change(function(e) {
      var checked = $(this).prop("checked"),
          container = $(this).parent(),
          siblings = container.siblings();
  	  
      container.find('input[type="checkbox"]').prop({
          indeterminate: false,
          checked: checked
      });
	  
	  //dropdown childen plusminus
	 var currentId = $(this).attr('id');
	 //alert(checked + ' &&  ' + $('#plusminus_content_' + currentId).css('display') );
	 if ((checked == true) && ($('#plusminus_content_' + currentId).css('display') == 'none')) {
			$('#plusminus_' + currentId).removeClass("plus"); 
			$('#plusminus_' + currentId).addClass("minus");
			$('#plusminus_content_' + currentId).slideToggle();			 
	 }
	  
  
      function checkSiblings(el) {
          var parent = el.parent().parent(),
              all = true;
  
          el.siblings().each(function() {
              return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
          });
  
          if (all && checked) {
              parent.children('input[type="checkbox"]').prop({
                  indeterminate: false,
                  checked: checked
              });
              checkSiblings(parent);
          } else if (all && !checked) {
              parent.children('input[type="checkbox"]').prop("checked", checked);
              parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
              checkSiblings(parent);
          } else {
              el.parents("li").children('input[type="checkbox"]').prop({
                  indeterminate: true,
                  checked: false
              });
          }
        }
    
        checkSiblings(container);
		
      });
	  
	  $("input.indeterminate").prop("indeterminate", true);
	
	
	
	/**** COMMENTS TABS ****/
	$('.commentsTab').click(function() {
		var fieldId = $(this).attr('id').replace('tab_','');
		//each tab OFF
		$('.commentsTab').each(function( index ) {
			$(this).removeClass();
			var fieldId = $(this).attr('id').replace('tab_','');
			$(this).addClass("commentsTab" );
			$(this).addClass( fieldId + "_OFF" );
		});
		//set current ON
		$(this).removeClass( fieldId + "_OFF" );
		$(this).addClass( fieldId + "_ON" );
		
		
		//content
		$('.commentsDiv').hide();
		$('#'+fieldId).show();

	});
	  
	/**** PLUS MINUS ZOOM ***************************************************************************/
	$('.plusminus').click(function() {
		var fieldId = $(this).attr('id').replace('plusminus_','');
		if ($('#plusminus_content_' + fieldId).css('display') == 'none') {
			$(this).removeClass("plus"); 
			$(this).addClass("minus");
			$('#plusminus_content_' + fieldId).slideToggle();			
		} else {
			$(this).removeClass("minus"); 
			$(this).addClass("plus");
			$('#plusminus_content_' + fieldId).slideToggle();			
		}

	});
	
	
	/*** LOGO CLICK ***/
	$("#logo").click(function() { window.location = './'; } );
	
	//NO ENTER SUBMIT
	$('.noEnterSubmit').keypress(function(e){
		if ( e.which == 13 ) return false;
		//if ( e.which == 13 ) e.preventDefault();
	});	
	
	//ADD LINK INPUT
	$('#add_link').click(function(e){
		var id = parseInt($("input.link_sursa:last").attr('id').replace('link_sursa_',''));
		var $clone = $("input.link_sursa:last").clone().insertAfter("input.link_sursa:last");;
		$clone.attr('id', 'link_sursa_' + parseInt(id + 1));
		$clone.val('');	
	});	


	//ADD FILE INPUT
	$('#add_file_input').click(function(e){
		var id = parseInt($("input.file_input:last").attr('id').replace('file_input_',''));
		var $clone = $("input.file_input:last").clone().insertAfter("input.file_input:last");;
		$clone.attr('id', 'file_input_' + parseInt(id + 1));
		$clone.val('');	
		
	});	
	
	//ADD VIDEO EMBEDS
	$('#add_embed').click(function(e){
		var id = parseInt($("input.embed_sursa:last").attr('id').replace('embed_sursa_',''));
		var $clone = $("input.embed_sursa:last").clone().insertAfter("input.embed_sursa:last");;
		$clone.attr('id', 'embed_sursa_' + parseInt(id + 1));
		$clone.val('');		
	});		

	
	//LOCATION SEARCH
	$('#search_location_btn').click(function(e){
		
		var queryString = $('#location_search').val();
		submitSearchLocation(queryString);	
	});
	//ENTER ON INPUT location_search
	$('#location_search').keypress(function(e){
		if ( e.which == 13 ) {
			var queryString = $(this).val();
			submitSearchLocation(queryString);				
		}
	});			
		  
	  
	  
	//SUBMIT ADAUGA SESIZARE
	$("#btn_adauga").click(function(event) {
		//return true;
		var submitOK = 1;

		var titlu = $('#titlu');
		if(!titlu.val().length) { titlu.addClass('error'); submitOK = 0; event.preventDefault(); alert('Completati titlul!'); return false; } else { titlu.removeClass('error');  }	
		
		var descriere = $('#descriere');
		if(!descriere.val().length) { descriere.addClass('error'); submitOK = 0; event.preventDefault(); alert('Completati o descriere!'); return false; } else { descriere.removeClass('error');  }			
		
		var data_ora = $('#data_ora');
		if(!data_ora.val().length) { data_ora.addClass('error'); submitOK = 0; event.preventDefault(); alert('Completati data si ora!'); return false; } else { data_ora.removeClass('error');  }					

		//check if file selected
		/*
		if($('#file_input').val().length) {
			var filename = $('#file_input').val().split('\\').pop();
			var ext = filename.split('.').pop().toLowerCase();
			if($.inArray(ext, ['jpg','gif','png']) == -1) { submitOK = 0; event.preventDefault(); alert('Alegeti un fisier de tip imagine!'); return false; }		
		}
		*/
		
		
		if((coord_lon == 0) || (coord_lat == 0)) { submitOK = 0; event.preventDefault(); alert('Alegeti localizarea!'); return false; }
		
		//check categories
		var atLeastOneIsChecked = $('input[type="checkbox"]:checked').length > 0;
		if(atLeastOneIsChecked == false) { submitOK = 0; event.preventDefault(); alert('Alegeti cel putin o categorie!'); return false; }
		
		if(submitOK == 1) {
			$("#coord_lon").val(coord_lon);
			$("#coord_lat").val(coord_lat);
			
			$(this).attr('disabled','true');
			$(this).attr('value','Asteptati...');
			$("form#sendForm").submit();
			return true;
		} else {
			event.preventDefault();
			return false;
		}
		
	});	  
	  
	
	
	//CLICK TRIMITE ALERTA
	$("#btn_adauga_alerte").click(function(event) {
			$("form#add_alert_form").submit();		
	});
	
	//SUBMIT ADAUGA ALERTA
	$("form#add_alert_form").submit(function( event ) {
		var submitOK = 1;
		var obj, obj2;
		obj = $("#add_alert_form input[name='nume']");
		if(obj.val().length < 3) { obj.addClass('error'); submitOK = 0; } else { obj.removeClass('error'); }
		
		obj = $("#add_alert_form input[name='email']");
		if(!isValidEmailAddress(obj.val())) { obj.addClass('error'); submitOK = 0; } else { obj.removeClass('error'); }				
		
		if($('.formBoxInput ul').find('input[type=checkbox]:checked').length == 0) {
			$('#add_alert_errors').html('Va rugam selectati cel putin o categorie!');
			submitOK = 0;
		} else {
			$('#add_alert_errors').html('');
		}
		
		if(submitOK == 1) {
			$(this).attr('disabled','true');
			$(this).attr('value','Asteptati...');			
			return true;
		} else {
			return false;
		}		
		
	});
		  
	  
}); /* End JQuery */

function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function bubbleSortMarkersLon(A) {
	var n = A.length;
	while (n > 0) {
		var newn = 0;
			for(var i=0; i < (A.length - 1); i++) { 
				if(A[i]['LONGITUDE'] < A[i+1]['LONGITUDE']) {
										
					var LONGITUDE = A[i]['LONGITUDE'];
					var LATITUDE = A[i]['LATITUDE'];
					var NAME = A[i]['NAME'];
					var ID = A[i]['ID'];	
					A[i]['LONGITUDE'] = A[i+1]['LONGITUDE'];
					A[i]['LATITUDE'] = A[i+1]['LATITUDE'];
					A[i]['NAME'] = A[i+1]['NAME'];
					A[i]['ID'] = A[i+1]['ID'];
					A[i+1]['LONGITUDE'] = LONGITUDE;
					A[i+1]['LATITUDE'] = LATITUDE;
					A[i+1]['NAME'] = NAME;
					A[i+1]['ID'] = ID;					
									
					newn = i;
				}//endif
			} //endfor
		n = newn;
	}
}


/*
function ts(cb) {
  if (cb.readOnly) cb.checked=cb.readOnly=false;
  else if (!cb.checked) cb.readOnly=cb.indeterminate=true;
}
*/

