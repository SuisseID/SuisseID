<?php 

require 'includes/SuisseID/lib/SuisseID.php';
require 'includes/SuisseID/lib/XMLToArrayParser.php';
include 'includes/SuisseID/conf/Attributes.php';

$CONF['suisseid_error_log'] = '/pjlog';

$CONF['loginURL'] 	 		= 'http://www.yourshop.com/login.php';
$CONF['responseURL'] 		= 'https://www.yourshop.com/login.php';
$CONF['disclaimerURL'] 		= 'http://www.yourshop.com/conditions.php';
$CONF['samlRedirectorURL'] 	= 'http://www.yourshop.com/create_account.php';
$CONF['mediaURL']			= '/includes/SuisseID/media/';


$CONF['userLanguage'] = 'de';

$CONF['logErrors']  = true;
$CONF['showErrors'] = true;


$CONF['defaultRequest'][] = $attributes['givenname'];
$CONF['defaultRequest'][] = $attributes['surname'];
$CONF['defaultRequest'][] = $attributes['gender']; 

$CONF['createCustomer'][] = $attributes['givenname'];
$CONF['createCustomer'][] = $attributes['surname'];
$CONF['createCustomer'][] = $attributes['gender']; 
$CONF['createCustomer'][] = $attributes['dateofbirth']; 


//all idp's
$CONF['idp']['swisssign']['samlEndpoint'] 	= 'https://idp.swisssign.net/suisseid/eidp/';
$CONF['idp']['swisssign']['name'] 			= 'Post SwissSign';

$CONF['idp']['quovadis']['samlEndpoint']  	= 'https://idp.quovadis.ch/suisseid/eidp/';
$CONF['idp']['quovadis']['name']  			= 'QuoVadis';


//Texts and translations
$CONF['text']['de']['sendButton']		= "Absenden";
$CONF['text']['en']['sendButton']		= "Send";

$CONF['text']['de']['redirectionBox'] 	= "Bitte klicken Sie hier um fortzufahren";
$CONF['text']['en']['redirectionBox'] 	= "Please click here to continue";


$suisseID = new SuisseID($CONF);
unset($CONF);
unset($attributes);



//Set Error Handler
//set_error_handler("exception_error_handler");

function exception_error_handler($errno, $errstr, $errfile, $errline ) {	
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
//set_error_handler('error_handler', E_ALL);

