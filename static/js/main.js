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
/*
function validEmail(email) {
	var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return regex.test(email);
}
*/
/**
* Checks if the phone number is valid
*/
/*
function validPhone(phone){
	//regex for all valid phone number formats
	var regexNumeric = /^\d{10}$/;
	var regexInternational = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
	var regexPlusInternational = /^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;
	
	//if the phone number does not match any of the formats, then it is invalid.
	if( !(regexNumeric.test(phone)) && !(regexInternational.test(phone)) && !(regexPlusInternational.test(phone))){
		return false;
	}
	
	// if the phone number satisfies any of the formats, then it is valid.
	return true;
}
*/

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

function validateSignup(f, names){
	var names = names.split("||");	
	var len = names.length;
	var passwordName = '';
	var password0 = '';
	var password1 = '';
	for (var i = 0; i < len; i++){
		var labelName = names[i].split("::");
		var label = labelName[0]; //A label to let us identify the field name.
		var name = labelName[1]; //HTML name attribute value.
		
		//ensure all have values
		//var valu = f.name.value;
		if(f.name.value.trim() == ''){
			f.name.focus();
			return false;
		}
		/*
		//ensure valid names
		if((label == 'firstname') || (label == 'lastname')){
			var regexLetters = /^[A-Za-z]+$/;
			if(regexLetters.test(f.name.value)){
				f.name.focus();
				return false;
			}
		}
		
		//ensure valid email addresses
		if(label == 'email'){
			if(!validEmail(f.name.value)){
				f.name.focus();
				return false;
			}
		}
		
		//ensure valid telephone numbers
		if(label == 'telephone'){
			if(!validPhone(f.name.value)){
				f.name.focus();
				return false;
			}
		}
		*/
		//access password input values
		if(label == 'password'){
			password0 = f.name.value;
			passwordName = name;
		}
		if(label == 'passwordc'){
			password1 = f.name.value;
		}
	}
	
	/*
	//ensure strong passwords
	var regexAlphaNumeric = /^[0-9a-zA-Z]+$/;
	if(password0 != password1){
		f.passwordName.focus();
		return false;
	} else if(password0.length < 8){
		f.passwordName.focus();
		return false;
	} else if(!regexAlphaNumeric.test(password0)){
		f.passwordName.focus();
		return false;
	}
	*/
	//submit the form if we got this far and eveything seems fine.
	return false;
}

function validateLogin(e, f, names){
	//implementation of login validation goes in here should the need arise
	return true;
}

function validateRide(e, f, names){
	var names = names.split("||");	
	var len = names.length;
	
	for(var i = 0; i < len; i++){
		var labelName = names[i].split("::");
		var label = labelName[0];
		var name = labelName[1];
		
		//ensure all fields have values.
		if(f.name.value.trim() == ""){
			f.name.focus();
			return false;
		}
		
		//ensure valid length for origin and destination
		if((label == 'origin') || (label == 'destination')){
			if(f.name.value.length < 4){
				f.name.focus();
				return false;
			}
		}
	}
	
	//if we've come this far and everything seems okay, then submit the form
	return true;
}