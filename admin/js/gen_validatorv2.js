/*
  -------------------------------------------------------------------------
	                    JavaScript Form Validator 
                                Version 2.0.2
	Copyright 2003 JavaScript-coder.com. All rights reserved.
	You use this script in your Web pages, provided these opening credit
    lines are kept intact.
	The Form validation script is distributed free from JavaScript-Coder.com

	You may please add a link to JavaScript-Coder.com, 
	making it easy for others to find this script.
	Checkout the Give a link and Get a link page:
	http://www.javascript-coder.com/links/how-to-link.php

    You may not reprint or redistribute this code without permission from 
    JavaScript-Coder.com.
	
	JavaScript Coder
	It precisely codes what you imagine!
	Grab your copy here:
		http://www.javascript-coder.com/
    -------------------------------------------------------------------------  
*/
function Validator(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("BUG: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj.onsubmit=form_submit_handler;
	this.addValidation = add_validation;
	this.setAddnlValidationFunction=set_addnl_vfunction;
	this.clearAllValidations = clear_all_validations;
}
function set_addnl_vfunction(functionname)
{
  this.formobj.addnlvalidation = functionname;
}
function clear_all_validations()
{
	for(var itr=0;itr < this.formobj.elements.length;itr++)
	{
		this.formobj.elements[itr].validationset = null;
	}
}
function form_submit_handler()
{
	// first, disable all buttons
	var canSubmitForm = true;
	for(var itr=0;itr < this.elements.length;itr++){
		if (this.elements[itr].type && (this.elements[itr].type == 'submit' || this.elements[itr].type == 'button' || this.elements[itr].type == 'reset')){
			this.elements[itr].disabled = true;
		}
	}
	for(var itr=0;itr < this.elements.length;itr++)
	{
		if(this.elements[itr].validationset &&
	   !this.elements[itr].validationset.validate())
		{
		  canSubmitForm = false;
		  break;
		}
	}
	if(canSubmitForm && this.addnlvalidation)
	{
	  str =" var ret = "+this.addnlvalidation+"()";
	  eval(str);
    	canSubmitForm = ret;
	}
	
	// if the form cannot be submitted, enable buttons and reverse textarea content 
	if (!canSubmitForm){
		for(var itr=0;itr < this.elements.length;itr++){
			if (this.elements[itr].type && (this.elements[itr].type == 'submit' || this.elements[itr].type == 'button' || this.elements[itr].type == 'reset')){
				this.elements[itr].disabled = false;
			}else if (this.elements[itr].type.rows){
				this.elements[itr].value = unescape(this.elements[itr].value);
			}
		}	
	}
	
	return canSubmitForm;
}
function add_validation(itemname,descriptor,errstr)
{
  if(!this.formobj)
	{
	  alert("BUG: the form object is not set properly");
		return;
	}//if
	var itemobj = this.formobj[itemname];
  if(!itemobj)
	{
	  alert("BUG: Could not get the input object named: "+itemname);
		return;
	}
	if(!itemobj.validationset)
	{
	  itemobj.validationset = new ValidationSet(itemobj);
	}
  itemobj.validationset.add(descriptor,errstr);
}
function ValidationDesc(inputitem,desc,error)
{
  this.desc=desc;
	this.error=error;
	this.itemobj = inputitem;
	this.validate=vdesc_validate;
}
function vdesc_validate()
{
 if(!V2validateData(this.desc,this.itemobj,this.error))
 {
    this.itemobj.focus();
		return false;
 }
 return true;
}
function ValidationSet(inputitem)
{
    this.vSet=new Array();
	this.add= add_validationdesc;
	this.validate= vset_validate;
	this.itemobj = inputitem;
}
function add_validationdesc(desc,error)
{
  this.vSet[this.vSet.length]= 
	  new ValidationDesc(this.itemobj,desc,error);
}
function vset_validate()
{
   for(var itr=0;itr<this.vSet.length;itr++)
	 {
	   if(!this.vSet[itr].validate())
		 {
		   return false;
		 }
	 }
	 return true;
}
function validateEmailv2(email)
{
// a very simple email validation checking. 
// you can add more complex email checking if it helps 
    if(email.length <= 0)
	{
	  return true;
	}
    var splitted = email.match("^(.+)@(.+)$");
    if(splitted == null) return false;
    if(splitted[1] != null )
    {
      var regexp_user=/^\"?[\w-_\.]*\"?$/;
      if(splitted[1].match(regexp_user) == null) return false;
    }
    if(splitted[2] != null)
    {
      var regexp_domain=/^[\w-\.]*\.[A-Za-z]{2,4}$/;
      if(splitted[2].match(regexp_domain) == null) 
      {
	    var regexp_ip =/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
	    if(splitted[2].match(regexp_ip) == null) return false;
      }// if
      return true;
    }
return false;
}
function V2validateData(strValidateStr,objValue,strError) 
{ 
    var epos = strValidateStr.search("="); 
    var  command  = ""; 
    var  cmdvalue = ""; 
    if(epos >= 0) 
    { 
     command  = strValidateStr.substring(0,epos); 
     cmdvalue = strValidateStr.substr(epos+1); 
    } 
    else 
    { 
     command = strValidateStr; 
    }
	
	if (eval(objValue.disabled)){
		return true;
	}
    switch(command) 
    { 
        case "req": 
        case "required": 
         { 
           if(!eval(objValue.disabled) && eval(objValue.value.length) == 0) 
           { 
              if(!strError || strError.length ==0) 
              { 
                strError = objValue.name + " : Camp obligatoriu"; 
              }//if 
              alert(strError); 
              return false; 
           }//if 
           break;             
         }//case required 
        case "maxlength": 
        case "maxlen": 
          { 
             if(eval(objValue.value.length) >  eval(cmdvalue)) 
             { 
               if(!strError || strError.length ==0) 
               { 
                 strError = objValue.name + " : "+cmdvalue+" este numarul maxim de caractere permis. "; 
               }//if 
               alert(strError + "\n[Lungimea actuala = " + objValue.value.length + " ]"); 
               return false; 
             }//if 
             break; 
          }//case maxlen 
        case "minlength": 
        case "minlen": 
           { 
             if(eval(objValue.value.length) <  eval(cmdvalue)) 
             { 
               if(!strError || strError.length ==0) 
               { 
                 strError = objValue.name + " : " + cmdvalue + " este numarul minim de carctere permis.  "; 
               }//if               
               alert(strError + "\n[Lungimea actuala = " + objValue.value.length + " ]"); 
               return false;                 
             }//if 
             break; 
            }//case minlen 
		// required min length means	
        case "reqminlen": 
           { 
             if(eval(objValue.value.length) > 0 && eval(objValue.value.length) <  eval(cmdvalue)) 
             { 
               if(!strError || strError.length ==0) 
               { 
                 strError = objValue.name + " : " + cmdvalue + " este numarul minim de carctere permis.  "; 
               }//if               
               alert(strError + "\n[Lungimea actuala = " + objValue.value.length + " ]"); 
               return false;                 
             }//if 
             break; 
            }//case reqminlen 			
        case "alnum": 
        case "alphanumeric": 
           { 
              var charpos = objValue.value.search("[^A-Za-z0-9]"); 
              if(objValue.value.length > 0 &&  charpos >= 0) 
              { 
               if(!strError || strError.length ==0) 
                { 
                  strError = objValue.name+": Sunt permise numai caracterele alfanumerice. "; 
                }//if 
                alert(strError + "\n [Eroare la caracterul " + eval(charpos+1)+"]"); 
                return false; 
              }//if 
              break; 
           }//case alphanumeric 
        case "num": 
        case "numeric": 
           { 
              var charpos = objValue.value.search("[^0-9]"); 
              if(objValue.value.length > 0 &&  charpos >= 0) 
              { 
                if(!strError || strError.length ==0) 
                { 
                  strError = objValue.name+": Sunt permise numai cifre. "; 
                }//if               
                alert(strError + "\n [Eroare la caracterul " + eval(charpos+1)+"]"); 
                return false; 
              }//if 
              break;               
           }//numeric 
		 case "decimal": 
           { 
              if(!validateDecimal(objValue.value)) 
              { 
                if(!strError || strError.length ==0) 
                { 
                  strError = objValue.name+": Formatul corect este nn,nn. "; 
                }
				alert(strError);
                return false; 
              }//if 
              break;               
           }//decimal 		   
        case "alphabetic": 
        case "alpha": 
           { 
              var charpos = objValue.value.search("[^A-Za-z]"); 
              if(objValue.value.length > 0 &&  charpos >= 0) 
              { 
                  if(!strError || strError.length ==0) 
                { 
                  strError = objValue.name+": Sunt permise numai litere (un singur cuvant). "; 
                }//if                             
                alert(strError + "\n [Error character position " + eval(charpos+1)+"]"); 
                return false; 
              }//if 
              break; 
           }//alpha 
		case "alnumhyphen":
			{
              var charpos = objValue.value.search("[^A-Za-z0-9\-_]"); 
              if(objValue.value.length > 0 &&  charpos >= 0) 
              { 
                  if(!strError || strError.length ==0) 
                { 
                  strError = objValue.name+": Caracterele permise A-Z,a-z,0-9,- si _"; 
                }//if                             
                alert(strError + "\n [Error character position " + eval(charpos+1)+"]"); 
                return false; 
              }//if 			
			break;
			}
        case "email": 
          { 
               if(!validateEmailv2(objValue.value)) 
               { 
                 if(!strError || strError.length ==0) 
                 { 
                    strError = objValue.name+": Introduceti o adresa corecta de email. "; 
                 }//if                                               
                 alert(strError); 
                 return false; 
               }//if 
           break; 
          }//case email 
        case "lt": 
        case "lessthan": 
         { 
            if(isNaN(objValue.value)) 
            { 
              alert(objValue.name+": Trebuie sa fie un numar "); 
              return false; 
            }//if 
            if(eval(objValue.value) >=  eval(cmdvalue)) 
            { 
              if(!strError || strError.length ==0) 
              { 
                strError = objValue.name + " : valoarea trebuie sa fie mai mica decat "+ cmdvalue; 
              }//if               
              alert(strError); 
              return false;                 
             }//if             
            break; 
         }//case lessthan 
        case "gt": 
        case "greaterthan": 
         { 
            if(isNaN(objValue.value)) 
            { 
              alert(objValue.name+": Trebuie sa fie un numar "); 
              return false; 
            }//if 
             if(eval(objValue.value) <=  eval(cmdvalue)) 
             { 
               if(!strError || strError.length ==0) 
               { 
                 strError = objValue.name + " : valoarea trebuie sa fie mai mare decat "+ cmdvalue; 
               }//if               
               alert(strError); 
               return false;                 
             }//if             
            break; 
         }//case greaterthan 
        case "regexp": 
         { 
		 	if(objValue.value.length > 0)
			{
	            if(!objValue.value.match(cmdvalue)) 
	            { 
	              if(!strError || strError.length ==0) 
	              { 
	                strError = objValue.name+": Caractere invalide au fost gasite "; 
	              }//if                                                               
	              alert(strError); 
	              return false;                   
	            }//if 
			}
           break; 
         }//case regexp 
        case "dontselect": 
         { 
            if(objValue.selectedIndex == null) 
            { 
              alert("BUG: dontselect command for non-select Item"); 
              return false; 
            } 
            if(objValue.selectedIndex == eval(cmdvalue)) 
            { 
             if(!strError || strError.length ==0) 
              { 
              strError = objValue.name+": Va rugam selectati o optiune "; 
              }//if                                                               
              alert(strError); 
              return false;                                   
             } 
             break; 
         }//case dontselect 
		 
		// added "pass" verification type: checks if the password is re-typed correctly
		// the rule is the following (e.g): addValidation("txtUserPassword","pass=document.frmControls.txtUserPassword2", "errorString");
		case "pass":
		{
			if (!objValue.disabled){
				if (!(objValue.value == eval(cmdvalue).value)){
					if(!strError || strError.length ==0) { 
						strError = "Va rugam sa introduceti aceeasi parola in ambele campuri!"; 
					}//if                                                               
					alert(strError); 
					return false;
				}
			}
			break;
		}// case pass
		// added "escape" verification type: escapes the text from a TEXTAREA just before submitting a form
		// the rule is the following (e.g): addValidation("txtAddress","escape","");
		case "escape":
		{
			if (!objValue.disabled){
				objValue.value = escape(objValue.value);
			}
			break;
		}// case escape
		// added "date" verification type: check for a date in the following format dd/mm/yyyy
		// the rule is the following (e.g): addValidation("txtHireDate","date","You must provide a valid date!");
		case "date":
		{
			if (!objValue.disabled){
				tfValue = objValue.value;
				
				if (!validateDate(tfValue)){
					if(!strError || strError.length ==0) { 
						strError = "Va rugam sa introduceti o data valida!"; 
					}//if                                                               
					alert(strError); 
					return false;
				}
			}
			break;
		}// case date
		// added "notin" verification type: checks if the value of the given textfield does not appear in an array of strings given as parameter
		// the rule is the following (e.g): addValidation("txtUserName","notin=allUserNames", "errorString");
		// used ONLY in maint modules (selectedRow)
		case "notin":
		{
			if (!objValue.disabled){
				var theArray = eval(cmdvalue);
				if (theArray){
					if (selectedRow < 0){
						// ADD mode
						for (k = 0; k < theArray.length; k++){
							if (objValue.value == theArray[k][1]){
								if(!strError || strError.length ==0) { 
									strError = "Valoarea mai exista odata!"; 
								}//if                                                               
								alert(strError); 
								return false;
							}
						}
					}else{
						// EDIT mode
						for (k = 0; k < theArray.length; k++){
							if (theArray[k][0] != reportData[selectedRow - 1][0] && objValue.value == theArray[k][1]){
								if(!strError || strError.length ==0) { 
									strError = "Valoarea mai exista odata!"; 
								}//if                                                               
								alert(strError); 
								return false;
							}
						}						
					}
				}
			}
			break;
		}// case notin
    }//switch 
    return true; 
}
/*
	Copyright 2003 JavaScript-coder.com. All rights reserved.
*/