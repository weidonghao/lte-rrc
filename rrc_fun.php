<?php
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

function cellBarred($in){
	if($in=='0'){return 'barred';}
	if($in=='1'){return 'unBarred';}
}

function intraFreqReselection($in){
	if($in=='0'){return 'allowed';}
	if($in=='1'){return 'notAllowed';}
}

function csg_Indication($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function q_RxLevMin($in){
	return bindec($in)-70;
}
function q_RxLevMinOffset_bit($in){
	return bindec($in);
}
function p_Max($in){
	return bindec($in)-30;
}
function freqBandIndicator($in){
	return bindec($in)+1;
}
function schedulingInfoList($in){
	return bindec($in)+1;
}
function si_Periodicity($in){
	switch ($in){
		case '000';
			$ret='rf8';
			break;
		case '001';
			$ret='rf16';
			break;
		case '010';
			$ret='rf32';
			break;
		case '011';
			$ret='rf64';
			break;
		case '100';
			$ret='rf128';
			break;
		case '101';
			$ret='rf256';
			break;
		case '110';
			$ret='rf512';
			break;
		default:
			break;
	}
	return $ret;
}
function sib_MappingInfo($in){
	return bindec($in);
}
function SIB_type($in){
	switch ($in){
		case '0000':
			$ret='sibType3';
			break;
		case '0001':
			$ret='sibType4';
			break;
		case '0010':
			$ret='sibType5';
			break;
		case '0011':
			$ret='sibType6';
			break;
		case '0100':
			$ret='sibType7';
			break;
		case '0101':
			$ret='sibType8';
			break;
		case '0110':
			$ret='sibType9';
			break;
		case '0111':
			$ret='sibType10';
			break;
		case '1000':
			$ret='sibType11';
			break;
		case '1001':
			$ret='sibType12-v920';
			break;
		case '1010':
			$ret='sibType13-v920';
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
function subframeAssignment($in){
	switch ($in){
		case '000':
			break;
			$ret='sa0';
		case '001':
			break;
			$ret='sa1';
		case '010':
			break;
			$ret='sa2';
		case '011':
			break;
			$ret='sa3';
		case '100':
			break;
			$ret='sa4';
		case '101':
			break;
			$ret='sa5';
		case '110':
			break;
			$ret='sa6';
		default:
			break;
	}
	return $ret;
}
function specialSubframePatterns($in){
	switch ($in){
		case '0000':
			$ret='ssp0';
			break;
		case '0001':
			$ret='ssp1';
			break;
		case '0010':
			$ret='ssp2';
			break;
		case '0011':
			$ret='ssp3';
			break;
		case '0100':
			$ret='ssp4';
			break;
		case '0101':
			$ret='ssp5';
			break;
		case '0110':
			$ret='ssp6';
			break;
		case '0111':
			$ret='ssp7';
			break;
		case '1000':
			$ret='ssp8';
			break;
		default:
			break;
	}
	return $ret;
}
function si_WindowLength($in){
	switch ($in){
		case '000':
			$ret='ms1';
			break;
		case '001':
			$ret='ms2';
			break;
		case '010':
			$ret='ms5';
			break;
		case '011':
			$ret='ms10';
			break;
		case '100':
			$ret='ms15';
			break;
		case '101':
			$ret='ms20';
			break;
		case '110':
			$ret='ms40';
			break;
		default:
			break;
		
	}
	return $ret;
}

function systemInfoValueTag($in){
	return bindec($in);
}

function dl_Bandwidth($in){
	switch ($in){
		case '000':
			$ret='n6';
			break;
		case '001':
			$ret='n15';
			break;
		case '010':
			$ret='n25';
			break;
		case '011':
			$ret='n50';
			break;
		case '100':
			$ret='n75';
			break;
		case '101':
			$ret='n100';
			break;
		default:
			break;
	}
	return $ret;
}
function phich_Duration($in){
if($in=='0'){return 'normal';}
if($in=='1'){return 'extended';}
}
function phich_Resource($in){
	switch ($in){
		case '00':
			$ret='onSixth';
			break;
		case '01':
			$ret='half';
			break;
		case '10':
			$ret='one';
			break;
		case '11':
			$ret='two';
			break;
		default:
			break;
	}
	return $ret;
}
function ac_BarringForEmergency($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}

function AC_BarringConfig(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'AC-BarringConfig {';
	//ac-BarringFactor
	//read 4 bits
	$ac_BarringFactor_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$ac_BarringFactor_value=ac_BarringFactor($ac_BarringFactor_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . $ac_BarringFactor_value . ',';
	//ac-BarringTime
	//read 3 bits
	$ac_BarringTime_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$ac_BarringTime_value=ac_BarringTime($ac_BarringTime_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . $ac_BarringTime_value . ',';
	
	//ac-BarringForSpecialAC BIT STRING (SIZE(5))
	$ac_BarringForSpecialAC_bit=substr($rrc_msg_bin,0,5);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . $ac_BarringForSpecialAC_bit;
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}
function ac_BarringFactor($in){
	switch($in){
		case '0000':
			$ret='p00';
			break;
		case '0001':
			$ret='p05';
			break;
		case '0010':
			$ret='p10';
			break;
		case '0011':
			$ret='p15';
			break;
		case '0100':
			$ret='p20';
			break;
		case '0101':
			$ret='p25';
			break;
		case '0110':
			$ret='p30';
			break;
		case '0111':
			$ret='p40';
			break;
		case '1000':
			$ret='p50';
			break;
		case '1001':
			$ret='p60';
			break;
		case '1010':
			$ret='p70';
			break;
		case '1011':
			$ret='p75';
			break;
		case '1100':
			$ret='p80';
			break;
		case '1101':
			$ret='p85';
			break;
		case '1110':
			$ret='p90';
			break;
		case '1111':
			$ret='p95';
			break;
		default:
			break;
	}
	return $ret;
}

function ac_BarringTime($in){
	switch ($in){
		case '000':
			break;
			$ret='s4';
		case '001':
			break;
			$ret='s8';
		case '010':
			break;
			$ret='s16';
		case '011':
			break;
			$ret='s32';
		case '100':
			break;
			$ret='s64';
		case '101':
			break;
			$ret='s128';
		case '110':
			break;
			$ret='s256';
		case '111':
			break;
			$ret='s512';
		default:
			break;
	}
	return $ret;
}

function numberOfRA_Preambles($in){
	switch ($in){
		case '0000':
			$ret='n4';
			break;
		case '0001':
			$ret='n8';
			break;
		case '0010':
			$ret='n12';
			break;
		case '0011':
			$ret='n16';
			break;
		case '0100':
			$ret='n20';
			break;
		case '0101':
			$ret='n24';
			break;
		case '0110':
			$ret='n28';
			break;
		case '0111':
			$ret='n32';
			break;
		case '1000':
			$ret='n36';
			break;
		case '1001':
			$ret='n40';
			break;
		case '1010':
			$ret='n44';
			break;
		case '1011':
			$ret='n48';
			break;
		case '1100':
			$ret='n52';
			break;
		case '1101':
			$ret='n56';
			break;
		case '1110':
			$ret='n60';
			break;
		case '1111':
			$ret='n64';
			break;
		default:
			break;
	}
	return $ret;
}
function sizeOfRA_PreamblesGroupA($in){
	switch ($in){
		case '0000':
			$ret='n4';
			break;
		case '0001':
			$ret='n8';
			break;
		case '0010':
			$ret='n12';
			break;
		case '0011':
			$ret='n16';
			break;
		case '0100':
			$ret='n20';
			break;
		case '0101':
			$ret='n24';
			break;
		case '0110':
			$ret='n28';
			break;
		case '0111':
			$ret='n32';
			break;
		case '1000':
			$ret='n36';
			break;
		case '1001':
			$ret='n40';
			break;
		case '1010':
			$ret='n44';
			break;
		case '1011':
			$ret='n48';
			break;
		case '1100':
			$ret='n52';
			break;
		case '1101':
			$ret='n56';
			break;
		case '1110':
			$ret='n60';
			break;
		default:
			break;
	}
	return $ret;
}
function messageSizeGroupA($in){
	switch($in){
		case '00':
			$ret='b56';
			break;
		case '01':
			$ret='b144';
			break;
		case '10':
			$ret='b208';
			break;
		case '11':
			$ret='b256';
			break;
		default:
			break;
	}
	return $ret;
}
function messagePowerOffsetGroupB($in){
	switch ($in){
		case '000':
			$ret='minusinfinity';
			break;
		case '001':
			$ret='dB0';
			break;
		case '010':
			$ret='dB5';
			break;
		case '011':
			$ret='dB8';
			break;
		case '100':
			$ret='dB10';
			break;
		case '101':
			$ret='dB12';
			break;
		case '110':
			$ret='dB15';
			break;
		case '111':
			$ret='d18';
			break;
		default:
			break;
	}
	return $ret;
}

function powerRampingStep($in){
	switch ($in){
		case '00':
			$ret='dB0';
			break;
		case '01':
			$ret='dB2';
			break;
		case '10':
			$ret='dB4';
			break;
		case '11':
			$ret='dB6';
			break;
		default:
			break;
	}
	return $ret;
}
function preambleInitialReceivedTargetPower($in){
	switch($in){
		case '0000':
			$ret='dBm-120';
			break;
		case '0001':
			$ret='dBm-118';
			break;
		case '0010':
			$ret='dBm-116';
			break;
		case '0011':
			$ret='dBm-114';
			break;
		case '0100':
			$ret='dBm-112';
			break;
		case '0101':
			$ret='dBm-110';
			break;
		case '0110':
			$ret='dBm-108';
			break;
		case '0111':
			$ret='dBm-106';
			break;
		case '1000':
			$ret='dBm-104';
			break;
		case '1001':
			$ret='dBm-102';
			break;
		case '1010':
			$ret='dBm-100';
			break;
		case '1011':
			$ret='dBm-98';
			break;
		case '1100':
			$ret='dBm-96';
			break;
		case '1101':
			$ret='dBm-94';
			break;
		case '1110':
			$ret='dBm-92';
			break;
		case '1111':
			$ret='dBm-90';
			break;
		default:
			break;
	}
	return $ret;
}

function preambleTransMax($in){
	switch ($in){
		case '0000':
			$ret='n3';
			break;
		case '0001':
			$ret='n4';
			break;
		case '0010':
			$ret='n5';
			break;
		case '0011':
			$ret='n6';
			break;
		case '0100':
			$ret='n7';
			break;
		case '0101':
			$ret='n8';
			break;
		case '0110':
			$ret='n10';
			break;
		case '0111':
			$ret='n20';
			break;
		case '1000':
			$ret='n50';
			break;
		case '1001':
			$ret='n100';
			break;
		case '1010':
			$ret='n200';
			break;
		default:
			break;
	}
	return $ret;
}
function ra_ResponseWindowSize($in){
	switch($in){
		case '000':
			$ret='sf2';
			break;
		case '001':
			$ret='sf3';
			break;
		case '010':
			$ret='sf4';
			break;
		case '011':
			$ret='sf5';
			break;
		case '100':
			$ret='sf6';
			break;
		case '101':
			$ret='sf7';
			break;
		case '110':
			$ret='sf8';
			break;
		case '111':
			$ret='sf10';
			break;
		default:
			break;
	}
	return $ret;
}
function mac_ContentionResolutionTimer($in){
	switch($in){
		case '000':
			$ret='sf8';
			break;
		case '001':
			$ret='sf16';
			break;
		case '010':
			$ret='sf24';
			break;
		case '011':
			$ret='sf32';
			break;
		case '100':
			$ret='sf40';
			break;
		case '101':
			$ret='sf48';
			break;
		case '110':
			$ret='sf56';
			break;
		case '111':
			$ret='sf64';
			break;
		default:
			break;
	}
	return $ret;
}

function maxHARQ_Msg3Tx($in){
	return bindec($in)+1;
}
function modificationPeriodCoeff($in){
	switch ($in){
		case '00':
			$ret='n2';
			break;
		case '01':
			$ret='n4';
			break;
		case '10':
			$ret='n8';
			break;
		case '11':
			$ret='n16';
			break;
		default:
			break;
	}
	return $ret;
}
function defaultPagingCycle($in){
	switch($in){
		case '00':
			$ret='rf32';
			break;
		case '01':
			$ret='rf64';
			break;
		case '10':
			$ret='rf128';
			break;
		case '11':
			$ret='rf256';
			break;
		default:
			break;
	}
	return $ret;
}
function nB($in){
	switch($in){
		case '000':
			$ret='fourT';
			break;
		case '001':
			$ret='twoT';
			break;
		case '010':
			$ret='oneT';
			break;
		case '011':
			$ret='halfT';
			break;
		case '100':
			$ret='quarterT';
			break;
		case '101':
			$ret='oneEighthT';
			break;
		case '110':
			$ret='oneSixteenthT';
			break;
		case '111':
			$ret='oneThirtySecondT';
			break;
		default:
			break;
	}
	return $ret;
}
function rootSequenceIndex($in){
	return bindec($in);
}
function prach_ConfigIndex($in){
	return bindec($in);
}
function highSpeedFlag($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function zeroCorrelationZoneConfig($in){
	return bindec($in);
}
function prach_FreqOffset($in){
	return bindec($in);
}
function referenceSignalPower($in){
	return bindec($in)-60;
}
function p_b($in){
	return bindec($in);
}
function n_SB($in){
	return bindec($in)+1;
}
function hoppingMode($in){
	if($in=='0'){return 'interSubFrame';}
	if($in=='1'){return 'intraAndInterSubFrame';}
}
function pusch_HoppingOffset($in){
	return bindec($in);
}
function enable64QAM($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function groupHoppingEnabled($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function groupAssignmentPUSCH($in){
	return bindec($in);
}
function sequenceHoppingEnabled($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function cyclicShift($in){
	return bindec($in);
}
function deltaPUCCH_Shift($in){
	switch ($in){
		case '00':
			$ret='ds1';
			break;
		case '01':
			$ret='ds2';
			break;
		case '10':
			$ret='ds3';
			break;
		default:
			break;
	}
	return $ret;
}
function nRB_CQI($in){
	return bindec($in);
}
function nCS_AN($in){
	return bindec($in);
}
function n1PUCCH_AN($in){
	return bindec($in);
}
function srs_BandwidthConfig($in){
	switch ($in){
		case '000':
			$ret='bw0';
			break;
		case '001':
			$ret='bw1';
			break;
		case '010':
			$ret='bw2';
			break;
		case '011':
			$ret='bw3';
			break;
		case '100':
			$ret='bw4';
			break;
		case '101':
			$ret='bw5';
			break;
		case '110':
			$ret='bw6';
			break;
		case '111':
			$ret='bw7';
			break;
		default:
			break;
	}
	return $ret;
}

function srs_SubframeConfig($in){
	switch($in){
		case '0000':
			$ret='sc0';
			break;
		case '0001':
			$ret='sc1';
			break;
		case '0010':
			$ret='sc2';
			break;
		case '0011':
			$ret='sc3';
			break;
		case '0100':
			$ret='sc4';
			break;
		case '0101':
			$ret='sc5';
			break;
		case '0110':
			$ret='sc6';
			break;
		case '0111':
			$ret='sc7';
			break;
		case '1000':
			$ret='sc8';
			break;
		case '1001':
			$ret='sc9';
			break;
		case '1010':
			$ret='sc10';
			break;
		case '1011':
			$ret='sc11';
			break;
		case '1100':
			$ret='sc12';
			break;
		case '1101':
			$ret='sc13';
			break;
		case '1110':
			$ret='sc14';
			break;
		case '1111':
			$ret='sc15';
			break;
		default:
			break;
	}
	return $ret;
}
function ackNackSRS_SimultaneousTransmission($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function p0_NominalPUSCH($in){
	return bindec($in)-126;
}
function alpha($in){
	switch($in){
		case '000':
			$ret='al0';
			break;
		case '001':
			$ret='al04';
			break;
		case '010':
			$ret='al05';
			break;
		case '011':
			$ret='al06';
			break;
		case '100':
			$ret='al07';
			break;
		case '101':
			$ret='al08';
			break;
		case '110':
			$ret='al09';
			break;
		case '111':
			$ret='al1';
			break;
		default:
			break;
	}
	return $ret;
}
function p0_NominalPUCCH($in){
	return bindec($in)-127;
}
function deltaF_PUCCH_Format1($in){
	switch($in){
		case '00':
			$ret='deltaF-2';
			break;
		case '01':
			$ret='deltaF0';
			break;
		case '10':
			$ret='deltaF2';
			break;
		default:
			break;
	}
	return $ret;
}
function deltaF_PUCCH_Format1b($in){
	switch($in){
		case '00':
			$ret='deltaF1';
			break;
		case '01':
			$ret='deltaF3';
			break;
		case '10':
			$ret='deltaF5';
			break;
		default:
			break;
	}
	return $ret;
}
function deltaF_PUCCH_Format2($in){
	switch($in){
		case '00':
			$ret='deltaF-2';
			break;
		case '01':
			$ret='deltaF0';
			break;
		case '10':
			$ret='deltaF1';
			break;
		case '11':
			$ret='deltaF2';
			break;
		default:
			break;
	}
	return $ret;
}
function deltaF_PUCCH_Format2a($in){
	switch($in){
		case '00':
			$ret='deltaF-2';
			break;
		case '01':
			$ret='deltaF0';
			break;
		case '10':
			$ret='deltaF2';
			break;
		default:
			break;
	}
	return $ret;
}
function deltaF_PUCCH_Format2b($in){
	switch($in){
		case '00':
			$ret='deltaF-2';
			break;
		case '01':
			$ret='deltaF0';
			break;
		case '10':
			$ret='deltaF2';
			break;
		default:
			break;
	}
	return $ret;
}
function deltaPreambleMsg3($in){
	return bindec($in)-1;
}
function ul_CyclicPrefixLength($in){
	if($in=='0'){return 'len1';}
	if($in=='1'){return 'len2';}
}
function t300($in){
	switch($in){
		case '000':
			$ret='ms100';
			break;
		case '001':
			$ret='ms200';
			break;
		case '010':
			$ret='ms300';
			break;
		case '011':
			$ret='ms400';
			break;
		case '100':
			$ret='ms600';
			break;
		case '101':
			$ret='ms1000';
			break;
		case '110':
			$ret='ms1500';
			break;
		case '111':
			$ret='ms2000';
			break;
		default:
			break;
	}
	return $ret;
}
function t301($in){
	switch($in){
		case '000':
			$ret='ms100';
			break;
		case '001':
			$ret='ms200';
			break;
		case '010':
			$ret='ms300';
			break;
		case '011':
			$ret='ms400';
			break;
		case '100':
			$ret='ms600';
			break;
		case '101':
			$ret='ms1000';
			break;
		case '110':
			$ret='ms1500';
			break;
		case '111':
			$ret='ms2000';
			break;
		default:
			break;
	}
	return $ret;
}
function t310($in){
	switch($in){
		case '000':
			$ret='ms0';
			break;
		case '001':
			$ret='ms50';
			break;
		case '010':
			$ret='ms100';
			break;
		case '011':
			$ret='ms200';
			break;
		case '100':
			$ret='ms500';
			break;
		case '101':
			$ret='ms1000';
			break;
		case '110':
			$ret='ms2000';
			break;
		default:
			break;
	}
	return $ret;
}
function n310($in){
	switch($in){
		case '000':
			$ret='n1';
			break;
		case '001':
			$ret='n2';
			break;
		case '010':
			$ret='n3';
			break;
		case '011':
			$ret='n4';
			break;
		case '100':
			$ret='n6';
			break;
		case '101':
			$ret='n8';
			break;
		case '110':
			$ret='n10';
			break;
		case '111':
			$ret='n20';
			break;
		default:
			break;
	}
	return $ret;
}
function t311($in){
	switch($in){
		case '000':
			$ret='ms1000';
			break;
		case '001':
			$ret='ms3000';
			break;
		case '010':
			$ret='ms5000';
			break;
		case '011':
			$ret='ms10000';
			break;
		case '100':
			$ret='ms15000';
			break;
		case '101':
			$ret='ms20000';
			break;
		case '110':
			$ret='ms30000';
			break;
		default:
			break;
	}
	return $ret;
}
function n311($in){
	switch($in){
		case '000':
			$ret='n1';
			break;
		case '001':
			$ret='n2';
			break;
		case '010':
			$ret='n3';
			break;
		case '011':
			$ret='n4';
			break;
		case '100':
			$ret='n5';
			break;
		case '101':
			$ret='n6';
			break;
		case '110':
			$ret='n8';
			break;
		case '111':
			$ret='n10';
			break;
		default:
			break;
	}
	return $ret;
}
function ul_CarrierFreq($in){
	return bindec($in);
}
function ul_Bandwidth($in){
	switch($in){
		case '000':
			$ret='n6';
			break;
		case '001':
			$ret='n15';
			break;
		case '010':
			$ret='n25';
			break;
		case '011':
			$ret='n50';
			break;
		case '100':
			$ret='n75';
			break;
		case '101':
			$ret='n100';
			break;
		default:
			break;
	}
	return $ret;
}
function additionalSpectrumEmission($in){
	return bindec($in)+1;
}
function timeAlignmentTimerCommon($in){
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
function radioframeAllocationPeriod($in){
	switch($in){
		case '000':
			$ret='n1';
			break;
		case '001':
			$ret='n2';
			break;
		case '010':
			$ret='n4';
			break;
		case '011':
			$ret='n8';
			break;
		case '100':
			$ret='n16';
			break;
		case '101':
			$ret='n32';
			break;
		default:
			break;
	}
	return $ret;
}
function radioframeAllocationOffset($in){
	return bindec($in);
}
function q_Hyst($in){
	switch($in){
		case '0000':
			$ret='dB0';
			break;
		case '0001':
			$ret='dB1';
			break;
		case '0010':
			$ret='dB2';
			break;
		case '0011':
			$ret='dB3';
			break;
		case '0100':
			$ret='dB4';
			break;
		case '0101':
			$ret='dB5';
			break;
		case '0110':
			$ret='dB6';
			break;
		case '0111':
			$ret='dB8';
			break;
		case '1000':
			$ret='dB10';
			break;
		case '1001':
			$ret='dB12';
			break;
		case '1010':
			$ret='dB14';
			break;
		case '1011':
			$ret='dB16';
			break;
		case '1100':
			$ret='dB18';
			break;
		case '1101':
			$ret='dB20';
			break;
		case '1110':
			$ret='dB22';
			break;
		case '1111':
			$ret='dB24';
			break;
		default:
			break;
	}
	return $ret;
}
function t_Evaluation($in){
	switch($in){
		case '000':
			$ret='s30';
			break;
		case '001':
			$ret='s60';
			break;
		case '010':
			$ret='s120';
			break;
		case '011':
			$ret='s180';
			break;
		case '100':
			$ret='s240';
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
function t_HystNormal($in){
	switch($in){
		case '000':
			$ret='s30';
			break;
		case '001':
			$ret='s60';
			break;
		case '010':
			$ret='s120';
			break;
		case '011':
			$ret='s180';
			break;
		case '100':
			$ret='s240';
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
function n_CellChangeMedium($in){
	return bindec($in)+1;
}
function n_CellChangeHigh($in){
	return bindec($in)+1;
}
function sf_Medium($in){
	switch($in){
		case '00':
			$ret='dB-6';
			break;
		case '01':
			$ret='dB-4';
			break;
		case '10':
			$ret='dB-2';
			break;
		case '11':
			$ret='dB0';
			break;
		default:
			break;
	}
	return $ret;
}
function sf_High($in){
	switch($in){
		case '00':
			$ret='dB-6';
			break;
		case '01':
			$ret='dB-4';
			break;
		case '10':
			$ret='dB-2';
			break;
		case '11':
			$ret='dB0';
			break;
		default:
			break;
	}
	return $ret;
}
function ReselectionThreshold($in){
	return bindec($in);
}
function CellReselectionPriority($in){
	return bindec($in);
}
//function Q_RxLevMin($in){
//	return bindec($in)-70;
//}
function AllowedMeasBandwidth($in){
	switch($in){
		case '000':
			$ret='mbw6';
			break;
		case '000':
			$ret='mbw15';
			break;
		case '000':
			$ret='mbw25';
			break;
		case '000':
			$ret='mbw50';
			break;
		case '000':
			$ret='mbw75';
			break;
		case '000':
			$ret='mbw100';
			break;
		default:
			break;
	}
	return $ret;
}
function PresenceAntennaPort1($in){
	if($in=='0'){return 'FALSE';}
	if($in=='1'){return 'TRUE';}
}
function T_Reselection($in){
	return bindec($in);
}
function SpeedStateScaleFactors($in){
	switch($in){
		case '00':
			$ret='oDot25';
			break;
		case '00':
			$ret='oDot5';
			break;
		case '00':
			$ret='oDot75';
			break;
		case '00':
			$ret='oDot0';
			break;
		default:
			break;
	}
	return $ret;
}
function PhysCellId($in){
	return bindec($in);
}
function Q_OffsetRange($in){
	switch ($in){
		case '00000':
			$ret='dB-24';
			break;
		case '00001':
			$ret='dB-22';
			break;
		case '00010':
			$ret='dB-20';
			break;
		case '00011':
			$ret='dB-18';
			break;
		case '00100':
			$ret='dB-16';
			break;
		case '00101':
			$ret='dB-14';
			break;
		case '00110':
			$ret='dB-12';
			break;
		case '00111':
			$ret='dB-10';
			break;
		case '01000':
			$ret='dB-8';
			break;
		case '01001':
			$ret='dB-6';
			break;
		case '01010':
			$ret='dB-5';
			break;
		case '01011':
			$ret='dB-4';
			break;
		case '01100':
			$ret='dB-3';
			break;
		case '01101':
			$ret='dB-2';
			break;
		case '01110':
			$ret='dB-1';
			break;
		case '01111':
			$ret='dB0';
			break;
		case '10000':
			$ret='dB1';
			break;
		case '10001':
			$ret='dB2';
			break;
		case '10010':
			$ret='dB3';
			break;
		case '10011':
			$ret='dB4';
			break;
		case '10100':
			$ret='dB5';
			break;
		case '10101':
			$ret='dB6';
			break;
		case '10110':
			$ret='dB8';
			break;
		case '10111':
			$ret='dB10';
			break;
		case '11000':
			$ret='dB12';
			break;
		case '11001':
			$ret='dB14';
			break;
		case '11010':
			$ret='dB16';
			break;
		case '11011':
			$ret='dB18';
			break;
		case '11100':
			$ret='dB20';
			break;
		case '11101':
			$ret='dB22';
			break;
		case '11110':
			$ret='dB24';
			break;
		default:
			break;
	}
	return $ret;
}
function PhysCellIdRange(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	
	$range_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$start_bit=substr($rrc_msg_bin,0,9);
	$rrc_msg_bin=substr($rrc_msg_bin,9);
	$start_value=PhysCellId($start_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'start ' . $start_value . ',';
	
	$range_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$range_value=PhysCellIdRange_range($range_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'range ' . $range_value;
	
}



function PhysCellIdRange_range($in){
	switch($in){
		case '0000':
			$ret='n4';
			break;
		case '0001':
			$ret='n8';
			break;
		case '0010':
			$ret='n12';
			break;
		case '0011':
			$ret='n16';
			break;
		case '0100':
			$ret='n24';
			break;
		case '0101':
			$ret='n32';
			break;
		case '0110':
			$ret='n48';
			break;
		case '0111':
			$ret='n64';
			break;
		case '1000':
			$ret='n84';
			break;
		case '1001':
			$ret='n96';
			break;
		case '1010':
			$ret='n128';
			break;
		case '1011':
			$ret='n168';
			break;
		case '1100':
			$ret='n252';
			break;
		case '1101':
			$ret='n504';
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
function MeasConfig_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'MeasConfig {';
	//check extention and do nothing
	$MeasConfig_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//check options
	$measObjectToRemoveList_option_bit=substr($rrc_msg_bin,0,1);
	$measObjectToAddModList_option_bit=substr($rrc_msg_bin,1,1);
	$reportConfigToRemoveList_option_bit=substr($rrc_msg_bin,2,1);
	$reportConfigToAddModList_option_bit=substr($rrc_msg_bin,3,1);
	$measIdToRemoveList_option_bit=substr($rrc_msg_bin,4,1);
	$measIdToAddModList_option_bit=substr($rrc_msg_bin,5,1);
	$quantityConfig_option_bit=substr($rrc_msg_bin,6,1);
	$measGapConfig_option_bit=substr($rrc_msg_bin,7,1);
	$s_Measure_option_bit=substr($rrc_msg_bin,8,1);
	$preRegistrationInfoHRPD_option_bit=substr($rrc_msg_bin,9,1);
	$speedStatePars_option_bit=substr($rrc_msg_bin,10,1);
	$rrc_msg_bin=substr($rrc_msg_bin,11);

	//$ll=strlen($rrc_msg_bin);
	//$rrc_msg_bin=substr($rrc_msg_bin,0,$ll);
	
	//measObjectToRemoveList				MeasObjectToRemoveList
	if($measObjectToRemoveList_option_bit=='1'){		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'measObjectToRemoveList {';
		$maxObjectId=32;
		//read 5 bits
		$measObjectToRemoveList_size=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		while($measObjectToRemoveList_size>0){
			//MeasObjectId ::=					INTEGER (1..maxObjectId)
			$MeasObjectId_value=bindec(substr($rrc_msg_bin,0,5))+1;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'MeasObjectId ' . $MeasObjectId_value; 
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			//
			$measObjectToRemoveList_size=$measObjectToRemoveList_size-1;
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}

	//return;
	//measObjectToAddModList				MeasObjectToAddModList
	if($measObjectToAddModList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'measObjectToAddModList {';
		$measObjectToAddModList_size=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'size ' . $measObjectToAddModList_size;
		//return;
		while($measObjectToAddModList_size>0){
			//
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'MeasObjectToAddMod {';
			
			//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'MeasObjectId ' . bindec(substr($rrc_msg_bin,0,5))+1; 
			$MeasObjectId_value=bindec(substr($rrc_msg_bin,0,5))+1;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'MeasObjectId ' . $MeasObjectId_value;
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			
			//measObject
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'measObject {';
			//return;
			
			$tabs=$tabs . str_repeat($tab,4);
			//check extention and do nothing
			$measObject_ext_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			$measObject_choice_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			switch($measObject_choice_bit){
				case '00':
					MeasObjectEUTRA();
					break;
				case '01':
					MeasObjectUTRA();
					break;
				case '10':
					MeasObjectGERAN();
					break;
				case '11':
					MeasObjectCDMA2000();
					break;
				default:
					break;
			}
			$tabs=substr($tabs,0,-16);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . '}';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			
			$measObjectToAddModList_size=$measObjectToAddModList_size-1;
		}

		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	
	//$rrc_out=$rrc_out . "\n" . $tabs  . 'DEBUG:' . substr($rrc_msg_bin,0,8);
	//$rrc_msg_bin=substr($rrc_msg_bin,5);
	//reportConfigToRemoveList			ReportConfigToRemoveList
	if($reportConfigToRemoveList_option_bit=='1'){
		//ReportConfigToRemoveList ::=		SEQUENCE (SIZE (1..maxReportConfigId)) OF ReportConfigId
		//maxReportConfigId			INTEGER ::= 32
		$reportConfigToRemoveList_size=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'reportConfigToRemoveList {';
		while($reportConfigToRemoveList_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . ReportConfigId_1();
			$reportConfigToRemoveList_size=$reportConfigToRemoveList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	
	//reportConfigToAddModList			ReportConfigToAddModList
	if($reportConfigToAddModList_option_bit=='1'){
		//ReportConfigToAddModList ::=		SEQUENCE (SIZE (1..maxReportConfigId)) OF ReportConfigToAddMod
		//maxReportConfigId			INTEGER ::= 32
		
		$reportConfigToAddModList_size=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,5);

		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'reportConfigToAddModList {';
		
		while($reportConfigToAddModList_size>0){
			//reportConfigId
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'reportConfigId ' . ReportConfigId_1();

			$reportConfig_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
	
			if($reportConfig_choice_bit=='0'){
				//reportConfigEUTRA					ReportConfigEUTRA,
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'reportConfigEUTRA	{';
				$tabs=$tabs . str_repeat($tab,4);
	
				//echo "DEBUG:" . strlen($rrc_msg_bin);
				ReportConfigEUTRA_1();
				//$rrc_msg_bin=substr($rrc_msg_bin,1);
				$tabs=substr($tabs,0,-16);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			}
			/*
			if($reportConfig_choice_bit=='1'){
				//reportConfigInterRAT				ReportConfigInterRAT
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'reportConfigInterRAT {';
				$tabs=$tabs . str_repeat($tab,4);
				ReportConfigInterRAT_1();
				$tabs=substr($tabs,0,-16);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			}
			*/
			$reportConfigToAddModList_size=$reportConfigToAddModList_size-1;
		}		
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}

	//measIdToRemoveList					MeasIdToRemoveList	
	if($measIdToRemoveList_option_bit=='1'){
		//MeasIdToRemoveList ::=				SEQUENCE (SIZE (1..maxMeasId)) OF MeasId
		//maxMeasId					INTEGER ::= 32
		$measIdToRemoveList_size=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'MeasIdToRemoveList {';
		while($measIdToRemoveList_size>0){
			//
			//MeasId
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'MeasId ' . MeasId_1();
			$measIdToRemoveList_size=$measIdToRemoveList_size-1;
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//measIdToAddModList					MeasIdToAddModList
	if($measIdToAddModList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'measIdToAddModList {';
		//
		$tabs=$tabs . str_repeat($tab,3);
		MeasIdToAddModList_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//quantityConfig						QuantityConfig
	if($quantityConfig_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'quantityConfig {';
		//
		$tabs=$tabs . str_repeat($tab,3);
		QuantityConfig_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//measGapConfig						MeasGapConfig
	if($measGapConfig_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'measGapConfig {';
		//
		$tabs=$tabs . str_repeat($tab,3);
		MeasGapConfig_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//s-Measure							RSRP-Range
	if($s_Measure_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 's-Measure ' . RSRP_Range_1();
	}
	//preRegistrationInfoHRPD				PreRegistrationInfoHRPD
	if($preRegistrationInfoHRPD_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'preRegistrationInfoHRPD {';
		$tabs=$tabs . str_repeat($tab,3);
		PreRegistrationInfoHRPD_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//speedStatePars
	if($speedStatePars_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'speedStatePars {';
		$tabs=$tabs . str_repeat($tab,2);
		$speedStatePars_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($speedStatePars_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'release NULL';
		}
		if($speedStatePars_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'setup {';
			//mobilityStateParameters				MobilityStateParameters,
			//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mobilityStateParameters ' . MobilityStateParameters_1();
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mobilityStateParameters {';
			$tabs=$tabs . str_repeat($tab,3);
			MobilityStateParameters_1();
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			//timeToTrigger-SF					SpeedStateScaleFactors			
			//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'timeToTrigger-SF ' . SpeedStateScaleFactors_1();
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'timeToTrigger-SF {';
			$tabs=$tabs . str_repeat($tab,3);
			SpeedStateScaleFactors_1();
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		}
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	//close for MeasConfig
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}
function MeasObjectEUTRA(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'MeasObjectEUTRA {';
	$MeasObjectEUTRA_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$offsetFreq_default_bit=substr($rrc_msg_bin,0,1);
	$cellsToRemoveList_option_bit=substr($rrc_msg_bin,1,1);
	$cellsToAddModList_option_bit=substr($rrc_msg_bin,2,1);
	$blackCellsToRemoveList_option_bit=substr($rrc_msg_bin,3,1);
	$blackCellsToAddModList_option_bit=substr($rrc_msg_bin,4,1);
	$cellForWhichToReportCGI_option_bit=substr($rrc_msg_bin,5,1);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	//$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//carrierFreq							ARFCN-ValueEUTRA,	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'carrierFreq ' . ARFCN_ValueEUTRA_1();	
	
	
	//allowedMeasBandwidth				AllowedMeasBandwidth,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'allowedMeasBandwidth ' . AllowedMeasBandwidth_1();

	//presenceAntennaPort1				PresenceAntennaPort1,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'presenceAntennaPort1 ' . PresenceAntennaPort1_1();
	
	//neighCellConfig						NeighCellConfig,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'neighCellConfig ' . NeighCellConfig_1();
	//$rrc_out=$rrc_out . "\n" . "DEBUG:" . substr($rrc_msg_bin,0,1) ;
	//$rrc_out=$rrc_out . "\n" . "DEBUG:" . substr($rrc_msg_bin,1,8) ;
	//$rrc_msg_bin=substr($rrc_msg_bin,2);
	
	//offsetFreq							Q-OffsetRange				DEFAULT dB0,
	if($offsetFreq_default_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq DEFAULT dB0';
	}
	if($offsetFreq_default_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq ' . Q_OffsetRange_1();
	}
	//cellsToRemoveList					CellIndexList				OPTIONAL,
	if($cellsToRemoveList_option_bit=='1'){
		$tabs=$tabs . str_repeat($tab,2);
		$rrc_out=$rrc_out . "\n" . $tabs . 'cellsToRemoveList {';
		CellIndexList_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$tabs=substr($tabs,0,-8);
	}
	//cellsToAddModList					CellsToAddModList			OPTIONAL,
	if($cellsToAddModList_option_bit=='1'){
		$tabs=$tabs . str_repeat($tab,2);
		CellsToAddModList_1();
		$tabs=substr($tabs,0,-8);
	}
	//blackCellsToRemoveList				CellIndexList				OPTIONAL,
	if($blackCellsToRemoveList_option_bit=='1'){
		$tabs=$tabs . str_repeat($tab,2);
		$rrc_out=$rrc_out . "\n" . $tabs . 'blackCellsToRemoveList {';
		CellIndexList_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$tabs=substr($tabs,0,-8);
	}
	//blackCellsToAddModList				BlackCellsToAddModList		OPTIONAL,
	if($blackCellsToAddModList_option_bit=='1'){
		$tabs=$tabs . str_repeat($tab,2);
		$rrc_out=$rrc_out . "\n" . $tabs . 'blackCellsToAddModList {';
		BlackCellsToAddModList_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$tabs=substr($tabs,0,-8);
	}
	//cellForWhichToReportCGI				PhysCellId					OPTIONAL,
	if($cellForWhichToReportCGI_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellForWhichToReportCGI ' . PhysCellId_1();
	}
	
	//$rrc_out=$rrc_out . "\n" . "DEBUG:" . substr($rrc_msg_bin,0,8); 
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}
function MeasObjectUTRA(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'MeasObjectEUTRA {';
	$MeasObjectUTRA_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options
	$offsetFreq_default_bit=substr($rrc_msg_bin,0,1);
	$cellsToRemoveList_options_bit=substr($rrc_msg_bin,1,1);
	$cellsToAddModList_option_bit=substr($rrc_msg_bin,2,1);
	$cellForWhichToReportCGI_option_bit=substr($rrc_msg_bin,3,1);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	
	//carrierFreq							ARFCN-ValueUTRA,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'carrierFreq ' . ARFCN_ValueUTRA_1();
	//offsetFreq							Q-OffsetRangeInterRAT		DEFAULT 0,
	if($offsetFreq_default_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq DEFAULT 0';
	}
	if($offsetFreq_default_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq ' . Q_OffsetRangeInterRAT_1();;
	}
	//cellsToRemoveList					CellIndexList				OPTIONAL,
	if($cellsToRemoveList_options_bit=='1'){
		$tabs=$tabs . str_repeat($tab,2);
		$rrc_out=$rrc_out . "\n" . $tabs . 'cellsToRemoveList {';
		CellIndexList_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$tabs=substr($tabs,0,-8);
	}
	//cellsToAddModList	 OPTIONAL
	if($cellsToAddModList_option_bit=='1'){
		$cellsToAddModList_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($cellsToAddModList_choice_bit=='0'){
			//cellsToAddModListUTRA-FDD			CellsToAddModListUTRA-FDD,
			$tabs=$tabs . str_repeat($tab,2);
			$rrc_out=$rrc_out . "\n" . $tabs . 'cellsToAddModListUTRA-FDD {';
			CellsToAddModListUTRA_FDD_1();
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			$tabs=substr($tabs,0,-8);
		}
		if($cellsToAddModList_choice_bit=='1'){
			//cellsToAddModListUTRA-TDD			CellsToAddModListUTRA-TDD
			$tabs=$tabs . str_repeat($tab,2);
			$rrc_out=$rrc_out . "\n" . $tabs . 'cellsToAddModListUTRA-TDD {';
			CellsToAddModListUTRA_TDD_1();
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			$tabs=substr($tabs,0,-8);
		}
	}
	//cellForWhichToReportCGI OPTIONAL
	if($cellForWhichToReportCGI_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellForWhichToReportCGI {';
		$cellForWhichToReportCGI_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($cellForWhichToReportCGI_choice_bit=='0'){
			//utra-FDD							PhysCellIdUTRA-FDD
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'utra-FDD ' .PhysCellIdUTRA_FDD_1();
		}
		if($cellForWhichToReportCGI_choice_bit=='1'){
			//utra-TDD							PhysCellIdUTRA-TDD
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'utra-TDD ' . PhysCellIdUTRA_TDD_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	
	//close MeasObjectUTRA
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}
function MeasObjectGERAN(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'MeasObjectGERAN {';
	$MeasObjectUTRA_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$offsetFreq_default_bit=substr($rrc_msg_bin,0,1);
	$ncc_Permitted_default_bit=substr($rrc_msg_bin,1,1);
	$cellForWhichToReportCGI_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	
	//carrierFreqs						CarrierFreqsGERAN,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'carrierFreqs {';
	$tabs=$tabs . str_repeat($tab,3);
	CarrierFreqsGERAN_1();
	$tabs = substr($tabs,0,-12);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	//offsetFreq							Q-OffsetRangeInterRAT		DEFAULT 0,
	if($offsetFreq_default_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq DEFAULT 0';
	}
	if($offsetFreq_default_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'offsetFreq ' . Q_OffsetRangeInterRAT_1();
	}
	//ncc-Permitted						BIT STRING(SIZE (8))		DEFAULT '11111111'B,
	if($ncc_Permitted_default_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ncc-Permitted DEFAULT 11111111';
	}
	if($ncc_Permitted_default_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ncc-Permitted ' . substr($rrc_msg_bin,0,8);
		$rrc_msg_bin=substr($rrc_msg_bin,8);
	}
	//cellForWhichToReportCGI				PhysCellIdGERAN				OPTIONAL
	if($cellForWhichToReportCGI_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellForWhichToReportCGI {';
		$tabs=$tabs . str_repeat($tab,3);
		PhysCellIdGERAN_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}
function MeasObjectCDMA2000(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'MeasObjectCDMA2000 {';
	$MeasObjectUTRA_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check default and options
	
	$searchWindowSize_option_bit=substr($rrc_msg_bin,0,1);
	$offsetFreq_default_bit=substr($rrc_msg_bin,1,1);
	$cellsToRemoveList_option_bit=substr($rrc_msg_bin,0,1);
	$cellsToAddModList_option_bit=substr($rrc_msg_bin,0,1);
	$cellForWhichToReportCGI_option_bit=substr($rrc_msg_bin,0,1);
	
	//cdma2000-Type						CDMA2000-Type,
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cdma2000-Type ' . CDMA2000_Type_1();
	//carrierFreq							CarrierFreqCDMA2000,
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'carrierFreq {';
	$tabs=$tabs . str_repeat($tab,2);
	CarrierFreqCDMA2000_1();	
	$tabs=substr($tabs,0,-8);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	//searchWindowSize					INTEGER (0..15)						OPTIONAL,	-- Need ON
	if($searchWindowSize_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'searchWindowSize ' . bindec(substr($rrc_msg_bin,0,4));
		$rrc_msg_bin=substr($rrc_msg_bin,4);
	}
	//offsetFreq							Q-OffsetRangeInterRAT				DEFAULT 0,
	if($offsetFreq_default_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'offsetFreq DEFAULT 0';
	}
	if($offsetFreq_default_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'offsetFreq ' . Q_OffsetRangeInterRAT_1();
	}
	//cellsToRemoveList					CellIndexList						OPTIONAL,	-- Need ON
	if($cellsToRemoveList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellsToRemoveList {';
		$tabs=$tabs . str_repeat($tab,2);	
		CellIndexList_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//cellsToAddModList					CellsToAddModListCDMA2000			OPTIONAL,	-- Need ON
	if($cellsToAddModList_option_bit=='1'){
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellsToAddModList {';
		$tabs=$tabs . str_repeat($tab,2);
		CellsToAddModListCDMA2000_1();		
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//cellForWhichToReportCGI				PhysCellIdCDMA2000					OPTIONAL,	-- Need ON
	if($cellForWhichToReportCGI_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellForWhichToReportCGI ' . PhysCellIdCDMA2000_1();
	}

	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
}

function ARFCN_ValueEUTRA_1(){
	global $rrc_msg_bin;
	//ARFCN-ValueEUTRA ::=				INTEGER (0..maxEARFCN)
	//maxEARFCN					INTEGER ::= 65535
	$ret=bindec(substr($rrc_msg_bin,0,16));
	$rrc_msg_bin=substr($rrc_msg_bin,16);
	return $ret;
}
function AllowedMeasBandwidth_1(){
	global $rrc_msg_bin;
	//AllowedMeasBandwidth ::=	ENUMERATED {mbw6, mbw15, mbw25, mbw50, mbw75, mbw100}
	$AllowedMeasBandwidth_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($AllowedMeasBandwidth_bit){
		case '000':
			$ret='mbw6';
			break;
		case '001':
			$ret='mbw15';
			break;
		case '010':
			$ret='mbw25';
			break;
		case '011':
			$ret='mbw50';
			break;
		case '100':
			$ret='mbw75';
			break;
		case '101':
			$ret='mbw100';
			break;
		default:
			break;
	}
	return $ret;
}
function PresenceAntennaPort1_1(){
	global $rrc_msg_bin;
	//PresenceAntennaPort1 ::=				BOOLEAN
	$PresenceAntennaPort1_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($PresenceAntennaPort1_bit=='0'){return 'FALSE';}
	if($PresenceAntennaPort1_bit=='1'){return 'TRUE';}
}
function NeighCellConfig_1(){
	global $rrc_msg_bin;
	//NeighCellConfig ::=			BIT STRING (SIZE (2))
	$ret=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	return $ret;
}
function Q_OffsetRange_1(){
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);	
	switch ($in){
		case '00000':
			$ret='dB-24';
			break;
		case '00001':
			$ret='dB-22';
			break;
		case '00010':
			$ret='dB-20';
			break;
		case '00011':
			$ret='dB-18';
			break;
		case '00100':
			$ret='dB-16';
			break;
		case '00101':
			$ret='dB-14';
			break;
		case '00110':
			$ret='dB-12';
			break;
		case '00111':
			$ret='dB-10';
			break;
		case '01000':
			$ret='dB-8';
			break;
		case '01001':
			$ret='dB-6';
			break;
		case '01010':
			$ret='dB-5';
			break;
		case '01011':
			$ret='dB-4';
			break;
		case '01100':
			$ret='dB-3';
			break;
		case '01101':
			$ret='dB-2';
			break;
		case '01110':
			$ret='dB-1';
			break;
		case '01111':
			$ret='dB0';
			break;
		case '10000':
			$ret='dB1';
			break;
		case '10001':
			$ret='dB2';
			break;
		case '10010':
			$ret='dB3';
			break;
		case '10011':
			$ret='dB4';
			break;
		case '10100':
			$ret='dB5';
			break;
		case '10101':
			$ret='dB6';
			break;
		case '10110':
			$ret='dB8';
			break;
		case '10111':
			$ret='dB10';
			break;
		case '11000':
			$ret='dB12';
			break;
		case '11001':
			$ret='dB14';
			break;
		case '11010':
			$ret='dB16';
			break;
		case '11011':
			$ret='dB18';
			break;
		case '11100':
			$ret='dB20';
			break;
		case '11101':
			$ret='dB22';
			break;
		case '11110':
			$ret='dB24';
			break;
		default:
			break;
	}
	return $ret;

}
function CellIndexList_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CellIndexList ::=						SEQUENCE (SIZE (1..maxCellMeas)) OF CellIndex
	//CellIndex ::=							INTEGER (1..maxCellMeas)
	//maxCellMeas					INTEGER ::= 32
	//$rrc_out=$rrc_out . "\n" . $tabs . 'CellIndexList {';
	$CellIndexList_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($CellIndexList_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'CellIndex ' . bindec(substr($rrc_msg_bin,0,5));
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$CellIndexList_size=$CellIndexList_size-1;
	}
	//$rrc_out=$rrc_out . "\n" . $tabs . '}';
}
function CellsToAddModList_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CellsToAddModList ::=				SEQUENCE (SIZE (1..maxCellMeas)) OF CellsToAddMod
	//CellsToAddMod ::=	SEQUENCE {
	//cellIndex							INTEGER (1..maxCellMeas),
	//physCellId							PhysCellId,
	//cellIndividualOffset				Q-OffsetRange
	//}
	$rrc_out=$rrc_out . "\n" . $tabs . 'CellsToAddModList {';
	$CellsToAddModList_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($CellsToAddModList_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'CellsToAddMod {';
		$cellIndex_value=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_out=$rrc_out . "\n" . str_repeat($tab,2) . 'cellIndex ' . $cellIndex_value;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'physCellId ' . PhysCellId_1();
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellIndividualOffset ' . Q_OffsetRange_1();
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		$CellsToAddModList_size=$CellsToAddModList_size-1;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
}
function PhysCellId_1(){
	global $rrc_msg_bin;
	//PhysCellId ::=						INTEGER (0..503)
	$ret=bindec(substr($rrc_msg_bin,0,9));
	$rrc_msg_bin=substr($rrc_msg_bin,9);
	return $ret;
}
function BlackCellsToAddModList_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//BlackCellsToAddModList ::=			SEQUENCE (SIZE (1..maxCellMeas)) OF BlackCellsToAddMod
	//BlackCellsToAddMod ::=	SEQUENCE {
	//cellIndex							INTEGER (1..maxCellMeas),
	//physCellIdRange						PhysCellIdRange
	//}
	$BlackCellsToAddModList_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($BlackCellsToAddModList_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'BlackCellsToAddMod {';
		$cellIndex_value=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellIndex ' . $cellIndex_value;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		
		//physCellIdRange
		//$tabs=$tabs . 
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'physCellIdRange {';
		$tabs=$tabs . str_repeat($tab,3);
		PhysCellIdRange_1();
		$tabs=substr($tabs,0,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		$BlackCellsToAddModList_size=$BlackCellsToAddModList_size-1;
	}
}
function PhysCellIdRange_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//PhysCellIdRange ::=				SEQUENCE {
	//start							PhysCellId,
	//range							ENUMERATED {
	//			n4, n8, n12, n16, n24, n32, n48, n64, n84,
	//			n96, n128, n168, n252, n504, spare2,
	//			spare1} 					OPTIONAL	-- Need OP
	//}
	$rrc_out=$rrc_out . "\n" . $tabs . 'start ' . PhysCellId_1();
	//$rrc_out=$rrc_out . "\n" . $tabs . 'range ' . 
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='n4';
			break;
		case '0001':
			$ret='n8';
			break;
		case '0010':
			$ret='n12';
			break;
		case '0011':
			$ret='n16';
			break;
		case '0100':
			$ret='n24';
			break;
		case '0101':
			$ret='n32';
			break;
		case '0110':
			$ret='n48';
			break;
		case '0111':
			$ret='n64';
			break;
		case '1000':
			$ret='n84';
			break;
		case '1001':
			$ret='n96';
			break;
		case '1010':
			$ret='n128';
			break;
		case '1011':
			$ret='n168';
			break;
		case '1100':
			$ret='n252';
			break;
		case '1101':
			$ret='n504';
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
	$rrc_out=$rrc_out . "\n" . $tabs . 'range ' . $ret;
}
function ARFCN_ValueUTRA_1(){
	global $rrc_msg_bin;
	//ARFCN-ValueUTRA ::=					INTEGER (0..16383)
	$in=substr($rrc_msg_bin,0,14);
	$rrc_msg_bin=substr($rrc_msg_bin,14);
	return bindec($in);
}
function Q_OffsetRangeInterRAT_1(){
	global $rrc_msg_bin;
	//Q-OffsetRangeInterRAT ::=					INTEGER (-15..15)
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)-15;
}
function CellsToAddModListUTRA_FDD_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CellsToAddModListUTRA-FDD ::=		SEQUENCE (SIZE (1..maxCellMeas)) OF CellsToAddModUTRA-FDD 
	//CellsToAddModUTRA-FDD ::=	SEQUENCE {
	//	cellIndex							INTEGER (1..maxCellMeas),
	//	physCellId							PhysCellIdUTRA-FDD
	//}
	//maxCellMeas					INTEGER ::= 32
	$CellsToAddModListUTRA_FDD_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	
	while($CellsToAddModListUTRA_FDD_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'CellsToAddModListUTRA-FDD {';
		$cellIndex_value=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellIndex ' . $cellIndex_value;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'physCellId ' . PhysCellIdUTRA_FDD_1();
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		$CellsToAddModListUTRA_FDD_size=$CellsToAddModListUTRA_FDD_size-1;
	}
	
}
function PhysCellIdUTRA_FDD_1(){
	global $rrc_msg_bin;
	//PhysCellIdUTRA-FDD ::=				INTEGER (0..511)
	$in=substr($rrc_msg_bin,0,9);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in);
}
function CellsToAddModListUTRA_TDD_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CellsToAddModListUTRA-TDD ::=		SEQUENCE (SIZE (1..maxCellMeas)) OF CellsToAddModUTRA-TDD 
	//CellsToAddModUTRA-TDD ::=	SEQUENCE {
	//cellIndex							INTEGER (1..maxCellMeas),
	//physCellId							PhysCellIdUTRA-TDD
	//}
	//maxCellMeas=32
	$CellsToAddModListUTRA_TDD_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($CellsToAddModListUTRA_TDD_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'CellsToAddModListUTRA-FDD {';
		$cellIndex_value=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellIndex ' . $cellIndex_value;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'physCellId ' . PhysCellIdUTRA_TDD_1();
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		$CellsToAddModListUTRA_TDD_size=$CellsToAddModListUTRA_TDD_size-1;
	}
}
function PhysCellIdUTRA_TDD_1(){
	global $rrc_msg_bin;
	//PhysCellIdUTRA-TDD ::=				INTEGER (0..127)
	$in=substr($rrc_msg_bin,0,7);
	$rrc_msg_bin=substr($rrc_msg_bin,7);
	return bindec($in);
}
function CarrierFreqsGERAN_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CarrierFreqsGERAN ::=			SEQUENCE {
	//startingARFCN						ARFCN-ValueGERAN,
	//bandIndicator						BandIndicatorGERAN,
	//followingARFCNs						CHOICE {
	//	explicitListOfARFCNs				ExplicitListOfARFCNs,
	//	equallySpacedARFCNs					SEQUENCE {
	//		arfcn-Spacing						INTEGER (1..8),
	//		numberOfFollowingARFCNs				INTEGER (0..31)
	//	},
	//	variableBitMapOfARFCNs				OCTET STRING (SIZE (1..16))
	//}
	//}
	//ExplicitListOfARFCNs ::=			SEQUENCE (SIZE (0..31)) OF ARFCN-ValueGERAN
	$rrc_out=$rrc_out . "\n" . $tabs . 'startingARFCN ' . ARFCN_ValueGERAN_1();
	$rrc_out=$rrc_out . "\n" . $tabs . 'bandIndicator ' . BandIndicatorGERAN_1();
	
	
}
function ARFCN_ValueGERAN_1(){
	global $rrc_msg_bin;
	//ARFCN-ValueGERAN ::=			INTEGER (0..1023)
	$in=substr($rrc_msg_bin,0,10);
	$rrc_msg_bin=substr($rrc_msg_bin,10);
	return bindec($in);
}
function BandIndicatorGERAN_1(){
	global $rrc_msg_bin;
	//BandIndicatorGERAN ::=			ENUMERATED {dcs1800, pcs1900}
	$in=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($in=='0'){return 'dcs1800';}
	if($in=='1'){return 'pcs1900';}
}
function PhysCellIdGERAN_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//networkColourCode					BIT STRING (SIZE (3)),
	//baseStationColourCode				BIT STRING (SIZE (3))
	$rrc_out=$rrc_out . "\n" . $tabs . 'networkColourCode ' . substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$rrc_out=$rrc_out . "\n" . $tabs . 'baseStationColourCode ' .  substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
}
function CDMA2000_Type_1(){
	global $rrc_msg_bin;
	//CDMA2000-Type ::=					ENUMERATED {type1XRTT, typeHRPD}
	$in=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($in=='0'){return 'type1XRTT';}
	if($in=='1'){return 'typeHRPD';}
}
function CarrierFreqCDMA2000_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//CarrierFreqCDMA2000 ::=			SEQUENCE {
	//bandClass							BandclassCDMA2000,
	//arfcn							ARFCN-ValueCDMA2000
	//}
	$rrc_out=$rrc_out . "\n" . $tabs. $tab . 'bandClass ' . BandclassCDMA2000_1();	
	$rrc_out=$rrc_out . "\n" . $tabs. $tab . 'arfcn ' . ARFCN_ValueCDMA2000_1();
}
function BandclassCDMA2000_1(){
	global $rrc_msg_bin;
	//check extention
	$ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	switch($in){
		case '00000':
			$ret='bc0';
			break;
		case '00001':
			$ret='bc1';
			break;
		case '00010':
			$ret='bc2';
			break;
		case '00011':
			$ret='bc3';
			break;
		case '00100':
			$ret='bc4';
			break;
		case '00101':
			$ret='bc5';
			break;
		case '00110':
			$ret='bc6';
			break;
		case '00111':
			$ret='bc7';
			break;
		case '01000':
			$ret='bc8';
			break;
		case '01001':
			$ret='bc9';
			break;
		case '01010':
			$ret='bc10';
			break;
		case '01011':
			$ret='bc11';
			break;
		case '01100':
			$ret='bc12';
			break;
		case '01101':
			$ret='bc13';
			break;
		case '01110':
			$ret='bc14';
			break;
		case '01111':
			$ret='bc15';
			break;
		case '10000':
			$ret='bc16';
			break;
		case '10001':
			$ret='bc17';
			break;
		case '10010':
			$ret='bc18-v9a0';
			break;
		case '10011':
			$ret='bc19-v9a0';
			break;
		case '10100':
			$ret='bc20-v9a0';
			break;
		case '10101':
			$ret='bc21-v9a0';
			break;
		case '10110':
			$ret='spare10';
			break;
		case '10111':
			$ret='spare9';
			break;
		case '11000':
			$ret='spare8';
			break;
		case '11001':
			$ret='spare7';
			break;
		case '11010':
			$ret='spare6';
			break;
		case '11011':
			$ret='spare5';
			break;
		case '11100':
			$ret='spare4';
			break;
		case '11101':
			$ret='spare3';
			break;
		case '11110':
			$ret='spare2';
			break;
		case '11111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}
function ARFCN_ValueCDMA2000_1(){
	global $rrc_msg_bin;
	//ARFCN-ValueCDMA2000 ::=			INTEGER (0..2047)
	$in=substr($rrc_msg_bin,0,11);
	$rrc_msg_bin=substr($rrc_msg_bin,11);
	return bindec($in);
}
function CellsToAddModListCDMA2000_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$CellsToAddModListCDMA2000_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($CellsToAddModListCDMA2000_size>0){
		//cellIndex INTEGER (1..maxCellMeas),
		$cellIndex_value=bindec(substr($rrc_msg_bin,0,5))+1;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellIndex ' . $cellIndex_value;
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		//physCellId PhysCellIdCDMA2000
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'physCellId ' . PhysCellIdCDMA2000_1();
		$CellsToAddModListCDMA2000_size=$CellsToAddModListCDMA2000_size-1;
	}
}
function PhysCellIdCDMA2000_1(){
	global $rrc_msg_bin;
	//PhysCellIdCDMA2000 ::=			INTEGER (0..maxPNOffset)
	//maxPNOffset					INTEGER ::=	511
	$in=substr($rrc_msg_bin,0,9);
	$rrc_msg_bin=substr($rrc_msg_bin,9);
	return bindec($in);
}
function ReportConfigId_1(){
	global $rrc_msg_bin;
	//ReportConfigId ::=					INTEGER (1..maxReportConfigId)
	//maxReportConfigId			INTEGER ::= 32
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)+1;
}
function ReportConfigEUTRA_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention and do nothing
	$ReportConfigEUTRA_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//return;
	//triggerType
	$triggerType_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//return;
	if($triggerType_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'triggerType	: event {';
		//eventId	
		$eventId_ext_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$eventId_choice_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);

		//ThresholdEUTRA ::=					CHOICE{
		//threshold-RSRP						RSRP-Range,
		//threshold-RSRQ						RSRQ-Range
		//}

		//RSRP-Range ::=						INTEGER(0..97)
		//RSRQ-Range ::=						INTEGER(0..34)
		//$rrc_out=$rrc_out . "\n" . 'DEBUG:' . $eventId_choice_bit;
		//return;
		switch($eventId_choice_bit){
			case '000':
				//eventA1
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'eventId: eventA1: a1-Threshold {';
				$tabs=$tabs . str_repeat($tab,2);
				ThresholdEUTRA_1();
				$tabs=substr($tabs,0,-8);
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
				break;
			case '001':
				//eventA2
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'eventId: eventA2: a2-Threshold {';
				$tabs=$tabs . str_repeat($tab,2);
				ThresholdEUTRA_1();
				$tabs=substr($tabs,0,-8);
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
				break;
			case '010':
				//eventA3
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'eventId: eventA3 {';
				//a3-Offset							INTEGER (-30..30),
				$a3_Offset_value=bindec(substr($rrc_msg_bin,0,6))-30;
				$rrc_msg_bin=substr($rrc_msg_bin,6);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'a3-Offset ' . $a3_Offset_value;
				
				//reportOnLeave						BOOLEAN
				$reportOnLeave_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				if($reportOnLeave_bit=='0'){
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'reportOnLeave	FALSE';
				}
				if($reportOnLeave_bit=='1'){
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'reportOnLeave	FALSE';
				}
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
				break;
			case '011':
				//eventA4
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'eventId: eventA4: a4-Threshold {';
				$tabs=$tabs . str_repeat($tab,2);
				ThresholdEUTRA_1();
				$tabs=substr($tabs,0,-8);
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
				break;
			case '100':
				//eventA5
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'eventId: eventA5 {';
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'a5-Threshold1 {';
				$tabs=$tabs . str_repeat($tab,3);
				ThresholdEUTRA_1();
				$tabs=substr($tabs,0,-12);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'a5-Threshold2 {';
				$tabs=$tabs . str_repeat($tab,3);
				ThresholdEUTRA_1();
				$tabs=substr($tabs,0,-12);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
				break;
			default:
				break;
		}

		//hysteresis						Hysteresis,
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'hysteresis ' . Hysteresis_1();
		//timeToTrigger						TimeToTrigger
		//$rrc_out=$rrc_out . "\n" . 'DEBUG:' . substr($rrc_msg_bin,0,8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'timeToTrigger ' . TimeToTrigger_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	if($triggerType_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'triggerType	: periodical{';
		//
		$purpose_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($purpose_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'purpose reportStrongestCells';
		}
		if($purpose_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'purpose reportCGI';
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//triggerQuantity ENUMERATED {rsrp, rsrq},
	$triggerQuantity_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($triggerQuantity_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'triggerQuantity rsrp';
	}
	if($triggerQuantity_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'triggerQuantity rsrq';
	}
	//reportQuantity						ENUMERATED {sameAsTriggerQuantity, both},
	$reportQuantity_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($reportQuantity_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'reportQuantity sameAsTriggerQuantity';
	}
	if($reportQuantity_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'reportQuantity both';
	}
	//maxReportCells						INTEGER (1..maxCellReport),
	//maxCellReport				INTEGER ::= 8
	$maxReportCells_value=bindec(substr($rrc_msg_bin,0,3))+1;
	$rrc_out=$rrc_out . "\n" . $tabs . 'maxReportCells ' . $maxReportCells_value;
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	//reportInterval						ReportInterval,
	$rrc_out=$rrc_out . "\n" . $tabs . 'reportInterval ' . ReportInterval_1();
	//reportAmount						ENUMERATED {r1, r2, r4, r8, r16, r32, r64, infinity},
	$reportAmount_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($reportAmount_bit){
		case '000':
			$reportAmount_value='r1';
			break;
		case '001':
			$reportAmount_value='r2';
			break;
		case '010':
			$reportAmount_value='r4';
			break;
		case '011':
			$reportAmount_value='r8';
			break;
		case '100':
			$reportAmount_value='r16';
			break;
		case '101':
			$reportAmount_value='r32';
			break;
		case '110':
			$reportAmount_value='r64';
			break;
		case '111':
			$reportAmount_value='infinity';
			break;
		default:
			break;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . 'reportAmount ' . $reportAmount_value;
	
}
function ThresholdEUTRA_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$ThresholdEUTRA_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//RSRP-Range ::=						INTEGER(0..97)
	//RSRQ-Range ::=						INTEGER(0..34)
	if($ThresholdEUTRA_choice_bit=='0'){
		//threshold-RSRP						RSRP-Range,
		$rrc_out=$rrc_out . "\n" . $tabs . 'threshold-RSRP ' . bindec(substr($rrc_msg_bin,0,7));
		$rrc_msg_bin=substr($rrc_msg_bin,7);
	}
	if($ThresholdEUTRA_choice_bit=='1'){
		//threshold-RSRQ						RSRQ-Range
		$rrc_out=$rrc_out . "\n" . $tabs . 'threshold-RSRQ ' . bindec(substr($rrc_msg_bin,0,6));
		$rrc_msg_bin=substr($rrc_msg_bin,6);
	}
}
function Hysteresis_1(){
	global $rrc_msg_bin;
	//Hysteresis ::=							INTEGER (0..30)
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in);
}
function TimeToTrigger_1(){
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='ms0';
			break;
		case '0001':
			$ret='ms40';
			break;
		case '0010':
			$ret='ms64';
			break;
		case '0011':
			$ret='ms80';
			break;
		case '0100':
			$ret='ms100';
			break;
		case '0101':
			$ret='ms128';
			break;
		case '0110':
			$ret='ms160';
			break;
		case '0111':
			$ret='ms256';
			break;
		case '1000':
			$ret='ms320';
			break;
		case '1001':
			$ret='ms480';
			break;
		case '1010':
			$ret='ms512';
			break;
		case '1011':
			$ret='ms640';
			break;
		case '1100':
			$ret='ms1024';
			break;
		case '1101':
			$ret='ms1280';
			break;
		case '1110':
			$ret='ms2560';
			break;
		case '1111':
			$ret='ms5120';
			break;
		default:
			break;
	}
	return $ret;
}
function ReportInterval_1(){
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='ms120';
			break;
		case '0001':
			$ret='ms240';
			break;
		case '0010':
			$ret='ms480';
			break;
		case '0011':
			$ret='ms640';
			break;
		case '0100':
			$ret='ms1024';
			break;
		case '0101':
			$ret='ms2048';
			break;
		case '0110':
			$ret='ms5120';
			break;
		case '0111':
			$ret='ms10240';
			break;
		case '1000':
			$ret='min1';
			break;
		case '1001':
			$ret='min6';
			break;
		case '1010':
			$ret='min12';
			break;
		case '1011':
			$ret='min30';
			break;
		case '1100':
			$ret='min60';
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
function ReportConfigInterRAT_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention and do nothing
	$ReportConfigInterRAT_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//triggerType
	$rrc_out=$rrc_out . "\n" . $tabs . 'triggerType {'; 
	$triggerType_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($triggerType_choice_bit=='0'){
		//event
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'event {';
		//eventId
		//eventId extention
		$eventId_ext_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//eventId choice
		$eventId_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($eventId_choice_bit=='0'){
			//eventB1
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'eventId : eventB1 : b1-Threshold {';
			//
			$b1_Threshold_choice_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			if($b1_Threshold_choice_bit=='00'){
				//b1-ThresholdUTRA					ThresholdUTRA
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b1-ThresholdUTRA {';
				$tabs=$tabs . str_repeat($tab,4);
				ThresholdUTRA_1();
				$tabs = substr($tabs,0,-16);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			}
			if($b1_Threshold_choice_bit=='01'){
				//b1-ThresholdGERAN					ThresholdGERAN,
				//ThresholdGERAN ::= 				INTEGER (0..63)
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b1-ThresholdGERAN ' . bindec(substr($rrc_msg_bin,0,6));
				$rrc_msg_bin=substr($rrc_msg_bin,6);
			}
			if($b1_Threshold_choice_bit=='10'){
				//b1-ThresholdCDMA2000				ThresholdCDMA2000
				//ThresholdCDMA2000 ::= 			INTEGER (0..63)
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b1-ThresholdCDMA2000 ' . bindec(substr($rrc_msg_bin,0,6));
				$rrc_msg_bin=substr($rrc_msg_bin,6);
			}
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		if($eventId_choice_bit=='1'){
			//eventB2
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'eventId : eventB2 {';
			//
			//b2-Threshold1						ThresholdEUTRA
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b2-Threshold1 {';
			$tabs=$tabs . str_repeat($tab,4);
			ThresholdUTRA_1();
			$tabs = substr($tabs,0,-16);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			
			//b2-Threshold2
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . ' : b2-Threshold {';
			//
			$b2_Threshold_choice_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			if($b2_Threshold_choice_bit=='00'){
				//b2-Threshold2UTRA					ThresholdUTRA
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b2-ThresholdUTRA {';
				$tabs=$tabs . str_repeat($tab,4);
				ThresholdUTRA_1();
				$tabs = substr($tabs,0,-16);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			}
			if($b2_Threshold_choice_bit=='01'){
				//b2-Threshold2GERAN				ThresholdGERAN,
				//ThresholdGERAN ::= 				INTEGER (0..63)
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b2-ThresholdGERAN ' . bindec(substr($rrc_msg_bin,0,6));
				$rrc_msg_bin=substr($rrc_msg_bin,6);
			}
			if($b2_Threshold_choice_bit=='10'){
				//b2-Threshold2CDMA2000				ThresholdCDMA2000
				//ThresholdCDMA2000 ::= 			INTEGER (0..63)
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'b2-ThresholdCDMA2000 ' . bindec(substr($rrc_msg_bin,0,6));
				$rrc_msg_bin=substr($rrc_msg_bin,6);
			}
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		//hysteresis						Hysteresis,
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'hysteresis ' . Hysteresis_1();
		//timeToTrigger					TimeToTrigger
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'timeToTrigger ' . TimeToTrigger_1();
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($triggerType_choice_bit=='1'){
		//periodical
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'periodical {';
		//
		$periodical_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($periodical_bit=='00'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'purpose reportStrongestCells';
		}
		if($periodical_bit=='01'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'purpose reportStrongestCellsForSON';
		}
		if($periodical_bit=='10'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'purpose reportCGI';
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//maxReportCells						INTEGER (1..maxCellReport),
	//maxCellReport				INTEGER ::= 8
	$maxCellReport=bindec(substr($rrc_msg_bin,0,3))+1;
	$rrc_out=$rrc_out . "\n" . $tabs . 'maxReportCells ' . $maxCellReport;
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	//reportInterval						ReportInterval,
	$rrc_out=$rrc_out . "\n" . $tabs . 'reportInterval ' . ReportInterval_1();
	//reportAmount						ENUMERATED {r1, r2, r4, r8, r16, r32, r64, infinity},
	$reportAmount_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($reportAmount_bit){
		case '000':
			$reportAmount_value='r1';
			break;
		case '001':
			$reportAmount_value='r2';
			break;
		case '010':
			$reportAmount_value='r4';
			break;
		case '011':
			$reportAmount_value='r8';
			break;
		case '100':
			$reportAmount_value='r16';
			break;
		case '101':
			$reportAmount_value='r32';
			break;
		case '110':
			$reportAmount_value='r64';
			break;
		case '111':
			$reportAmount_value='infinity';
			break;
		default:
			break;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . 'reportAmount ' . $reportAmount_value;
}
function ThresholdUTRA_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//ThresholdUTRA ::=					CHOICE{
	//utra-RSCP							INTEGER (-5..91),
	//utra-EcN0							INTEGER (0..49)
	//}
	$ThresholdUTRA_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($ThresholdUTRA_choice_bit=='0'){
		$utra_RSCP_value=bindec(substr($rrc_msg_bin,0,7))-5;
		$rrc_out=$rrc_out . "\n" . $tabs . 'utra-RSCP ' . $utra_RSCP_value;
		$rrc_msg_bin=substr($rrc_msg_bin,7);
	}
	if($ThresholdUTRA_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'utra-EcN0 ' . bindec(substr($rrc_msg_bin,0,6));
		$rrc_msg_bin=substr($rrc_msg_bin,6);
	}
}
function MeasId_1(){
	//MeasId ::=							INTEGER (1..maxMeasId)
	//maxMeasId					INTEGER ::= 32
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)+1;
}
function MeasIdToAddModList_1(){
	//MeasIdToAddModList ::=				SEQUENCE (SIZE (1..maxMeasId)) OF MeasIdToAddMod
	//maxMeasId					INTEGER ::= 32
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$MeasIdToAddModList_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($MeasIdToAddModList_size>0){
		//MeasIdToAddMod
		$rrc_out=$rrc_out . "\n" . $tabs . '{';
		//measId								MeasId,	
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'measId' . MeasId_1();
		//measObjectId						MeasObjectId,
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'measObjectId ' . MeasObjectId_1();
		//reportConfigId						ReportConfigId
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'reportConfigId ' . ReportConfigId_1();
		
		$rrc_out=$rrc_out . "\n" . $tabs . '{';
		$MeasIdToAddModList_size=$MeasIdToAddModList_size-1;
	}
	
}
function MeasObjectId_1(){
	global $rrc_msg_bin;
	//MeasObjectId ::=					INTEGER (1..maxObjectId)
	//maxObjectId					INTEGER ::= 32
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)+1;
}
function QuantityConfig_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention and do nothhing
	$QuantityConfig_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options
	$quantityConfigEUTRA_option_bit=substr($rrc_msg_bin,0,1);
	$quantityConfigUTRA_option_bit=substr($rrc_msg_bin,1,1);
	$quantityConfigGERAN_option_bit=substr($rrc_msg_bin,2,1);
	$quantityConfigCDMA2000_option_bit=substr($rrc_msg_bin,3,1);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	
	if($quantityConfigEUTRA_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'quantityConfigEUTRA {';
		//check default
		$filterCoefficientRSRP_default_bit=substr($rrc_msg_bin,0,1);
		$filterCoefficientRSRQ_default_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($filterCoefficientRSRP_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficientRSRP DEFAULT fc4';
		}
		if($filterCoefficientRSRP_default_bit=='1'){
			//filterCoefficientRSRP				FilterCoefficient
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficientRSRP ' . FilterCoefficient_1();
		}
		if($filterCoefficientRSRQ_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficientRSRQ DEFAULT fc4';
		}
		if($filterCoefficientRSRQ_default_bit=='1'){
			//filterCoefficientRSRQ				FilterCoefficient
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficientRSRQ ' . FilterCoefficient_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	if($quantityConfigUTRA_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'quantityConfigUTRA {';
		//check default
		$filterCoefficient_default_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		//measQuantityUTRA-FDD				ENUMERATED {cpich-RSCP, cpich-EcN0},
		$measQuantityUTRA_FDD_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($measQuantityUTRA_FDD_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'measQuantityUTRA-FDD cpich-RSCP';
		}
		if($measQuantityUTRA_FDD_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'measQuantityUTRA-FDD cpich-EcN0';
		}
		//measQuantityUTRA-TDD				ENUMERATED {pccpch-RSCP},
		$measQuantityUTRA_TDD_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . $tabs . 'measQuantityUTRA-TDD pccpch-RSCP';
		//filterCoefficient					FilterCoefficient					DEFAULT fc4		
		if($filterCoefficient_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'filterCoefficient DEFAULT fc4 ';
		}
		if($filterCoefficient_default_bit=='1'){			
			$rrc_out=$rrc_out . "\n" . $tabs . 'filterCoefficient ' . FilterCoefficient_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	if($quantityConfigGERAN_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'quantityConfigGERAN {';
		//check default
		$filterCoefficient2_FDD_r10_default_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($filterCoefficient2_FDD_r10_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficient2-FDD-r10 DEFAULT fc4';
		}
		if($filterCoefficient2_FDD_r10_default_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficient2-FDD-r10 ' . FilterCoefficient_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	if($quantityConfigCDMA2000_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'quantityConfigCDMA2000 {';
		//check default
		$filterCoefficient_default_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//measQuantityGERAN					ENUMERATED {rssi},
		$measQuantityGERAN_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'measQuantityGERAN rssi';
		//filterCoefficient					FilterCoefficient					DEFAULT fc2
		if($filterCoefficient_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficient DEFAULT fc2';
		}
		if($filterCoefficient_default_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'filterCoefficient ' . FilterCoefficient_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	
}
function FilterCoefficient_1(){
	global $rrc_msg_bin;
	//FilterCoefficient ::=					ENUMERATED {
	//										fc0, fc1, fc2, fc3, fc4, fc5,
	//										fc6, fc7, fc8, fc9, fc11, fc13, 
	//										fc15, fc17, fc19, spare1, ...}

	//check extention
	$FilterCoefficient_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	
	switch($in){
		case '0000':
			$ret='fc0';
			break;
		case '0001':
			$ret='fc1';
			break;
		case '0010':
			$ret='fc2';
			break;
		case '0011':
			$ret='fc3';
			break;
		case '0100':
			$ret='fc4';
			break;
		case '0101':
			$ret='fc5';
			break;
		case '0110':
			$ret='fc6';
			break;
		case '0111':
			$ret='fc7';
			break;
		case '1000':
			$ret='fc8';
			break;
		case '1001':
			$ret='fc9';
			break;
		case '1010':
			$ret='fc11';
			break;
		case '1011':
			$ret='fc13';
			break;
		case '1100':
			$ret='fc15';
			break;
		case '1101':
			$ret='fc17';
			break;
		case '1110':
			$ret='fc19';
			break;
		case '1111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}
function MeasGapConfig_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//MeasGapConfig ::=					CHOICE {
	//release								NULL,
	//setup								SEQUENCE {
	//	gapOffset							CHOICE {
	//			gp0									INTEGER (0..39),
	//			gp1									INTEGER (0..79),
	//			...
	//	}
	//}
	
	//check choice
	$MeasGapConfig_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	if($MeasGapConfig_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'release NULL';
	}
	if($MeasGapConfig_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'setup {';
		//check extention
		$gapOffset_ext_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//check choice
		$gapOffset_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		if($gapOffset_choice_bit=='0'){
			$gp0_bit=substr($rrc_msg_bin,0,6);
			$rrc_msg_bin=substr($rrc_msg_bin,6);
			$rrc_out=$rrc_out . "\n" . $tabs . 'gapOffset : gp0 ' . bindec($gp0_bit);
		}
		if($gapOffset_choice_bit=='1'){
			$gp1_bit=substr($rrc_msg_bin,0,7);
			$rrc_msg_bin=substr($rrc_msg_bin,7);
			$rrc_out=$rrc_out . "\n" . $tabs . 'gapOffset : gp1 ' . bindec($gp1_bit);
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
}
function RSRP_Range_1(){
	global $rrc_msg_bin;
	//RSRP-Range ::=						INTEGER(0..97)
	$in=substr($rrc_msg_bin,0,7);
	$rrc_msg_bin=substr($rrc_msg_bin,7);
	return bindec($in);
}
function PreRegistrationInfoHRPD_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//
	//check options
	$preRegistrationZoneId_option_bit=substr($rrc_msg_bin,0,1);
	$secondaryPreRegistrationZoneIdList_option_bit=substr($rrc_msg_bin,1,1);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	
	//preRegistrationAllowed				BOOLEAN,
	$reRegistrationAllowed_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($reRegistrationAllowed_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'preRegistrationAllowed FALSE';
	}
	if($reRegistrationAllowed_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'preRegistrationAllowed TRUE';
	}
	//preRegistrationZoneId				PreRegistrationZoneIdHRPD	OPTIONAL
	if($secondaryPreRegistrationZoneIdList_option_bit=='1'){
		$preRegistrationZoneId_bit=substr($rrc_msg_bin,0,8);
		$rrc_msg_bin=substr($rrc_msg_bin,8);
		$rrc_out=$rrc_out . "\n" . $tabs . 'preRegistrationZoneId ' . bindec($preRegistrationZoneId_bit);
	}
	//secondaryPreRegistrationZoneIdList	SecondaryPreRegistrationZoneIdListHRPD	OPTIONAL 
	if($secondaryPreRegistrationZoneIdList_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . 'secondaryPreRegistrationZoneIdList {';
		$secondaryPreRegistrationZoneIdList_size=bindec(substr($rrc_msg_bin,0,1))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		while($secondaryPreRegistrationZoneIdList_size>0){
			//
			$preRegistrationZoneId_bit=substr($rrc_msg_bin,0,8);
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'preRegistrationZoneId ' . bindec($preRegistrationZoneId_bit);
		
			$secondaryPreRegistrationZoneIdList_size=$secondaryPreRegistrationZoneIdList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function MobilityStateParameters_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//MobilityStateParameters ::=			SEQUENCE {
	//t-Evaluation						ENUMERATED {
	//										s30, s60, s120, s180, s240, spare3, spare2, spare1},
	//t-HystNormal						ENUMERATED {
	//										s30, s60, s120, s180, s240, spare3, spare2, spare1},
	//n-CellChangeMedium					INTEGER (1..16),
	//n-CellChangeHigh					INTEGER (1..16)
	//}
	
	//t-Evaluation	
	$t_Evaluation_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($t_Evaluation_bit){
		case '000':
			$t_Evaluation_value='s30';
			break;
		case '001':
			$t_Evaluation_value='s60';
			break;
		case '010':
			$t_Evaluation_value='s120';
			break;
		case '011':
			$t_Evaluation_value='s180';
			break;
		case '100':
			$t_Evaluation_value='s240';
			break;
		case '101':
			$t_Evaluation_value='spare3';
			break;
		case '110':
			$t_Evaluation_value='spare2';
			break;
		case '111':
			$t_Evaluation_value='sprare1';
			break;
		default:
			break;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . 't-Evaluation ' . $t_Evaluation_value;
	
	//t-HystNormal	
	$t_HystNormal_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($t_HystNormal_bit){
		case '000':
			$t_HystNormal_value='s30';
			break;
		case '001':
			$t_HystNormal_value='s60';
			break;
		case '010':
			$t_HystNormal_value='s120';
			break;
		case '011':
			$t_HystNormal_value='s180';
			break;
		case '100':
			$t_HystNormal_value='s240';
			break;
		case '101':
			$t_HystNormal_value='spare3';
			break;
		case '110':
			$t_HystNormal_value='spare2';
			break;
		case '111':
			$t_HystNormal_value='sprare1';
			break;
		default:
			break;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . 't-HystNormal ' . $t_HystNormal_value;
	
	//
	//n-CellChangeMedium					INTEGER (1..16),
	$n_CellChangeMedium_value=bindec(substr($rrc_msg_bin,0,4))+1;
	$rrc_out=$rrc_out . "\n" . $tabs . 'n-CellChangeMedium ' . $n_CellChangeMedium_value;
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	//n-CellChangeHigh					INTEGER (1..16)
	$n_CellChangeHigh_value=bindec(substr($rrc_msg_bin,0,4))+1;
	$rrc_out=$rrc_out . "\n" . $tabs . 'n-CellChangeHigh ' . $n_CellChangeHigh_value;
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	
}
function SpeedStateScaleFactors_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//SpeedStateScaleFactors ::=			SEQUENCE {
	//sf-Medium							ENUMERATED {oDot25, oDot5, oDot75, lDot0},
	//sf-High								ENUMERATED {oDot25, oDot5, oDot75, lDot0}
	$sf_Medium_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($sf_Medium_bit=='00'){$sf_Medium_value='oDot25';}
	if($sf_Medium_bit=='01'){$sf_Medium_value='oDot5';}
	if($sf_Medium_bit=='10'){$sf_Medium_value='oDot75';}
	if($sf_Medium_bit=='11'){$sf_Medium_value='1Dot0';}
	$rrc_out=$rrc_out . "\n" . $tabs . 'sf-Medium ' . $sf_Medium_value;
	
	
	$sf_High_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($sf_High_bit=='00'){$sf_High_value='oDot25';}
	if($sf_High_bit=='01'){$sf_High_value='oDot5';}
	if($sf_High_bit=='10'){$sf_High_value='oDot75';}
	if($sf_High_bit=='11'){$sf_v_value='1Dot0';}
	$rrc_out=$rrc_out . "\n" . $tabs . 'sf-High ' . $sf_High_value;
}
function MobilityControlInfo_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention and do nothing	
	
	$MobilityControlInfo_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options
	$carrierFreq_option_bit=substr($rrc_msg_bin,0,1);
	$carrierBandwidth_option_bit=substr($rrc_msg_bin,1,1);
	$additionalSpectrumEmission_option_bit=substr($rrc_msg_bin,2,1);
	$rach_ConfigDedicated_option_bit=substr($rrc_msg_bin,3,1);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	
	//targetPhysCellId					PhysCellId,
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'targetPhysCellId ' . PhysCellId_1();
	//carrierFreq							CarrierFreqEUTRA					OPTIONAL
	if($carrierFreq_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'carrierFreq {';
		//check option
		$ul_CarrierFreq_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//dl-CarrierFreq						ARFCN-ValueEUTRA
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'dl-CarrierFreq ' . ARFCN_ValueEUTRA_1(); 
		//ul-CarrierFreq						ARFCN-ValueEUTRA				OPTIONAL
		if($ul_CarrierFreq_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ul-CarrierFreq ' . ARFCN_ValueEUTRA_1(); 
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//carrierBandwidth					CarrierBandwidthEUTRA				OPTIONAL
	if($carrierBandwidth_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'carrierBandwidth {';
		//check option
		$ul_Bandwidth_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//dl-Bandwidth
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'dl-Bandwidth ' . CarrierBandwidthEUTRA_Bandwidth_1();
		//ul-Bandwidth
		if($ul_Bandwidth_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ul-Bandwidth ' . CarrierBandwidthEUTRA_Bandwidth_1();
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//additionalSpectrumEmission			AdditionalSpectrumEmission			OPTIONAL
	if($additionalSpectrumEmission_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'additionalSpectrumEmission ' . AdditionalSpectrumEmission_1();
	}
	//t304
	$t304_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	if($t304_bit=='000'){$t304='ms50';}
	if($t304_bit=='001'){$t304='ms100';}
	if($t304_bit=='010'){$t304='ms150';}
	if($t304_bit=='011'){$t304='ms200';}
	if($t304_bit=='100'){$t304='ms500';}
	if($t304_bit=='101'){$t304='ms1000';}
	if($t304_bit=='110'){$t304='ms2000';}
	if($t304_bit=='111'){$t304='spare1';}
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 't304 ' . $t304 ;
	
	//newUE-Identity						C-RNTI,
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'newUE-Identity ' . C_RNTI_1();
	//radioResourceConfigCommon			RadioResourceConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'radioResourceConfigCommon {';
	$tabs=$tabs . str_repeat($tab,2);
	RadioResourceConfigCommon_1();
	$tabs=substr($tabs,0,-8);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	//rach-ConfigDedicated				RACH-ConfigDedicated				OPTIONAL
	if($rach_ConfigDedicated_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rach-ConfigDedicated {';
		$tabs=$tabs . str_repeat($tab,2);
		RACH_ConfigDedicated_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
}
function CarrierBandwidthEUTRA_Bandwidth_1(){
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='n6';
			break;
		case '0001':
			$ret='n15';
			break;
		case '0010':
			$ret='n25';
			break;
		case '0011':
			$ret='n50';
			break;
		case '0100':
			$ret='n75';
			break;
		case '0101':
			$ret='n100';
			break;
		case '0110':
			$ret='spare10';
			break;
		case '0111':
			$ret='spare9';
			break;
		case '1000':
			$ret='spare8';
			break;
		case '1001':
			$ret='spare7';
			break;
		case '1010':
			$ret='spare6';
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
function AdditionalSpectrumEmission_1(){
	global $rrc_msg_bin;
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)+1;
}
function C_RNTI_1(){
	global $rrc_msg_bin;
	//C-RNTI ::=							BIT STRING (SIZE (16))
	$in=substr($rrc_msg_bin,0,16);
	$rrc_msg_bin=substr($rrc_msg_bin,16);
	return $in;
}
function RadioResourceConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention and do nothing
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options
	$rach_ConfigCommon_option_bit=substr($rrc_msg_bin,0,1);
	$pdsch_ConfigCommon_option_bit=substr($rrc_msg_bin,1,1);
	$phich_Config_option_bit=substr($rrc_msg_bin,2,1);
	$pucch_ConfigCommon_option_bit=substr($rrc_msg_bin,3,1);
	$soundingRS_UL_ConfigCommon_option_bit=substr($rrc_msg_bin,4,1);
	$uplinkPowerControlCommon_option_bit=substr($rrc_msg_bin,5,1);
	$antennaInfoCommon_option_bit=substr($rrc_msg_bin,6,1);
	$p_Max_option_bit=substr($rrc_msg_bin,7,1);
	$tdd_Config_option_bit=substr($rrc_msg_bin,8,1);
	$rrc_msg_bin=substr($rrc_msg_bin,9);
	
	//rach-ConfigCommon					RACH-ConfigCommon					OPTIONAL
	if($rach_ConfigCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'rach-ConfigCommon {';
		//
		$tabs=$tabs . str_repeat($tab,1);
		//
		RACH_ConfigCommon_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//prach-Config						PRACH-Config,
	$rrc_out=$rrc_out . "\n" . $tabs . 'prach-Config {';
	$tabs=$tabs . str_repeat($tab,1);
	PRACH_Config_1();
	$tabs=substr($tabs,0,-4);
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	
	//pdsch-ConfigCommon					PDSCH-ConfigCommon					OPTIONAL,	
	if($pdsch_ConfigCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'pdsch-ConfigCommon {';
		$tabs=$tabs . str_repeat($tab,1);
		PDSCH_ConfigCommon_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//pusch-ConfigCommon					PUSCH-ConfigCommon,
	$rrc_out=$rrc_out . "\n" . $tabs . 'pusch-ConfigCommon {';
	$tabs=$tabs . str_repeat($tab,1);
	PUSCH_ConfigCommon_1();
	$tabs=substr($tabs,0,-4);		
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	
	//phich-Config						PHICH-Config						OPTIONAL,
	if($phich_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'phich-Config {';
		$tabs=$tabs . str_repeat($tab,1);
		PHICH_Config_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//pucch-ConfigCommon					PUCCH-ConfigCommon					OPTIONAL,
	if($pucch_ConfigCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'pucch-ConfigCommon {';
		$tabs=$tabs . str_repeat($tab,1);
		PUCCH_ConfigCommon_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//soundingRS-UL-ConfigCommon			SoundingRS-UL-ConfigCommon			OPTIONAL,
	if($soundingRS_UL_ConfigCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'soundingRS-UL-ConfigCommon {';
		$tabs=$tabs . str_repeat($tab,1);
		SoundingRS_UL_ConfigCommon_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//uplinkPowerControlCommon			UplinkPowerControlCommon			OPTIONAL,
	if($uplinkPowerControlCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'uplinkPowerControlCommon {';
		$tabs=$tabs . str_repeat($tab,1);
		UplinkPowerControlCommon_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//antennaInfoCommon					AntennaInfoCommon			OPTIONAL,
	if($antennaInfoCommon_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'antennaInfoCommon {';
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'antennaPortsCount ' . AntennaInfoCommon_1();
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//p-Max								P-Max								OPTIONAL,
	if($p_Max_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'p-Max ' . P_Max_1();
	}
	//tdd-Config							TDD-Config							OPTIONAL,
	if($tdd_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'uplinkPowerControlCommon {';
		$tabs=$tabs . str_repeat($tab,1);
		TDD_Config_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//ul-CyclicPrefixLength				UL-CyclicPrefixLength,
	//UL-CyclicPrefixLength ::=			ENUMERATED {len1, len2}
	$ul_CyclicPrefixLength_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($ul_CyclicPrefixLength_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'ul-CyclicPrefixLength len1';
	}
	if($ul_CyclicPrefixLength_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'ul-CyclicPrefixLength len2';
	}
	
}
function RACH_ConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,0,-12);
	///rach-ConfigCommon	RACH-ConfigCommon
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'rach-ConfigCommon {';
	//check extention and do nothing
	$rach_ConfigCommon_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	////preambleInfo
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'preambleInfo {';
	//check options
	$preamblesGroupAConfig_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	/////numberOfRA-Preambles
	//read 4 bits
	$numberOfRA_Preambles_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$numberOfRA_Preambles_value=numberOfRA_Preambles($numberOfRA_Preambles_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'numberOfRA-Preambles ' . $numberOfRA_Preambles_value;
	
	//echo strlen($rrc_msg_bin)/8;
	//echo substr($rrc_msg_bin,0,8);
	/////preamblesGroupAConfig
	if($preamblesGroupAConfig_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'preamblesGroupAConfig {';
		//check extention and do nothing
		$preamblesGroupAConfig_ext_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		//////sizeOfRA-PreamblesGroupA
		$sizeOfRA_PreamblesGroupA_bit=substr($rrc_msg_bin,0,4);
		//echo $sizeOfRA_PreamblesGroupA_bit;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$sizeOfRA_PreamblesGroupA_value=sizeOfRA_PreamblesGroupA($sizeOfRA_PreamblesGroupA_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,5) . 'sizeOfRA-PreamblesGroupA ' . $sizeOfRA_PreamblesGroupA_value . ',';
		
		//////messageSizeGroupA
		//2 bit
		$messageSizeGroupA_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		$messageSizeGroupA_value=messageSizeGroupA($messageSizeGroupA_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,5) . 'messageSizeGroupA ' . $messageSizeGroupA_value . ',';
		//////messagePowerOffsetGroupB
		//3 bits
		$messagePowerOffsetGroupB_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$messagePowerOffsetGroupB_value=messagePowerOffsetGroupB($messagePowerOffsetGroupB_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,5) . 'messagePowerOffsetGroupB ' . $messagePowerOffsetGroupB_value;
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . '},';
	}
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '},';
	////powerRampingParameters
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'powerRampingParameters {';
	/////powerRampingStep
	//2 bits
	$powerRampingStep_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$powerRampingStep_value=powerRampingStep($powerRampingStep_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'powerRampingStep ' . $powerRampingStep_value . ',';
	
	/////preambleInitialReceivedTargetPower
	//4 bits
	$preambleInitialReceivedTargetPower_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$preambleInitialReceivedTargetPower_value=preambleInitialReceivedTargetPower($preambleInitialReceivedTargetPower_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'preambleInitialReceivedTargetPower ' . $preambleInitialReceivedTargetPower_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '},';
	////ra-SupervisionInfo
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'ra-SupervisionInfo {';
	/////preambleTransMax
	//4 bits
	$preambleTransMax_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$preambleTransMax_value=preambleTransMax($preambleTransMax_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'preambleTransMax ' . $preambleTransMax_value . ',';
	/////ra-ResponseWindowSize
	//3 bits
	$ra_ResponseWindowSize_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$ra_ResponseWindowSize_value=ra_ResponseWindowSize($ra_ResponseWindowSize_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'ra-ResponseWindowSize ' . $ra_ResponseWindowSize_value . ',';
	/////mac-ContentionResolutionTimer
	$mac_ContentionResolutionTimer_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$mac_ContentionResolutionTimer_value=mac_ContentionResolutionTimer($mac_ContentionResolutionTimer_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'mac-ContentionResolutionTimer ' . $mac_ContentionResolutionTimer_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '},';
	////maxHARQ-Msg3Tx
	//3 bits
	$maxHARQ_Msg3Tx_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$maxHARQ_Msg3Tx_value=maxHARQ_Msg3Tx($maxHARQ_Msg3Tx_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'maxHARQ-Msg3Tx ' . $maxHARQ_Msg3Tx_value;
	$tabs=$tabs . str_repeat($tab,3);
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';

}
function PRACH_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,0,-12);
	//rootSequenceIndex					INTEGER (0..837),
	//prach-ConfigInfo					PRACH-ConfigInfo					OPTIONAL
	
	//check option
	$prach_ConfigInfo_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	///prach-Config	PRACH-ConfigSIB
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'prach-Config  {';
	////rootSequenceIndex
	$rootSequenceIndex_bit=substr($rrc_msg_bin,0,10);
	$rrc_msg_bin=substr($rrc_msg_bin,10);
	$rootSequenceIndex_value=rootSequenceIndex($rootSequenceIndex_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'rootSequenceIndex ' . $rootSequenceIndex_value . ',';
	
	if($prach_ConfigInfo_option_bit=='1'){
		////prach-ConfigInfo
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'prach-ConfigInfo {';
		/////prach-ConfigIndex INTEGER (0..63)
		$prach_ConfigIndex_bit=substr($rrc_msg_bin,0,6);
		
		$rrc_msg_bin=substr($rrc_msg_bin,6);
		$prach_ConfigIndex_value=prach_ConfigIndex($prach_ConfigIndex_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'prach-ConfigIndex  ' . $prach_ConfigIndex_value . ',';
		/////highSpeedFlag	BOOLEAN,
		$highSpeedFlag_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$highSpeedFlag_value=highSpeedFlag($highSpeedFlag_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'highSpeedFlag  ' . $highSpeedFlag_value . ',';
		/////zeroCorrelationZoneConfig		INTEGER (0..15),
		$zeroCorrelationZoneConfig_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$zeroCorrelationZoneConfig_value=zeroCorrelationZoneConfig($zeroCorrelationZoneConfig_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'zeroCorrelationZoneConfig ' . $zeroCorrelationZoneConfig_value . ',';
		/////prach-FreqOffset		INTEGER (0..94)
		$prach_FreqOffset_bit=substr($rrc_msg_bin,0,7);
		$rrc_msg_bin=substr($rrc_msg_bin,7);
		
		$prach_FreqOffset_value=prach_FreqOffset($prach_FreqOffset_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'prach-FreqOffset ' . $prach_FreqOffset_value;
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			
		//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	$tabs=$tabs . str_repeat($tab,3);
}
function PDSCH_ConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,0,-12);
	///pdsch-ConfigCommon	PDSCH-ConfigCommon
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pdsch-ConfigCommon {';
	////referenceSignalPower				INTEGER (-60..50),
	$referenceSignalPower_bit=substr($rrc_msg_bin,0,7);
	$rrc_msg_bin=substr($rrc_msg_bin,7);
	$referenceSignalPower_value=referenceSignalPower($referenceSignalPower_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'referenceSignalPower ' . $referenceSignalPower_value . ',';
	////p-b									INTEGER (0..3)
	$p_b_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$p_b_value=p_b($p_b_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'p-b ' . $p_b_value;
	
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	$tabs=$tabs . str_repeat($tab,3);
}
function PUSCH_ConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,0,-12);
	///pusch-ConfigCommon	PUSCH-ConfigCommon
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pusch-ConfigCommon {';
	////pusch-ConfigBasic
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'pusch-ConfigBasic {';
	/////n-SB				INTEGER (1..4),
	$n_SB_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$n_SB_value=n_SB($n_SB_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'n-SB ' . $n_SB_value . ',';
	
	/////hoppingMode		ENUMERATED {interSubFrame, intraAndInterSubFrame},
	$hoppingMode_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$hoppingMode_value=hoppingMode($hoppingMode_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'hoppingMode ' . $hoppingMode_value . ',';
	
	/////pusch-HoppingOffset					INTEGER (0..98),
	$pusch_HoppingOffset_bit=substr($rrc_msg_bin,0,7);
	$rrc_msg_bin=substr($rrc_msg_bin,7);
	$pusch_HoppingOffset_value=pusch_HoppingOffset($pusch_HoppingOffset_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'pusch-HoppingOffset ' . $pusch_HoppingOffset_value . ',';
	
	/////enable64QAM							BOOLEAN
	$enable64QAM_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$enable64QAM_value=enable64QAM($enable64QAM_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'enable64QAM ' . $enable64QAM_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
	////ul-ReferenceSignalsPUSCH			UL-ReferenceSignalsPUSCH
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'ul-ReferenceSignalsPUSCH {';
	/////groupHoppingEnabled					BOOLEAN,
	$groupHoppingEnabled_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$groupHoppingEnabled_value=groupHoppingEnabled($groupHoppingEnabled_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'groupHoppingEnabled ' . $groupHoppingEnabled_value . ','; 
	
	/////groupAssignmentPUSCH				INTEGER (0..29),
	$groupAssignmentPUSCH_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$groupAssignmentPUSCH_value=groupAssignmentPUSCH($groupAssignmentPUSCH_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'groupAssignmentPUSCH ' . $groupAssignmentPUSCH_value . ','; 
	
	/////sequenceHoppingEnabled				BOOLEAN,
	$sequenceHoppingEnabled_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$sequenceHoppingEnabled_value=sequenceHoppingEnabled($sequenceHoppingEnabled_bit);
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'sequenceHoppingEnabled	' . $sequenceHoppingEnabled_value . ','. 
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'sequenceHoppingEnabled ' . $sequenceHoppingEnabled_value . ',';
	
	/////cyclicShift							INTEGER (0..7)
	$cyclicShift_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$cyclicShift_value=cyclicShift($cyclicShift_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'cyclicShift ' . $cyclicShift_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
	
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	$tabs=$tabs . str_repeat($tab,3);
}
function PHICH_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//phich-Duration						ENUMERATED {normal, extended},
	$phich_Duration_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($phich_Duration_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'phich-Duration normal';
	}
	if($phich_Duration_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'phich-Duration extended';
	}
	//phich-Resource						ENUMERATED {oneSixth, half, one, two}
	$phich_Resource_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($phich_Resource_bit=='00'){$ret='oneSixth';}
	if($phich_Resource_bit=='01'){$ret='half';}
	if($phich_Resource_bit=='10'){$ret='one';}
	if($phich_Resource_bit=='11'){$ret='two';}
	$rrc_out=$rrc_out . "\n" . $tabs . 'phich-Resource ' . $ret;
}
function PUCCH_ConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//PUCCH-ConfigCommon ::=				SEQUENCE {
	//deltaPUCCH-Shift					ENUMERATED {ds1, ds2, ds3},
	//nRB-CQI								INTEGER (0..98),
	//nCS-AN								INTEGER (0..7),
	//n1PUCCH-AN							INTEGER (0..2047)
	//}

}
function SoundingRS_UL_ConfigCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,-12);
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'soundingRS-UL-ConfigCommon {';
	$soundingRS_UL_ConfigCommon_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($soundingRS_UL_ConfigCommon_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'release  NULL';
	}
	if($soundingRS_UL_ConfigCommon_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'setup {';
		//check option
		$srs_MaxUpPts_option_bit=ssubstr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		////srs-BandwidthConfig
		$srs_BandwidthConfig_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$srs_BandwidthConfig_value=srs_BandwidthConfig($srs_BandwidthConfig_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'srs-BandwidthConfig ' . $srs_BandwidthConfig_value . ',';
		////srs-SubframeConfig
		$srs_SubframeConfig_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$srs_SubframeConfig_value=srs_SubframeConfigsrs_SubframeConfig($srs_SubframeConfig_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'srs-SubframeConfig ' . $srs_SubframeConfig_value . ',';
		////ackNackSRS-SimultaneousTransmission
		$ackNackSRS_SimultaneousTransmission_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$ackNackSRS_SimultaneousTransmission_value=ackNackSRS_SimultaneousTransmission($ackNackSRS_SimultaneousTransmission_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'ackNackSRS-SimultaneousTransmission ' .  $ackNackSRS_SimultaneousTransmission_value;
		////srs-MaxUpPts
		if($srs_MaxUpPts_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'srs-MaxUpPts true';
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
	}
	
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	$tabs=$tabs . str_repeat($tab,3);
}
function UplinkPowerControlCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,-12);
	///uplinkPowerControlCommon	UplinkPowerControlCommon
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'uplinkPowerControlCommon {';
	////p0-NominalPUSCH						INTEGER (-126..24),
	$p0_NominalPUSCH_bit=substr($rrc_msg_bin,0,8);
	$rrc_msg_bin=substr($rrc_msg_bin,8);
	$p0_NominalPUSCH_value=p0_NominalPUSCH($p0_NominalPUSCH_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'p0-NominalPUSCH ' . $p0_NominalPUSCH_value . ',';
	
	////alpha
	$alpha_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$alpha_value=alpha($alpha_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'alpha ' . $alpha_value . ',';
	
	////p0-NominalPUCCH						INTEGER (-127..-96),
	$p0_NominalPUCCH_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$p0_NominalPUCCH_value=p0_NominalPUCCH($p0_NominalPUCCH_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'p0-NominalPUCCH ' . $p0_NominalPUCCH_value . ',';
	////deltaFList-PUCCH			DeltaFList-PUCCH,
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'deltaFList-PUCCH {';
	/////deltaF-PUCCH-Format1
	$deltaF_PUCCH_Format1_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaF_PUCCH_Format1_value=deltaF_PUCCH_Format1($deltaF_PUCCH_Format1_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'deltaF-PUCCH-Format1 ' . $deltaF_PUCCH_Format1_value . ','; 
	/////deltaF-PUCCH-Format1b
	$deltaF_PUCCH_Format1b_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaF_PUCCH_Format1b_value=deltaF_PUCCH_Format1b($deltaF_PUCCH_Format1b_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'deltaF-PUCCH-Format1b ' . $deltaF_PUCCH_Format1b_value . ','; 
	/////deltaF-PUCCH-Format2
	$deltaF_PUCCH_Format2_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaF_PUCCH_Format2_value=deltaF_PUCCH_Format2($deltaF_PUCCH_Format2_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'deltaF-PUCCH-Format2 ' . $deltaF_PUCCH_Format2_value . ','; 
	/////deltaF-PUCCH-Format2a
	$deltaF_PUCCH_Format2a_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaF_PUCCH_Format2a_value=deltaF_PUCCH_Format2a($deltaF_PUCCH_Format2a_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'deltaF-PUCCH-Format2a ' . $deltaF_PUCCH_Format2a_value . ','; 
	/////deltaF-PUCCH-Format2b
	$deltaF_PUCCH_Format2b_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaF_PUCCH_Format2b_value=deltaF_PUCCH_Format2b($deltaF_PUCCH_Format2b_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'deltaF-PUCCH-Format2b ' . $deltaF_PUCCH_Format2b_value; 
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
	
	////deltaPreambleMsg3					INTEGER (-1..6)
	$deltaPreambleMsg3_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$deltaPreambleMsg3_value=deltaPreambleMsg3($deltaPreambleMsg3_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'deltaPreambleMsg3 ' . $deltaPreambleMsg3_value;
	
	//$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	$tabs=$tabs . str_repeat($tab,3);
}
function AntennaInfoCommon_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//AntennaInfoCommon ::=				SEQUENCE {
	//antennaPortsCount					ENUMERATED {an1, an2, an4, spare1}
	//}
	$in=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($in=='00'){return 'an1';}
	if($in=='01'){return 'an2';}
	if($in=='10'){return 'an4';}
	if($in=='11'){return 'spare1';}

}
function AntennaInfoDedicated_1(){
	//$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . 'antennaInfo {';
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,-12);
	
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
		//$rrc_out=$rrc_out . "\n" . str_repeat($tab,5) . '}';
	$tabs =$tabs . str_repeat($tab,3);
}
function P_Max_1(){
	global $rrc_msg_bin;
	//P-Max ::=				INTEGER (-30..33)
	$in=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	return bindec($in)-30;
}
function TDD_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$tabs=substr($tabs,-12);
	//$rrc_out=$rrc_out . "\n" .$tabs . 'TDD-Config {';
		//subframeAssignment
		$subframeAssignment_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$subframeAssignment_value=subframeAssignment($subframeAssignment_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'subframeAssignment ' . $subframeAssignment_value . ',';
		
		//specialSubframePatterns
		$specialSubframePatterns_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$specialSubframePatterns_value=specialSubframePatterns($specialSubframePatterns_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'specialSubframePatterns ' . $specialSubframePatterns_value . ',';
		
	//	$rrc_out=$rrc_out . "\n" .$tabs . '}';
	$tabs=$tabs . str_repeat($tab,3);
}
function RACH_ConfigDedicated_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//RACH-ConfigDedicated ::=		SEQUENCE {
	//ra-PreambleIndex					INTEGER (0..63),
	//ra-PRACH-MaskIndex					INTEGER (0..15)
	//}
	$rrc_out=$rrc_out . "\n" . $tabs . 'ra-PreambleIndex ' . bindec(substr($rrc_msg_bin,0,6));
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$rrc_out=$rrc_out . "\n" . $tabs . 'ra-PRACH-MaskIndex ' . bindec(substr($rrc_msg_bin,0,4));
	$rrc_msg_bin=substr($rrc_msg_bin,4);

}
function DedicatedInfoNAS_1(){
	global $rrc_msg_bin;
	$dedicatedInfoNAS_length_bit=substr($rrc_msg_bin,0,8);
	$rrc_msg_bin=substr($rrc_msg_bin,8);
	$dedicatedInfoNAS_length_value=bindec($dedicatedInfoNAS_length_bit);
	
	$dedicatedInfoNAS_content_bit=substr($rrc_msg_bin,0,8*$dedicatedInfoNAS_length_value);
	$rrc_msg_bin=substr($rrc_msg_bin,8*$dedicatedInfoNAS_length_value);
	//
	//$rrc_out=$rrc_out . "\n" . $tabs . 'dedicatedInfoNAS ' . my_bin2hex($dedicatedInfoNAS_content_bit);
	return my_bin2hex($dedicatedInfoNAS_content_bit);
}
function RadioResourceConfigDedicated_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//$tabs=$tabs . $tab;
	//check extention
	$ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check optional fields indication
	$srb_ToAddModList_option_bit=substr($rrc_msg_bin,0,1);
	$drb_ToAddModList_option_bit=substr($rrc_msg_bin,1,1);
	$drb_ToReleaseList_option_bit=substr($rrc_msg_bin,2,1);
	$mac_MainConfig_option_bit=substr($rrc_msg_bin,3,1);
	$sps_Config_option_bit=substr($rrc_msg_bin,4,1);
	$physicalConfigDedicated_option_bit=substr($rrc_msg_bin,5,1);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	//srb-ToAddModList					SRB-ToAddModList			OPTIONAL, 
	//$rrc_out=$rrc_out . "\n" . $tabs . 'DEBUG: ' . $srb_ToAddModList_option_bit;
	if($srb_ToAddModList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'srb-ToAddModList {';
		//
		$srb_ToAddModList_size=bindec(substr($rrc_msg_bin,0,1))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		while($srb_ToAddModList_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '{';
			$tabs=$tabs . str_repeat($tab,2);
			//
			SRB1_ToAddMod_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
			$srb_ToAddModList_size=$srb_ToAddModList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//drb-ToAddModList					DRB-ToAddModList			OPTIONAL, 
	//DRB-ToAddModList ::=				SEQUENCE (SIZE (1..maxDRB)) OF DRB-ToAddMod
	//maxDRB						INTEGER ::= 11	
	if($drb_ToAddModList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'drb-ToAddModList {';
		$DRB_ToAddModList_size=bindec(substr($rrc_msg_bin,0,4))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		while($DRB_ToAddModList_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '{';
			$tabs=$tabs . str_repeat($tab,2);
			//
			DRB1_ToAddMod_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';

			$DRB_ToAddModList_size=$DRB_ToAddModList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//drb-ToReleaseList					DRB-ToReleaseList			OPTIONAL, 	
	if($drb_ToReleaseList_option_bit=='1'){
		//DRB-ToReleaseList ::=				SEQUENCE (SIZE (1..maxDRB)) OF DRB-Identity
		//maxDRB						INTEGER ::= 11	
		//DRB-Identity ::=					INTEGER (1..32)
		$rrc_out=$rrc_out . "\n" . $tabs . 'drb-ToReleaseList {';
		$DRB_Identity_size=bindec(substr($rrc_msg_bin,0,4))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		while($DRB_Identity_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '{';
			$DRB_Identity_value=bindec(substr($rrc_msg_bin,0,5))+1;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . $DRB_Identity_value;
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
			$DRB_Identity_size=$DRB_Identity_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//mac-MainConfig	OPTIONAL,
	if($mac_MainConfig_option_bit=='1'){
		$mac_MainConfig_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($mac_MainConfig_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'mac-MainConfig defaultValue : NULL';
		}
		if($mac_MainConfig_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'mac-MainConfig {';
			$tabs=$tabs . str_repeat($tab,1);
			MAC_MainConfig_1();
			$tabs=substr($tabs,0,-4);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
		}
	}
	//sps-Config							SPS-Config 					OPTIONAL,
	if($sps_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'sps-Config {';
		$tabs=$tabs . str_repeat($tab,1);
		SPS_Config_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//physicalConfigDedicated				PhysicalConfigDedicated		OPTIONAL
	if($physicalConfigDedicated_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'physicalConfigDedicated {';
		$tabs=$tabs . str_repeat($tab,1);
		PhysicalConfigDedicated_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	//$tabs=substr($tabs,0,-4);
	
}
function SRB1_ToAddMod_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention
	$SRB_ToAddMod_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//check options
	$rlc_config_option_bit=substr($rrc_msg_bin,1,1);
	$logicalChannelConfig_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	//srb-Identity						INTEGER (1..2),
	$srb_Indentity_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($srb_Identity_bit='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'srb_Identity 1,';
	}
	if($srb_Identity_bit='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'srb_Identity 2,';
	}
	
	//rlc-Config	OPTIONAL
	if($rlc_config_option_bit=='1'){
		$rlc_config_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($rlc_config_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'rlc-Config : defaultValue : NULL,';
		}
		if($rlc_config_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'rlc-Config : explicitValue {';
			$tabs=$tabs . $tab;
			RLC_Config_1();
			$tabs=substr($tabs,0,-4);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
		}
	}
	//logicalChannelConfig	OPTIONAL
	if($logicalChannelConfig_option_bit=='1'){
		$logicalChannelConfig_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($logicalChannelConfig_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'logicalChannelConfig defaultValue : NULL,';
		}
		if($logicalChannelConfig_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'logicalChannelConfig {';
			$tabs=$tabs . $tab;
			LogicalChannelConfig_1();
			$tabs=substr($tabs,0,-4);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
		}
	}
}
function RLC_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//read one bit to check if extension is used
	$rlc_Config_ext_bit=substr($rrc_msg_bin,0,1);
	//read two bit to check which choice is used
	$rlc_Config_choice_bit=substr($rrc_msg_bin,1,2);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	
	switch ($rlc_Config_choice_bit){
		case '00':
			$rrc_out=$rrc_out . "\n" . $tabs . 'am : {';
			//
			$tabs=$tabs . str_repeat($tab,2);
			//
			ul_AM_RLC_1();
			dl_AM_RLC_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			break;
		case '01':
			$rrc_out=$rrc_out . "\n" . $tabs . 'um-Bi-Directional : {';
			//
			$tabs=$tabs . str_repeat($tab,2);
			//
			ul_AM_RLC_1();
			dl_AM_RLC_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			break;
		case '10':
			$rrc_out=$rrc_out . "\n" . $tabs . 'um-Uni-Directional-UL : {';
			//
			$tabs=$tabs . str_repeat($tab,2);
			//
			ul_AM_RLC_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			break;
		case '11':
			$rrc_out=$rrc_out . "\n" . $tabs . 'um-Uni-Directional-DL : {';
			//
			$tabs=$tabs . str_repeat($tab,2);
			//
			dl_AM_RLC_1();
			$tabs=substr($tabs,0,-8);
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			break;
		default:
			break;
	}
}
function ul_AM_RLC_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . 'ul-AM-RLC {';
	//t-PollRetransmit					T-PollRetransmit,
	$t_PollRetransmit_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$t_PollRetransmit_value=t_PollRetransmit($t_PollRetransmit_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 't-PollRetransmit ' . $t_PollRetransmit_value;
	//pollPDU								PollPDU,
	$pollPDU_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$pollPDU_value=pollPDU($pollPDU_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'pollPDU ' . $pollPDU_value . ',';
	//pollByte							PollByte,
	$pollByte_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$pollByte_value=pollByte($pollByte_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'pollByte ' . $pollByte_value . ',';
	//maxRetxThreshold					ENUMERATED {t1, t2, t3, t4, t6, t8, t16, t32}
	$maxRetxThreshold_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$maxRetxThreshold_value=maxRetxThreshold($maxRetxThreshold_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'maxRetxThreshold ' . $maxRetxThreshold_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
}
function dl_AM_RLC_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out=$rrc_out . "\n" . $tabs . 'dl-AM-RLC {';
	//
	$t_Reordering_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$t_Reordering_value=t_Reordering($t_Reordering_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 't_Reordering ' . $t_Reordering_value . ',';
	//read 6 bits for t_StatusProhibit
	$t_StatusProhibit_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$t_StatusProhibit_value=t_StatusProhibit($t_StatusProhibit_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 't_StatusProhibit ' . $t_StatusProhibit_value ;
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
}
function LogicalChannelConfig_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//chec extention
	$LogicalChannelConfig_ext_bit=substr($rrc_msg_bin,0,1);
	//check option
	$ul_SpecificParameters_option_bit=substr($rrc_msg_bin,1,1);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	if($ul_SpecificParameters_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'ul-SpecificParameters {';
		$logicalChannelGroup_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//priority							INTEGER (1..16),
		$priority_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$priority_value=priority($priority_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'priority ' . $priority_value . ',';
		//prioritisedBitRate
		$prioritisedBitRate_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$prioritisedBitRate_value=prioritisedBitRate($prioritisedBitRate_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'prioritisedBitRate ' . $prioritisedBitRate_value . ',';
		//bucketSizeDuration
		$bucketSizeDuration_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$bucketSizeDuration_value=bucketSizeDuration($bucketSizeDuration_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'bucketSizeDuration ' . $bucketSizeDuration_value . ',';
		//logicalChannelGroup					INTEGER (0..3)			OPTIONAL
		if($logicalChannelGroup_option_bit=='1'){
			$logicalChannelGroup_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$logicalChannelGroup_value=logicalChannelGroup($logicalChannelGroup_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'logicalChannelGroup ' . $logicalChannelGroup_value ;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function DRB1_ToAddMod_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//check options
	$eps_BearerIdentity_option_bit=substr($rrc_msg_bin,0,1);
	$pdcp_Config_option_bit=substr($rrc_msg_bin,1,1);
	$rlc_Config_option_bit=substr($rrc_msg_bin,2,1);
	$logicalChannelIdentity_option_bit=substr($rrc_msg_bin,3,1);
	$logicalChannelConfig_option_bit=substr($rrc_msg_bin,4,1);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	//eps-BearerIdentity					INTEGER (0..15)			OPTIONAL,
	if($eps_BearerIdentity_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'eps-BearerIdentity ' . bindec(substr($rrc_msg_bin,0,4));
		$rrc_msg_bin=substr($rrc_msg_bin,4);
	}
	//drb-Identity						DRB-Identity,
	$rrc_out=$rrc_out . "\n" . $tabs . 'drb-Identity ' . DRB_Identity_1();
	//pdcp-Config							PDCP-Config				OPTIONAL,
	if($pdcp_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'pdcp-Config {';
		$tabs =$tabs . $tab ;
		PDCP_Config_1();			
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//rlc-Config							RLC-Config				OPTIONAL,
	if($rlc_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'rlc-Config {';
		$tabs=$tabs . $tab;
		RLC_Config_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//logicalChannelIdentity				INTEGER (3..10)			OPTIONAL,
	if($logicalChannelIdentity_option_bit=='1'){
		$logicalChannelIdentity_value=bindec(substr($rrc_msg_bin,0,3))+3;
		$rrc_out=$rrc_out . "\n" . $tabs . 'logicalChannelIdentity' . $logicalChannelIdentity_value;
		$rrc_msg_bin=substr($rrc_msg_bin,3);
	}
	//logicalChannelConfig				LogicalChannelConfig	OPTIONAL,
	if($logicalChannelConfig_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'logicalChannelConfig {';
		$tabs=$tabs . $tab;
		LogicalChannelConfig_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
}
function DRB_Identity_1(){
	global $rrc_msg_bin;
	//DRB-Identity ::=					INTEGER (1..32)
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	return bindec($in)+1;
}
function PDCP_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check extention
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//check options
		$discardTimer_option_bit=substr($rrc_msg_bin,0,1);
		$rlc_AM_option_bit=substr($rrc_msg_bin,1,1);
		$rlc_UM_option_bit=substr($rrc_msg_bin,2,1);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		
		//discardTimer 	OPTIONAL,
		if($discardTimer_option_bit=='1'){
			$discardTimer_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			if($discardTimer_bit=='000'){$discardTimer_value='ms50';}
			if($discardTimer_bit=='001'){$discardTimer_value='ms100';}
			if($discardTimer_bit=='010'){$discardTimer_value='ms150';}
			if($discardTimer_bit=='011'){$discardTimer_value='ms300';}
			if($discardTimer_bit=='100'){$discardTimer_value='ms500';}
			if($discardTimer_bit=='101'){$discardTimer_value='ms750';}
			if($discardTimer_bit=='110'){$discardTimer_value='ms1500';}
			if($discardTimer_bit=='111'){$discardTimer_value='infinity';}
			
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'discardTimer ' . $discardTimer_value;
			
		}
		//rlc-AM	OPTIONAL,
		if($rlc_AM_option_bit=='1'){
			$rlc_AM_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			if($rlc_AM_bit=='0'){$rlc_AM_value='FALSE';}
			if($rlc_AM_bit=='1'){$rlc_AM_value='TRUE';}
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rlc-AM : statusReportRequired ' . $rlc_AM_value;
		}
		//rlc-UM	OPTIONAL,
		if($rlc_UM_option_bit=='1'){
			$pdcp_SN_Size_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			if($pdcp_SN_Size_bit=='0'){$pdcp_SN_Size_value='FALSE';}
			if($pdcp_SN_Size_bit=='1'){$pdcp_SN_Size_value='TRUE';}
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rlc-UM : pdcp-SN-Size ' . $pdcp_SN_Size_value;
		}
		//headerCompression
		$headerCompression_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($headerCompression_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'headerCompression : notUsed NULL';
		}
		if($headerCompression_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rohc {';
			//check extention
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//check default 
			$maxCID_default_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			//maxCID								INTEGER (1..16383)				DEFAULT 15,
			if($maxCID_default_bit=='0'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'maxCID DEFAULT 15';
			}
			if($maxCID_default_bit=='1'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'maxCID ' . bindec(substr($rrc_msg_bin,0,14));
				$rrc_msg_bin=substr($rrc_msg_bin,14);
			}
			//profiles
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'profiles {';
			//profile0x0001						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0001 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0001 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0002						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0002 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0002 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0003						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0003 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0003 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0004						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0004 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0004 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0006						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0006 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0006 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0101						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0101 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0101 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0102						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0102 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0102 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0103						BOOLEAN,
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0103 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0103 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//profile0x0104						BOOLEAN
			if(substr($rrc_msg_bin,0,1)=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0104 FALSE' ;}
			else {$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'profile0x0104 TRUE' ;}
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		}
}

function MAC_MainConfig_1(){
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
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//read one bit to check if extension exists
	$mac_MainConfig_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//read three bits to check option fields
	$ul_SCH_Config_option_bit=substr($rrc_msg_bin,0,1);
	$drx_Config_option_bit=substr($rrc_msg_bin,1,1);
	$phr_Config_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	
	//echo 'ul-SCH-Config: ' .  $ul_SCH_Config_option_bit . "\n";
	if($ul_SCH_Config_option_bit=='1'){
	
		//
		$rrc_out=$rrc_out . "\n" . $tabs . 'ul-SCH-Config {';
		
		//read two bits to check option for maxHARQ-Tx and periodicBSR-Timer
		$maxHARQ_Tx_option_bit=substr($rrc_msg_bin,0,1);
		$periodicBSR_Timer_option_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($maxHARQ_Tx_option_bit=='1'){
			//read 4 bits to decode maxHARQ-Tx
			$maxHARQ_Tx_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$maxHARQ_Tx_value=maxHARQ_Tx($maxHARQ_Tx_bit);
			$rrc_out=$rrc_out . "\n" .  $tabs . str_repeat($tab,1) . 'maxHARQ-Tx ' . $maxHARQ_Tx_value . ',';
		}
		if($periodicBSR_Timer_option_bit=='1'){
			//$rrc_out=$rrc_out . "\n" .  str_repeat($tab,6) . 'maxHARQ-Tx ' . $maxHARQ_Tx_value . ',';
			//red 4 bits for periodicBSR_Timer
			$periodicBSC_Timer_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$periodicBSC_Timer_value=periodicBSC_Timer($periodicBSC_Timer_bit);
			$rrc_out=$rrc_out . "\n" .  $tabs . str_repeat($tab,1) . 'periodicBSC-Timer ' . $periodicBSC_Timer_value . ',';
		}
		
		//retxBSR-Timer
		//read 3 bits
		$retxBSC_Timer_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$retxBSC_Timer_value=retxBSC_Timer($retxBSC_Timer_bit);
		$rrc_out=$rrc_out . "\n" .  $tabs . str_repeat($tab,1) . 'retxBSC-Timer ' . $retxBSC_Timer_value . ',';
		
		
		//ttiBundling
		//read one bit
		$ttiBundling_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$ttiBundling_value=ttiBundling($ttiBundling_bit);
		$rrc_out=$rrc_out . "\n" .  $tabs . str_repeat($tab,1) . 'ttiBundling ' . $ttiBundling_value;
		
		//
		$rrc_out=$rrc_out . "\n" . $tabs  . '},';
		
	}
	
	//echo 'left string: ' . strlen($rrc_msg_bin);
	//echo 'DRX-Config option:' . $drx_Config_option_bit . "\n";
	
	//2013-1-15: need to talk with yuanping
	//2013-1-16: YP confirmed it is not used in RRCConnectionSetup
	if($drx_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'drx-Config {';
		$tabs=$tabs . $tab;
		DRX_Config_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
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
	$rrc_out=$rrc_out . "\n" . $tabs  . 'timeAlignmentTimerDedicated ' . $timeAlignmentTimer_value;
	
	
	////2013-1-16: YP confirmed it is not used in RRCConnectionSetup
	if($phr_Config_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs  . 'phr-Config {';
		$tabs=$tabs . $tab;
		phr_config_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs  . '}';
	}
	
	
	
}
function DRX_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$DRX_Config_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($DRX_Config_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'release NULL';
	}
	if($DRX_Config_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'setup {';
		//check option
		$shortDRX_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//onDurationTimer
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'onDurationTimer ' . onDurationTimer_1();
		//drx-InactivityTimer
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'drx-InactivityTimer ' . drx_InactivityTimer_1();
		//drx-RetransmissionTimer
		$rrc_out=$rrc_out . "\n" . $tabs . $tab .  'drx-RetransmissionTimer ' . drx_RetransmissionTimer_1();
		//longDRX-CycleStartOffset
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'longDRX-CycleStartOffset {';
		$tabs=$tabs . str_repeat($tab,2);
		longDRX_CycleStartOffset_1();
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		//shortDRX	OPTIONAL
		if($shortDRX_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'shortDRX {';
			//shortDRX-Cycle
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'shortDRX-Cycle ' . shortDRX_Cycle_1();
			//drxShortCycleTimer INTEGER (1..16)
			$drxShortCycleTimer_value=bindec(substr($rrc_msg_bin,0,4))+1;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'drxShortCycleTimer ' . $drxShortCycleTimer_value;
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function onDurationTimer_1(){
	global $rrc_msg_bin;
	/*
	onDurationTimer						ENUMERATED {
												psf1, psf2, psf3, psf4, psf5, psf6,
												psf8, psf10, psf20, psf30, psf40,
												psf50, psf60, psf80, psf100,
												psf200},

	*/
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='psf1';
			break;
		case '0001':
			$ret='psf2';
			break;
		case '0010':
			$ret='psf3';
			break;
		case '0011':
			$ret='psf4';
			break;
		case '0100':
			$ret='psf5';
			break;
		case '0101':
			$ret='psf6';
			break;
		case '0110':
			$ret='psf8';
			break;
		case '0111':
			$ret='psf10';
			break;
		case '1000':
			$ret='psf20';
			break;
		case '1001':
			$ret='psf30';
			break;
		case '1010':
			$ret='psf40';
			break;
		case '1011':
			$ret='psf50';
			break;
		case '1100':
			$ret='psf60';
			break;
		case '1101':
			$ret='psf80';
			break;
		case '1110':
			$ret='psf100';
			break;
		case '1111':
			$ret='psf200';
			break;
		default:
			break;
	}
	return $ret;
		
}
function drx_InactivityTimer_1(){
	global $rrc_msg_bin;
	/*
	drx-InactivityTimer					ENUMERATED {
												psf1, psf2, psf3, psf4, psf5, psf6,
												psf8, psf10, psf20, psf30, psf40,
												psf50, psf60, psf80, psf100,
												psf200, psf300, psf500, psf750,
												psf1280, psf1920, psf2560, psf0-v1020,
												spare9, spare8, spare7, spare6,
												spare5, spare4, spare3, spare2,
												spare1},

	*/
	$in=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	switch($in){
		case '00000':
			$ret='psf1';
			break;
		case '00001':
			$ret='psf2';
			break;
		case '00010':
			$ret='psf3';
			break;
		case '00011':
			$ret='psf4';
			break;
		case '00100':
			$ret='psf5';
			break;
		case '00101':
			$ret='psf6';
			break;
		case '00110':
			$ret='psf8';
			break;
		case '00111':
			$ret='psf10';
			break;
		case '01000':
			$ret='psf20';
			break;
		case '01001':
			$ret='psf30';
			break;
		case '01010':
			$ret='psf40';
			break;
		case '01011':
			$ret='psf50';
			break;
		case '01100':
			$ret='psf60';
			break;
		case '01101':
			$ret='psf80';
			break;
		case '01110':
			$ret='psf100';
			break;
		case '01111':
			$ret='psf200';
			break;
		case '10000':
			$ret='psf300';
			break;
		case '10001':
			$ret='psf500';
			break;
		case '10010':
			$ret='psf750';
			break;
		case '10011':
			$ret='psf1280';
			break;
		case '10100':
			$ret='psf1920';
			break;
		case '10101':
			$ret='psf2560';
			break;
		case '10110':
			$ret='psf0-v1020';
			break;
		case '10111':
			$ret='spare9';
			break;
		case '11000':
			$ret='spare8';
			break;
		case '11001':
			$ret='spare7';
			break;
		case '11010':
			$ret='spare6';
			break;
		case '11011':
			$ret='spare5';
			break;
		case '11100':
			$ret='spare4';
			break;
		case '11101':
			$ret='spare3';
			break;
		case '11110':
			$ret='spare2';
			break;
		case '11111':
			$ret='spare1';
			break;
		default:
			break;
	}
	return $ret;
}
function drx_RetransmissionTimer_1(){
	global $rrc_msg_bin;
	/*
	drx-RetransmissionTimer				ENUMERATED {
												psf1, psf2, psf4, psf6, psf8, psf16,
												psf24, psf33},

	*/
	$in=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($in){
		case '000':
			$ret='psf1';
			break;
		case '001':
			$ret='psf2';
			break;
		case '010':
			$ret='psf4';
			break;
		case '011':
			$ret='psf6';
			break;
		case '100':
			$ret='psf8';
			break;
		case '101':
			$ret='psf16';
			break;
		case '110':
			$ret='psf24';
			break;
		case '111':
			$ret='psf33';
			break;
		default:
			break;
	}
	return $ret;
}
function longDRX_CycleStartOffset_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
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

	*/
	$choice_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($choice_bit){
		case '0000':
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf10 ' . bindec(substr($rrc_msg_bin,0,4));
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			break;
		case '0001':
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf20 ' . bindec(substr($rrc_msg_bin,0,5));
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			break;
		case '0010':
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf32 ' . bindec(substr($rrc_msg_bin,0,5));
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			break;
		case '0011':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf40 ' . bindec(substr($rrc_msg_bin,0,6));
			$rrc_msg_bin=substr($rrc_msg_bin,6);
			break;
		case '0100':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf64 ' . bindec(substr($rrc_msg_bin,0,6));
			$rrc_msg_bin=substr($rrc_msg_bin,6);
			break;
		case '0101':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf80 ' . bindec(substr($rrc_msg_bin,0,7));
			$rrc_msg_bin=substr($rrc_msg_bin,7);
			break;
		case '0110':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf128 ' . bindec(substr($rrc_msg_bin,0,7));
			$rrc_msg_bin=substr($rrc_msg_bin,7);
			break;
		case '0111':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf160 ' . bindec(substr($rrc_msg_bin,0,8));
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			break;
		case '1000':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf256 ' . bindec(substr($rrc_msg_bin,0,8));
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			break;
		case '1001':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf320 ' . bindec(substr($rrc_msg_bin,0,9));
			$rrc_msg_bin=substr($rrc_msg_bin,9);
			break;
		case '1010':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf512 ' . bindec(substr($rrc_msg_bin,0,9));
			$rrc_msg_bin=substr($rrc_msg_bin,9);
			break;
		case '1011':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf640 ' . bindec(substr($rrc_msg_bin,0,10));
			$rrc_msg_bin=substr($rrc_msg_bin,10);
			break;
		case '1100':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf1024 ' . bindec(substr($rrc_msg_bin,0,10));
			$rrc_msg_bin=substr($rrc_msg_bin,10);
			break;
		case '1101':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf1280 ' . bindec(substr($rrc_msg_bin,0,11));
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			break;
		case '1110':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf2048 ' . bindec(substr($rrc_msg_bin,0,11));
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			break;
		case '1111':			
			$rrc_out=$rrc_out . "\n" . $tabs . 'sf2560 ' . bindec(substr($rrc_msg_bin,0,12));
			$rrc_msg_bin=substr($rrc_msg_bin,12);
			break;
		default:
			break;
	}
}
function shortDRX_Cycle_1(){
	global $rrc_msg_bin;
	/*
	shortDRX-Cycle						ENUMERATED	{
													sf2, sf5, sf8, sf10, sf16, sf20,
													sf32, sf40, sf64, sf80, sf128, sf160,
													sf256, sf320, sf512, sf640},

	*/
	$in=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	switch($in){
		case '0000':
			$ret='sf2';
			break;
		case '0001':
			$ret='sf5';
			break;
		case '0010':
			$ret='sf8';
			break;
		case '0011':
			$ret='sf10';
			break;
		case '0100':
			$ret='sf16';
			break;
		case '0101':
			$ret='sf20';
			break;
		case '0110':
			$ret='sf32';
			break;
		case '0111':
			$ret='sf40';
			break;
		case '1000':
			$ret='sf64';
			break;
		case '1001':
			$ret='sf80';
			break;
		case '1010':
			$ret='sf128';
			break;
		case '1011':
			$ret='sf160';
			break;
		case '1100':
			$ret='sf256';
			break;
		case '1101':
			$ret='sf320';
			break;
		case '1110':
			$ret='sf512';
			break;
		case '1111':
			$ret='sf640';
			break;
		default:
			break;
	}
	return $ret;
}
function phr_config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
	phr-Config							CHOICE {
		release								NULL,
		setup								SEQUENCE {
			periodicPHR-Timer					ENUMERATED {sf10, sf20, sf50, sf100, sf200,
															sf500, sf1000, infinity},
			prohibitPHR-Timer					ENUMERATED {sf0, sf10, sf20, sf50, sf100,
																sf200, sf500, sf1000},
			dl-PathlossChange					ENUMERATED {dB1, dB3, dB6, infinity}
		}
	}

	*/
	$choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'release NULL';
	}
	if($choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'setup ';
		//periodicPHR-Timer
		$periodicPHR_Timer_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		switch($periodicPHR_Timer_bit){
			case '000':
				$periodicPHR_Timer_value='sf10';
				break;
			case '001':
				$periodicPHR_Timer_value='sf20';
				break;
			case '010':
				$periodicPHR_Timer_value='sf50';
				break;
			case '011':
				$periodicPHR_Timer_value='sf100';
				break;
			case '100':
				$periodicPHR_Timer_value='sf200';
				break;
			case '101':
				$periodicPHR_Timer_value='sf500';
				break;
			case '110':
				$periodicPHR_Timer_value='sf1000';
				break;
			case '111':
				$periodicPHR_Timer_value='infinity';
				break;
			default:
				break;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'periodicPHR-Timer ' . $periodicPHR_Timer_value;
		//prohibitPHR-Timer
		$prohibitPHR_Timer_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		switch($prohibitPHR_Timer_bit){
			case '000':
				$prohibitPHR_Timer_value='sf10';
				break;
			case '001':
				$prohibitPHR_Timer_value='sf20';
				break;
			case '010':
				$prohibitPHR_Timer_value='sf50';
				break;
			case '011':
				$prohibitPHR_Timer_value='sf100';
				break;
			case '100':
				$prohibitPHR_Timer_value='sf200';
				break;
			case '101':
				$prohibitPHR_Timer_value='sf500';
				break;
			case '110':
				$prohibitPHR_Timer_value='sf1000';
				break;
			case '111':
				$prohibitPHR_Timer_value='infinity';
				break;
			default:
				break;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'prohibitPHR-Timer ' . $prohibitPHR_Timer_value;
		//dl-PathlossChange
		$dl_PathlossChange_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($dl_PathlossChange_bit=='00'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dl-PathlossChange dB1';}
		if($dl_PathlossChange_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dl-PathlossChange dB3';}
		if($dl_PathlossChange_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dl-PathlossChange dB6';}
		if($dl_PathlossChange_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dl-PathlossChange infinity';}
		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function SPS_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//check options
	$semiPersistSchedC_RNTI_option_bit=substr($rrc_msg_bin,0,1);
	$sps_ConfigDL_option_bit=substr($rrc_msg_bin,1,1);
	$sps_ConfigUL_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	//semiPersistSchedC-RNTI			C-RNTI					OPTIONAL,
	if($semiPersistSchedC_RNTI_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'semiPersistSchedC-RNTI ' . C_RNTI_1();
	}
	//sps-ConfigDL					SPS-ConfigDL			OPTIONAL,
	if($sps_ConfigDL_option_bit=='1'){
		//check choice
		$choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'sps-ConfigDL : release NULL';
		}
		if($choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'sps-ConfigDL : setup';
			//check extention
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			//semiPersistSchedIntervalDL
			$semiPersistSchedIntervalDL_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			if($semiPersistSchedIntervalDL_bit=='0000'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf10';}
			if($semiPersistSchedIntervalDL_bit=='0001'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf20';}
			if($semiPersistSchedIntervalDL_bit=='0010'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf32';}
			if($semiPersistSchedIntervalDL_bit=='0011'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf40';}
			if($semiPersistSchedIntervalDL_bit=='0100'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf64';}
			if($semiPersistSchedIntervalDL_bit=='0101'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf80';}
			if($semiPersistSchedIntervalDL_bit=='0110'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf128';}
			if($semiPersistSchedIntervalDL_bit=='0111'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf160';}
			if($semiPersistSchedIntervalDL_bit=='1000'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf320';}
			if($semiPersistSchedIntervalDL_bit=='1001'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL sf640';}
			if($semiPersistSchedIntervalDL_bit=='1010'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare6';}
			if($semiPersistSchedIntervalDL_bit=='1011'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare5';}
			if($semiPersistSchedIntervalDL_bit=='1100'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare4';}
			if($semiPersistSchedIntervalDL_bit=='1101'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare3';}
			if($semiPersistSchedIntervalDL_bit=='1110'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare2';}
			if($semiPersistSchedIntervalDL_bit=='1111'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalDL spare1';}
			//numberOfConfSPS-Processes			INTEGER (1..8),
			$numberOfConfSPS_Processes_value=bindec(substr($rrc_msg_bin,0,3))+1;
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'numberOfConfSPS-Processes ' . $numberOfConfSPS_Processes_value;
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			//n1PUCCH-AN-PersistentList			N1PUCCH-AN-PersistentList
			//N1PUCCH-AN-PersistentList ::=		SEQUENCE (SIZE (1..4)) OF INTEGER (0..2047)
			$N1PUCCH_AN_PersistentList_size=bindec(substr($rrc_msg_bin,0,2))+1;
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'n1PUCCH-AN-PersistentList {';
			while($N1PUCCH_AN_PersistentList_size>0){
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . bindec($rrc_msg_bin,0,11);
				$rrc_msg_bin=substr($rrc_msg_bin,11);
				$N1PUCCH_AN_PersistentList_size=$N1PUCCH_AN_PersistentList_size-1;
			}
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
			
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
		}
	}
	//sps-ConfigUL					SPS-ConfigUL			OPTIONAL
	if($sps_ConfigUL_option_bit=='1'){
		//check choice
		$choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'sps-ConfigUL : release NULL';
		}
		if($choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . 'sps-ConfigUL : setup';
			//check extention
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			//check options
			$p0_Persistent_option_bit=substr($rrc_msg_bin,0,1);
			$twoIntervalsConfig_option_bit=substr($rrc_msg_bin,1,1);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			
			//semiPersistSchedIntervalUL
			$semiPersistSchedIntervalUL_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			if($semiPersistSchedIntervalDL_bit=='0000'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf10';}
			if($semiPersistSchedIntervalDL_bit=='0001'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf20';}
			if($semiPersistSchedIntervalDL_bit=='0010'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf32';}
			if($semiPersistSchedIntervalDL_bit=='0011'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf40';}
			if($semiPersistSchedIntervalDL_bit=='0100'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf64';}
			if($semiPersistSchedIntervalDL_bit=='0101'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf80';}
			if($semiPersistSchedIntervalDL_bit=='0110'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf128';}
			if($semiPersistSchedIntervalDL_bit=='0111'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf160';}
			if($semiPersistSchedIntervalDL_bit=='1000'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf320';}
			if($semiPersistSchedIntervalDL_bit=='1001'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL sf640';}
			if($semiPersistSchedIntervalDL_bit=='1010'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare6';}
			if($semiPersistSchedIntervalDL_bit=='1011'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare5';}
			if($semiPersistSchedIntervalDL_bit=='1100'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare4';}
			if($semiPersistSchedIntervalDL_bit=='1101'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare3';}
			if($semiPersistSchedIntervalDL_bit=='1110'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare2';}
			if($semiPersistSchedIntervalDL_bit=='1111'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'semiPersistSchedIntervalUL spare1';}
			//implicitReleaseAfter				ENUMERATED {e2, e3, e4, e8},
			$implicitReleaseAfter_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			if($implicitReleaseAfter_bit=='00'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'implicitReleaseAfter e2';}
			if($implicitReleaseAfter_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'implicitReleaseAfter e3';}
			if($implicitReleaseAfter_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'implicitReleaseAfter e4';}
			if($implicitReleaseAfter_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'implicitReleaseAfter e8';}
			//p0-Persistent	 OPTIONAL
			if($p0_Persistent_option_bit=='1'){
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'p0-Persistent {';
				//p0-NominalPUSCH-Persistent			INTEGER (-126..24),
				$p0_NominalPUSCH_Persistent_value=bindec(substr($rrc_msg_bin,0,8))-126;
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'p0-NominalPUSCH-Persistent ' . $p0_NominalPUSCH_Persistent_value;
				$rrc_msg_bin=substr($rrc_msg_bin,8);
				//p0-UE-PUSCH-Persistent				INTEGER (-8..7)
				$p0_UE_PUSCH_Persistent_value=bindec(substr($rrc_msg_bin,0,4))-8;
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'p0-UE-PUSCH-Persistent ' . $p0_UE_PUSCH_Persistent_value;
				$rrc_msg_bin=substr($rrc_msg_bin,4);
				
				$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
			}
			//twoIntervalsConfig				ENUMERATED {true}	OPTIONAL
			//TBD: 2012-2-23: not sure if 1 bit is needed for it, need to check ASN.1 syntax
			if($twoIntervalsConfig_option_bit=='1'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'twoIntervalsConfig true';
				$rrc_msg_bin=substr($rrc_msg_bin,1);
			}
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
		}
	}
	
}
function PhysicalConfigDedicated_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//$tabs=$tabs . $tab;
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
	//$rrc_out=$rrc_out . "\n" . str_repeat($tab,4) . 'physicalConfigDedicated  {' ;
	
	//check PDSCH-ConfigDedicated, read 3 bits
	if($pdsch_ConfigDedicated_option_bit=='1'){
		$pdsch_ConfigDedicated_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$pdsch_ConfigDedicated_value=pdsch_ConfigDedicated($pdsch_ConfigDedicated_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . 'PDSCH-ConfigDedicated {' ;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'p-a ' . $pdsch_ConfigDedicated_value;
		$rrc_out=$rrc_out . "\n" . $tabs . '},';
	}
	//pucch-ConfigDedicated p179, PUCCH-Config
	if($pucch_ConfigDedicated_option_bit=='1'){
		//read one bit to check PUCCH-ConfigDedicated option
		$tdd_AckNackFeedbackMode_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . $tabs . 'PUCCH-ConfigDedicated {' ;
		
		//
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'ackNackRepetition {' ;
		
		//read one bit to check ackNackRepetition choice
		$ackNackRepetition_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);		
		
		if($ackNackRepetition_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'release NULL,';
		}
		if($ackNackRepetition_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'setup {';
			
			//repetitionFactor
			//read 2 bits
			$repetitionFactor_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$repetitionFactor_value=repetitionFactor($repetitionFactor_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'repetitionFactor ' . $repetitionFactor_value . ',';
					
			
			//n1PUCCH-AN-Rep
			//read 11 bits
			$n1PUCCH_AN_Rep_bit=substr($rrc_msg_bin,0,11);
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			$n1PUCCH_AN_Rep_value=bindec($n1PUCCH_AN_Rep_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'n1PUCCH-AN-Rep ' . $n1PUCCH_AN_Rep_value;			
			
			//close setup
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . '}' ;
		//
		if($tdd_AckNackFeedbackMode_option_bit=='1'){
			$tdd_AckNackFeedbackMode_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			$tdd_AckNackFeedbackMode_value=tdd_AckNackFeedbackMode($tdd_AckNackFeedbackMode_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'tdd_AckNackFeedbackMode ' . $tdd_AckNackFeedbackMode_value;
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '}' ;
	}
	
	if($pusch_ConfigDedicated_option_bit=='1'){
	/*
	PUSCH-ConfigDedicated ::=			SEQUENCE {
	betaOffset-ACK-Index				INTEGER (0..15),
	betaOffset-RI-Index					INTEGER (0..15),
	betaOffset-CQI-Index				INTEGER (0..15)
	}
	*/
		$rrc_out=$rrc_out . "\n" . $tabs . 'pusch-ConfigDedicated  {' ;
		//read 4 bites for betaOffset-ACK-Index
		$betaOffset_ACK_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_ACK_Index_value=bindec($betaOffset_ACK_Index_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'betaOffset-ACK-Index ' . $betaOffset_ACK_Index_value . ",";
		
		//read 4 bits for betaOffset-RI-Index
		$betaOffset_RI_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_RI_Index_value=bindec($betaOffset_RI_Index_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'betaOffset-RI-Index ' . $betaOffset_RI_Index_value . ",";
	
		//read 4 bits for betaOffset-RI-Index
		$betaOffset_CQI_Index_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$betaOffset_CQI_Index_value=bindec($betaOffset_CQI_Index_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'betaOffset-CQI-Index ' . $betaOffset_CQI_Index_value ;
		
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '},' ;
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
		$rrc_out=$rrc_out . "\n" . $tabs . 'UplinkPowerControlDedicated  {' ;
		
		//read 1 bit to check if DEFAULT is used for filterCoefficient		
		$filterCoefficient_default_bit = substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		//read 4 bits for p0-UE-PUSCH
		$p0_UE_PUSCH_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$p0_UE_PUSCH_value=bindec($p0_UE_PUSCH_bit)-8;
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'p0-UE-PUSCH ' . $p0_UE_PUSCH_value . ',' ;
		//echo $p0_UE_PUSCH_bit;
		//echo substr($rrc_msg_bin,0,4);
		
		//read one bit for deltaMCS-Enabled
		$deltaMCS_Enabled_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$deltaMCS_Enabled_value=deltaMCS_Enabled($deltaMCS_Enabled_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'deltaMCS-Enabled ' . $deltaMCS_Enabled_value . ',' ;
		
		//read 1 bit for accumulationEnabled	
		$accumulationEnabled_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$accumulationEnabled_value=accumulationEnabled($accumulationEnabled_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'accumulationEnabled ' . $accumulationEnabled_value . ',' ;
		
		//read 4 bits for p0-UE-PUCCH
		$p0_UE_PUCCH_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$p0_UE_PUCCH_value=p0_UE_PUCCH($p0_UE_PUCCH_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'p0_UE_PUCCH ' . $p0_UE_PUCCH_value . ',' ;
		
		
		//read 4 bits for pSRS-Offset
		$pSRS_Offset_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$pSRS_Offset_value=pSRS_Offset($pSRS_Offset_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'pSRS_Offset ' . $pSRS_Offset_value . ',' ;
		
		//
		if($filterCoefficient_default_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'filterCoefficient   fc4 '  ;
		}
		if($filterCoefficient_default_bit=='1'){
			
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '}' ;
	}
	
	if($tpc_PDCCH_ConfigPUCCH_option_bit=='1'){
		//exit('tpc_PDCCH_ConfigPUCCH decoding is not supported!');
		$rrc_out=$rrc_out . "\n" . $tabs . 'tpc-PDCCH-ConfigPUCCH {';
		$tabs=$tabs . $tab;
		TPC_PDCCH_Config_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	if($tpc_PDCCH_ConfigPUSCH_option_bit=='1'){
		//exit('tpc_PDCCH_ConfigPUSCH decoding is not supported!');
		$rrc_out=$rrc_out . "\n" . $tabs . 'tpc-PDCCH-ConfigPUSCH {';
		$tabs=$tabs . $tab;
		TPC_PDCCH_Config_1();
		$tabs=substr($tabs,0,-4);		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	if($cqi_ReportConfig_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . 'cqi-ReportConfig  {' ;
		
		$cqi_ReportModeAperiodic_option_bit=substr($rrc_msg_bin,0,1);
		$cqi_ReportPeriodic_option_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($cqi_ReportModeAperiodic_option_bit=='1'){
			$cqi_ReportModeAperiodic_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$cqi_ReportModeAperiodic_value=cqi_ReportModeAperiodic($cqi_ReportModeAperiodic_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . 'cqi_ReportModeAperiodic ' . $cqi_ReportModeAperiodic_value . ',';
		}
		
		//nomPDSCH-RS-EPRE-Offset
		//read 3 bits
		$nomPDSCH_RS_EPRE_Offset_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$nomPDSCH_RS_EPRE_Offset_value=nomPDSCH_RS_EPRE_Offset($nomPDSCH_RS_EPRE_Offset_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'nomPDSCH_RS_EPRE_Offset ' . $nomPDSCH_RS_EPRE_Offset_value . ',';
		
		
		if($cqi_ReportPeriodic_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'cqi-ReportPeriodic {';
			$cqi_ReportPeriodic_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			if($cqi_ReportPeriodic_choice_bit=='0'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'release   NULL,';
			}
			if($cqi_ReportPeriodic_choice_bit=='1'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'setup {';
				//read 1 bit for ri-ConfigIndex option
				$ri_ConfigIndex_option_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				
				//cqi-PUCCH-ResourceIndex
				$cqi_PUCCH_ResourceIndex_bit=substr($rrc_msg_bin,0,11);
				$rrc_msg_bin=substr($rrc_msg_bin,11);
				$cqi_PUCCH_ResourceIndex_value=cqi_PUCCH_ResourceIndex($cqi_PUCCH_ResourceIndex_bit);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'cqi-PUCCH-ResourceIndex ' . $cqi_PUCCH_ResourceIndex_value . ',';
				
				//cqi-pmi-ConfigIndex
				$cqi_pmi_ConfigIndex_bit=substr($rrc_msg_bin,0,10);
				$rrc_msg_bin=substr($rrc_msg_bin,10);
				$cqi_pmi_ConfigIndex_value=cqi_pmi_ConfigIndex($cqi_pmi_ConfigIndex_bit);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'cqi-pmi-ConfigIndex ' . $cqi_pmi_ConfigIndex_value . ',';
				
				//read 1 bit to check cqi-FormatIndicatorPeriodic choice
				$cqi_FormatIndicatorPeriodic_choice_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'cqi-FormatIndicatorPeriodic {';
				
				if($cqi_FormatIndicatorPeriodic_choice_bit=='0'){
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'widebandCQI  NULL';
				}
				if($cqi_FormatIndicatorPeriodic_choice_bit=='1'){
					//
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . 'subbandCQI {';
					$k_bit=substr($rrc_msg_bin,0,2);
					$rrc_msg_bin=substr($rrc_msg_bin,2);
					$k_value=bindec($k_bit);
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,5) . 'k ' . $k_value;
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) . '}';
				}
				//close cqi-FormatIndicatorPeriodic
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
				
				//
				if($ri_ConfigIndex_option_bit=='1'){
					//read 10 bits for ri-ConfigIndex
					$ri_ConfigIndex_bit=substr($rrc_msg_bin,0,10);
					$rrc_msg_bin=substr($rrc_msg_bin,10);
					$ri_ConfigIndex_value=ri_ConfigIndex($ri_ConfigIndex_bit);
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'ri-ConfigIndex ' . $ri_ConfigIndex_value;
				}
				
				//simultaneousAckNackAndCQI
				//read 1 bit
				$simultaneousAckNackAndCQI_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				$simultaneousAckNackAndCQI_value=simultaneousAckNackAndCQI($simultaneousAckNackAndCQI_bit);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'simultaneousAckNackAndCQI ' . $simultaneousAckNackAndCQI_value;
				
				//close
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			}
			
			//close
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . '}';
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '}' ;
	}
	
	//soundingRS-UL-ConfigDedicated		SoundingRS-UL-ConfigDedicated	OPTIONAL
	if($soundingRS_UL_ConfigDedicated_option_bit=='1'){
		//exit('note: soundingRS_UL_ConfigDedicated decoding is not supported at this time!!!');
		$rrc_out=$rrc_out . "\n" . $tabs . 'soundingRS-UL-ConfigDedicated {';
		$tabs=$tabs . $tab;
		SoundingRS_UL_ConfigDedicated_1();
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	if($antennaInfo_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . 'antennaInfo {';
		
		$antennaInfo_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($antennaInfo_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'AntennaInfoDedicated {';
			//read 1 bit to check if codebookSubsetRestriction option exist
			$codebookSubsetRestriction_option_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			//transmissionMode
			//read 3 bits
			$transmissionMode_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$transmissionMode_value=transmissionMode($transmissionMode_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'transmissionMode ' . $transmissionMode_value . ',';
			
			//codebookSubsetRestriction
			if($codebookSubsetRestriction_option_bit=='1'){
				//
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'codebookSubsetRestriction {' ;
				//read 3 bits to check codebookSubsetRestriction choice
				$codebookSubsetRestriction_choice_bit=substr($rrc_msg_bin,0,3);
				$rrc_msg_bin=substr($rrc_msg_bin,3);
				$codebookSubsetRestriction_value=codebookSubsetRestriction($codebookSubsetRestriction_choice_bit);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $codebookSubsetRestriction_value ;
				
				
				//close
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}' ;
			}
			
			//ue-TransmitAntennaSelection
			//read 1 bit to check ue-TransmitAntennaSelection choice
			$ue_TransmitAntennaSelection_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ue-TransmitAntennaSelection {' ;
			if($ue_TransmitAntennaSelection_choice_bit=='0'){
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'release NULL' ;
			}
			if($ue_TransmitAntennaSelection_choice_bit=='1'){
				//read 1 bit to check value for setup
				$ue_TransmitAntennaSelection_setup_bit=substr($rrc_msg_bin,0,1);
				$rrc_msg_bin=substr($rrc_msg_bin,1);
				if($ue_TransmitAntennaSelection_setup_bit=='0'){
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'setup closedLoop' ;
				}
				if($ue_TransmitAntennaSelection_setup_bit=='1'){
					$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'setup openLoop' ;
				}
			}
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}' ;
			
			//close
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . '}';
		}
		if($antennaInfo_choice_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'defaultValue   NULL';
		}
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	
	if($schedulingRequestConfig_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . 'SchedulingRequestConfig {';
		//read 1 bit to check choice
		$schedulingRequestConfig_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($schedulingRequestConfig_choice_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'release NULL';
		}
		if($schedulingRequestConfig_choice_bit=='1'){
			//
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . 'setup {';
			//sr-PUCCH-ResourceIndex	
			//read 11 bits
			$sr_PUCCH_ResourceIndex_bit=substr($rrc_msg_bin,0,11);
			$rrc_msg_bin=substr($rrc_msg_bin,11);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'sr-PUCCH-ResourceIndex ' . bindec($sr_PUCCH_ResourceIndex_bit) . ',';
			
			//sr-ConfigIndex
			//read 8 bits
			$sr_ConfigIndex_bit=substr($rrc_msg_bin,0,8);
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'sr-ConfigIndex ' . bindec($sr_ConfigIndex_bit) . ',';
			
			//dsr-TransMax
			//read 3 bits
			$dsr_TransMax_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$dsr_TransMax_value=dsr_TransMax($dsr_TransMax_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'dsr-TransMax ' . $dsr_TransMax_value;
			
			
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,1) . '}';
		}
		
		//close
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
	//close physicalConfigDedicated 
	

}
function SoundingRS_UL_ConfigDedicated_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
	SoundingRS-UL-ConfigDedicated ::=	CHOICE{
	release								NULL,
	setup								SEQUENCE {
		srs-Bandwidth						ENUMERATED {bw0, bw1, bw2, bw3},
		srs-HoppingBandwidth				ENUMERATED {hbw0, hbw1, hbw2, hbw3},
		freqDomainPosition					INTEGER (0..23),
		duration							BOOLEAN,
		srs-ConfigIndex						INTEGER (0..1023),
		transmissionComb					INTEGER (0..1),
		cyclicShift							ENUMERATED {cs0, cs1, cs2, cs3, cs4, cs5, cs6, cs7}
	}
	}

	*/
	$choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	if($choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'release NULL';
	}
	if($choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'setup {';
		$tabs=$tabs . $tab;
		//srs-Bandwidth						ENUMERATED {bw0, bw1, bw2, bw3},
		$srs_Bandwidth_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($srs_Bandwidth_bit=='00'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-Bandwidth bw0';}
		if($srs_Bandwidth_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-Bandwidth bw1';}
		if($srs_Bandwidth_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-Bandwidth bw2';}
		if($srs_Bandwidth_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-Bandwidth bw3';}
		//srs-HoppingBandwidth				ENUMERATED {hbw0, hbw1, hbw2, hbw3},
		$srs_HoppingBandwidth_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		if($srs_Bandwidth_bit=='00'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-HoppingBandwidth bhw0';}
		if($srs_Bandwidth_bit=='01'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-HoppingBandwidth bhw1';}
		if($srs_Bandwidth_bit=='10'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-HoppingBandwidth bhw2';}
		if($srs_Bandwidth_bit=='11'){$rrc_out=$rrc_out . "\n" . $tabs . 'srs-HoppingBandwidth bhw3';}
		//freqDomainPosition					INTEGER (0..23),
		$freqDomainPosition_bit=substr($rrc_msg_bin,0,5);
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$rrc_out=$rrc_out . "\n" . $tabs . 'freqDomainPosition ' . bindec($freqDomainPosition_bit);
		//duration							BOOLEAN,
		$duration_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($duration_bit=='0'){$rrc_out=$rrc_out . "\n" . $tabs . 'duration FALSE';}
		if($duration_bit=='1'){$rrc_out=$rrc_out . "\n" . $tabs . 'duration TRUE';}
		//srs-ConfigIndex						INTEGER (0..1023),
		$srs_ConfigIndex_bit=substr($rrc_msg_bin,0,10);
		$rrc_msg_bin=substr($rrc_msg_bin,10);
		$rrc_out=$rrc_out . "\n" . $tabs . 'srs-ConfigIndex ' . bindec($srs_ConfigIndex_bit);
		//transmissionComb					INTEGER (0..1),
		$transmissionComb_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . $tabs . 'transmissionComb ' . bindec($transmissionComb_bit);
		//cyclicShift							ENUMERATED {cs0, cs1, cs2, cs3, cs4, cs5, cs6, cs7}
		$cyclicShift_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		if($cyclicShift_bit=='000'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs0';}
		if($cyclicShift_bit=='001'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs1';}
		if($cyclicShift_bit=='010'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs2';}
		if($cyclicShift_bit=='011'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs3';}
		if($cyclicShift_bit=='100'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs4';}
		if($cyclicShift_bit=='101'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs5';}
		if($cyclicShift_bit=='110'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs6';}
		if($cyclicShift_bit=='111'){$rrc_out=$rrc_out . "\n" . $tabs . 'cyclicShift cs7';}
		
		$tabs=substr($tabs,0,-4);
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function SecurityConfigHO_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
	SecurityConfigHO ::=				SEQUENCE {
	handoverType						CHOICE {
		intraLTE							SEQUENCE {
			securityAlgorithmConfig				SecurityAlgorithmConfig		OPTIONAL,	-- Cond fullConfig
			keyChangeIndicator					BOOLEAN,
			nextHopChainingCount				NextHopChainingCount
		},
		interRAT							SEQUENCE {
			securityAlgorithmConfig				SecurityAlgorithmConfig,
			nas-SecurityParamToEUTRA			OCTET STRING (SIZE(6))
		}
	},
	...
	}

	*/
	$rrc_out=$rrc_out . "\n" . $tabs . 'handoverType {';
	//check extention
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//check choice
	$handoverType_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	if($handoverType_choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'intraLTE';
		//check option
		$securityAlgorithmConfig_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//securityAlgorithmConfig				SecurityAlgorithmConfig		OPTIONAL
		if($securityAlgorithmConfig_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'securityAlgorithmConfig {';
			//
			$tabs=$tabs . str_repeat($tab,3);
			SecurityAlgorithmConfig_1();
			$tabs=substr($tabs,0,-12);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		//keyChangeIndicator					BOOLEAN,
		$keyChangeIndicator_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($keyChangeIndicator_bit=='0'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'keyChangeIndicator FALSE';}
		if($keyChangeIndicator_bit=='1'){$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'keyChangeIndicator TRUE';}
		//nextHopChainingCount				NextHopChainingCount
		//NextHopChainingCount ::=					INTEGER (0..7)
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'nextHopChainingCount ' .bindec(substr($rrc_msg_bin,0,3));
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	if($handoverType_choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'interLTE';
		
		//securityAlgorithmConfig				SecurityAlgorithmConfig,
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'securityAlgorithmConfig {';
		$tabs=$tabs . str_repeat($tab,3);
		SecurityAlgorithmConfig_1();
		$tabs=substr($tabs,-12);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		//nas-SecurityParamToEUTRA			OCTET STRING (SIZE(6))
		$nas_SecurityParamToEUTRA_bit=substr($rrc_msg_bin,0,48);
		$rrc_msg_bin=substr($rrc_msg_bin,48);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'nas-SecurityParamToEUTRA ' . $nas_SecurityParamToEUTRA_bit;
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
}
function SecurityAlgorithmConfig_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
	SecurityAlgorithmConfig ::=			SEQUENCE {
	cipheringAlgorithm					ENUMERATED {
											eea0, eea1, eea2, eea3-v11xy, spare4, spare3,
											spare2, spare1, ...},
	integrityProtAlgorithm				ENUMERATED {
											eia0-v920, eia1, eia2, eia3-v11xy, spare4, spare3,
											spare2, spare1, ...}
	}

	*/
	//check extention for cipheringAlgorithm
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$cipheringAlgorithm_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	if($cipheringAlgorithm_bit=='000'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm eea0';}
	if($cipheringAlgorithm_bit=='001'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm eea1';}
	if($cipheringAlgorithm_bit=='010'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm eea2';}
	if($cipheringAlgorithm_bit=='011'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm eea3-v11xy';}
	if($cipheringAlgorithm_bit=='100'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm spare4';}
	if($cipheringAlgorithm_bit=='101'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm spare3';}
	if($cipheringAlgorithm_bit=='110'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm spare2';}
	if($cipheringAlgorithm_bit=='111'){$rrc_out=$rrc_out . "\n" . $tabs . 'cipheringAlgorithm spare1';}
	
	//$rrc_out=$rrc_out . "\n" . $tabs . 'DEBUG:' . $rrc_msg_bin;
	//check extention for integrityProtAlgorithm
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$integrityProtAlgorithm_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	if($integrityProtAlgorithm_bit=='000'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm eia0-v920';}
	if($integrityProtAlgorithm_bit=='001'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm eia1';}
	if($integrityProtAlgorithm_bit=='010'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm eia2';}
	if($integrityProtAlgorithm_bit=='011'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm eia3-v11xy';}
	if($integrityProtAlgorithm_bit=='100'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm spare4';}
	if($integrityProtAlgorithm_bit=='101'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm spare3';}
	if($integrityProtAlgorithm_bit=='110'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm spare2';}
	if($integrityProtAlgorithm_bit=='111'){$rrc_out=$rrc_out . "\n" . $tabs . 'integrityProtAlgorithm spare1';}
}
function my_bin2hex($in){
	$ret='';
	do{
		$temp1=substr($in,0,4);
		$temp2=substr($in,4,4);
		$in=substr($in,8);
		$ret=$ret . my_bin2hex_1($temp1) . my_bin2hex_1($temp2) . ' ';
	}while(strlen($in)>0);
	
	return $ret;
}

function my_bin2hex_1($in){
	switch($in){
		case '0000':
			$ret='0';
			break;
		case '0001':
			$ret='1';
			break;
		case '0010':
			$ret='2';
			break;
		case '0011':
			$ret='3';
			break;
		case '0100':
			$ret='4';
			break;
		case '0101':
			$ret='5';
			break;
		case '0110':
			$ret='6';
			break;
		case '0111':
			$ret='7';
			break;
		case '1000':
			$ret='8';
			break;
		case '1001':
			$ret='9';
			break;
		case '1010':
			$ret='A';
			break;
		case '1011':
			$ret='B';
			break;
		case '1100':
			$ret='C';
			break;
		case '1101':
			$ret='D';
			break;
		case '1110':
			$ret='E';
			break;
		case '1111':
			$ret='F';
			break;
		default:
			break;
	}
	return $ret;
}
function tdd_AckNackFeedbackMode($in){
	if($in=='0'){return 'bundling';}
	if($in=='1'){return 'multiplexing';}
}
function TPC_PDCCH_Config_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	/*
	TPC-PDCCH-Config ::=					CHOICE {
	release								NULL,
	setup								SEQUENCE {
		tpc-RNTI							BIT STRING (SIZE (16)),
		tpc-Index							TPC-Index
	}
	}

	TPC-Index ::=							CHOICE {
	indexOfFormat3							INTEGER (1..15),
	indexOfFormat3A							INTEGER (1..31)

	*/
	$choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($choice_bit=='0'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'release NULL';
	}
	if($choice_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . 'setup {';
		//tpc-RNTI							BIT STRING (SIZE (16)),
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'tpc-RNTI ' . substr($rrc_msg_bin,0,16);
		$rrc_msg_bin=substr($rrc_msg_bin,16);
		//tpc-Index							TPC-Index
		$tpc_Index_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($tpc_Index_choice_bit=='0'){
			$indexOfFormat3_value=bindec(substr($rrc_msg_bin,0,4))+1;
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'tpc-Index : indexOfFormat3 ' . $indexOfFormat3_value;
		}
		if($tpc_Index_choice_bit=='1'){
			$indexOfFormat3A_value=bindec(substr($rrc_msg_bin,0,4))+1;
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'tpc-Index : indexOfFormat3 ' . $indexOfFormat3A_value;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
	}
}
function RAT_Type_1(){
	global $rrc_msg_bin;
	/*
	RAT-Type ::=						ENUMERATED {
										eutra, utra, geran-cs, geran-ps, cdma2000-1XRTT,
										spare3, spare2, spare1, ...}
	*/
	//check extention
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	$in=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	switch($in){
		case '000':
			$ret='eutra';
			break;
		case '001':
			$ret='utra';
			break;
		case '010':
			$ret='geran-cs';
			break;
		case '011':
			$ret='geran-ps';
			break;
		case '100':
			$ret='cdma2000-1XRTT';
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
function UE_CapabilityRAT_ContainerList_1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	/*
	UE-CapabilityRAT-ContainerList ::=SEQUENCE (SIZE (0..maxRAT-Capabilities)) OF UE-CapabilityRAT-Container

	UE-CapabilityRAT-Container ::= SEQUENCE {
		rat-Type							RAT-Type,
		ueCapabilityRAT-Container			OCTET STRING
	}
	maxRAT-Capabilities			INTEGER ::= 8
	*/
	$UE_CapabilityRAT_ContainerList_size=bindec(substr($rrc_msg_bin,0,4));
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	while($UE_CapabilityRAT_ContainerList_size>0){
		$rrc_out=$rrc_out . "\n" . $tabs . '{';
		//rat-Type					RAT-Type,
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'rat-Type ' . RAT_Type_1();
		//ueCapabilityRAT-Container			OCTET STRING
		$ueCapabilityRAT_Container_size=bindec(substr($rrc_msg_bin,0,8))*8;
		$rrc_msg_bin=substr($rrc_msg_bin,8);
		$in=substr($rrc_msg_bin,0,$ueCapabilityRAT_Container_size);
		$rrc_msg_bin=substr($rrc_msg_bin,$ueCapabilityRAT_Container_size);
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'ueCapabilityRAT-Container ' . my_bin2hex($in);
		
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$UE_CapabilityRAT_ContainerList_size=$UE_CapabilityRAT_ContainerList_size-1;
	}
	
}
?>

