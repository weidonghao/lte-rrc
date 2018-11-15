<?php
//SIB1
//$rrc_msg="40 D1 80 81 A3 01 02 08 04 08 00 20 38 78 02 20 21 01 08 5E C0 00 00 00 00 00 00 00 00 00 00 00";
//$rrc_msg="00 00 19 03 1F 2F D4 A0 30 46 03 96 A0 C0 01 04 01 20 53 FD 15 44 2E 19 A3 73 35 C0 00 00 00 00";
//SIB2
//$rrc_msg="00 00 19 03 1F 2F D4 A0 30 46 03 96 A0 C0 01 04 01 20 53 FD 15 44 2E 19 A3 73 35 C0 00 00 00 00";
//SIB3
//$rrc_msg="00 04 10 4A 2D 90 40 00 00 00 00 00 00 00 00";

//SIB4
//$rrc_msg="00 09 84 0A 88 17 10 80 44 00 00 00 00 00 00";

include('rrc_fun.php');

$rrc_msg=$_POST["hexStr"];

//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);

//read 1 bit to check choice for BCCH-DL-SCH-MessageType
$BCCH_DL_SCH_msgtype_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($BCCH_DL_SCH_msgtype_bit=='1'){
	//echo 'Note: messageClassExtension decoding is supported so far!';
	exit('Note: messageClassExtension decoding is supported so far!');
}
//construct output string
$tab='    ';
$rrc_out='BCCH-DL-SCH-Message : {';
$rrc_out_end='}';
$tabs='';
//read 1 bites to check the message type of c1
$c1_type_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);

if($c1_type_bit=='0'){
	systemInformation();
}
if($c1_type_bit=='1'){
	systemInformationBlockType1();
}

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

//function start
function systemInformation(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : systemInformation : ' . '{';
	$rrc_out_end= $tab . '}' . "\n" . $rrc_out_end;
	
	$tabs = $tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'criticalExtensions {'; 
	$rrc_out_end= $tabs . '}' . "\n" . $rrc_out_end;
	
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		//systemInformation-r8				SystemInformation-r8-IEs
		systemInformation_r8();
	}
	if($criticalExtensions_choice_bit=='1'){
		//criticalExtensionsFuture			SEQUENCE {}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'criticalExtensionsFuture {';
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	$tabs = substr($tabs,0,-4);
	
}

function systemInformationBlockType1(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$rrc_out= $rrc_out . "\n" . $tabs . 'message c1 : systemInformationBlockType1 : ' . '{';
	$rrc_out_end= $tab . '}' . "\n" . $rrc_out_end;
	//check options of SystemInformationBlockType1
	$p_Max_option_bit=substr($rrc_msg_bin,0,1);
	$tdd_Config_option_bit=substr($rrc_msg_bin,1,1);
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$tabs = $tabs . $tab;
	
	//cellAccessRelatedInfo
	$rrc_out=$rrc_out . "\n" . $tabs . 'cellAccessRelatedInfo {'; 
	//check option for cellAccessRelatedInfo
	$csg_Identity_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	//plmn-IdentityList
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'plmn-IdentityList {';
	
	$PLMN_IdentityList_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$PLMN_IdentityList_value=1+bindec($PLMN_IdentityList_bit);
	
	do{
		//
		$tabs=$tabs . $tab;
		$rrc_out=$rrc_out . "\n" . $tabs . 'plmn-Identity {';
		//check option for mcc
		$mcc_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		//mcc
		if($mcc_option_bit=='1'){
			$mcc1=bindec(substr($rrc_msg_bin,0,4));
			$mcc2=bindec(substr($rrc_msg_bin,4,4));
			$mcc3=bindec(substr($rrc_msg_bin,8,4));
			$rrc_msg_bin=substr($rrc_msg_bin,12);
			//$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mcc ' . $mcc1 . $mcc2 . $mcc3;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mcc {';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mcc1 . ',';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mcc2 . ',';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mcc3 ;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '},';
		}
		//mnc
		$mnc_size_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($mnc_size_bit=='0'){
			$mnc1=bindec(substr($rrc_msg_bin,0,4));
			$mnc2=bindec(substr($rrc_msg_bin,4,4));
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			//$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mnc ' . $mnc1 . $mnc2 ;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mnc {';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mnc1 . ',';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mnc2;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '},';
		}
		if($mnc_size_bit=='1'){
			$mnc1=bindec(substr($rrc_msg_bin,0,4));
			$mnc2=bindec(substr($rrc_msg_bin,4,4));
			$mnc3=bindec(substr($rrc_msg_bin,8,4));
			$rrc_msg_bin=substr($rrc_msg_bin,12);
			//$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mnc ' . $mnc1 . $mnc2 . $mnc3;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mnc {';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mnc1 . ',';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mnc2 . ',';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . $mnc3 ;
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '},';
		}
		//cellReservedForOperatorUse
		$cellReservedForOperatorUse_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		if($cellReservedForOperatorUse_bit=='0'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellReservedForOperatorUse ' . 'reserved';
		}
		if($cellReservedForOperatorUse_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellReservedForOperatorUse ' . 'notReserved';
		}
		$rrc_out=$rrc_out . "\n" . $tabs . '},';
		
		//
		$PLMN_IdentityList_value=$PLMN_IdentityList_value-1;
		//$rrc_out=$rrc_out . "\n" . '}';
		$tabs=substr($tabs,0,-4);
	} while ($PLMN_IdentityList_value>0);
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
	//trackingAreaCode
	$trackingAreaCode_bit=substr($rrc_msg_bin,0,16);
	$rrc_msg_bin=substr($rrc_msg_bin,16);
	$rrc_out=$rrc_out . "\n" . $tabs . 'trackingAreaCode ' . $trackingAreaCode_bit . ',';
	//cellIdentity
	$cellIdentity_bit=substr($rrc_msg_bin,0,28);
	$rrc_msg_bin=substr($rrc_msg_bin,28);
	$rrc_out=$rrc_out . "\n" . $tabs . 'cellIdentity ' . $cellIdentity_bit . ',';
	//cellBarred
	$cellBarred_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$cellBarred_value=cellBarred($cellBarred_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . 'cellBarred ' . $cellBarred_value . ',';
	//intraFreqReselection
	$intraFreqReselection_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$intraFreqReselection_value=intraFreqReselection($intraFreqReselection_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . 'intraFreqReselection ' . $intraFreqReselection_value . ',';
	//csg-Indication
	$csg_Indication_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$csg_Indication_value=csg_Indication($csg_Indication_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . 'csg-Indication ' . $csg_Indication_value;
	
	//csg-Identity
	if($csg_Identity_option_bit=='1'){
		$csg_Identity_bit=substr($rrc_msg_bin,0,27);
		$rrc_msg_bin=substr($rrc_msg_bin,27);
		$rrc_out=$rrc_out . ',';
		$rrc_out=$rrc_out . "\n" . $tabs . 'csg-Identity ' . $csg_Identity_bit;
	}
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//$tabs=substr($tabs,0,-4);
	
	//cellSelectionInfo
	$rrc_out=$rrc_out . "\n" . $tabs . 'cellSelectionInfo ';
	$q_RxLevMinOffset_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	///q-RxLevMin
	$q_RxLevMin_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$q_RxLevMin_value=q_RxLevMin($q_RxLevMin_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'q-RxLevMin ' . $q_RxLevMin_value; 
	
	///q-RxLevMinOffset
	if($q_RxLevMinOffset_option_bit=='1'){
		$q_RxLevMinOffset_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$q_RxLevMinOffset_value=q_RxLevMinOffset($q_RxLevMinOffset_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . ',';
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'q-RxLevMinOffset ' . $q_RxLevMinOffset_value;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	
	//p-Max
	if($p_Max_option_bit=='1'){
		//read 6 bit
		$p_Max_bit=substr($rrc_msg_bin,0,6);
		$rrc_msg_bin=substr($rrc_msg_bin,6);
		$p_Max_value=p_Max($p_Max_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . 'p-Max ' . $p_Max_value . ',';
		
	}
	//freqBandIndicator
	//read 6 bits
	$freqBandIndicator_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$freqBandIndicator_value=freqBandIndicator($freqBandIndicator_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . 'freqBandIndicator ' . $freqBandIndicator_value . ',';
	//schedulingInfoList
	$rrc_out=$rrc_out . "\n" . $tabs. 'schedulingInfoList {';
	$schedulingInfoList_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$schedulingInfoList_value=schedulingInfoList($schedulingInfoList_bit);
	do{
		///ScheduleingInfo
		$rrc_out=$rrc_out . "\n" . $tabs . $tab .'SchedulingInfo {';
		////si-Periodicity
		$si_Periodicity_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$si_Periodicity_value=si_Periodicity($si_Periodicity_bit);
		$rrc_out=$rrc_out ."\n" . $tabs . str_repeat($tab,2) . 'si-Periodicity ' . $si_Periodicity_value . ',';
		
		////sib-MappingInfo
		$rrc_out=$rrc_out ."\n" . $tabs . str_repeat($tab,2) . 'sib-MappingInfo { ';
		$sib_MappingInfo_bit=substr($rrc_msg_bin,0,5);
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$sib_MappingInfo_value=sib_MappingInfo($sib_MappingInfo_bit);
		while($sib_MappingInfo_value>0){
			//SIB-Type
			$SIB_type_ext_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			
			$SIB_type_bit=substr($rrc_msg_bin,0,4);
			$rrc_msg_bin=substr($rrc_msg_bin,4);
			$SIB_type_value=SIB_type($SIB_type_bit);
			
			$rrc_out=$rrc_out ."\n" . $tabs . str_repeat($tab,3) . $SIB_type_value;
			$sib_MappingInfo_value=$sib_MappingInfo_value-1;
		}
		
		$rrc_out=$rrc_out ."\n" . $tabs . str_repeat($tab,2) . '}';
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab .'}';
		$schedulingInfoList_value=$schedulingInfoList_value-1;	
	}while($schedulingInfoList_value>0);
	
	$rrc_out=$rrc_out . "\n" . $tabs. '}';
	
	//tdd-Config
	if($tdd_Config_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" .$tabs . 'TDD-Config {';
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
		
		$rrc_out=$rrc_out . "\n" .$tabs . '}';
	}
	//si-WindowLength
	$si_WindowLength_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$si_WindowLength_value=si_WindowLength($si_WindowLength_bit);
	$rrc_out=$rrc_out . "\n" .$tabs . 'si-WindowLength ' . $si_WindowLength_value . ',';
	
	//systemInfoValueTag
	$systemInfoValueTag_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$systemInfoValueTag_value=systemInfoValueTag($systemInfoValueTag_bit);
	$rrc_out=$rrc_out . "\n" .$tabs . 'systemInfoValueTag ' . $systemInfoValueTag_value ;
	
	//nonCriticalExtension
	if($nonCriticalExtension_option_bit=='1'){
	}
	
	$tabs=substr($tabs,0,-4);
}

function systemInformation_r8(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	$maxSIB=32;
	//
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'systemInformation-r8 {';
	$nonCriticalExtension_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
		
	//sib-TypeAndInfo
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'sib-TypeAndInfo {';
	//check size of sib-TypeAndInfo, read 5 bits
	$sib_TypeAndInfo_size=bindec(substr($rrc_msg_bin,0,5))+1;
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	while($sib_TypeAndInfo_size>0){
		//check extension and do nothing
		$sib_TypeAndInfo_ext_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
				
		//read 4 bits to check the choice
		$sib_TypeAndInfo_choice_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		switch($sib_TypeAndInfo_choice_bit){
			case '0000':
				sib2();
				break;
			case '0001':
				sib3();
				break;
			case '0010':
				sib4();
				break;
			case '0011':
				sib5();
				break;
			case '0100':
				sib6();
				break;
			case '0101':
				sib7();
				break;
			case '0110':
				sib8();
				break;
			case '0111':
				sib9();
				break;
			case '1000':
				sib10();
				break;
			case '1001':
				sib11();
				break;
			default:
				break;
		}
		
		//
		$sib_TypeAndInfo_size=$sib_TypeAndInfo_size-1;
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
	//nonCriticalExtension		SystemInformation-v8a0-IEs		OPTIONAL
	if($nonCriticalExtension_option_bit=='1'){
	}
	//
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}

function sib2(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'sib2 {';
	
	//check extention for sib2, and do nothing
	$sib2_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//check options
	$ac_BarringInfo_option_bit=substr($rrc_msg_bin,0,1);
	$mbsfn_SubframeConfigList_option_bit=substr($rrc_msg_bin,1,1);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	//ac-BarringInfo option
	if($ac_BarringInfo_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'ac-BarringInfo {';
		//check options
		$ac_BarringForMO_Signalling_option_bit=substr($rrc_msg_bin,0,1);
		$ac_BarringForMO_Data_option_bit=substr($rrc_msg_bin,1,1);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		
		//ac-BarringForEmergency	BOOLEAN
		$ac_BarringForEmergency_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$ac_BarringForEmergency_value=ac_BarringForEmergency($ac_BarringForEmergency_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ac-BarringForEmergency ' . $ac_BarringForEmergency_value;
		
		//ac-BarringForMO-Signalling	AC-BarringConfig	OPTIONAL
		if($ac_BarringForMO_Signalling_option_bit=='1'){
			AC_BarringConfig();
		}
		//ac-BarringForMO-Data	AC-BarringConfig	OPTIONAL
		if($ac_BarringForMO_Data_option_bit=='1'){
			AC_BarringConfig();
		}
		//
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//radioResourceConfigCommon			RadioResourceConfigCommonSIB
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'radioResourceConfigCommon {';
	//check extention and do nothing
	$RadioResourceConfigCommonSIB_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	///rach-ConfigCommon	RACH-ConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'rach-ConfigCommon {';
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
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///bcch-Config	BCCH-Config
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'bcch-Config {';
	////modificationPeriodCoeff 
	//2 bits
	$modificationPeriodCoeff_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$modificationPeriodCoeff_value=modificationPeriodCoeff($modificationPeriodCoeff_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'modificationPeriodCoeff  ' . $modificationPeriodCoeff_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///pcch-Config	PCCH-Config
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pcch-Config {';
	////defaultPagingCycle
	//2 bits
	$defaultPagingCycle_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$defaultPagingCycle_value=defaultPagingCycle($defaultPagingCycle_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'defaultPagingCycle ' . $defaultPagingCycle_value . ','; 
	////nB
	//3 bits
	$nB_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$nB_value=nB($nB_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'nB ' . $nB_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///prach-Config	PRACH-ConfigSIB
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'prach-Config  {';
	////rootSequenceIndex
	$rootSequenceIndex_bit=substr($rrc_msg_bin,0,10);
	$rrc_msg_bin=substr($rrc_msg_bin,10);
	$rootSequenceIndex_value=rootSequenceIndex($rootSequenceIndex_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'rootSequenceIndex ' . $rootSequenceIndex_value . ',';
		
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
		
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///pdsch-ConfigCommon	PDSCH-ConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pdsch-ConfigCommon {';
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
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///pusch-ConfigCommon	PUSCH-ConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pusch-ConfigCommon {';
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
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///pucch-ConfigCommon	PUCCH-ConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'pucch-ConfigCommon {';
	////deltaPUCCH-Shift					ENUMERATED {ds1, ds2, ds3},
	$deltaPUCCH_Shift_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$deltaPUCCH_Shift_value=deltaPUCCH_Shift($deltaPUCCH_Shift_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'deltaPUCCH-Shift ' . $deltaPUCCH_Shift_value . ',';
	////nRB-CQI								INTEGER (0..98),
	$nRB_CQI_bit=substr($rrc_msg_bin,0,7);
	$rrc_msg_bin=substr($rrc_msg_bin,7);
	$nRB_CQI_value=nRB_CQI($nRB_CQI_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'nRB-CQI ' . $nRB_CQI_value . ',';
	////nCS-AN								INTEGER (0..7),
	$nCS_AN_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$nCS_AN_value=nCS_AN($nCS_AN_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'nCS-AN ' . $nCS_AN_value . ',';
	////n1PUCCH-AN							INTEGER (0..2047)
	$n1PUCCH_AN_bit=substr($rrc_msg_bin,0,11);
	$rrc_msg_bin=substr($rrc_msg_bin,11);
	$n1PUCCH_AN_value=n1PUCCH_AN($n1PUCCH_AN_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'n1PUCCH-AN ' . $n1PUCCH_AN_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	///soundingRS-UL-ConfigCommon	SoundingRS-UL-ConfigCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'soundingRS-UL-ConfigCommon {';
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
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	
	///uplinkPowerControlCommon	UplinkPowerControlCommon
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'uplinkPowerControlCommon {';
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
	
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	
	///ul-CyclicPrefixLength	UL-CyclicPrefixLength
	$ul_CyclicPrefixLength_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$ul_CyclicPrefixLength_value=ul_CyclicPrefixLength($ul_CyclicPrefixLength_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ul-CyclicPrefixLength ' . $ul_CyclicPrefixLength_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	
	//ue-TimersAndConstants
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'ue-TimersAndConstants {';
	//check extention and do nothing
	$ue_TimersAndConstants_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	///t300
	$t300_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$t300_value=t300($t300_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't300 ' . $t300_value . ',';
	///t301
	$t301_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$t301_value=t301($t301_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't301 ' . $t301_value . ',';
	///t310
	$t310_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$t310_value=t310($t310_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't310 ' . $t310_value . ',';
	///n310
	$n310_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$n310_value=n310($n310_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'n310 ' . $n310_value . ',';

	///t311
	$t311_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$t311_value=t311($t311_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't311 ' . $t311_value . ',';
	///n311
	$n311_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$n311_value=n311($n311_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'n311 ' . $n311_value ;
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	//freqInfo	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'freqInfo {';
	$ul_CarrierFreq_option_bit=substr($rrc_msg_bin,0,1);
	$ul_Bandwidth_option_bit=substr($rrc_msg_bin,1,1);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	///ul-CarrierFreq	ARFCN-ValueEUTRA
	//maxEARFCN=65535
	if($ul_CarrierFreq_option_bit=='1'){
		$ul_CarrierFreq_bit=substr($rrc_msg_bin,0,16);
		$rrc_msg_bin=substr($rrc_msg_bin,16);
		$ul_CarrierFreq_value=ul_CarrierFreq($ul_CarrierFreq_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ul-CarrierFreq ' . $ul_CarrierFreq_value . ',';
		
	}
	///ul-Bandwidth
	if($ul_Bandwidth_option_bit=='1'){
		$ul_Bandwidth_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$ul_Bandwidth_value=ul_Bandwidth($ul_Bandwidth_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'ul-Bandwidth ' . $ul_Bandwidth_value . ',';
	}
	///additionalSpectrumEmission			AdditionalSpectrumEmission
	$additionalSpectrumEmission_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$additionalSpectrumEmission_value=additionalSpectrumEmission($additionalSpectrumEmission_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'additionalSpectrumEmission ' . $additionalSpectrumEmission_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	//mbsfn-SubframeConfigList option
	//mbsfn-SubframeConfigList MBSFN-SubframeConfigList
	//MBSFN-SubframeConfigList    (SIZE (1..maxMBSFN-Allocations)) OF MBSFN-SubframeConfig
	//maxMBSFN-Allocations=8
	
	if($mbsfn_SubframeConfigList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mbsfn-SubframeConfigList {';
		$mbsfn_SubframeConfigList_size=bindec(substr($rrc_msg_bin,0,3))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		while($mbsfn_SubframeConfigList_size>0){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'MBSFN-SubframeConfig {';
			///radioframeAllocationPeriod
			$radioframeAllocationPeriod_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$radioframeAllocationPeriod_value=radioframeAllocationPeriod($radioframeAllocationPeriod_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'radioframeAllocationPeriod '. $radioframeAllocationPeriod_value . ',';
			///radioframeAllocationOffset
			$radioframeAllocationOffset_bit=substr($rrc_msg_bin,0,3);
			$rrc_msg_bin=substr($rrc_msg_bin,3);
			$radioframeAllocationOffset_value=radioframeAllocationOffset($radioframeAllocationOffset_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'radioframeAllocationOffset ' . $radioframeAllocationOffset_value . ',';
			///subframeAllocation
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'subframeAllocation {';
			$subframeAllocation_choice_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			if($subframeAllocation_choice_bit=='0'){
				$oneFrame_bit=substr($rrc_msg_bin,6);
				$rrc_msg_bin=substr($rrc_msg_bin,6);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) .  'oneFrame ' . $oneFrame_bit;
			}
			if($subframeAllocation_choice_bit=='1'){
				$fourFrame_bit=substr($rrc_msg_bin,24);
				$rrc_msg_bin=substr($rrc_msg_bin,24);
				$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,4) .  'oneFrame ' . $fourFrame_bit;
			}
			
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . '}';
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			$mbsfn_SubframeConfigList_size=$mbsfn_SubframeConfigList_size-1;
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	//timeAlignmentTimerCommon
	$timeAlignmentTimerCommon_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$timeAlignmentTimerCommon_value=timeAlignmentTimerCommon($timeAlignmentTimerCommon_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'timeAlignmentTimerCommon ' . $timeAlignmentTimerCommon_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//
	$tabs = substr($tabs,0,-4);
}
function sib3(){

	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'sib3 {';
	//check extention and do nothing
	$sib3_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//cellReselectionInfoCommon
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellReselectionInfoCommon {';
	//check options
	$speedStateReselectionPars_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	///q-Hyst
	$q_Hyst_bit=substr($rrc_msg_bin,0,4);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	$q_Hyst_value=q_Hyst($q_Hyst_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'q-Hyst ' . $q_Hyst_value;
	
	///speedStateReselectionPars
	if($speedStateReselectionPars_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'speedStateReselectionPars {';
		//check option
		$q_HystSF_option_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		
		////mobilityStateParameters				MobilityStateParameters,
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'mobilityStateParameters {';
		/////t-Evaluation
		$t_Evaluation_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$t_Evaluation_value=t_Evaluation($t_Evaluation_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 't-Evaluation ' . $t_Evaluation_value . ','; 
		
		/////t-HystNormal
		$t_HystNormal_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$t_HystNormal_value=t_HystNormal($t_HystNormal_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 't-HystNormal ' . $t_HystNormal_value . ',';
		/////n-CellChangeMedium					INTEGER (1..16),
		$n_CellChangeMedium_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$n_CellChangeMedium_value=n_CellChangeMedium($n_CellChangeMedium_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'n-CellChangeMedium	 ' . $n_CellChangeMedium_value . ',';
		/////n-CellChangeHigh					INTEGER (1..16)
		$n_CellChangeHigh_bit=substr($rrc_msg_bin,0,4);
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		$n_CellChangeHigh_value=n_CellChangeHigh($n_CellChangeHigh_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'n-CellChangeHigh	 ' . $n_CellChangeHigh_value . ',';
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		/////
		////q-HystSF 
		if($q_HystSF_option_bit=='1'){
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'q-HystSF {';
			/////sf-Medium
			$sf_Medium_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$sf_Medium_value=sf_Medium($sf_Medium_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'sf-Medium ' . $sf_Medium_value . ',';
			
			/////sf-High
			$sf_High_bit=substr($rrc_msg_bin,0,2);
			$rrc_msg_bin=substr($rrc_msg_bin,2);
			$sf_High_value=sf_High($sf_High_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'sf-High ' . $sf_High_value;
			
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
		}
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	//cellReselectionServingFreqInfo
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'cellReselectionServingFreqInfo {';
	//check options
	$s_NonIntraSearch_option_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	///s-NonIntraSearch					ReselectionThreshold		OPTIONAL
	if($s_NonIntraSearch_option_bit=='1'){
		$s_NonIntraSearch_bit=substr($rrc_msg_bin,0,5);
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$s_NonIntraSearch_value=ReselectionThreshold($s_NonIntraSearch_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 's-NonIntraSearch ' . $s_NonIntraSearch_value . ','; 
	}
	///threshServingLow					ReselectionThreshold
	$threshServingLow_bit=substr($rrc_msg_bin,0,5);
	$rrc_msg_bin=substr($rrc_msg_bin,5);
	$threshServingLow_value=ReselectionThreshold($threshServingLow_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'threshServingLow ' . $threshServingLow_value . ','; 
	///cellReselectionPriority				CellReselectionPriority
	$cellReselectionPriority_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$cellReselectionPriority_value=CellReselectionPriority($cellReselectionPriority_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'cellReselectionPriority ' . $cellReselectionPriority_value;
	
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	
	//intraFreqCellReselectionInfo
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'intraFreqCellReselectionInfo {';
	//check options
	$p_Max_option_bit=substr($rrc_msg_bin,0,1);
	$s_IntraSearch_option_bit=substr($rrc_msg_bin,1,1);
	$allowedMeasBandwidth_option_bit=substr($rrc_msg_bin,2,1);
	$t_ReselectionEUTRA_SF_option_bit=substr($rrc_msg_bin,3,1);
	$rrc_msg_bin=substr($rrc_msg_bin,4);
	///q-RxLevMin							Q-RxLevMin,
	$q_RxLevMin_bit=substr($rrc_msg_bin,0,6);
	$rrc_msg_bin=substr($rrc_msg_bin,6);
	$q_RxLevMin_value=q_RxLevMin($q_RxLevMin_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'q-RxLevMin ' . $q_RxLevMin_value . ',';
	///p-Max								P-Max						OPTIONAL
	if($p_Max_option_bit=='1'){
		$p_Max_bit=substr($rrc_msg_bin,0,6);
		$rrc_msg_bin=substr($rrc_msg_bin,6);
		$p_Max_value=p_Max($p_Max_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'p-Max ' . $p_Max_value . ',';
	}
	///s-IntraSearch						ReselectionThreshold		OPTIONAL,
	if($s_IntraSearch_option_bit=='1'){
		$s_IntraSearch_bit=substr($rrc_msg_bin,0,5);
		$rrc_msg_bin=substr($rrc_msg_bin,5);
		$s_IntraSearch_value=ReselectionThreshold($s_IntraSearch_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 's-IntraSearch ' . $s_IntraSearch_value . ',';
	}
	///allowedMeasBandwidth				AllowedMeasBandwidth		OPTIONAL
	if($allowedMeasBandwidth_option_bit=='1'){
		$allowedMeasBandwidth_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$allowedMeasBandwidth_value=AllowedMeasBandwidth($allowedMeasBandwidth_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'allowedMeasBandwidth ' . $allowedMeasBandwidth_value . ',';
	}
	///presenceAntennaPort1				PresenceAntennaPort1
	$presenceAntennaPort1_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	$presenceAntennaPort1_value=PresenceAntennaPort1($presenceAntennaPort1_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'presenceAntennaPort1 ' . $presenceAntennaPort1_value . ',';
	
	///neighCellConfig						NeighCellConfig
	$neighCellConfig_bit=substr($rrc_msg_bin,0,2);
	$rrc_msg_bin=substr($rrc_msg_bin,2);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'neighCellConfig ' . $neighCellConfig_bit . ',';
	///t-ReselectionEUTRA					T-Reselection,
	$t_ReselectionEUTRA_bit=substr($rrc_msg_bin,0,3);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	$t_ReselectionEUTRA_value=T_Reselection($t_ReselectionEUTRA_bit);
	$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't-ReselectionEUTRA ' . $t_ReselectionEUTRA_value;
	///t-ReselectionEUTRA-SF				SpeedStateScaleFactors		OPTIONAL
	if($t_ReselectionEUTRA_SF_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 't-ReselectionEUTRA-SF {';
		////sf-Medium
		$sf_Medium_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		$sf_Medium_value=SpeedStateScaleFactors($sf_Medium_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'sf-Medium ' . $sf_Medium_value . ','; 
		////sf-High
		$sf_High_bit=substr($rrc_msg_bin,0,2);
		$rrc_msg_bin=substr($rrc_msg_bin,2);
		$sf_High_value=SpeedStateScaleFactors($sf_High_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'sf-High ' . $sf_High_value;
		
		$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
	}
	$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	//
	$tabs = substr($tabs,0,-4);
}
function sib4(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tab,$tabs;
	//
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'sib4 {';
	//check extention and do nothing
	$sib4_ext_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	
	//check options
	$intraFreqNeighCellList_option_bit=substr($rrc_msg_bin,0,1);
	$intraFreqBlackCellList_option_bit=substr($rrc_msg_bin,1,1);
	$csg_PhysCellIdRange_option_bit=substr($rrc_msg_bin,2,1);
	$rrc_msg_bin=substr($rrc_msg_bin,3);
	
	//intraFreqNeighCellList				IntraFreqNeighCellList		OPTIONAL
	if($intraFreqNeighCellList_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'intraFreqNeighCellList {';
		$intraFreqNeighCellList_size=bindec(substr($rrc_msg_bin,0,4))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		while($intraFreqNeighCellList_size>0){
			///IntraFreqNeighCellInfo
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'intraFreqNeighCellList {';
			//check extention and do nothing
			$IntraFreqNeighCellInfo_ext_bit=substr($rrc_msg_bin,0,1);
			$rrc_msg_bin=substr($rrc_msg_bin,1);
			///physCellId		PhysCellId
			$physCellId_bit=substr($rrc_msg_bin,0,9);
			$rrc_msg_bin=substr($rrc_msg_bin,9);
			$physCellId_value=PhysCellId($physCellId_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'physCellId ' . $physCellId_value . ','; 
			///q-OffsetCell	Q-OffsetRange
			$q_OffsetCell_bit=substr($rrc_msg_bin,0,5);
			$rrc_msg_bin=substr($rrc_msg_bin,5);
			$q_OffsetCell_bit_value=Q_OffsetRange($q_OffsetCell_bit);
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,3) . 'q-OffsetCell ' . $q_OffsetCell_bit_value;
			
			$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . '}';
			$intraFreqNeighCellList_size=$intraFreqNeighCellList_size-1;
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	
	//intraFreqBlackCellList				IntraFreqBlackCellList		OPTIONAL
	if($intraFreqBlackCellList_option_bit=='1'){
		//
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'intraFreqBlackCellList {';
		$intraFreqBlackCellList_size=bindec(substr($rrc_msg_bin,0,4))+1;
		$rrc_msg_bin=substr($rrc_msg_bin,4);
		
		while($intraFreqBlackCellList_size>0){
			$tabs=$tabs . str_repeat($tab,2);
			$rrc_out=$rrc_out . "\n" . $tabs . '{';
			PhysCellIdRange();			
			$rrc_out=$rrc_out . "\n" . $tabs . '}';
			$tabs=substr($tabs,0,-8);
			$intraFreqBlackCellList_size=$intraFreqBlackCellList_size-1;
		}
		
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	
	//csg-PhysCellIdRange					PhysCellIdRange				OPTIONAL
	if($csg_PhysCellIdRange_option_bit=='1'){
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'csg-PhysCellIdRange {';
		$tabs=$tabs . str_repeat($tab,2);
		$rrc_out=$rrc_out . "\n" . $tabs . '{';
		PhysCellIdRange();			
		$rrc_out=$rrc_out . "\n" . $tabs . '}';
		$tabs=substr($tabs,0,-8);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . '}';
	}
	
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}
function sib5(){
}
function sib6(){
}
function sib7(){
}
function sib8(){
}
function sib9(){
}
function sib10(){
}
function sib11(){
}


?>