<?php 

unset($attributes);
unset($CONF);

$attributes['givenNames'] 					= 'http://www.ech.ch/xmlns/eCH-0113/1/givenNames';
$attributes['givenname'] 					= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
$attributes['surname'] 						= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
$attributes['dateofbirth'] 					= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/dateofbirth';
$attributes['dateOfBirthPartiallyKnown'] 	= 'http://www.ech.ch/xmlns/eCH-0113/1/dateOfBirthPartiallyKnown';
$attributes['placeOfBirth'] 				= 'http://www.ech.ch/xmlns/eCH-0113/1/placeOfBirth';
$attributes['origin'] 						= 'http://www.ech.ch/xmlns/eCH-0113/1/origin';
$attributes['gender'] 						= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/gender';
$attributes['nationality'] 					= 'http://www.ech.ch/xmlns/eCH-0113/1/nationality';
$attributes['identificationNumber'] 		= 'http://www.ech.ch/xmlns/eCH-0113/1/identificationNumber';
$attributes['identificationKind'] 			= 'http://www.ech.ch/xmlns/eCH-0113/1/identificationKind';
$attributes['issuingCountry'] 				= 'http://www.ech.ch/xmlns/eCH-0113/1/issuingCountry';
$attributes['issuingOffice'] 				= 'http://www.ech.ch/xmlns/eCH-0113/1/issuingOffice';
$attributes['identificationIssuedOn'] 		= 'http://www.ech.ch/xmlns/eCH-0113/1/identificationIssuedOn';
$attributes['identificationValidUntil'] 	= 'http://www.ech.ch/xmlns/eCH-0113/1/identificationValidUntil';
$attributes['Age'] 							= 'http://www.ech.ch/xmlns/eCH-0113/1/age';
$attributes['isOver16'] 					= 'http://www.ech.ch/xmlns/eCH-0113/1/isOver16';
$attributes['isOver18'] 					= 'http://www.ech.ch/xmlns/eCH-0113/1/isOver18';
//$attributes['age-18-or-over'] 				= 'http://schemas.informationcard.net/@ics/age-18-orover/2008-11'; //doesn't work, was taken from SuisseID_Specification_1.3.pdf
$attributes['isSwissCitizen'] 				= 'http://www.ech.ch/xmlns/eCH-0113/1/isSwissCitizen';
$CONF['attributes'] = $attributes;

$CONF['languages']['german']  	= 'de';
$CONF['languages']['italian']  	= 'it';
$CONF['languages']['french']  	= 'fr';
$CONF['languages']['english'] 	= 'en';
 