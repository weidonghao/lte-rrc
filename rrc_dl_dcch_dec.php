<?php
//
include('rrc_fun.php');
$rrc_msg=$_POST["hexStr"];
//$rrc_msg="22 0B 14 97 33 BA 6E EC 33 27 63 0C DA 81 81 B0 1C A0 06 00 07 EB 00 29 F1 10 94 E9 4B C9 89 8F 2A A3 66 00 00 A2 02 00 04 61 02 00 00 00 00 00";
//$rrc_msg="22 16 15 E8 00 00 00 96 28 30 02 98 0D 38 F0 84 1E 1F 05 9C 78 89 E8 46 6F C0 20 00 01 00 50 01 20 00 B6 EE A0 E6 4F D4 7F AB 18 04 0E 84 02 5E 0C 40 85 E0 20 12 02 00 82 A4 03 82 02 0E 20 0C DE DC D8 D2 DC CA 0A E8 CA D8 D2 C2 04 E6 CA 0A 02 14 14 29 06 BC 0D FD FD 3D 3C 00 00 4E 37 01 00 42 14 04 00 00 15 02 0D 81 50 02 03 00 42 14 04 00 00 15 06 0D 81 50 02 04 A0 17 EC 85 E0 21 00 00 02 00 00 00 04 2E 58 B2 92 94 06 85 E0 20 68 08 06 3E 23 E4 C8 02 06 D4 E0 9F EC A0 20 FA 8D AC 31 4F 94 51 72 46 3A 21 29 D1 C6 32 40 2B 86 00 61 02 B5 F8";
//DLInformationTransfer
//$rrc_msg="0C 01 20 3A 90 15 6A B6 98 5E 2E CA BC B5 DA E0 B4 83 97 7C 14 90 84 BC E2 E7 D2 78 B5 C2 2B 94 D8 A6 07 2A C4 A4 50";
//$rrc_msg="0C 01 20 3A 90 03 1C D5 CD CA 24 1B EC 63 CF D7 DB A1 3E 0F D0 48 82 CF 90 87 47 B4 CF 2D 48 EF C3 0C 53 E5 C1 B3 F8";
//$rrc_msg="0C 00 81 B8 76 4A 55 E0 00 3A E8 10 00 2F 07 06 02 03 00";
//ueCapabilityEnquiry
//$rrc_msg="3C 00 00 00";
//securityModeCommand
//$rrc_msg="34 00 20 00";
//$rrc_msg="34 00 70";

//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);

//read 1 bit to check choice for BCCH-DL-SCH-MessageType
$DL_DCCH_msgtype_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($DL_DCCH_msgtype_bit=='1'){
	//echo 'Note: messageClassExtension decoding is supported so far!';
	exit('Note: messageClassExtension decoding is supported so far!');
}

//construct output string
$tab='    ';
$rrc_out='DL-DCCH-Message : {';
$rrc_out_end='}';
$tabs='';

//read 4 bites to check the message type of c1
$c1_type_bit=substr($rrc_msg_bin,0,4);
$rrc_msg_bin=substr($rrc_msg_bin,4);

switch($c1_type_bit){
	case '0000':
		CSFBParametersResponseCDMA2000();
		break;
	case '0001':
		DLInformationTransfer();
		break;
	case '0010':
		HandoverFromEUTRAPreparationRequest();
		break;
	case '0011':
		MobilityFromEUTRACommand();
		break;
	case '0100':
		RRCConnectionReconfiguration();
		break;
	case '0101':
		RRCConnectionRelease();
		break;
	case '0110':
		SecurityModeCommand();
		break;
	case '0111':
		UECapabilityEnquiry();
		break;
	case '1000':
		CounterCheck();
		break;
	case '1001':
		UEInformationRequest_r9();
		break;
	case '1010':
		LoggedMeasurementConfiguration_r10();
		break;
	case '1011':
		RNReconfiguration_r10();
		break; 
	default:
		break;
}

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

//functions start
function CSFBParametersResponseCDMA2000(){}
function DLInformationTransfer(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" .	$tabs . $tab . 'message c1: dlInformationTransfer {';
	$tabs = $tabs . str_repeat($tab,2);
	//DLInformationTransfer();
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" .	$tabs . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	//criticalExtensions
	$rrc_out=$rrc_out . "\n" .	$tabs . 'criticalExtensions {';
	//check choice
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		//c1
		$rrc_out=$rrc_out . "\n" .	$tabs . $tab . 'c1 {';
		//check choice
		$c1_choice_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($c1_choice_bit=='00'){
			//dlInformationTransfer-r8			DLInformationTransfer-r8-IEs
			$rrc_out=$rrc_out . "\n" .	$tabs . str_repeat($tab,2) . 'dlInformationTransfer-r8 {';
			$tabs=$tabs . str_repeat($tab,3);
			DLInformationTransfer_r8_IEs();	
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" .	$tabs . str_repeat($tab,2) . '}';		
		}
		if($c1_choice_bit=='01'){$rrc_out=$rrc_out . "\n" .	$tabs . str_repeat($tab,2) . 'spare3 NULL';}
		if($c1_choice_bit=='10'){$rrc_out=$rrc_out . "\n" .	$tabs . str_repeat($tab,2) . 'spare2 NULL';}
		if($c1_choice_bit=='11'){$rrc_out=$rrc_out . "\n" .	$tabs . str_repeat($tab,2) . 'spare1 NULL';}
		
		$rrc_out=$rrc_out . "\n" .	$tabs . $tab . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		//criticalExtensionsFuture			SEQUENCE {}
		$rrc_out=$rrc_out . "\n" .	$tabs . $tab . 'criticalExtensionsFuture';		
	}
	$rrc_out=$rrc_out . "\n" .	$tabs . '}';
	$tabs=substr($tabs,0,-8);
	$rrc_out=$rrc_out . "\n" .	$tabs . $tab . '}';
	
	
}
function HandoverFromEUTRAPreparationRequest() {}
function MobilityFromEUTRACommand(){}
function RRCConnectionReconfiguration(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//
	$tabs = $tabs . $tab;
	$rrc_out=$rrc_out . "\n" .	$tabs . 'message c1: RRCConnectionReconfiguration : {';
	$rrc_out_end="\n" . $tabs . '}' . "\n" . $rrc_out_end;

	$tabs = $tabs . $tab;
	//read two bits for RRC-TransactionIdentifier
	$RRC_TranId=RRC_TransactionIdentifier(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . $tabs . 'rrc-TransactionIdentifier ' . $RRC_TranId . ',';
	
	//criticalExtensions start
	$rrc_out=$rrc_out . "\n" . $tabs . 'criticalExtensions {'; 
	
	//read one bit to check criticalExtensions
	$criticalExt_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExt_bit=='1') {
		//echo 'Note: Decoding for criticalExtensionsFuture is not supported!';
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensionsFuture	{}';
	}
	if($criticalExt_bit=='0') {
		$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		if($criticalExtensions_choice_bit=='000'){
			$tabs=$tabs . $tab;
			$rrc_out=$rrc_out . "\n" . $tabs . 'c1 : rrcConnectionReconfiguration-r8 {';
			RRCConnectionReconfiguration_r8_IEs();
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			$tabs=substr($tabs,0,-4);
		}
		else {
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'c1 : spare {}';
		}
	}
	
	//criticalExtensions end
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//
	$tabs=substr($tabs,0,-8);
}
function RRCConnectionRelease(){}
function SecurityModeCommand(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=$tab;	
	$rrc_out=$rrc_out . "\n" . $tabs . 'message : c1 : securityModeCommand {';
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	//criticalExtensions
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : c1 {';
		$c1_choice_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($c1_choice_bit=='00'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'securityModeCommand-r8 {';
			//
			$tabs=$tabs . str_repeat($tab,3);
			SecurityModeCommand_r8_IEs();
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		if($c1_choice_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare3';}
		if($c1_choice_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare2';}
		if($c1_choice_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare1';}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : criticalExtensionsFuture';
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}
function UECapabilityEnquiry(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=$tab;	
	$rrc_out=$rrc_out . "\n" . $tabs . 'message : c1 : ueCapabilityEnquiry {';
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	
	//criticalExtensions
	//check choice for criticalExtensions
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : c1 {';
		//check choice for c1
		$c1_choice_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($c1_choice_bit=='00'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ueCapabilityEnquiry-r8 {';
			//
			$tabs=$tabs . str_repeat($tab,3);
			UECapabilityEnquiry_r8_IEs();
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		if($c1_choice_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare3';}
		if($c1_choice_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare2';}
		if($c1_choice_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'spare1';}
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($criticalExtensions_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensions : criticalExtensionsFuture';
	}
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	
	$tabs=substr($tabs,0,-4);
}
function CounterCheck(){}
function UEInformationRequest_r9(){}
function LoggedMeasurementConfiguration_r10(){}
function RNReconfiguration_r10(){}

//RRCConnectionReconfiguration-r8-IEs
function RRCConnectionReconfiguration_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check options
	$measConfig_option_bit=substr($rrc_msg_bin,0,1);
	$mobilityControlInfo_option_bit=substr($rrc_msg_bin,1,1);
	$dedicatedInfoNASList_option_bit=substr($rrc_msg_bin,2,1);
	$radioResourceConfigDedicated_option_bit=substr($rrc_msg_bin,3,1);
	$securityConfigHO_option_bit=substr($rrc_msg_bin,4,1);
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,5,1);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	
	
	if($measConfig_option_bit=='1'){
		MeasConfig_1();
	}
	
	if($mobilityControlInfo_option_bit=='1'){
		//mobilityControlInfo
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mobilityControlInfo {';
		$tabs=$tabs . str_repeat($tab,1);
		MobilityControlInfo_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($dedicatedInfoNASList_option_bit=='1'){
		//dedicatedInfoNASList
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dedicatedInfoNASList {';
		$maxDRB=11;
		$dedicatedInfoNASList_size=bindec(substr($rrc_msg_bin,0,4))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		while($dedicatedInfoNASList_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . DedicatedInfoNAS_1();
			//DedicatedInfoNAS ::=		OCTET STRING
			
			$dedicatedInfoNASList_size=$dedicatedInfoNASList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//radioResourceConfigDedicated		RadioResourceConfigDedicated	OPTIONAL
	if($radioResourceConfigDedicated_option_bit=='1'){
		//radioResourceConfigDedicated();
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'radioResourceConfigDedicated {';
		$tabs=$tabs . str_repeat($tab,2);
		RadioResourceConfigDedicated_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';		
	}
	if($securityConfigHO_option_bit=='1'){
		//SecurityConfigHO();
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'securityConfigHO {';
		$tabs=$tabs . str_repeat($tab,2);
		SecurityConfigHO_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';	
	}
	if($nonCriticalExtension_option_bit=='1'){
		//RRCConnectionReconfiguration_v890_IEs();
		//2013-2-23: not implemented.
		
	}
}
/*
function DLInformationTransfer(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//rrc-TransactionIdentifier			RRC-TransactionIdentifier
	$rrc_TransactionIdentifier_value=bindec(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" .	$tabs . 'rrc-TransactionIdentifier ' . $rrc_TransactionIdentifier_value;
	//criticalExtensions
}
*/
function DLInformationTransfer_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
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
	
	//nonCriticalExtension				DLInformationTransfer-v8a0-IEs		OPTIONAL
	if($nonCriticalExtension_option_bit=='1'){
	}
}
function UECapabilityEnquiry_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check option for nonCriticalExtension
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//ue-CapabilityRequest				UE-CapabilityRequest,
	//UE-CapabilityRequest ::=		SEQUENCE (SIZE (1..maxRAT-Capabilities)) OF RAT-Type
	//maxRAT-Capabilities			INTEGER ::= 8
	$rrc_out=$rrc_out . "\n" . $tabs . 'ue-CapabilityRequest {';
	$UE_CapabilityRequest_size=bindec(substr($rrc_msg_bin,0,3))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,3);	
	while($UE_CapabilityRequest_size>0){
		//RAT-Type
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . RAT_Type_1();
		$UE_CapabilityRequest_size=$UE_CapabilityRequest_size-1;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//nonCriticalExtension				UECapabilityEnquiry-v8a0-IEs							OPTIONAL
	//Note: not implemented.
	if($nonCriticalExtension_option_bit=='1'){
	
	}
}
function SecurityModeCommand_r8_IEs(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check option
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//securityConfigSMC					SecurityConfigSMC,
	$rrc_out=$rrc_out . "\n" . $tabs . 'securityConfigSMC {';
	//check extention
	$SecurityConfigSMC_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//securityAlgorithmConfig					SecurityAlgorithmConfig
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'securityAlgorithmConfig {';
	$tabs=$tabs . str_repeat($tab,2);
	SecurityAlgorithmConfig_1();
	$tabs=substr($tabs,0,-8);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//nonCriticalExtension				SecurityModeCommand-v8a0-IEs							OPTIONAL
	if($nonCriticalExtension_option_bit=='1'){}
}
?>