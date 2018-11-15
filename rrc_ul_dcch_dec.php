<?php
/*
-- ASN1START

UL-DCCH-Message ::= SEQUENCE {
	message					UL-DCCH-MessageType
}

UL-DCCH-MessageType ::= CHOICE {
	c1						CHOICE {
		csfbParametersRequestCDMA2000			CSFBParametersRequestCDMA2000,
		measurementReport						MeasurementReport,
		rrcConnectionReconfigurationComplete	RRCConnectionReconfigurationComplete,
		rrcConnectionReestablishmentComplete	RRCConnectionReestablishmentComplete,
		rrcConnectionSetupComplete				RRCConnectionSetupComplete,
		securityModeComplete					SecurityModeComplete,
		securityModeFailure						SecurityModeFailure,
		ueCapabilityInformation					UECapabilityInformation,
		ulHandoverPreparationTransfer			ULHandoverPreparationTransfer,
		ulInformationTransfer					ULInformationTransfer,
		counterCheckResponse					CounterCheckResponse,
		ueInformationResponse-r9				UEInformationResponse-r9,
		proximityIndication-r9					ProximityIndication-r9,
		rnReconfigurationComplete-r10			RNReconfigurationComplete-r10,
		mbmsCountingResponse-r10				MBMSCountingResponse-r10,
		interFreqRSTDMeasurementIndication-r10	InterFreqRSTDMeasurementIndication-r10
	},
	messageClassExtension	SEQUENCE {}
}

-- ASN1STOP

*/

include('rrc_fun.php');

//RRC-Connection-Setup-Complete
//$rrc_msg="24 20 80 00 01 46 07 41 71 0B F6 42 F0 10 80 00 01 00 00 00 02 04 E0 E0 C0 40 00 21 02 01 D0 11 D1 27 1A 80 80 21 10 01 00 00 10 81 06 00 00 00 00 83 06 00 00 00 00 00 0D 00 00 0A 00 52 42 F0 10 09 01 5C 0A 00 31 03 E5 C0 34 E0";
//ULInformationTransfer
//$rrc_msg="48 01 60 EA 61 01 53 E2 C6 FA 0B 9F 3C A0";
//$rrc_msg="48 01 60 EA C1 05 20 82 04 28 6C A0 01 20";
//RRCConnectionReconfigurationComplete
//$rrc_msg="12 20";
//ueCapabilityInformation 
//$rrc_msg="3C 01 02 48 12 00 00 40 01 06 0E 4C 24 4F 8D FE 27 C6 FF 13 E3 7F 89 F1 BF C4 F8 DF D7 C3 FF 20 22 04 01 1C 32 9D 2A 00";
//securityModeComplete
//$rrc_msg="2C 00";

$rrc_msg=$_POST["hexStr"];

//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);

//read 1 bit to check choice for UL-DCCH-MessageType
$UL_DCCH_msgtype_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($UL_DCCH_msgtype_bit=='1'){
	//echo 'Note: messageClassExtension decoding is supported so far!';
	exit('Note: messageClassExtension decoding is supported so far!');
}

//construct output string
$tab='    ';
$rrc_out='UL-DCCH-Message : {';
$rrc_out_end='}';
$tabs='';

//read 4 bites to check the message type of c1
$c1_type_bit=substr($rrc_msg_bin,0,4);
$rrc_msg_bin=substr($rrc_msg_bin,4);
switch ($c1_type_bit)
{
	case '0000': 
		break;
	case '0001':
		break;
	case '0010':
		rrcConnectionReconfigurationComplete();
		break;
	case '0011':
		break;
	case '0100':
		rrcConnectionSetupComplete();
		break;
	case '0101':
		securityModeComplete();
		break;
	case '0110':
		break;
	case '0111':
		ueCapabilityInformation();
		break;
	case '1000': 
		break;
	case '1001':
		ULInformationTransfer();
		break;
	case '1010':
		break;
	case '1011':
		break;
	case '1100': 
		break;
	case '1101':
		break;
	case '1110':
		break;
	case '1111':
		break;
	default:
		break;
}

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

//procedure end here.





function rrcConnectionSetupComplete(){
/*
-- ASN1START

RRCConnectionSetupComplete ::=		SEQUENCE {
	rrc-TransactionIdentifier			RRC-TransactionIdentifier,
	criticalExtensions					CHOICE {
		c1									CHOICE{
			rrcConnectionSetupComplete-r8		RRCConnectionSetupComplete-r8-IEs,
			spare3 NULL, spare2 NULL, spare1 NULL
		},
		criticalExtensionsFuture			SEQUENCE {}
	}
}

RRCConnectionSetupComplete-r8-IEs ::= SEQUENCE {
	selectedPLMN-Identity				INTEGER (1..6),
	registeredMME						RegisteredMME						OPTIONAL,
	dedicatedInfoNAS					DedicatedInfoNAS,
	nonCriticalExtension				RRCConnectionSetupComplete-v8a0-IEs	OPTIONAL
}

RRCConnectionSetupComplete-v8a0-IEs ::= SEQUENCE {
	lateNonCriticalExtension			OCTET STRING						OPTIONAL,
	nonCriticalExtension				RRCConnectionSetupComplete-v1020-IEs	OPTIONAL
}

RRCConnectionSetupComplete-v1020-IEs ::= SEQUENCE {
	gummei-Type-r10						ENUMERATED {native, mapped}			OPTIONAL,
	rlf-InfoAvailable-r10				ENUMERATED {true}					OPTIONAL,
	logMeasAvailable-r10				ENUMERATED {true}					OPTIONAL,
	rn-SubframeConfigReq-r10			ENUMERATED {required, notRequired}	OPTIONAL,
	nonCriticalExtension				SEQUENCE {}							OPTIONAL
}

RegisteredMME ::=					SEQUENCE {
	plmn-Identity						PLMN-Identity						OPTIONAL,
	mmegi								BIT STRING (SIZE (16)),
	mmec								MMEC
}

-- ASN1STOP

*/
/*
-- ASN1START

RRC-TransactionIdentifier ::=		INTEGER (0..3)

-- ASN1STOP
*/
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	$rrc_out= $rrc_out . "\n" . $tab . 'message c1 : rrcConnectionSetupComplete : ' . '{';
	$rrc_out_end= $tab . '}' . "\n" . $rrc_out_end;
	
	//read two bits for RRC-TransactionIdentifier
	$RRC_TranId=RRC_TransactionIdentifier(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'rrc-TransactionIdentifier ' . $RRC_TranId . ',';
	
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions {' ;
	
	//process criticalExtensions choice
	//read 1 bit
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,3) . 'c1 {' ;
		//c1 is choosed
		//process c1 
		//read 2 bits to check the c1 choice
		$c1_choice_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($c1_choice_bit=='00'){
			//
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'RRCConnectionSetupComplete-r8-IEs {' ;
			rrcConnectionSetupComplete_r8();
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . '}' ;
		}
		else{
			exit('Note: spare value for c1 is not supported!');
		}
		
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,3) . '}' ;
	}
	if($criticalExtensions_choice_bit=='1'){
		exit('Note: criticalExtensionsFuture process is not supported.');
	}
	
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . '}';
	
}

function rrcConnectionSetupComplete_r8(){
/*
RRCConnectionSetupComplete-r8-IEs ::= SEQUENCE {
	selectedPLMN-Identity				INTEGER (1..6),
	registeredMME						RegisteredMME						OPTIONAL,
	dedicatedInfoNAS					DedicatedInfoNAS,
	nonCriticalExtension				RRCConnectionSetupComplete-v8a0-IEs	OPTIONAL
}

*/
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	//read 2 bits to check optional fields
	$registeredMME_option_bit=substr($rrc_msg_bin,0,1);
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,1,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	
	//selectedPLMN-Identity
	//read 3 bits
	$selectedPLMN_Identity_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$selectedPLMN_Identity_value=selectedPLMN_Identity($selectedPLMN_Identity_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'selectedPLMN-Identity ' . $selectedPLMN_Identity_value;
	
	//registeredMME
	if($registeredMME_option_bit=='1'){
		//check option of plmn-Identity
		//read 1 bit
		$plmn_Identity_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		//plmn-Identity
		if($plmn_Identity_bit=='1'){
			$tabs=str_repeat($tab,6);
			plmn_Identity();
			$tabs=substr($tabs,0,-4);
		}
		
		//mmegi
		//read 16 bits
		$mmegi_bit=substr($rrc_msg_bin,0,16);
		$rrc_msg_bin=substr($rrc_msg_bin,16);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'mmegi ' . $mmegi_bit . ','; 
		
		//mmec
		//read 8 bits
		$mmec_bit=substr($rrc_msg_bin,0,8);
		$rrc_msg_bin=substr($rrc_msg_bin,8);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'mmec ' . $mmec_bit; 
		
		
	}
	
	//dedicatedInfoNAS
	//read 8 bits for the length of NAS
	$dedicatedInfoNAS_length_bit=substr($rrc_msg_bin,0,8);
	$rrc_msg_bin=substr($rrc_msg_bin,8);
	$dedicatedInfoNAS_length_value=bindec($dedicatedInfoNAS_length_bit);
	
	$dedicatedInfoNAS_content_bit=substr($rrc_msg_bin,0,8*$dedicatedInfoNAS_length_value);
	
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'dedicatedInfoNAS ' . my_bin2hex($dedicatedInfoNAS_content_bit);
	
	//nonCriticalExtension
	if($nonCriticalExtension_option_bit=='1'){
		
	}
	
	
	
	
	
}

function selectedPLMN_Identity($in){
	return bindec($in)+1;
}

function plmn_Identity(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs;
	
	//
	$rrc_out= $rrc_out . "\n" . $tabs . 'PLMN-Identity {';
	
	//check option for mcc
	//read 1 bit
	$mcc_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//mcc
	if(	$mcc_option_bit=='1'){
		//read 3 bits 
		$mcc_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MCC ' . $mcc_bit . ',';
	}
	//mnc
	//read 1 bit to check the size for mnc
	$mnc_size_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($mnc_size_bit=='0'){
		//size is 2
		//read 4 bits for each mcc
		$mnc1_bit=substr($rrc_msg_bin,0,4);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MNC ' . bindec($mnc1_bit) . ',';
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		
		$mnc2_bit=substr($rrc_msg_bin,0,4);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MNC ' . bindec($mnc2_bit) . ',';
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		
	}
	
	if($mnc_size_bit=='1'){
		//size is 3
		$mnc1_bit=substr($rrc_msg_bin,0,4);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MNC ' . bindec($mnc1_bit) . ',';
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		
		$mnc2_bit=substr($rrc_msg_bin,0,4);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MNC ' . bindec($mnc2_bit) . ',';
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		
		$mnc3_bit=substr($rrc_msg_bin,0,4);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'MNC ' . bindec($mnc3_bit) . ',';
		$rrc_msg_bin=substr($rrc_msg_bin,4);
	}
	//
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
}

function ULInformationTransfer(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	$tabs=$tab;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : ulInformationTransfer {';
	//check criticalExtensions choice
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$tabs=$tabs . $tab;
	if($criticalExtensions_choice_bit=='0'){
		$rrc_out= $rrc_out . "\n" . $tabs . 'criticalExtensions : c1 {';
		//check c1 choice
		$c1_choice_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		switch($c1_choice_bit){
			case '00':
				$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'ulInformationTransfer-r8 {';
				$tabs=$tabs . str_repeat($tab,2);
				ULInformationTransfer_r8_IEs();
				$tabs=substr($tabs,0,-8);
				$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
				break;
			case '01':
				$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'spare3 NULL';
				break;
			case '10':
				$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'spare2 NULL';
				break;
			case '11':
				$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'spare1 NULL';
				break;
			default:
				break;
		}
		
		$rrc_out= $rrc_out . "\n" . $tabs . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		$rrc_out= $rrc_out . "\n" . $tabs . 'criticalExtensions : criticalExtensionsFuture {';
		$rrc_out= $rrc_out . "\n" . $tabs . '}';
	}
	$tabs=substr($tabs,0,-4);
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
}
function ULInformationTransfer_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	//check option
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//dedicatedInfoType
	$rrc_out= $rrc_out . "\n" . $tabs . 'dedicatedInfoType {';
	//check choice for dedicatedInfoType
	$dedicatedInfoType_choice_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($dedicatedInfoType_choice_bit=='00'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'dedicatedInfoNAS {';
		$tabs=$tabs . str_repeat($tab,2);
		//DedicatedInfoNAS_1();
		$rrc_out= $rrc_out . "\n" . $tabs . DedicatedInfoNAS_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
	}  
	if($dedicatedInfoType_choice_bit=='01'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'dedicatedInfoCDMA2000-1XRTT {';
		$tabs=$tabs . str_repeat($tab,2);
		//DedicatedInfoCDMA2000_1();
		$rrc_out= $rrc_out . "\n" . $tabs . DedicatedInfoNAS_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($dedicatedInfoType_choice_bit=='10'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'dedicatedInfoCDMA2000-HRPD {';
		$tabs=$tabs . str_repeat($tab,2);
		//DedicatedInfoCDMA2000_1();
		$rrc_out= $rrc_out . "\n" . $tabs . DedicatedInfoNAS_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
	}
	
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
	//nonCriticalExtension				ULInformationTransfer-v8a0-IEs		OPTIONAL
	if($nonCriticalExtension_option_bit=='1'){
		
	}
}
function rrcConnectionReconfigurationComplete(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	$tabs=$tab;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : rrcConnectionReconfigurationComplete {';
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	
	//criticalExtensions
	//check choice for criticalExtensions
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		//rrcConnectionReconfigurationComplete-r8	RRCConnectionReconfigurationComplete-r8-IEs,
		//check option
		$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($nonCriticalExtension_option_bit=='1'){
			//nonCriticalExtension				RRCConnectionReconfigurationComplete-v8a0-IEs	OPTIONAL
			//Note: not implemented.
			$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : rrcConnectionReconfigurationComplete-r8 : nonCriticalExtension ';
		}
	}
	if($criticalExtensions_choice_bit=='1'){
		//criticalExtensionsFuture			SEQUENCE {}
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : criticalExtensionsFuture';
	}
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
	
	
}
function ueCapabilityInformation(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	$tabs=$tab;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : ueCapabilityInformation {';
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier,
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	//criticalExtensions
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : c1 {';
		//
		$c1_choice_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		switch($c1_choice_bit){
			case '000':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ueCapabilityInformation-r8 {';
				$tabs=$tabs . str_repeat($tab,3);
				UECapabilityInformation_r8_IEs();
				$tabs=substr($tabs,0,-12);
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
				break;
			case '001':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare7 NULL';
				break;
			case '010':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare6 NULL';
				break;
			case '011':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare5 NULL';
				break;
			case '100':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare4 NULL';
				break;
			case '101':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare3 NULL';
				break;
			case '110':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare2 NULL';
				break;
			case '111':
				$rrc_out= $rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare1 NULL';
				break;
			default:
				break;
		}
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : criticalExtensionsFuture';
	}
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}
function UECapabilityInformation_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	/*
	UECapabilityInformation-r8-IEs ::=	SEQUENCE {
	ue-CapabilityRAT-ContainerList		UE-CapabilityRAT-ContainerList,
	nonCriticalExtension				UECapabilityInformation-v8a0-IEs							OPTIONAL
	}
	*/
	//check option
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//ue-CapabilityRAT-ContainerList		UE-CapabilityRAT-ContainerList,
	$rrc_out= $rrc_out . "\n" . $tabs . 'ue-CapabilityRAT-ContainerList {';
	$tabs=$tabs . $tab;
	UE_CapabilityRAT_ContainerList_1();
	$tabs=substr($tabs,0,-4);
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
	//nonCriticalExtension				UECapabilityInformation-v8a0-IEs							OPTIONAL
	if($nonCriticalExtension_option_bit=='1'){
		//
	}
}
function securityModeComplete(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	$tabs=$tab;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : securityModeComplete {';
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	
	//criticalExtensions
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : securityModeComplete-r8 {';
		//securityModeComplete-r8				SecurityModeComplete-r8-IEs
		//Note: not implemented. 
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		$rrc_out= $rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : criticalExtensionsFuture';
	}
	$rrc_out= $rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}
?>