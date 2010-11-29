<?php

class SuisseID {
		
	private $CONF;
	public  $samlResponseArray;
	
	public  $personAttributes;
	public  $personSuisseIDNo;
	public  $statusText;	
	private $userIsAuthenticated = false;
	public  $sessionID;
    	  		
	function SuisseID($CONF)
	{
		$this->CONF = $CONF;	
	}	
	
	public function processSamlResponse($shortAttributeNames = false, $onlyValues = false, $redirectIfNotSet = true){
			
		try {	

			if(empty($_POST ['SAMLResponse']))
			{
				if($redirectIfNotSet)
				{
					header("Location: index.php");
				}
				return false;
			}								
			
			$SAML = base64_decode ( $_POST ['SAMLResponse'] );		
			$SAML = str_replace("\r\n", '', $SAML);
			$SAML = str_replace('  ', '', $SAML);
								
			$xml2array 		 				= new XMLToArrayParser($SAML);
			$this->samlResponseArray 		= $xml2array->convertedArray;							
				
			$this->userIsAuthenticated  	= $this->setPersonAttributes($shortAttributeNames, $onlyValues);				
				
			return $this->userIsAuthenticated;		 			
		}
		catch(Exception $ex)
		{						 
			$this->statusText = $this->writeLog($ex);		
		}
		return false;
	}

	private function setPersonAttributes($shortAttributeNames = false, $onlyValues = true) {		

		//$this->printArray($this->samlResponseArray);
		
		if($this->CONF['addSessionIDToSAMLRequestURL'])
		{
			if(empty($_GET['SID1']))
			{
				throw new Exception("\$this->CONF['addSessionIDToSAMLRequestURL'] is true but SAML response has no Session-ID in URL -> \$_GET['SID1'] was empty");				
			}
			else
			{
				$this->sessionID = $_GET['SID1'];			
			}
		}		
		
		if(empty($this->sessionID ) && $this->CONF['useSessionIDasSAMLID'])
		{
			if(empty($this->samlResponseArray['saml2p:Response']['attrib']['InResponseTo']))
			{
				throw new Exception("\$this->CONF['useSessionIDasSAMLID'] is true, but no value in \$this->samlResponseArray['saml2p:Response']['attrib']['ID']");									
			}
			else
			{
				$this->sessionID = 	$this->samlResponseArray['saml2p:Response']['attrib']['InResponseTo'];							
			}
		}
		else if($this->samlResponseArray['saml2p:Response']['attrib']['InResponseTo'] != md5($_SERVER['REMOTE_ADDR']))
		{
			throw new Exception("Wrong value in \$this->samlResponseArray['saml2p:Response']['attrib']['InResponseTo']");								
		}
				
		if($this->CONF['addSessionIDToSAMLRequestURL'] && $this->CONF['useSessionIDasSAMLID'] && ($_GET['SID1'] != $this->samlResponseArray['saml2p:Response']['attrib']['ID']))
		{
			$txt = "\$_GET['SID1'] and \$this->samlResponseArray['saml2p:Response']['attrib']['ID'] have different values: ";
			if(!empty($_GET['SID1'])){ $txt .= $_GET['SID1']; };
			$txt .= " and ";
			if(!empty($this->samlResponseArray['saml2p:Response']['attrib']['ID'])){ $txt .= $this->samlResponseArray['saml2p:Response']['attrib']['ID']; };			
			throw new Exception($txt);
		}

		if(($this->CONF['addSessionIDToSAMLRequestURL'] || $this->CONF['useSessionIDasSAMLID']) &&
		    !empty($this->CONF['sessionIdentifier']) && 
		    !empty($this->sessionID))
		{
			$_GET[$this->CONF['sessionIdentifier']] = $this->sessionID;			
		}
									
		
		//make some checks
		if(!empty($this->samlResponseArray['saml2p:Response']['saml2p:Status']['saml2p:StatusCode']['saml2p:StatusCode']['attrib']['Value']))
		{
			throw new Exception($this->samlResponseArray['saml2p:Response']['saml2p:Status']['saml2p:StatusCode']['saml2p:StatusCode']['attrib']['Value']);								                                         
		}	
		
		if(empty($this->samlResponseArray['saml2p:Response']['saml2:Assertion']['saml2:Subject']['saml2:NameID']['cdata']) ||
		   strlen($this->samlResponseArray['saml2p:Response']['saml2:Assertion']['saml2:Subject']['saml2:NameID']['cdata']) != 19)
		{								
			throw new Exception("SuisseID No. is wrong");								
		}
		 
		if(empty($this->samlResponseArray['saml2p:Response']['saml2p:Status']['saml2p:StatusCode']['attrib']['Value']) ||
		   $this->samlResponseArray['saml2p:Response']['saml2p:Status']['saml2p:StatusCode']['attrib']['Value'] != 'urn:oasis:names:tc:SAML:2.0:status:Success')
		{
			throw new Exception("Wrong Response-Status");								
		}
		
		
		if(empty($this->samlResponseArray['saml2p:Response']['attrib']['IssueInstant']) ||
		   strlen($this->samlResponseArray['saml2p:Response']['attrib']['IssueInstant']) < 20 ||
		   strlen($this->samlResponseArray['saml2p:Response']['attrib']['IssueInstant']) > 27
		  )		  
		{			
			throw new Exception("\$this->samlResponseArray['saml2p:Response']['attrib']['IssueInstant'] has a wrong value");								
		}
		else
		{
			$samlTime = $this->samlResponseArray['saml2p:Response']['attrib']['IssueInstant'];					
			$unixTime = gmmktime(substr($samlTime,11,2), substr($samlTime,14,2), substr($samlTime,17,2), substr($samlTime,5,2), substr($samlTime,8,2), substr($samlTime,0,4));
			
			if($unixTime < (time() - 300))
			{
				throw new Exception("SAML-Response is too old");
			}
			
			if($unixTime > (time() + 300))
			{
				throw new Exception("SAML-Response has a wrong timestamp");
			}
			
		}
								
		$this->personSuisseIDNo = $this->samlResponseArray['saml2p:Response']['saml2:Assertion']['saml2:Subject']['saml2:NameID']['cdata'];
				
		if(empty($this->samlResponseArray['saml2p:Response']['saml2:Assertion']['saml2:AttributeStatement']['saml2:Attribute']))
		{		
			return true;
		}						
		
		$attributes = $this->samlResponseArray['saml2p:Response']['saml2:Assertion']['saml2:AttributeStatement']['saml2:Attribute'];			
		
		//$this->printArray($attributes);		
		
		
		foreach($attributes as $key => $value)
		{
			$indexKey = $value['attrib']['Name'];
			
			if($shortAttributeNames)
			{
				$tempKey = array_search($value['attrib']['Name'], $this->CONF['attributes']);
				if($tempKey)
				{
					$indexKey = $tempKey;
				}
			}			
			
			$cdata = '';

			if (isset($value['saml2:AttributeValue']['cdata']))
			{
				$cdata = $value['saml2:AttributeValue']['cdata'];
			}
			else if(isset($value['saml2:AttributeValue']))
			{
				foreach($value['saml2:AttributeValue'] as $k2 => $v2)
				{
					if($k2 == 'cdata')
					{
						$cdata = $v2;
					}
				}
			}
			
			
			
			if($onlyValues)
			{				
				$personAttributeArray[$indexKey]= $cdata;	
			}
			else 
			{
				$personAttributeArray[$indexKey]['FriendlyName'] 		= $value['attrib']['FriendlyName'];
				$personAttributeArray[$indexKey]['Name'] 				= $value['attrib']['Name']; 
				$personAttributeArray[$indexKey]['NameFormat'] 			= $value['attrib']['NameFormat'];
				if(isset($value['saml2:AttributeValue']))
				{
					$personAttributeArray[$indexKey]['xmlns:icc'] 		= $value['saml2:AttributeValue']['attrib']['xmlns:icc'];
					$personAttributeArray[$indexKey]['xmlns:xsi'] 		= $value['saml2:AttributeValue']['attrib']['xmlns:xsi'];
					$personAttributeArray[$indexKey]['xsi:type'] 		= $value['saml2:AttributeValue']['attrib']['xsi:type'];
				}
				$personAttributeArray[$indexKey]['cdata'] 				= $cdata;				
			}				
		}				
		
		$this->personAttributes = array_reverse($personAttributeArray);
		
		return true;
	}
	
	public function printArray($value, $key = '') {
					
		if (is_array ( $value )) {					
			foreach ( $value as $k => $v ) {							
				$this->printArray ( $v, $key . '[' . $k . ']' );								
			}
		} else if(!is_object($value)) {
			echo "<br><b>$key</b> $value";			
		}
	}
		
	public function writeLog($ex) {

		$msg = '';
		try {	
			if(is_object($ex))
			{
				$msg = substr($ex->getMessage()." on <b>line ".$ex->getLine()."</b> in file ".$ex->getFile(), 0, 200);
			}
			else 
			{
				$msg = substr($ex, 0, 200);
			}
						
			if($this->CONF['showErrors'])
			{
				echo $msg;
			}
		} catch ( Exception $ex ) {}
		
		
		
		try {
			if(empty($this->CONF['logErrors']) || $this->CONF['logErrors'])
			{										
				$filename = $this->CONF['suisseid_error_log'];
				$fp = fopen ( $filename, "a" );
				$logs = gmdate("d.m.Y H:i:s") . " | $msg \n";
				fputs ( $fp, utf8_decode ( $logs ) );
				fclose ( $fp );
			}			
		} catch ( Exception $ex ) {}

		return $msg;
	}

	public function getSAML($isOnlyAuthentication = true, $selectedIdp, $requestAttributes = array(), $returnInBase64Encoding = true)
	{									
		$selectedIdp = $this->checkSelectedIdp($selectedIdp);				
		
		$forceAuthn = 'true';
		
		$requestURL = $this->CONF['idp'][$selectedIdp]['samlEndpointQuery'];
		if($isOnlyAuthentication)
		{
			$requestAttributes = array();
			$forceAuthn = 'false';
			$requestURL = $this->CONF['idp'][$selectedIdp]['samlEndpointSSO'];
		}
		else if(empty($requestAttributes) || count($requestAttributes) == 0)
		{
			$requestAttributes = $this->CONF['defaultRequest'];
		}	
		
		$sessionID = '';
		if(!empty($this->sessionID) && $this->CONF['addSessionIDToSAMLRequestURL'] && !empty($this->CONF['sessionIdentifier']))
		{			
			$sessionID = '?SID1='.$this->sessionID;
		} 
				
		$samlID = md5($_SERVER['REMOTE_ADDR']);
		if(!empty($this->sessionID) && $this->CONF['useSessionIDasSAMLID'])
		{
			$samlID = $this->sessionID;
		}				
				
		$SAML = '<?xml version="1.0" encoding="utf-8"?>
				<AuthnRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ic="http://schemas.xmlsoap.org/ws/2005/05/identity" ID="'.$samlID.'" Version="2.0" IssueInstant="'.gmdate("Y-m-d").'T'.gmdate("H:i:s").'Z" Destination="'.$requestURL.'" ForceAuthn="'.$forceAuthn.'" IsPassive="false" ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" AssertionConsumerServiceURL="'.$this->CONF['responseURL'].$sessionID.'" xmlns="urn:oasis:names:tc:SAML:2.0:protocol">					
					<Issuer xmlns="urn:oasis:names:tc:SAML:2.0:assertion">'.$this->CONF['idp'][$selectedIdp]['issuer'].'</Issuer>
					<Extensions>';
					
						foreach($requestAttributes as $key => $fieldName)
						{
							$SAML .= '<saml:Attribute Name="'.$fieldName.'" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri" eCH-0113:required="true" xmlns:eCH-0113="http://www.ech.ch/xmlns/eCH-0113/1" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" />';
						}
						
		$SAML .= '		<ic:PrivacyNotice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1" xsi:type="ic:PrivacyNoticeLocationType" xmlns:ic="http://schemas.xmlsoap.org/ws/2005/05/identity">'.$this->CONF['disclaimerURL'].'</ic:PrivacyNotice>
					</Extensions>
				</AuthnRequest>';
											
		$SAML = str_replace("\r\n", '', $SAML);
		$SAML = str_replace("\t", '', $SAML);
		$SAML = str_replace('  ', '', $SAML);		
				
		
		if($returnInBase64Encoding)
		{		
			return base64_encode($SAML);
		}
		return $SAML;
	}
	
	public function getLoginButton($isOnlyAuthentication = true, $includeFormsInMenu = true, $language = '', $sessionID = '', $confAttributeSet = '')
	{		
		if(!$includeFormsInMenu && empty($this->CONF['samlRedirectorURL']))
		{
			$this->writeLog(new Exception("\$CONF['samlRedirectorURL'] is empty and has to be defined in /conf/Config.php"));			
			return;
		}				
		
		if(!$isOnlyAuthentication && !empty($confAttributeSet) && empty($this->CONF[$confAttributeSet]))
		{
			$this->writeLog(new Exception("Parameter '\$confAttributeSet' has a wrong value: ".$confAttributeSet));
			return;
		}		
		
		$this->sessionID = $sessionID;
		
		$attr = '';
		if($isOnlyAuthentication)
		{
			$attr = '&attr=none';
		}
		else if(!empty($confAttributeSet))
		{
			$attr = "&attr=".urlencode($confAttributeSet);
		}
		
		$button =	'<div class="menu" align="center"><ul><li><a id="current"></a><ul>';
				
		$lang = '&lang=' . $this->getLanguage($language);
		
		foreach($this->CONF['idp'] as $selectedIdp => $idp)
		{		
			if($includeFormsInMenu)
			{
		 		$button .= '<li><a href="" target="_self"></a>'.$this->getSamlForm($isOnlyAuthentication, $selectedIdp).'</li>';
		 		//$button .= '<li>'.$this->getSamlForm($isOnlyAuthentication, $selectedIdp).'</li>';
			}
			else
			{
				$button .= '<li><a href="'.$this->CONF['samlRedirectorURL'].'?selectedidp='.$selectedIdp.$lang.$attr.'" target="_self">'.$idp['name'].'</a></li>';			
			}		
		}
							
		$button .= '</ul></li></ul></div>';
		
		if($includeFormsInMenu)
		{
			$button .= 	'<script type="text/javascript">';
			$button .= 	"var jsLinks = document.getElementsByName('jsLinkSuisseID');";				
			$button .= 	"for(var i = 0; i < jsLinks.length; i++){jsLinks[i].style.display = 'block';}";
			$button .= 	'function submitform(idpName){document.forms[idpName].submit();}';
			$button .= 	'</script>';
		}
		
		return $button;
	}

	public function getSamlForm($isOnlyAuthentication = true, $selectedIdp = '', $menuButtons = true, $noFormButtons = false, $saml = '', $requestAttributes = array())
	{	
		$selectedIdp = $this->checkSelectedIdp($selectedIdp);		
		if($isOnlyAuthentication)
		{
			$formAction  = $this->CONF['idp'][$selectedIdp]['samlEndpointSSO'];
		}
		else 
		{
			$formAction  = $this->CONF['idp'][$selectedIdp]['samlEndpointQuery'];
		}
		
		$formAction .= '?lang='.$this->getLanguage();;
		 
		if(empty($saml))
		{
			$saml = $this->getSAML($isOnlyAuthentication, $selectedIdp, $requestAttributes);
		}		
		 
		$form  = '<form action="'.$formAction.'" method="POST" id="'.$selectedIdp.'" name="'.$selectedIdp.'">';
		$form .= '<input name="SAMLRequest" type="hidden" value="'.$saml.'" />';
		
		if(!$noFormButtons)
		{
			if($menuButtons)
			{
				$form .= '<a href="javascript: submitform(\''.$selectedIdp.'\')" name="jsLinkSuisseID" style="display: none">'.$this->CONF['idp'][$selectedIdp]['name'].'</a>';
				$form .= '<noscript><button type="submit" class="jsLink">'.$this->CONF['idp'][$selectedIdp]['name'].'</button></noscript>';
			}
			else
			{		
				$form .= '<input type="submit" value="'.$this->getText('sendButton').'" />';
			}
		}
		
		$form .= '</form>';
		
		return $form;
	}
	
	public function getSamlRedirectorForms($isOnlyAuthentication = true, $selectedIdp = '', $confAttributeSet = '', $sessionID = '', $requestAttributes = array())
	{
		if(!empty($confAttributeSet))
		{
			$requestAttributes 		= $this->CONF[$confAttributeSet];
			$isOnlyAuthentication 	= false;			
		}
		else if(!empty($_GET['attr']) && $_GET['attr'] == "none")
		{
			$isOnlyAuthentication = true;
			$requestAttributes = array();			
		} 		
		else if(!$isOnlyAuthentication && count($requestAttributes) == 0 && !empty($_GET['attr']) && !empty($this->CONF[$_GET['attr']]))
		{
			$requestAttributes = $this->CONF[$_GET['attr']];
		}
		
		$this->sessionID = $sessionID;		
		
		$saml 	= $this->getSAML($isOnlyAuthentication, $selectedIdp, $requestAttributes);		
		$forms 	= $this->getSamlForm($isOnlyAuthentication, $selectedIdp, false, true, $saml);

		$forms .=	'<noscript>
						<div id="mydiv">
						
							<p><strong>'.$this->getText('redirectionBox').'</strong></p>
							
							<div class="comments" style="height: 25px">
								'.$this->getSamlForm($isOnlyAuthentication, $selectedIdp, false, false, $saml).'
							</div>
						</div>
					</noscript>
						
					<script type="text/javascript">
						function myfunc () {
							var frm = document.getElementById("'.$selectedIdp.'");
							frm.submit();
						}
						window.onload = myfunc;
					</script>';				
		
		return $forms;
	} 
	
	private function checkSelectedIdp($selectedIdp = '')
	{
		if(empty($selectedIdp) && !empty($_GET['selectedidp']))
		{
			$selectedIdp = $_GET['selectedidp']; 
		}	
		
		if(!isset($this->CONF['idp'][$selectedIdp]))
		{
			throw new Exception("The selected idP is incorrect");
		}
		
		return $selectedIdp;
	}
	
	public function setUserLanguage($languageCode = '')
	{
		if(!empty($languageCode) && strlen($languageCode) == 2)
		{
			$this->CONF['userLanguage'] = $languageCode;
			return true;
		}
		return false;
	}

	public function getText($textKey)
	{
		if(isset($this->CONF['text'][$this->CONF['userLanguage']][$textKey]))
		{
			return $this->CONF['text'][$this->CONF['userLanguage']][$textKey];
		}
		else if(isset($this->CONF['text']['en'][$textKey]))
		{
			return $this->CONF['text']['en'][$textKey];
		}
		else 
		{
			return '';		
		}
	}
	
	public function checkRedirection()
	{
		if(empty($_GET['selectedidp']) || !isset($this->CONF['idp'][$_GET['selectedidp']]['samlEndpoint']))
		{
			$value = "";
			if(isset($_GET['selectedidp']))
			{
				$value = $_GET['selectedidp'];
			}
			$this->CONF['showErrors'] = false;
			$this->writeLog(new Exception('Wrong $_GET-Parameter in samlRedirector.php: '.$value));
			header('Location: '.$this->CONF['loginURL']);	
			die();
		}
		
		if(isset($_GET['lang']) && !empty($this->CONF['text'][$_GET['lang']]))
		{
			$this->CONF['userLanguage'] = $_GET['lang'];						
		}
	}

	public function getMediaPath()
	{
		if(empty($this->CONF['mediaURL']))
		{
			return '../media/';
		}
		else if(substr($this->CONF['mediaURL'], -1) != '/')
		{
			return $this->CONF['mediaURL'].'/';
		}	
			
		return $this->CONF['mediaURL'];
	}

	public function generateLoginButtonHTML()
	{
		if(empty($this->CONF['samlRedirectorURL']))
		{
			$ex = new Exception("To use this function, please define first CONF['samlRedirectorURL'] in /lib/Conf.php");
			$this->writeLog($ex);
			return $ex->Message();
		}
		
		return $this->getLoginButton(false, false);
	}

	public function userIsAuthenticated()
	{
		return $this->userIsAuthenticated();
	}

	private function getLanguage($language = '')
	{
		if(!empty($language))
		{
			if(key_exists($language, $this->CONF['languages']))
			{
				$lang = $this->CONF['languages'][$language];				
			}
			else
			{
				$lang = substr($language,0,2);
			}
		}
		else if(!empty($_GET['lang']) && key_exists($_GET['lang'], $this->CONF['languages']))
		{
			$lang = $this->CONF['languages'][$_GET['lang']];
		}
		else
		{
			$lang = $this->CONF['userLanguage'];
		}
		
		$this->CONF['userLanguage'] = $lang;
		
		return $lang;
	}	

	public function convertDobToCHFormat($dateOfBirth)
	{
		$temp = explode('-', $dateOfBirth);
		if(count($temp) != 3)
		{
			$this->writeLog(new Exception('$dateOfBirth cannot be converted: '.$dateOfBirth));
			return $dateOfBirth;
		}
		
		return $temp[2].'.'.$temp[1].'.'.$temp[0];		
	}	
		
	public function convertGenderToCustomFormat($gender, $male, $female)
	{		
		if($gender == '0')
		{
			return $female;
		}	
		else if($gender == '1')
		{
			return $male;
		}	
		else
		{
			$this->writeLog(new Exception('$gender cannot be converted: '.$dateOfBirth));
		}
		
		return $gender;
	}
}

