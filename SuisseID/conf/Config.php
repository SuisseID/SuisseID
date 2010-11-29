<?php 

require 'includes/SuisseID/lib/SuisseID.php';
require 'includes/SuisseID/lib/XMLToArrayParser.php';
include 'includes/SuisseID/conf/Attributes.php';

$CONF['suisseid_error_log'] = 'pjlog/suisseid_errors.log';

$CONF['loginURL'] 	 		= 'http://www.yourshop.com/login.php';
$CONF['responseURL'] 		= 'https://www.yourshop.com/login.php';
$CONF['disclaimerURL'] 		= 'http://www.yourshop.com/conditions.php';
$CONF['samlRedirectorURL'] 	= 'http://www.yourshop.com/create_account.php';
$CONF['mediaURL']			= 'http://www.yourshop.com/includes/SuisseID/media/';

$CONF['useSessionIDasSAMLID'] 			= true;
$CONF['addSessionIDToSAMLRequestURL'] 	= false;
$CONF['sessionIdentifier']				= 'osCsid';

$CONF['userLanguage'] = 'de';

$CONF['logErrors']  = true;
$CONF['showErrors'] = false;


$CONF['defaultRequest'][] = $attributes['givenname'];
$CONF['defaultRequest'][] = $attributes['surname'];
$CONF['defaultRequest'][] = $attributes['gender']; 

$CONF['createCustomer'][] = $attributes['givenname'];
$CONF['createCustomer'][] = $attributes['surname'];
$CONF['createCustomer'][] = $attributes['gender']; 
$CONF['createCustomer'][] = $attributes['dateofbirth']; 


//all idp's
$CONF['idp']['swisssign']['name'] 				= 'Post SwissSign';
$CONF['idp']['swisssign']['issuer']				= 'Pro Juventute Webshop';
$CONF['idp']['swisssign']['samlEndpointSSO'] 	= 'https://idp.swisssign.net/suisseid/SSOPOST/metaAlias/swisssign.net/idp';
$CONF['idp']['swisssign']['samlEndpointQuery'] 	= 'https://idp.swisssign.net/suisseid/SSOPOST/metaAlias/swisssign.net/idp';

$CONF['idp']['quovadis']['name']  				= 'QuoVadis';
$CONF['idp']['quovadis']['issuer']				= 'Pro Juventute Webshop';
$CONF['idp']['quovadis']['samlEndpointSSO']  	= 'https://idp.quovadis.ch/suisseid/eidp/';
$CONF['idp']['quovadis']['samlEndpointQuery']  	= 'https://idp.quovadis.ch/suisseid/eidp/';



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

