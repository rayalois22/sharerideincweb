/**
* The main JavaScript file.
* Used for the clock and input validation.
*/

$("document").ready(function(){
	Clock.startTime();//display the clock
	$("#clock").css({});
});

function toggle(t){
	$("t + input").checked = !t.checked;
}

/**
* Checks if an email address is valid
*/
function validateEmailAddress(emailAddress) {
	//TODO: Block 1 character domain names because they are reserved by the Internet Registry
	var regexEmailAddress = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return regexEmailAddress.test(emailAddress);
}

/**
* Checks if the phone number is valid
*/
function validateTelephone(telephone){
	//a phone number must match at least one of the 3 formats below
	
	//can be numeric
	var regexNumeric = /^\d{10}$/;
	//can be an international number
	var regexInternational = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
	//can have country code
	var regexPlusInternational = /^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;
	var regexPlusInternationalStrippedChars = /^\+[1-9]{1}[0-9]{3,14}$/;
	
	//if the phone number does not match any of the formats, then it is invalid.
	if( !(regexNumeric.test(telephone)) && !(regexInternational.test(telephone)) && !(regexPlusInternational.test(telephone)) && !(regexPlusInternationalStrippedChars.test(telephone))){
		return false;
	}
	
	// if the phone number satisfies any of the formats above, then it is valid.
	return true;
}

/**
* Checks if the provided value is a name
*/
function validateName(name){
	var regexLetters = /^[A-Za-z]+$/;
	
	if(!regexLetters.test(name)){
		return false;
	}
	if(name.length < 3){
		return false;
	}
	return true;
}

/**
* Checks for valid password
*/
function validatePassword(pass){
	var regexAlphaNumeric = /^[0-9a-zA-Z]+$/;
	// A very strong password must
			//Contain atleast one uppercase letter
			//contain at least one lowercase letter
			//contain atleast one number or special character
			//be at least 8 characters long
	var regexVeryStrongPassword = /(?=^.{8,}$)((?=.*\d)|(?=.*W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
	// A strong password must
			//contain at least one lowercase letter
			//contain atleast one number or special character
			//be at least 8 characters long
			//TODO: Correct regexStrongPassword
	var regexStrongPassword = /(?=^.{8,}$)((?=.*\d)|(?=.*W+))(?![.\n])(?=.*[a-z]).*$/;
	
	if(!regexAlphaNumeric.test(pass) && !regexStrongPassword.test(pass) && !regexVeryStrongPassword.test(pass)){
		return false;
	}
	if(pass.length < 8){
		return false;
	}
	if(pass.length > 40){
		return false;
	}
	return true;
}

/**
* Autofills selected vehicle details into the form.
*/
function vehicleSelector(sel){
	var i = sel.selectedIndex;
	var vehicle = sel.options[i].value;
	var vehicle = vehicle.split("||");	
	document.getElementById("rvreg").value = vehicle[0];
	document.getElementById("rvmod").value = vehicle[1];
	document.getElementById("rvcap").value = vehicle[2];	
}

function notifyE(id, message){	
	var notify = document.getElementById(id);
	notify.textContent = message;
	notify.className = 'alert alert-warning';
	notify.style.display = 'block';
}
function notifyS(id, message){
	var notify = document.getElementById(id);
	notify.textContent = message;
	notify.className = 'alert alert-warning form-control';
	notify.style.display = 'block';
}
function notifyReset(id){
	var notify = document.getElementById(id);
	notify.textContent = '';
	notify.className = '';
	notify.style.display = 'none';
}

/**
* Checks for valid signup details.
*
* Accepts
*@param e event just incase it is needed in future, for example for event.preventDefault
*@param f form element
*@param names a string containing all input labels and names
*/
function validateSignup(e, f, names){
	//disable the default behaviour of the form so that we can decide whether or not to submit it
	//e.preventDefault();
	//notify id
	var nid = "notifySignup";
		
	var names = names.split("||");	
	var len = names.length;
	var passwordName = '';
	var password0 = '';
	var password1 = '';
	for (var i = 0; i < len; i++){
		var labelName = names[i].split("::");
		var label = labelName[0]; //A label to let us identify the field name.
		var name = labelName[1]; //HTML name attribute value.
		var el = document.getElementsByName(name)[0];
		var elementValue = el.value.trim();
		
		//resets any previous notification
		notifyReset(nid);
		
		//If any of the inputs is empty, do not submit the form to the server
		if(elementValue == ''){
			notifyE(nid, label + ' cannot be empty!');
			return false;
		}
		
		if(label == 'First name'){
			//validate firstname
			if(!validateName(elementValue)){
				notifyE(nid, label + ' must be a valid name!');
				return false;
			}
		}
		if(label == 'Last name'){
			//validate lastname
			if(!validateName(elementValue)){
				notifyE(nid, label + ' must be a valid name!');
				return false;
			}
		}
		if(label == 'Email address'){
			//validate email address
			if(!validateEmailAddress(elementValue)){
				notifyE(nid, label + ' must be a valid email address!');
				return false;
			}
		}
		if(label == 'Telephone'){
			//validate telephone
			if(!validateTelephone(elementValue)){
				notifyE(nid, label + ' must be a valid phone number!');
				return false;
			}
		}
		if(label == 'Enter password'){
			//update password
			password0 = elementValue;
		}
		if(label == 'Confirm password'){
			//update passwordc
			password1 = elementValue;
		}
	}
	//ensure passwords match
	if(password0 != password1){
		notifyE(nid, 'Passwords must match!');
		return false;
	}
	//ensure strong passwords
	if(!validatePassword(password0)){
		notifyE(nid, 'A valid password must be at least 8 characters long and be alphanumeric');
		return false;
	}
	
	//submit the form if we got this far and eveything seems fine.
	return true;
} // >>>>END OF SignUp form validation.

/**
* validates the input form.
*/
function validateLogin(e, f, names){
	//implementation of login validation goes in here should the need arise
	return true;
}

/**
* Checks for valid vehicle registration numbers
* TODO: Use a more robust regex to ensure correct kenyan formats.
*/
function validateVehicleRegNumber(regnumber){
	var regexAlphaNumericSpaced = /([\w ]+)/;
	if(!regexAlphaNumericSpaced.test(regnumber)){
		return false;
	}
	if(regnumber.length > 8){
		return false;
	}
	return true;
}

/**
*checks for correct vehicle capacity
* TODO: Allow word representation of integers.
*/
function validateVehicleCapacity(capacity){
	// some useful regex for integers
	regexNonLeadingZeroInt = /^([+-]?[1-9]\d*|0)$/;
	regexNonZeroNonLeadingZeroInt = /^[-+]?[1-9]\d*$/;
	regexPositiveInt = /^\d+$/;
	regexNonZeroNonLeadingZeroPositiveInt = /^[+]?[1-9]\d*$/;
	
	if(!regexNonZeroNonLeadingZeroPositiveInt.test(capacity)){
		return false;
	}
	return true;
}

/**
* Checks for valid ride details.
*/
function validateRide(e, f, names){
	//prevent default form submission behaviour
	//e.preventDefault();
	
	var nid = "notifyRide";
	
	var names = names.split("||");	
	var len = names.length;
	
	for(var i = 0; i < len; i++){
		var labelName = names[i].split("::");
		var label = labelName[0]; //A label to let us identify the field name.
		var name = labelName[1]; //HTML name attribute value.
		var el = document.getElementsByName(name)[0]; //HTML element
		var elementValue = el.value.trim();
		
		//resets any previous notification
		notifyReset(nid);
		
		if(elementValue != ''){
			if(label == 'Origin'){
				//origin must be at least 3 digits long
				if(elementValue.length < 3){
					notifyE(nid, label + ' invalid!');
					return false;
				}	
			}
			if(label == 'Destination'){
				//destination must be at least 3 digits long
				if(elementValue.length < 3){
					notifyE(nid, label + ' invalid!');
					return false;
				}				
			}
			if(label == 'Number plate'){
				if(!validateVehicleRegNumber(elementValue)){
					notifyE(nid, label + ' is not valid');
					return false;
				}
			}
			if(label == 'Model'){
				//model must be at least 3 digits long
				if(elementValue.length < 3){
					notifyE(nid, label + ' invalid!');
					return false;
				}	
			}
			if(label == 'Capacity'){
				//capacity must be a non-zero positive integer with no leading zero
				if(!validateVehicleCapacity(elementValue)){
					notifyE(nid, label + ' invalid!');
					return false;
				}
			}
		} else {
			//empty field not allowed.
			notifyE(nid, label + ' cannot be empty!');
			return false;
		}
	}
	//if we've come this far and everything seems okay, then submit the form
	return true;
} // END OF RIDE DETAIL VALIDATION.