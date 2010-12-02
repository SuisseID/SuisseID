<?php 

//IdP definitions
$CONF['idp']['swisssign']['name'] 				= 'Post SwissSign';
$CONF['idp']['swisssign']['issuer']				=  $CONF['websiteName'];
$CONF['idp']['swisssign']['samlEndpointSSO'] 	= 'https://idp.swisssign.net/suisseid/SSOPOST/metaAlias/swisssign.net/idp';
$CONF['idp']['swisssign']['samlEndpointQuery'] 	= 'https://idp.swisssign.net/suisseid/SSOPOST/metaAlias/swisssign.net/idp';
$CONF['idp']['quovadis']['name']  				= 'QuoVadis';
$CONF['idp']['quovadis']['issuer']				=  $CONF['websiteName'];
$CONF['idp']['quovadis']['samlEndpointSSO']  	= 'https://idp.quovadis.ch/suisseid/eidp/';
$CONF['idp']['quovadis']['samlEndpointQuery']  	= 'https://idp.quovadis.ch/suisseid/eidp/';