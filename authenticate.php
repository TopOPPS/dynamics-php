<?php
include 'CrmAuth.php';
include 'CrmExecuteSoap.php';
include "CrmAuthenticationHeader.php";

// CRM Online
$url = $_POST['instance_url'];
$username = $_POST['username'];
$password = $_POST["password"];

$crmAuth = new CrmAuth ();
$authHeader = $crmAuth->GetHeaderOnline ( $username, $password, $url );
// End CRM Online

// CRM On Premise - IFD
// $url = "https://org.domain.com/";
// //Username format could be domain\\username or username in the form of an email
// $username = "username";
// $password = "password";

// $crmAuth = new CrmAuth();
// $authHeader = $crmAuth->GetHeaderOnPremise($username, $password, $url);
// End CRM On Premise - IFD

$userid = WhoAmI ( $authHeader, $url );
if ($userid == null) {
	print "{}";
}
else {
	print '{"userid": "'.$userid.'"}';
}
return;

//$name = CrmGetUserName ( $authHeader, $userid, $url );

//print $name;

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

	$responsedom = new DomDocument ();
	$responsedom->loadXML ( $response );

	$values = $responsedom->getElementsbyTagName ( "KeyValuePairOfstringanyType" );

	foreach ( $values as $value ) {
		if ($value->firstChild->textContent == "UserId") {
			return $value->lastChild->textContent;
		}
	}

	return null;
}
function CrmGetUserName($authHeader, $id, $url) {
	$xml = "<s:Body>";
	$xml .= "<Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
	$xml .= "<request i:type=\"a:RetrieveRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
	$xml .= "<a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
	$xml .= "<a:KeyValuePairOfstringanyType>";
	$xml .= "<b:key>Target</b:key>";
	$xml .= "<b:value i:type=\"a:EntityReference\">";
	$xml .= "<a:Id>" . $id . "</a:Id>";
	$xml .= "<a:LogicalName>systemuser</a:LogicalName>";
	$xml .= "<a:Name i:nil=\"true\" />";
	$xml .= "</b:value>";
	$xml .= "</a:KeyValuePairOfstringanyType>";
	$xml .= "<a:KeyValuePairOfstringanyType>";
	$xml .= "<b:key>ColumnSet</b:key>";
	$xml .= "<b:value i:type=\"a:ColumnSet\">";
	$xml .= "<a:AllColumns>false</a:AllColumns>";
	$xml .= "<a:Columns xmlns:c=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">";
	$xml .= "<c:string>firstname</c:string>";
	$xml .= "<c:string>lastname</c:string>";
	$xml .= "</a:Columns>";
	$xml .= "</b:value>";
	$xml .= "</a:KeyValuePairOfstringanyType>";
	$xml .= "</a:Parameters>";
	$xml .= "<a:RequestId i:nil=\"true\" />";
	$xml .= "<a:RequestName>Retrieve</a:RequestName>";
	$xml .= "</request>";
	$xml .= "</Execute>";
	$xml .= "</s:Body>";

	$executeSoap = new CrmExecuteSoap ();

	$response = $executeSoap->ExecuteSOAPRequest ( $authHeader, $xml, $url );

	$responsedom = new DomDocument ();
	$responsedom->loadXML ( $response );

	$firstname = "";
	$lastname = "";

	$values = $responsedom->getElementsbyTagName ( "KeyValuePairOfstringanyType" );

	foreach ( $values as $value ) {
		if ($value->firstChild->textContent == "firstname") {
			$firstname = $value->lastChild->textContent;
		}

		if ($value->firstChild->textContent == "lastname") {
			$lastname = $value->lastChild->textContent;
		}
	}

	return $firstname . " " . $lastname;
}

?>