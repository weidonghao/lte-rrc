<?php
//2013-1-12, haoweidong, decode RRC connection setup message with 3GPP-36331-b00
/*
-- ASN1START

DL-CCCH-Message ::= SEQUENCE {
	message					DL-CCCH-MessageType
}

DL-CCCH-MessageType ::= CHOICE {
	c1						CHOICE {
		rrcConnectionReestablishment			RRCConnectionReestablishment,
		rrcConnectionReestablishmentReject		RRCConnectionReestablishmentReject,
		rrcConnectionReject						RRCConnectionReject,
		rrcConnectionSetup						RRCConnectionSetup
	},
	messageClassExtension	SEQUENCE {}
}

-- ASN1STOP
*/

//$rrc_msg=$_GET["q"];
$rrc_msg=$_POST["hexStr"];

//echo 'hello';
//$rrc_msg='70 12 9B 26 5C D0 44 D6 44 CC 02 04 C0';

//$rrc_msg="70 12 98 13 FD 94 04 9A 73 05 97 2B 21 0C 3F 0C 00 C2 01 6B F4 0D 8E C0";

//rrc_msg="70 12 98 13 FD 94 04 9A 73 05 97 2B 21 0C 3F 0C 00 C1 B1 6B F4 0D 8C 40";

//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);

//read one bit, to check CHOICE type for this DL-CCCH
//c1=0, messageClassExtension=1
$DL_CCCH_msgtype_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($DL_CCCH_msgtype_bit=='1'){
	//echo 'Note: messageClassExtension decoding is supported so far!';
	exit('Note: messageClassExtension decoding is supported so far!');
}
//construct output string
$tab='    ';
$rrc_out='DL-CCCH-Message : {';
$rrc_out_end='}';

//read two bites to check the message type of c1
$c1_type_bit=substr($rrc_msg_bin,0,2);
$rrc_msg_bin=substr($rrc_msg_bin,2);
switch ($c1_type_bit)
{
	case '00': //rrcConnectionReestablishment
		break;
	case '01':
		break;
	case '10':
		break;
	case '11':
		rrcConnectionSetup();
		break;
	default:
		break;
}

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

//procedure end here.

//functions definition
function my_hex2bin($hex_in)
{
$ret='';
do {
	$temp=substr($hex_in,0,1);
	$hex_in=substr($hex_in,1);
	$ret=$ret . my_hex2bin_one($temp);
} while(strlen($hex_in)>0);

return $ret;
}

function my_hex2bin_one($hex_in)
{
	$ret='';
	switch ($hex_in)
	{
		case '0':
			$ret='0000';
			break;
		case '1':
			$ret='0001';
			break;
		case '2':
			$ret='0010';
			break;
		case '3':
			$ret='0011';
			break;
		case '4':
			$ret='0100';
			break;
		case '5':
			$ret='0101';
			break;
		case '6':
			$ret='0110';
			break;
		case '7':
			$ret='0111';
			break;
		case '8':
			$ret='1000';
			break;
		case '9':
			$ret='1001';
			break;
		case 'a':
			$ret='1010';
			break;
		case 'A':
			$ret='1010';
			break;
		case 'b':
			$ret='1011';
			break;
		case 'B':
			$ret='1011';
			break;
		case 'c':
			$ret='1100';
			break;
		case 'C':
			$ret='1100';
			break;
		case 'd':
			$ret='1101';
			break;
		case 'D':
			$ret='1101';
			break;
		case 'e':
			$ret='1110';
			break;
		case 'E':
			$ret='1110';
			break;
		case 'f':
			$ret='1111';
			break;
		case 'F':
			$ret='1111';
			break;
		default:
			break;
			
	}
	return $ret;
}

function rrcConnectionSetup()
{
/*
	-- ASN1START

RRCConnectionSetup ::=				SEQUENCE {
	rrc-TransactionIdentifier			RRC-TransactionIdentifier,
	criticalExtensions					CHOICE {
		c1									CHOICE {
			rrcConnectionSetup-r8				RRCConnectionSetup-r8-IEs,
			spare7 NULL,
			spare6 NULL, spare5 NULL, spare4 NULL,
			spare3 NULL, spare2 NULL, spare1 NULL
		},
		criticalExtensionsFuture			SEQUENCE {}
	}
}

RRCConnectionSetup-r8-IEs ::=		SEQUENCE {
	radioResourceConfigDedicated		RadioResourceConfigDedicated,
	nonCriticalExtension				RRCConnectionSetup-v8a0-IEs							OPTIONAL
}

RRCConnectionSetup-v8a0-IEs ::= SEQUENCE {
	lateNonCriticalExtension			OCTET STRING						OPTIONAL,	-- Need OP
	nonCriticalExtension				SEQUENCE {}							OPTIONAL	-- Need OP
}

-- ASN1STOP

*/

/*
-- ASN1START

RRC-TransactionIdentifier ::=		INTEGER (0..3)

-- ASN1STOP

*/
	
	global $rrc_out,$rrc_out_end,$tab;
	global $rrc_msg_bin;
	
	
	$rrc_out= $rrc_out . "\n" . $tab . 'message c1 : rrcConnectionSetup : ' . '{';
	$rrc_out_end= $tab . '}' . "\n" . $rrc_out_end;
	
	//read two bits for RRC-TransactionIdentifier
	$RRC_TranId=RRC_TransactionIdentifier(substr($rrc_msg_bin,0,2));
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'rrc-TransactionIdentifier ' . $RRC_TranId . ',';
	
	//read one bit to check criticalExtensions
	$criticalExt_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExt_bit=='1') {
		echo 'Note: Decoding for criticalExtensionsFuture is not supported!';
	}
	else {
		//read three bits to check the choice for c1
		$c1_choice_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		switch ($c1_choice_bit) {
			case '000':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : rrcConnectionSetup-r8 : {' . "\n";
				//decode rrcConnectionSetup-r8
				//read one bit to check if nonCriticalExtension exists
				$rrcConnectionSetup_r8_option=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);				
				$rrc_out_end = str_repeat($tab,2) . '}' . "\n" . $rrc_out_end;
				radioResourceConfigDedicated();				
				break;
			case '001':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare7' . "\n";
				break;
			case '010':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare6' . "\n";
				break;
			case '011':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare5' . "\n";
				break;
			case '100':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare4' . "\n";
				break;
			case '101':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare3' . "\n";
				break;
			case '110':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare2' . "\n";
				break;
			case '111':
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'criticalExtensions c1 : spare1' . "\n";
				break;		
			default:
				break;
		}
		
		
	}
	
}

function RRC_TransactionIdentifier($in){
	switch ($in){
		case '00':
			$ret='0';
			break;
		case '01':
			$ret='1';
			break;
		case '10':
			$ret='2';
			break;
		case '11':
			$ret='3';
			break;
		default:
			break;	
	}
	return $ret;
}

function radioResourceConfigDedicated() {
/*
-- ASN1START

RadioResourceConfigDedicated ::=		SEQUENCE {
	srb-ToAddModList					SRB-ToAddModList			OPTIONAL, 		-- Cond HO-Conn
	drb-ToAddModList					DRB-ToAddModList			OPTIONAL, 		-- Cond HO-toEUTRA
	drb-ToReleaseList					DRB-ToReleaseList			OPTIONAL, 		-- Need ON
	mac-MainConfig						CHOICE {
			explicitValue					MAC-MainConfig,
			defaultValue					NULL
	}		OPTIONAL,																-- Cond HO-toEUTRA2
	sps-Config							SPS-Config 					OPTIONAL,		-- Need ON
	physicalConfigDedicated				PhysicalConfigDedicated		OPTIONAL,		-- Need ON
	...,
	[[	rlf-TimersAndConstants-r9			RLF-TimersAndConstants-r9		OPTIONAL	-- Need ON
	]],
	[[	measSubframePatternPCell-r10	MeasSubframePatternPCell-r10		OPTIONAL	-- Need ON
	]]
}

RadioResourceConfigDedicatedSCell-r10 ::=	SEQUENCE {
	-- UE specific configuration extensions applicable for an SCell
	physicalConfigDedicatedSCell-r10		PhysicalConfigDedicatedSCell-r10	OPTIONAL,	-- Need ON
	...
}

SRB-ToAddModList ::=				SEQUENCE (SIZE (1..2)) OF SRB-ToAddMod

SRB-ToAddMod ::=	SEQUENCE {
	srb-Identity						INTEGER (1..2),
	rlc-Config							CHOICE {
		explicitValue						RLC-Config,
		defaultValue						NULL
	}		OPTIONAL,																-- Cond Setup
	logicalChannelConfig				CHOICE {
		explicitValue						LogicalChannelConfig,
		defaultValue						NULL
	}		OPTIONAL,																-- Cond Setup
	...
}

DRB-ToAddModList ::=				SEQUENCE (SIZE (1..maxDRB)) OF DRB-ToAddMod

DRB-ToAddMod ::=	SEQUENCE {
	eps-BearerIdentity					INTEGER (0..15)			OPTIONAL,		-- Cond DRB-Setup
	drb-Identity						DRB-Identity,
	pdcp-Config							PDCP-Config				OPTIONAL,		-- Cond PDCP
	rlc-Config							RLC-Config				OPTIONAL,		-- Cond Setup
	logicalChannelIdentity				INTEGER (3..10)			OPTIONAL,		-- Cond DRB-Setup
	logicalChannelConfig				LogicalChannelConfig	OPTIONAL,		-- Cond Setup
	...
}

DRB-ToReleaseList ::=				SEQUENCE (SIZE (1..maxDRB)) OF DRB-Identity

MeasSubframePatternPCell-r10 ::=		CHOICE {
	release								NULL,
	setup							MeasSubframePattern-r10
}

-- ASN1STOP

*/	
global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;

$rrc_out = $rrc_out . str_repeat($tab,3) . 'radioResourceConfigDedicated {';
$rrc_out_end=str_repeat($tab,3) . '}' . "\n" . $rrc_out_end;

$ext_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($ext_bit=='1'){
	echo "Note: extension for radioResourceConfigDedicated is not supported!";
}
//check optional fields indication
$srb_ToAddModList_option_bit=substr($rrc_msg_bin,0,1);
$drb_ToAddModList_option_bit=substr($rrc_msg_bin,1,1);
$drb_ToReleaseList_option_bit=substr($rrc_msg_bin,2,1);
$mac_MainConfig_option_bit=substr($rrc_msg_bin,3,1);
$sps_Config_option_bit=substr($rrc_msg_bin,4,1);
$physicalConfigDedicated_option_bit=substr($rrc_msg_bin,5,1);
$rrc_msg_bin=substr($rrc_msg_bin,6);

if($srb_ToAddModList_option_bit=='1'){
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'srb-ToAddModList {' . "\n";
	//$rrc_out_end="\n" . str_repeat($tab,4) . '},' . "\n" . $rrc_out_end;
	
	$srb_ToAddModList_size_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check the size of srb-ToAddModList
	SRB_ToAddMod();
	if($srb_ToAddModList_size_bit=='1') {
		SRB_ToAddMod();
	}
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . '},';
}

//2012-1-14 17:10 stop here
//drb_ToAddModList will not exist in RRC setup, the implementation will be supported later. 
//if($drb_ToAddModList_option_bit=='1'){
//	
//}
/*
echo $srb_ToAddModList_option_bit;
echo $drb_ToAddModList_option_bit;
echo $drb_ToReleaseList_option_bit;
echo $mac_MainConfig_option_bit;
echo $sps_Config_option_bit;
echo $physicalConfigDedicated_option_bit;
echo "\n";
*/
//MAC-MainConfig
if($mac_MainConfig_option_bit=="1"){
	//check choice of mac-MainConfig
	$mac_MainConfig_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//echo 'mac_MainConfig_choice ' . $mac_MainConfig_choice_bit . "\n";
	if($mac_MainConfig_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'mac-MainConfig defaultValue : NULL' . "\n";
	}
	if($mac_MainConfig_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'mac-MainConfig explicitValue {' ;
		//echo 'msg length:' . strlen($rrc_msg_bin) . "\n";
		MAC_MainConfig();
	}
	//echo 'msg length:' . strlen($rrc_msg_bin) . "\n";
	
	//close mac_MainConfig
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . '},';
}

//echo $sps_Config_option_bit;
//echo $physicalConfigDedicated_option_bit;

//Note: 2013-1-16, sps-Config, not code at this time

//physicalConfigDedicated
if($physicalConfigDedicated_option_bit=='1'){
	//check extension
	$physicalConfigDedicated_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options, 10 options
	$pdsch_ConfigDedicated_option_bit=substr($rrc_msg_bin,0,1);
	$pucch_ConfigDedicated_option_bit=substr($rrc_msg_bin,1,1);
	$pusch_ConfigDedicated_option_bit=substr($rrc_msg_bin,2,1);
	$uplinkPowerControlDedicated_option_bit=substr($rrc_msg_bin,3,1);
	$tpc_PDCCH_ConfigPUCCH_option_bit=substr($rrc_msg_bin,4,1);
	$tpc_PDCCH_ConfigPUSCH_option_bit=substr($rrc_msg_bin,5,1);
	$cqi_ReportConfig_option_bit=substr($rrc_msg_bin,6,1);
	$soundingRS_UL_ConfigDedicated_option_bit=substr($rrc_msg_bin,7,1);
	$antennaInfo_option_bit=substr($rrc_msg_bin,8,1);
	$schedulingRequestConfig_option_bit=substr($rrc_msg_bin,9,1);
	$rrc_msg_bin=substr($rrc_msg_bin,10);
	
	//physicalConfigDedicated 
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'physicalConfigDedicated  {' ;
	
	//check PDSCH-ConfigDedicated, read 3 bits
	if($pdsch_ConfigDedicated_option_bit=='1'){
		$pdsch_ConfigDedicated_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$pdsch_ConfigDedicated_value=pdsch_ConfigDedicated($pdsch_ConfigDedicated_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'PDSCH-ConfigDedicated {' ;
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'p-a ' . $pdsch_ConfigDedicated_value;
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '},';
	}
	//pucch-ConfigDedicated p179, PUCCH-Config
	if($pucch_ConfigDedicated_option_bit=='1'){
		//read one bit to check PUCCH-ConfigDedicated option
		$tdd_AckNackFeedbackMode_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'PUCCH-ConfigDedicated {' ;
		
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'ackNackRepetition {' ;
		
		//read one bit to check ackNackRepetition choice
		$ackNackRepetition_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);		
		
		if($ackNackRepetition_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'release NULL,';
		}
		if($ackNackRepetition_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'setup {';
			
			//repetitionFactor
			//read 2 bits
			$repetitionFactor_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$repetitionFactor_value=repetitionFactor($repetitionFactor_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'repetitionFactor ' . $repetitionFactor_value . ',';
					
			
			//n1PUCCH-AN-Rep
			//read 11 bits
			$n1PUCCH_AN_Rep_bit=substr($rrc_msg_bin,0,11);
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			$n1PUCCH_AN_Rep_value=bindec($n1PUCCH_AN_Rep_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'n1PUCCH-AN-Rep ' . $n1PUCCH_AN_Rep_value;			
			
			//close setup
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '}';
		}
		
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '}' ;
		//
		if($tdd_AckNackFeedbackMode_option_bit=='1'){
			$tdd_AckNackFeedbackMode_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			$tdd_AckNackFeedbackMode_value=tdd_AckNackFeedbackMode($tdd_AckNackFeedbackMode_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'tdd_AckNackFeedbackMode ' . $tdd_AckNackFeedbackMode_value;
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}' ;
	}
	
	if($pusch_ConfigDedicated_option_bit=='1'){
	/*
	PUSCH-ConfigDedicated ::=			SEQUENCE {
	betaOffset-ACK-Index				INTEGER (0..15),
	betaOffset-RI-Index					INTEGER (0..15),
	betaOffset-CQI-Index				INTEGER (0..15)
	}
	*/
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'pusch-ConfigDedicated  {' ;
		//read 4 bites for betaOffset-ACK-Index
		$betaOffset_ACK_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_ACK_Index_value=bindec($betaOffset_ACK_Index_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'betaOffset-ACK-Index ' . $betaOffset_ACK_Index_value . ",";
		
		//read 4 bits for betaOffset-RI-Index
		$betaOffset_RI_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_RI_Index_value=bindec($betaOffset_RI_Index_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'betaOffset-RI-Index ' . $betaOffset_RI_Index_value . ",";
	
		//read 4 bits for betaOffset-RI-Index
		$betaOffset_CQI_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_CQI_Index_value=bindec($betaOffset_CQI_Index_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'betaOffset-CQI-Index ' . $betaOffset_CQI_Index_value ;
		
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '},' ;
	}
	
	if($uplinkPowerControlDedicated_option_bit=='1'){
	/*
	UplinkPowerControlDedicated ::=		SEQUENCE {
	p0-UE-PUSCH							INTEGER (-8..7),
	deltaMCS-Enabled					ENUMERATED {en0, en1},
	accumulationEnabled					BOOLEAN,
	p0-UE-PUCCH							INTEGER (-8..7),
	pSRS-Offset							INTEGER (0..15),
	filterCoefficient					FilterCoefficient					DEFAULT fc4
	}
	*/
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'UplinkPowerControlDedicated  {' ;
		
		//read 1 bit to check if DEFAULT is used for filterCoefficient		
		$filterCoefficient_default_bit = substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		//read 4 bits for p0-UE-PUSCH
		$p0_UE_PUSCH_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$p0_UE_PUSCH_value=bindec($p0_UE_PUSCH_bit)-8;
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'p0-UE-PUSCH ' . $p0_UE_PUSCH_value . ',' ;
		//echo $p0_UE_PUSCH_bit;
		//echo substr($rrc_msg_bin,0,4);
		
		//read one bit for deltaMCS-Enabled
		$deltaMCS_Enabled_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$deltaMCS_Enabled_value=deltaMCS_Enabled($deltaMCS_Enabled_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'deltaMCS-Enabled ' . $deltaMCS_Enabled_value . ',' ;
		
		//read 1 bit for accumulationEnabled	
		$accumulationEnabled_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$accumulationEnabled_value=accumulationEnabled($accumulationEnabled_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'accumulationEnabled ' . $accumulationEnabled_value . ',' ;
		
		//read 4 bits for p0-UE-PUCCH
		$p0_UE_PUCCH_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$p0_UE_PUCCH_value=p0_UE_PUCCH($p0_UE_PUCCH_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'p0_UE_PUCCH ' . $p0_UE_PUCCH_value . ',' ;
		
		
		//read 4 bits for pSRS-Offset
		$pSRS_Offset_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$pSRS_Offset_value=pSRS_Offset($pSRS_Offset_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'pSRS_Offset ' . $pSRS_Offset_value . ',' ;
		
		//
		if($filterCoefficient_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'filterCoefficient   fc4 '  ;
		}
		if($filterCoefficient_default_bit=='1'){
			
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}' ;
	}
	
	if($tpc_PDCCH_ConfigPUCCH_option_bit=='1'){
		exit('tpc_PDCCH_ConfigPUCCH decoding is not supported!');
	}
	
	if($tpc_PDCCH_ConfigPUSCH_option_bit=='1'){
		exit('tpc_PDCCH_ConfigPUSCH decoding is not supported!');
	}
	
	if($cqi_ReportConfig_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'cqi-ReportConfig  {' ;
		
		$cqi_ReportModeAperiodic_option_bit=substr($rrc_msg_bin,0,1);
		$cqi_ReportPeriodic_option_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($cqi_ReportModeAperiodic_option_bit=='1'){
			$cqi_ReportModeAperiodic_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$cqi_ReportModeAperiodic_value=cqi_ReportModeAperiodic($cqi_ReportModeAperiodic_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'cqi_ReportModeAperiodic ' . $cqi_ReportModeAperiodic_value . ',';
		}
		
		//nomPDSCH-RS-EPRE-Offset
		//read 3 bits
		$nomPDSCH_RS_EPRE_Offset_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$nomPDSCH_RS_EPRE_Offset_value=nomPDSCH_RS_EPRE_Offset($nomPDSCH_RS_EPRE_Offset_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'nomPDSCH_RS_EPRE_Offset ' . $nomPDSCH_RS_EPRE_Offset_value . ',';
		
		
		if($cqi_ReportPeriodic_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'cqi-ReportPeriodic {';
			$cqi_ReportPeriodic_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			if($cqi_ReportPeriodic_choice_bit=='0'){
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'release   NULL,';
			}
			if($cqi_ReportPeriodic_choice_bit=='1'){
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'setup {';
				//read 1 bit for ri-ConfigIndex option
				$ri_ConfigIndex_option_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				
				//cqi-PUCCH-ResourceIndex
				$cqi_PUCCH_ResourceIndex_bit=substr($rrc_msg_bin,0,11);
				$rrc_msg_bin=substr($rrc_msg_bin,11);
				$cqi_PUCCH_ResourceIndex_value=cqi_PUCCH_ResourceIndex($cqi_PUCCH_ResourceIndex_bit);
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'cqi-PUCCH-ResourceIndex ' . $cqi_PUCCH_ResourceIndex_value . ',';
				
				//cqi-pmi-ConfigIndex
				$cqi_pmi_ConfigIndex_bit=substr($rrc_msg_bin,0,10);
				$rrc_msg_bin=substr($rrc_msg_bin,10);
				$cqi_pmi_ConfigIndex_value=cqi_pmi_ConfigIndex($cqi_pmi_ConfigIndex_bit);
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'cqi-pmi-ConfigIndex ' . $cqi_pmi_ConfigIndex_value . ',';
				
				//read 1 bit to check cqi-FormatIndicatorPeriodic choice
				$cqi_FormatIndicatorPeriodic_choice_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'cqi-FormatIndicatorPeriodic {';
				
				if($cqi_FormatIndicatorPeriodic_choice_bit=='0'){
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,9) . 'widebandCQI  NULL';
				}
				if($cqi_FormatIndicatorPeriodic_choice_bit=='1'){
					//
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,9) . 'subbandCQI {';
					$k_bit=substr($rrc_msg_bin,0,2);
					$rrc_msg_bin=substr($rrc_msg_bin,2);
					$k_value=bindec($k_bit);
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,10) . 'k ' . $k_value;
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,9) . '}';
				}
				//close cqi-FormatIndicatorPeriodic
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . '}';
				
				//
				if($ri_ConfigIndex_option_bit=='1'){
					//read 10 bits for ri-ConfigIndex
					$ri_ConfigIndex_bit=substr($rrc_msg_bin,0,10);
					$rrc_msg_bin=substr($rrc_msg_bin,10);
					$ri_ConfigIndex_value=ri_ConfigIndex($ri_ConfigIndex_bit);
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'ri-ConfigIndex ' . $ri_ConfigIndex_value;
				}
				
				//simultaneousAckNackAndCQI
				//read 1 bit
				$simultaneousAckNackAndCQI_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				$simultaneousAckNackAndCQI_value=simultaneousAckNackAndCQI($simultaneousAckNackAndCQI_bit);
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'simultaneousAckNackAndCQI ' . $simultaneousAckNackAndCQI_value;
				
				//close
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '}';
			}
			
			//close
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '}';
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}' ;
	}
	
	if($soundingRS_UL_ConfigDedicated_option_bit=='1'){
		exit('note: soundingRS_UL_ConfigDedicated decoding is not supported at this time!!!');
	}
	
	if($antennaInfo_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'antennaInfo {';
		
		$antennaInfo_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($antennaInfo_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'AntennaInfoDedicated {';
			//read 1 bit to check if codebookSubsetRestriction option exist
			$codebookSubsetRestriction_option_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			//transmissionMode
			//read 3 bits
			$transmissionMode_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$transmissionMode_value=transmissionMode($transmissionMode_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'transmissionMode ' . $transmissionMode_value . ',';
			
			//codebookSubsetRestriction
			if($codebookSubsetRestriction_option_bit=='1'){
				//
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'codebookSubsetRestriction {' ;
				//read 3 bits to check codebookSubsetRestriction choice
				$codebookSubsetRestriction_choice_bit=substr($rrc_msg_bin,0,3);
				$rrc_msg_bin=substr($rrc_msg_bin,3);
				$codebookSubsetRestriction_value=codebookSubsetRestriction($codebookSubsetRestriction_choice_bit);
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . $codebookSubsetRestriction_value ;
				
				
				//close
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '}' ;
			}
			
			//ue-TransmitAntennaSelection
			//read 1 bit to check ue-TransmitAntennaSelection choice
			$ue_TransmitAntennaSelection_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'ue-TransmitAntennaSelection {' ;
			if($ue_TransmitAntennaSelection_choice_bit=='0'){
				$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'release NULL' ;
			}
			if($ue_TransmitAntennaSelection_choice_bit=='1'){
				//read 1 bit to check value for setup
				$ue_TransmitAntennaSelection_setup_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				if($ue_TransmitAntennaSelection_setup_bit=='0'){
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'setup closedLoop' ;
				}
				if($ue_TransmitAntennaSelection_setup_bit=='1'){
					$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'setup openLoop' ;
				}
			}
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '}' ;
			
			//close
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '}';
		}
		if($antennaInfo_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'defaultValue   NULL';
		}
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}';
	}
	
	if($schedulingRequestConfig_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'SchedulingRequestConfig {';
		//read 1 bit to check choice
		$schedulingRequestConfig_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($schedulingRequestConfig_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'release NULL';
		}
		if($schedulingRequestConfig_choice_bit=='1'){
			//
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'setup {';
			//sr-PUCCH-ResourceIndex	
			//read 11 bits
			$sr_PUCCH_ResourceIndex_bit=substr($rrc_msg_bin,0,11);
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'sr-PUCCH-ResourceIndex ' . bindec($sr_PUCCH_ResourceIndex_bit) . ',';
			
			//sr-ConfigIndex
			//read 8 bits
			$sr_ConfigIndex_bit=substr($rrc_msg_bin,0,8);
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'sr-ConfigIndex ' . bindec($sr_ConfigIndex_bit) . ',';
			
			//dsr-TransMax
			//read 3 bits
			$dsr_TransMax_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$dsr_TransMax_value=dsr_TransMax($dsr_TransMax_bit);
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'dsr-TransMax ' . $dsr_TransMax_value;
			
			
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '}';
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}';
	}
	//close physicalConfigDedicated 
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . '}';
}


}

function SRB_ToAddMod(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	$rrc_out=$rrc_out . str_repeat($tab,5) . '{';	
	//$rrc_out_end=str_repeat($tab,5) . '}' . $rrc_out_end;
	$SRB_ToAddMod_ext_bit=substr($rrc_msg_bin,0,1);
	//2013-1-14: not sure what need to do with this extension
	
	$rlc_config_option_bit=substr($rrc_msg_bin,1,1);
	$logicalChannelConfig_option_bit=substr($rrc_msg_bin,2,1);
	//srb_Identity
	$srb_Indentity_bit=substr($rrc_msg_bin,3,1);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	if($srb_Identity_bit='0'){
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'srb_Identity 1,';
	}
	if($srb_Identity_bit='1'){
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'srb_Identity 2,';
	}
	//
	//rlc-Config

	if($rlc_config_option_bit=='1'){
		$rlc_config_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($rlc_config_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'rlc-Config defaultValue : NULL,';
		}
		if($rlc_config_choice_bit=='0'){
			//echo 'Error: rlc-Config decoding is not supported!';
			//2012-1-13: check Yuanping if rlc-Config need to be supported.
			//exit('Fatal Error: rlc-Config decoding is not supported!');
			//read one bit to check if extension is used
			//$rlc_Config_ext_bit=substr($rrc_msg_bin,0,1);
			//read two bit to check which choice is used
			//$rlc_Config_choice_bit=susbtr($rrc_msg_bin,1,2);
			RLC_Config();			
		}
	}
	//logicalChannelConfig
	if($logicalChannelConfig_option_bit=='1'){
		//read one bit for choice
		$logicalChannelConfig_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($logicalChannelConfig_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'logicalChannelConfig defaultValue : NULL,';
		}
		if($logicalChannelConfig_choice_bit=='0'){
			LogicalChannelConfig();
		}
	}
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,5). '}';
}


function RLC_Config(){
/*
-- ASN1START

RLC-Config ::=				CHOICE {
	am									SEQUENCE {
		ul-AM-RLC							UL-AM-RLC,
		dl-AM-RLC							DL-AM-RLC
	},
	um-Bi-Directional					SEQUENCE {
		ul-UM-RLC							UL-UM-RLC,
		dl-UM-RLC							DL-UM-RLC
	},
	um-Uni-Directional-UL				SEQUENCE {
		ul-UM-RLC							UL-UM-RLC
	},
	um-Uni-Directional-DL				SEQUENCE {
		dl-UM-RLC							DL-UM-RLC
	},
	...
}

UL-AM-RLC ::=						SEQUENCE {
	t-PollRetransmit					T-PollRetransmit,
	pollPDU								PollPDU,
	pollByte							PollByte,
	maxRetxThreshold					ENUMERATED {
											t1, t2, t3, t4, t6, t8, t16, t32}
}

DL-AM-RLC ::=						SEQUENCE {
	t-Reordering						T-Reordering,
	t-StatusProhibit					T-StatusProhibit
}

UL-UM-RLC ::=						SEQUENCE {
	sn-FieldLength						SN-FieldLength
}

DL-UM-RLC ::=						SEQUENCE {
	sn-FieldLength						SN-FieldLength,
	t-Reordering						T-Reordering
}

SN-FieldLength ::=					ENUMERATED {size5, size10}

T-PollRetransmit ::=				ENUMERATED {
										ms5, ms10, ms15, ms20, ms25, ms30, ms35,
										ms40, ms45, ms50, ms55, ms60, ms65, ms70,
										ms75, ms80, ms85, ms90, ms95, ms100, ms105,
										ms110, ms115, ms120, ms125, ms130, ms135,
										ms140, ms145, ms150, ms155, ms160, ms165,
										ms170, ms175, ms180, ms185, ms190, ms195,
										ms200, ms205, ms210, ms215, ms220, ms225,
										ms230, ms235, ms240, ms245, ms250, ms300,
										ms350, ms400, ms450, ms500, spare9, spare8,
										spare7, spare6, spare5, spare4, spare3,
										spare2, spare1}

PollPDU ::=							ENUMERATED {
										p4, p8, p16, p32, p64, p128, p256, pInfinity}

PollByte ::=						ENUMERATED {
										kB25, kB50, kB75, kB100, kB125, kB250, kB375,
										kB500, kB750, kB1000, kB1250, kB1500, kB2000,
										kB3000, kBinfinity, spare1}

T-Reordering ::=					ENUMERATED {
										ms0, ms5, ms10, ms15, ms20, ms25, ms30, ms35,
										ms40, ms45, ms50, ms55, ms60, ms65, ms70,
										ms75, ms80, ms85, ms90, ms95, ms100, ms110,
										ms120, ms130, ms140, ms150, ms160, ms170,
										ms180, ms190, ms200, spare1}

T-StatusProhibit ::=				ENUMERATED {
										ms0, ms5, ms10, ms15, ms20, ms25, ms30, ms35,
										ms40, ms45, ms50, ms55, ms60, ms65, ms70,
										ms75, ms80, ms85, ms90, ms95, ms100, ms105,
										ms110, ms115, ms120, ms125, ms130, ms135,
										ms140, ms145, ms150, ms155, ms160, ms165,
										ms170, ms175, ms180, ms185, ms190, ms195,
										ms200, ms205, ms210, ms215, ms220, ms225,
										ms230, ms235, ms240, ms245, ms250, ms300,
										ms350, ms400, ms450, ms500, spare8, spare7,
										spare6, spare5, spare4, spare3, spare2,
										spare1}

-- ASN1STOP
	
*/
global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
//read one bit to check if extension is used
$rlc_Config_ext_bit=substr($rrc_msg_bin,0,1);
//read two bit to check which choice is used
$rlc_Config_choice_bit=substr($rrc_msg_bin,1,2);
$rrc_msg_bin=substr($rrc_msg_bin,3);
switch ($rlc_Config_choice_bit){
	case '00':
		//am
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'rlc-Config explicitValue : am : {';
		//$rrc_out_end=str_repeat($tab,6) . '}' . "\n" . $rrc_out_end;
		ul_AM_RLC();
		dl_AM_RLC();
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '},' ;
		break;
	case '01':
		//um-Bi-Directional
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'rlc-Config explicitValue : um-Bi-Directional : {';
		//$rrc_out_end=str_repeat($tab,6) . '}' . "\n" . $rrc_out_end;
		ul_UM_RLC();
		dl_UM_RLC();
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '},' ;
		break;
	case '10':
		//um-Uni-Directional-UL
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'rlc-Config explicitValue : um-Uni-Directional-UL : {';
		//$rrc_out_end=str_repeat($tab,6) . '}' . "\n" . $rrc_out_end;
		ul_UM_RLC();
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '},' ;
		break;
	case '11':
		//um-Uni-Directional-DL
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'rlc-Config explicitValue : um-Uni-Directional-DL : {';
		//$rrc_out_end=str_repeat($tab,6) . '}' . "\n" . $rrc_out_end;
		dl_UM_RLC();
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . '},' ;
		break;
	default:
		break;
}


}

function ul_AM_RLC(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'ul-AM-RLC {';
	//$rrc_out_end=str_repeat($tab,7) . '},' . "\n" . $rrc_out_end; 
	//$rrc_out=$rrc_out . "\n" . '},' ;
	//read 6 bits for t-PollRetransmit
	$t_PollRetransmit_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$t_PollRetransmit_value=t_PollRetransmit($t_PollRetransmit_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 't_PollRetransmit ' . $t_PollRetransmit_value . ',';
	//read 3 bits for pollPDU
	$pollPDU_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$pollPDU_value=pollPDU($pollPDU_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'pollPDU ' . $pollPDU_value . ',';
	
	//read 4 bits for pollByte
	$pollByte_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$pollByte_value=pollByte($pollByte_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'pollByte ' . $pollByte_value . ',';
	
	//read 3 bits for maxRetxThreshold
	$maxRetxThreshold_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$maxRetxThreshold_value=maxRetxThreshold($maxRetxThreshold_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'maxRetxThreshold ' . $maxRetxThreshold_value;
	
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '},';
}

function dl_AM_RLC(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'dl-AM-RLC {';
	//$rrc_out_end=str_repeat($tab,7) . '},' . "\n" . $rrc_out_end; 
	//t-Reordering	
	//read 5 bits for t-Reordering
	$t_Reordering_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$t_Reordering_value=t_Reordering($t_Reordering_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 't_Reordering ' . $t_Reordering_value . ',';
	//read 6 bits for t_StatusProhibit
	$t_StatusProhibit_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$t_StatusProhibit_value=t_StatusProhibit($t_StatusProhibit_bit);
	//$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 't_StatusProhibit ' . $t_StatusProhibit_bit ;
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 't_StatusProhibit ' . $t_StatusProhibit_value ;
	
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '},';
}

function ul_UM_RLC(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'ul-UM-RLC {';
	//read one bit for sn-FieldLength
	$sn_FieldLength=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$sn_FieldLength=sn_FieldLength($sn_FieldLength);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'sn-FieldLength ' . $t_StatusProhibit_value ;	
	//close 
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '},';
}

function dl_UM_RLC(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'dl-UM-RLC {';
	//read one bit for sn-FieldLength
	$sn_FieldLength=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$sn_FieldLength=sn_FieldLength($sn_FieldLength);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'sn-FieldLength ' . $t_StatusProhibit_value ;	
	
	//
	//read 5 bits for t-Reordering
	$t_Reordering_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$t_Reordering_value=t_Reordering($t_Reordering_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 't_Reordering ' . $t_Reordering_value ;
	//close
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . '},';
}

function t_PollRetransmit($in){
	switch ($in){
		case '000000':
			$ret='ms5';
			break;
		case '000001':
			$ret='ms10';
			break;
		case '000010':
			$ret='ms15';
			break;
		case '000011':
			$ret='ms20';
			break;
		case '000100':
			$ret='ms25';
			break;
		case '000101':
			$ret='ms30';
			break;
		case '000110':
			$ret='ms35';
			break;
		case '000111':
			$ret='ms40';
			break;
		case '001000':
			$ret='ms45';
			break;
		case '001001':
			$ret='ms50';
			break;
		case '001010':
			$ret='ms55';
			break;
		case '001011':
			$ret='ms60';
			break;
		case '001100':
			$ret='ms65';
			break;
		case '001101':
			$ret='ms70';
			break;
		case '001110':
			$ret='ms75';
			break;
		case '001111':
			$ret='ms80';
			break;
		case '010000':
			$ret='ms85';
			break;
		case '010001':
			$ret='ms90';
			break;
		case '010010':
			$ret='ms95';
			break;
		case '010011':
			$ret='ms100';
			break;
		case '010100':
			$ret='ms105';
			break;
		case '010101':
			$ret='ms110';
			break;
		case '010110':
			$ret='ms115';
			break;
		case '010111':
			$ret='ms120';
			break;
		case '011000':
			$ret='ms125';
			break;
		case '011001':
			$ret='ms130';
			break;
		case '011010':
			$ret='ms135';
			break;
		case '011011':
			$ret='ms140';
			break;
		case '011100':
			$ret='ms145';
			break;
		case '011101':
			$ret='ms150';
			break;
		case '011110':
			$ret='ms155';
			break;
		case '011111':
			$ret='ms160';
			break;
		case '100000':
			$ret='ms165';
			break;
		case '100001':
			$ret='ms170';
			break;
		case '100010':
			$ret='ms175';
			break;
		case '100011':
			$ret='ms180';
			break;
		case '100100':
			$ret='ms185';
			break;
		case '100101':
			$ret='ms190';
			break;
		case '100110':
			$ret='ms195';
			break;
		case '100111':
			$ret='ms200';
			break;
		case '101000':
			$ret='ms205';
			break;
		case '101001':
			$ret='ms210';
			break;
		case '101010':
			$ret='ms215';
			break;
		case '101011':
			$ret='ms220';
			break;
		case '101100':
			$ret='ms225';
			break;
		case '101101':
			$ret='ms230';
			break;
		case '101110':
			$ret='ms235';
			break;
		case '101111':
			$ret='ms240';
			break;
		case '110000':
			$ret='ms245';
			break;
		case '110001':
			$ret='ms250';
			break;
		case '110010':
			$ret='ms300';
			break;
		case '110011':
			$ret='ms350';
			break;
		case '110100':
			$ret='ms400';
			break;
		case '110101':
			$ret='ms450';
			break;
		case '110110':
			$ret='ms500';
			break;
		case '110111':
			$ret='spare9';
			break;
		case '111000':
			$ret='spare8';
			break;
		case '111001':
			$ret='spare7';
			break;
		case '111010':
			$ret='spare6';
			break;
		case '111011':
			$ret='spare5';
			break;
		case '111100':
			$ret='spare4';
			break;
		case '111101':
			$ret='spare3';
			break;
		case '111110':
			$ret='spare2';
			break;
		case '111111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function pollPDU($in){
	switch ($in){
		case '000':
			$ret='p4';
			break;
		case '001':
			$ret='p8';
			break;
		case '010':
			$ret='p16';
			break;
		case '011':
			$ret='p32';
			break;
		case '100':
			$ret='p64';
			break;
		case '101':
			$ret='p128';
			break;
		case '110':
			$ret='256';
			break;
		case '111':
			$ret='pInfinity';
			break;
		default:
			break;
	}
	return $ret;
}

function pollByte($in){
	switch($in){
		case '0000':
			$ret='kB25';
			break;
		case '0001':
			$ret='kB50';
			break;
		case '0010':
			$ret='kB75';
			break;
		case '0011':
			$ret='kB100';
			break;
		case '0100':
			$ret='kB125';
			break;
		case '0101':
			$ret='kB250';
			break;
		case '0110':
			$ret='kB375';
			break;
		case '0111':
			$ret='kB500';
			break;
		case '1000':
			$ret='kB750';
			break;
		case '1001':
			$ret='kB1000';
			break;
		case '1010':
			$ret='kB1250';
			break;
		case '1011':
			$ret='kB1500';
			break;
		case '1100':
			$ret='kB2000';
			break;
		case '1101':
			$ret='kB3000';
			break;
		case '1110':
			$ret='kBinfinity';
			break;
		case '1111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function maxRetxThreshold($in){
	switch ($in){
		case '000':
			$ret='t1';
			break;
		case '001':
			$ret='t2';
			break;	
		case '010':
			$ret='t3';
			break;		
		case '011':
			$ret='t4';
			break;
		case '100':
			$ret='t6';
			break;
		case '101':
			$ret='t8';
			break;		
		case '110':
			$ret='t16';
			break;		
		case '111':
			$ret='t32';
			break;
		default:
			break;
	}
	return $ret;
}

function t_Reordering($in) {
	switch ($in){
		case '00000':
			return 'ms0';
			break;
		case '00001':
			return 'ms5';
			break;
		case '00010':
			return 'ms10';
			break;
		case '00011':
			return 'ms15';
			break;
		case '00100':
			return 'ms20';
			break;
		case '00101':
			return 'ms25';
			break;
		case '00110':
			return 'ms30';
			break;
		case '00111':
			return 'ms35';
			break;
		case '01000':
			return 'ms40';
			break;
		case '01001':
			return 'ms45';
			break;
		case '01010':
			return 'ms50';
			break;
		case '01011':
			return 'ms55';
			break;
		case '01100':
			return 'ms60';
			break;
		case '01101':
			return 'ms65';
			break;
		case '01110':
			return 'ms70';
			break;
		case '01111':
			return 'ms75';
			break;
		case '10000':
			return 'ms80';
			break;
		case '10001':
			return 'ms85';
			break;
		case '10010':
			return 'ms90';
			break;
		case '10011':
			return 'ms95';
			break;
		case '10100':
			return 'ms100';
			break;
		case '10101':
			return 'ms110';
			break;
		case '10110':
			return 'ms120';
			break;
		case '10111':
			return 'ms130';
			break;
		case '11000':
			return 'ms140';
			break;
		case '11001':
			return 'ms150';
			break;
		case '11010':
			return 'ms160';
			break;
		case '11011':
			return 'ms170';
			break;
		case '11100':
			return 'ms180';
			break;
		case '11101':
			return 'ms190';
			break;
		case '11110':
			return 'ms200';
			break;
		case '11111':
			return 'spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function t_StatusProhibit($in){
	switch ($in) {
		case '000000':
			$ret='ms0';
			break;
		case '000001':
			$ret='ms5';
			break;
		case '000010':
			$ret='ms10';
			break;
		case '000011':
			$ret='ms15';
			break;
		case '000100':
			$ret='ms20';
			break;
		case '000101':
			$ret='ms25';
			break;
		case '000110':
			$ret='ms30';
			break;
		case '000111':
			$ret='ms35';
			break;
		case '001000':
			$ret='ms40';
			break;
		case '001001':
			$ret='ms45';
			break;
		case '001010':
			$ret='ms50';
			break;
		case '001011':
			$ret='ms55';
			break;
		case '001100':
			$ret='ms60';
			break;
		case '001101':
			$ret='ms65';
			break;
		case '001110':
			$ret='ms70';
			break;
		case '001111':
			$ret='ms75';
			break;
		case '010000':
			$ret='ms80';
			break;
		case '010001':
			$ret='ms85';
			break;
		case '010010':
			$ret='ms90';
			break;
		case '010011':
			$ret='ms95';
			break;
		case '010100':
			$ret='ms100';
			break;
		case '010101':
			$ret='ms105';
			break;
		case '010110':
			$ret='ms110';
			break;
		case '010111':
			$ret='ms115';
			break;
		case '011000':
			$ret='ms120';
			break;
		case '011001':
			$ret='ms125';
			break;
		case '011010':
			$ret='ms130';
			break;
		case '011011':
			$ret='ms135';
			break;
		case '011100':
			$ret='ms140';
			break;
		case '011101':
			$ret='ms145';
			break;
		case '011110':
			$ret='ms150';
			break;
		case '011111':
			$ret='ms155';
			break;
		case '100000':
			$ret='ms160';
			break;
		case '100001':
			$ret='ms165';
			break;
		case '100010':
			$ret='ms170';
			break;
		case '100011':
			$ret='ms175';
			break;
		case '100100':
			$ret='ms180';
			break;
		case '100101':
			$ret='ms185';
			break;
		case '100110':
			$ret='ms190';
			break;
		case '100111':
			$ret='ms195';
			break;
		case '101000':
			$ret='ms200';
			break;
		case '101001':
			$ret='ms205';
			break;
		case '101010':
			$ret='ms210';
			break;
		case '101011':
			$ret='ms215';
			break;
		case '101100':
			$ret='ms220';
			break;
		case '101101':
			$ret='ms225';
			break;
		case '101110':
			$ret='ms230';
			break;
		case '101111':
			$ret='ms235';
			break;
		case '110000':
			$ret='ms240';
			break;
		case '110001':
			$ret='ms245';
			break;
		case '110010':
			$ret='ms250';
			break;
		case '110011':
			$ret='ms300';
			break;
		case '110100':
			$ret='ms350';
			break;
		case '110101':
			$ret='ms400';
			break;
		case '110110':
			$ret='ms450';
			break;
		case '110111':
			$ret='ms500';
			break;
		case '111000':
			$ret='spare8';
			break;
		case '111001':
			$ret='spare7';
			break;
		case '111010':
			$ret='spare6';
			break;
		case '111011':
			$ret='spare5';
			break;
		case '111100':
			$ret='spare4';
			break;
		case '111101':
			$ret='spare3';
			break;
		case '111110':
			$ret='spare2';
			break;
		case '111111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function sn_FieldLength($in){
	switch ($in){
		case '0':
			$ret='size5';
			break;
		case '1':
			$ret='size10';
			break;
		default:
			break;		
	}
	return $ret;
}

function LogicalChannelConfig(){
/*
-- ASN1START

LogicalChannelConfig ::=			SEQUENCE {
	ul-SpecificParameters				SEQUENCE {
		priority							INTEGER (1..16),
		prioritisedBitRate					ENUMERATED {
												kBps0, kBps8, kBps16, kBps32, kBps64, kBps128,
												kBps256, infinity, kBps512-v1020, kBps1024-v1020,
												kBps2048-v1020, spare5, spare4, spare3, spare2,
												spare1},
		bucketSizeDuration					ENUMERATED {
												ms50, ms100, ms150, ms300, ms500, ms1000, spare2,
												spare1},
		logicalChannelGroup					INTEGER (0..3)			OPTIONAL			-- Need OR
	}		OPTIONAL,																	-- Cond UL
	...,
	[[	logicalChannelSR-Mask-r9			ENUMERATED {setup}		OPTIONAL		-- Cond SRmask
	]]
}

-- ASN1STOP

*/
global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
//read one bit to check if extension is used
$LogicalChannelConfig_ext_bit=substr($rrc_msg_bin,0,1);
//read one bit to check if optional field exists, ul-SpecificParameters
$ul_SpecificParameters_option_bit=substr($rrc_msg_bin,1,1);
$rrc_msg_bin=substr($rrc_msg_bin,2);
if($ul_SpecificParameters_option_bit=='1'){
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,6) . 'LogicalChannelConfig {' . "\n";
	$rrc_out_end=str_repeat($tab,6) . '}' . "\n" . $rrc_out_end;
	//
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,7) . 'ul-SpecificParameters {' . "\n";
	$rrc_out_end=str_repeat($tab,7) . '}' . "\n" . $rrc_out_end;
	
	//read one bit to check if logicalChannelGroup option exists
	$logicalChannelGroup_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//priority
	//read 4 bits for priority
	$priority_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$priority_value=priority($priority_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'priority ' . $priority_value . ',';
		
	//prioritisedBitRate	
	//read 4 bits for this field
	$prioritisedBitRate_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$prioritisedBitRate_value=prioritisedBitRate($prioritisedBitRate_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'prioritisedBitRate ' . $prioritisedBitRate_value . ',';
	
	//bucketSizeDuration	
	//read 3 bits
	$bucketSizeDuration_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$bucketSizeDuration_value=bucketSizeDuration($bucketSizeDuration_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'bucketSizeDuration ' . $bucketSizeDuration_value . ',';
	
	//logicalChannelGroup
	if($logicalChannelGroup_option_bit=='1'){
		//read 2 bits for logicalChannelGroup
		$logicalChannelGroup_bit=susbstr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		$logicalChannelGroup_value=logicalChannelGroup($logicalChannelGroup_bit);
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,8) . 'logicalChannelGroup ' . $logicalChannelGroup_value ;
		
	}
	
	
}

}

function priority($in){
	switch($in){
		case '0000':
			$ret='1';
			break;
		case '0001':
			$ret='2';
			break;
		case '0010':
			$ret='3';
			break;
		case '0011':
			$ret='4';
			break;
		case '0100':
			$ret='5';
			break;
		case '0101':
			$ret='6';
			break;
		case '0110':
			$ret='7';
			break;
		case '0111':
			$ret='8';
			break;
		case '1000':
			$ret='9';
			break;
		case '1001':
			$ret='10';
			break;
		case '1010':
			$ret='11';
			break;
		case '1011':
			$ret='12';
			break;
		case '1100':
			$ret='13';
			break;
		case '1101':
			$ret='14';
			break;
		case '1110':
			$ret='15';
			break;
		case '1111':
			$ret='16';
			break;
		default:
			break;
	}
	return $ret;
}

function prioritisedBitRate($in){
	switch ($in){
		case '0000':
			$ret='kBps0';
			break;
		case '0001':
			$ret='kBps8';
			break;
		case '0010':
			$ret='kBps16';
			break;
		case '0011':
			$ret='kBps32';
			break;
		case '0100':
			$ret='kBps64';
			break;
		case '0101':
			$ret='kBps128';
			break;
		case '0110':
			$ret='kBps256';
			break;
		case '0111':
			$ret='infinity';
			break;
		case '1000':
			$ret='kBps512-v1020';
			break;
		case '1001':
			$ret='kBps1024-v1020';
			break;
		case '1010':
			$ret='kBps2048-v1020';
			break;
		case '1011':
			$ret='spare5';
			break;
		case '1100':
			$ret='spare4';
			break;
		case '1101':
			$ret='spare3';
			break;
		case '1110':
			$ret='spare2';
			break;
		case '1111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function bucketSizeDuration($in){
	switch($in){
		case '000':
			$ret='ms50';
			break;
		case '001':
			$ret='ms100';
			break;
		case '010':
			$ret='ms150';
			break;
		case '011':
			$ret='ms300';
			break;
		case '100':
			$ret='ms500';
			break;
		case '101':
			$ret='ms1000';
			break;
		case '110':
			$ret='spare2';
			break;
		case '111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function logicalChannelGroup($in){
	switch ($in) {
		case '00':
			$ret='0';
			break;
		case '01':
			$ret='1';
			break;
		case '10':
			$ret='2';
			break;
		case '11':
			$ret='3';
			break;
		default:
			break;
			
	}
	return $ret;
}

function MAC_MainConfig(){
/*
-- ASN1START

MAC-MainConfig ::=					SEQUENCE {
	ul-SCH-Config						SEQUENCE {
		maxHARQ-Tx							ENUMERATED {
												n1, n2, n3, n4, n5, n6, n7, n8,
												n10, n12, n16, n20, n24, n28,
												spare2, spare1}		OPTIONAL,	-- Need ON
		periodicBSR-Timer					ENUMERATED {
												sf5, sf10, sf16, sf20, sf32, sf40, sf64, sf80,
												sf128, sf160, sf320, sf640, sf1280, sf2560,
												infinity, spare1}	OPTIONAL,	-- Need ON
		retxBSR-Timer						ENUMERATED {
												sf320, sf640, sf1280, sf2560, sf5120,
												sf10240, spare2, spare1},
		ttiBundling							BOOLEAN
	}																OPTIONAL, 	-- Need ON
	drx-Config							DRX-Config					OPTIONAL,	-- Need ON
	timeAlignmentTimerDedicated			TimeAlignmentTimer,
	phr-Config							CHOICE {
		release								NULL,
		setup								SEQUENCE {
			periodicPHR-Timer					ENUMERATED {sf10, sf20, sf50, sf100, sf200,
															sf500, sf1000, infinity},
			prohibitPHR-Timer					ENUMERATED {sf0, sf10, sf20, sf50, sf100,
																sf200, sf500, sf1000},
			dl-PathlossChange					ENUMERATED {dB1, dB3, dB6, infinity}
		}
	}																OPTIONAL,	-- Need ON
	...,
	[[	sr-ProhibitTimer-r9					INTEGER (0..7)			OPTIONAL	-- Need ON
	]],
	[[	mac-MainConfig-v1020				SEQUENCE {
			sCellDeactivationTimer-r10			ENUMERATED {
													rf2, rf4, rf8, rf16, rf32, rf64, rf128,
													spare}			OPTIONAL,	-- Need OP
			extendedBSR-Sizes-r10				ENUMERATED {setup}		OPTIONAL,	-- Need OR
			extendedPHR-r10						ENUMERATED {setup}		OPTIONAL	-- Need OR
		}															OPTIONAL	-- Need ON
	]]
}

DRX-Config ::=						CHOICE {
	release								NULL,
	setup								SEQUENCE {
		onDurationTimer						ENUMERATED {
												psf1, psf2, psf3, psf4, psf5, psf6,
												psf8, psf10, psf20, psf30, psf40,
												psf50, psf60, psf80, psf100,
												psf200},
		drx-InactivityTimer					ENUMERATED {
												psf1, psf2, psf3, psf4, psf5, psf6,
												psf8, psf10, psf20, psf30, psf40,
												psf50, psf60, psf80, psf100,
												psf200, psf300, psf500, psf750,
												psf1280, psf1920, psf2560, psf0-v1020,
												spare9, spare8, spare7, spare6,
												spare5, spare4, spare3, spare2,
												spare1},
		drx-RetransmissionTimer				ENUMERATED {
												psf1, psf2, psf4, psf6, psf8, psf16,
												psf24, psf33},
		longDRX-CycleStartOffset		CHOICE {
			sf10							INTEGER(0..9),
			sf20							INTEGER(0..19),
			sf32							INTEGER(0..31),
			sf40							INTEGER(0..39),
			sf64							INTEGER(0..63),
			sf80							INTEGER(0..79),
			sf128							INTEGER(0..127),
			sf160							INTEGER(0..159),
			sf256							INTEGER(0..255),
			sf320							INTEGER(0..319),
			sf512							INTEGER(0..511),
			sf640							INTEGER(0..639),
			sf1024							INTEGER(0..1023),
			sf1280							INTEGER(0..1279),
			sf2048							INTEGER(0..2047),
			sf2560							INTEGER(0..2559)
		},
		shortDRX							SEQUENCE {
			shortDRX-Cycle						ENUMERATED	{
													sf2, sf5, sf8, sf10, sf16, sf20,
													sf32, sf40, sf64, sf80, sf128, sf160,
													sf256, sf320, sf512, sf640},
			drxShortCycleTimer					INTEGER (1..16)
		}		OPTIONAL													-- Need OR
	}
}

-- ASN1STOP

*/
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab;
	//read one bit to check if extension exists
	$mac_MainConfig_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//echo 'mac_MainConfig extension : ' . $mac_MainConfig_ext_bit . "\n";
	
	//read three bits to check option fields
	$ul_SCH_Config_option_bit=substr($rrc_msg_bin,0,1);
	$drx_Config_option_bit=substr($rrc_msg_bin,1,1);
	$phr_Config_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	
	//echo 'ul-SCH-Config: ' .  $ul_SCH_Config_option_bit . "\n";
	if($ul_SCH_Config_option_bit=='1'){
	
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'ul-SCH-Config {';
		
		//read two bits to check option for maxHARQ-Tx and periodicBSR-Timer
		$maxHARQ_Tx_option_bit=substr($rrc_msg_bin,0,1);
		$periodicBSR_Timer_option_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($maxHARQ_Tx_option_bit=='1'){
			//read 4 bits to decode maxHARQ-Tx
			$maxHARQ_Tx_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$maxHARQ_Tx_value=maxHARQ_Tx($maxHARQ_Tx_bit);
			$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'maxHARQ-Tx ' . $maxHARQ_Tx_value . ',';
		}
		if($periodicBSR_Timer_option_bit=='1'){
			//$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'maxHARQ-Tx ' . $maxHARQ_Tx_value . ',';
			//red 4 bits for periodicBSR_Timer
			$periodicBSC_Timer_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$periodicBSC_Timer_value=periodicBSC_Timer($periodicBSC_Timer_bit);
			$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'periodicBSC-Timer ' . $periodicBSC_Timer_value . ',';
		}
		
		//retxBSR-Timer
		//read 3 bits
		$retxBSC_Timer_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$retxBSC_Timer_value=retxBSC_Timer($retxBSC_Timer_bit);
		$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'retxBSC-Timer ' . $retxBSC_Timer_value . ',';
		
		
		//ttiBundling
		//read one bit
		$ttiBundling_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$ttiBundling_value=ttiBundling($ttiBundling_bit);
		$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'ttiBundling ' . $ttiBundling_value;
		
		//
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '},';
		
	}
	
	//echo 'left string: ' . strlen($rrc_msg_bin);
	//echo 'DRX-Config option:' . $drx_Config_option_bit . "\n";
	
	//2013-1-15: need to talk with yuanping
	//2013-1-16: YP confirmed it is not used in RRCConnectionSetup
	if($drx_Config_option_bit=='1'){
		//read one bit to check choice 
		
	}
	
/*
-- ASN1START

TimeAlignmentTimer ::=					ENUMERATED {
												sf500, sf750, sf1280, sf1920, sf2560, sf5120,
												sf10240, infinity}
-- ASN1STOP

*/
	//timeAlignmentTimerDedicated process
	//read 3 bits
	
	$timeAlignmentTimer_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$timeAlignmentTimer_value=timeAlignmentTimer($timeAlignmentTimer_bit);
	$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'timeAlignmentTimerDedicated ' . $timeAlignmentTimer_value;
	
	
	////2013-1-16: YP confirmed it is not used in RRCConnectionSetup
	if($phr_Config_option_bit=='1'){
	
	}
	
	
	
}

function maxHARQ_Tx($in){
	switch ($in){
		case '0000':
			$ret='n1';
			break;
		case '0001':
			$ret='n2';
			break;
		case '0010':
			$ret='n3';
			break;
		case '0011':
			$ret='n4';
			break;
		case '0100':
			$ret='n5';
			break;
		case '0101':
			$ret='n6';
			break;
		case '0110':
			$ret='n7';
			break;
		case '0111':
			$ret='n8';
			break;
		case '1000':
			$ret='n10';
			break;
		case '1001':
			$ret='n12';
			break;
		case '1010':
			$ret='n16';
			break;
		case '1011':
			$ret='n20';
			break;
		case '1100':
			$ret='n24';
			break;
		case '1101':
			$ret='n28';
			break;
		case '1110':
			$ret='spare2';
			break;
		case '1111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function periodicBSC_Timer($in){
	switch ($in){
		case '0000':
			$ret='sf5';
			break;
		case '0001':
			$ret='sf10';
			break;
		case '0010':
			$ret='sf16';
			break;
		case '0011':
			$ret='sf20';
			break;
		case '0100':
			$ret='s32';
			break;
		case '0101':
			$ret='sf40';
			break;
		case '0110':
			$ret='sf64';
			break;
		case '0111':
			$ret='sf80';
			break;
		case '1000':
			$ret='sf128';
			break;
		case '1001':
			$ret='sf160';
			break;
		case '1010':
			$ret='sf320';
			break;
		case '1011':
			$ret='sf640';
			break;
		case '1100':
			$ret='sf1280';
			break;
		case '1101':
			$ret='sf2560';
			break;
		case '1110':
			$ret='infinity';
			break;
		case '1111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function retxBSC_Timer($in){
	switch($in){
		case '000':
			$ret='sf320';
			break;
		case '001':
			$ret='sf640';
			break;
		case '010':
			$ret='sf1280';
			break;
		case '011':
			$ret='sf2560';
			break;
		case '100':
			$ret='sf5120';
			break;
		case '101':
			$ret='sf10240';
			break;
		case '110':
			$ret='spare2';
			break;
		case '111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function ttiBundling($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}

function timeAlignmentTimer($in){
	switch($in){
		case '000':
			$ret='sf500';
			break;
		case '001':
			$ret='sf750';
			break;
		case '010':
			$ret='sf1280';
			break;
		case '011':
			$ret='sf1920';
			break;
		case '100':
			$ret='sf2560';
			break;
		case '101':
			$ret='sf5120';
			break;
		case '110':
			$ret='sf10240';
			break;
		case '111':
			$ret='infinity';
			break;
		default:
			break;
	}
	return $ret;
}

function pdsch_ConfigDedicated($in){
	switch ($in){
		case '000':
			$ret='dB-6';
			break;
		case '001':
			$ret='dB-4dot77';
			break;
		case '010':
			$ret='dB-3';
			break;
		case '011':
			$ret='dB-1dot77';
			break;
		case '100':
			$ret='dB0';
			break;
		case '101':
			$ret='dB1';
			break;
		case '110':
			$ret='dB2';
			break;
		case '111':
			$ret='dB3';
			break;
		default:
			break;
	}
	return $ret;
}

function repetitionFactor($in){
	switch ($in){
		case '00':
			$ret='n2';
			break;
		case '01':
			$ret='n4';
			break;
		case '10':
			$ret='n6';
			break;
		case '11':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function deltaMCS_Enabled($in){
	switch($in){
		case '0':
			$ret='en0';
			break;
		case '1':
			$ret='en1';
			break;
		default:
			break;
	}
	return $ret;
}

function accumulationEnabled($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}

function p0_UE_PUCCH($in){
	$ret=bindec($in)-8;
	return $ret;
}	

function pSRS_Offset($in){
	$ret=bindec($in);
	return $ret;
}

function cqi_ReportModeAperiodic($in){
	switch($in){
		case '000':
			$ret='rm12';
			break;
		case '001':
			$ret='rm20';
			break;
		case '010':
			$ret='rm22';
			break;
		case '011':
			$ret='rm30';
			break;
		case '100':
			$ret='rm31';
			break;
		case '101':
			$ret='spare3';
			break;
		case '110':
			$ret='spare2';
			break;
		case '111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

function nomPDSCH_RS_EPRE_Offset($in){
	return bindec($in)-1;
}

function cqi_PUCCH_ResourceIndex($in){
	return bindec($in);
}

function cqi_pmi_ConfigIndex($in){
	return bindec($in);
}

function ri_ConfigIndex($in){
	return bindec($in);
}

function simultaneousAckNackAndCQI($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}

function transmissionMode($in){
	switch ($in){
		case '000':
			$ret='tm1';
			break;
		case '001':
			$ret='tm2';
			break;
		case '010':
			$ret='tm3';
			break;
		case '011':
			$ret='tm4';
			break;
		case '100':
			$ret='tm5';
			break;
		case '101':
			$ret='tm6';
			break;
		case '110':
			$ret='tm7';
			break;
		case '111':
			$ret='tm8-v920';
			break;
		default:
			break;
	}
	return $ret;
}

function codebookSubsetRestriction($in){
	global $rrc_msg_bin;
	switch($in){
		case '000':
			$ret='n2TxAntenna-tm3 ' . substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			break;
		case '001':
			$ret='n4TxAntenna-tm3 ' . substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			break;
		case '010':
			$ret='n2TxAntenna-tm4 ' . substr($rrc_msg_bin,0,6);
			$rrc_msg_bin=substr($rrc_msg_bin,6);
			break;
		case '011':
			$ret='n4TxAntenna-tm4 ' . substr($rrc_msg_bin,0,64);
			$rrc_msg_bin=substr($rrc_msg_bin,64);
			break;
		case '100':
			$ret='n2TxAntenna-tm5 ' . substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			break;
		case '101':
			$ret='n4TxAntenna-tm5 ' . substr($rrc_msg_bin,0,16);
			$rrc_msg_bin=substr($rrc_msg_bin,16);
			break;
		case '110':
			$ret='n2TxAntenna-tm6 ' . substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			break;
		case '111':
			$ret='n4TxAntenna-tm6 ' . substr($rrc_msg_bin,0,16);
			$rrc_msg_bin=substr($rrc_msg_bin,16);
			break;
		default:
			break;
	}
	return $ret;
}

function dsr_TransMax($in){
	switch ($in){
		case '000':
			$ret='n4';
			break;
		case '001':
			$ret='n8';
			break;
		case '010':
			$ret='n16';
			break;
		case '011':
			$ret='n32';
			break;
		case '100':
			$ret='n64';
			break;
		case '101':
			$ret='spare3';
			break;
		case '110':
			$ret='spare2';
			break;
		case '111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}

?>