<?php 

//Includes
require '../lib/SuisseID.php';
require '../lib/XMLToArrayParser.php';
include '../conf/Attributes.php';

//General definitions
$CONF['websiteName']							= 'SuisseID Demo Site';

//URL definitions
$CONF['loginURL'] 	 							= 'http://www.yoursite.ch/demo/samlRequest.php';
$CONF['responseURL'] 							= 'https://www.yoursite.ch/demo/samlResponse.php';
$CONF['disclaimerURL'] 							= 'http://www.yoursite.ch/demo/yourDisclaimerPage.php';
$CONF['samlRedirectorURL'] 						= 'http://www.yoursite.ch/demo/samlRedirector.php';
$CONF['mediaURL']								= 'http://www.yoursite.ch/media/';

//Session handling
$CONF['useSessionIDasSAMLID'] 					= true;
$CONF['addSessionIDToSAMLRequestURL'] 			= false;
$CONF['sessionIdentifier']						= 'osCsid';
$CONF['AuthenticationTimeout']					= 15; 

//Error handling 
$CONF['logErrors']  							= true;
$CONF['showErrors'] 							= false;
$CONF['suisseid_error_log'] 					= 'includes/SuisseID/log/suisseid_errors.log';

//Language definitions
$CONF['userLanguage'] = 'de';

$CONF['text']['de']['sendButton']				= "Absenden";
$CONF['text']['en']['sendButton']				= "Send";

$CONF['text']['de']['redirectionBox'] 			= "Bitte klicken Sie hier um fortzufahren";
$CONF['text']['en']['redirectionBox'] 			= "Please click here to continue";

//Attribute set definition for IdP-Requests
$CONF['defaultRequest']['givenname'] 			= 'required';
$CONF['defaultRequest']['surname'] 				= 'required'; 
$CONF['defaultRequest']['gender'] 				= 'optional';

$CONF['createCustomer']['gender'] 				= 'required';		
$CONF['createCustomer']['givenname'] 			= 'required';			
$CONF['createCustomer']['surname'] 				= 'required';		
$CONF['createCustomer']['dateofbirth'] 			= 'required';			
$CONF['createCustomer']['issuingCountry'] 		= 'required';			


//Initialize and cleanup
include '../conf/Attributes.php';
$suisseID = new SuisseID($CONF);
unset($CONF);
unset($attributes);


//Set PHP error handling
/*Set Error Handler
set_error_handler("exception_error_handler");
error_reporting(E_ALL);
ini_set("display_errors", 1); 
set_error_handler('error_handler', E_ALL);
function exception_error_handler($errno, $errstr, $errfile, $errline ) {	
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

*/
