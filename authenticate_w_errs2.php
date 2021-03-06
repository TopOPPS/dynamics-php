<?php
include 'CrmAuth.php';
include 'CrmExecuteSoap.php';
include "CrmAuthenticationHeader.php";

function WhoAmI($authHeader, $url) {
	$xml = "<s:Body>";
	$xml .= "<Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\">";
	$xml .= "<request i:type=\"c:WhoAmIRequest\" xmlns:b=\"http://schemas.microsoft.com/xrm/2011/Contracts\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:c=\"http://schemas.microsoft.com/crm/2011/Contracts\">";
	$xml .= "<b:Parameters xmlns:d=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\"/>";
	$xml .= "<b:RequestId i:nil=\"true\"/>";
	$xml .= "<b:RequestName>WhoAmI</b:RequestName>";
	$xml .= "</request>";
	$xml .= "</Execute>";
	$xml .= "</s:Body>";

	$executeSoap = new CrmExecuteSoap ();
	$response = $executeSoap->ExecuteSOAPRequest ( $authHeader, $xml, $url );

	return $response;
}

function findUserId($responsedom){
	$values = $responsedom->getElementsbyTagName ( "KeyValuePairOfstringanyType" );

	foreach ( $values as $value ) {
		if ($value->firstChild->textContent == "UserId") {
			return $value->lastChild->textContent;
		}
	}

	return null;
}


$url = $_POST['instance_url'];
//Username format could be domain\\username or username in the form of an email
$username = $_POST['username'];
$password = $_POST["password"];

$password = str_replace("&", "&amp;", $password);
$password = str_replace('"', "&quot;", $password);
$password = str_replace("'", "&apos;", $password);
$password = str_replace('<', "&lt;", $password);
$password = str_replace('>', "&gt;", $password);

$crmAuth = new CrmAuth();
$finalresponse = array();

$authHeader = $crmAuth->GetHeaderOnline ($username, $password, $url);
$response = WhoAmI ( $authHeader, $url );
$responsedom = new DomDocument ();
$responsedom->loadXML ( $response );
$userid = findUserId($responsedom);

$response['onlineXMLResp'] = $responsedom->saveXML();

if ($userid == null) {
	$authHeader = $crmAuth->GetHeaderOnPremise($username, $password, $url);
	if (array_key_exists('error', $authHeader)) {
		$response['onPremXMLResp'] = $authHeader['error'];
		print json_encode($response);
		die;
	}
	$response = WhoAmI ( $authHeader, $url );
	$responsedom = new DomDocument ();
	$responsedom->loadXML ( $response );
	$userid = findUserId($responsedom);
}

$finalresponse['onPremXMLResp'] = $response;

if ($userid == null){} else{
	$finalresponse['userid'] = $userid;
}
print json_encode($finalresponse);


?>
