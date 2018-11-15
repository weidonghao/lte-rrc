<?php
include('rrc_fun.php');

$rrc_msg=$_POST["hexStr"];

//$rrc_msg="52 30 AC 4C 7E 46 AC 4C";
//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);
//read 1 bit to check choice for UL-DCCH-MessageType
$UL_CCCH_msgtype_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
if($UL_CCCH_msgtype_bit=='1'){
	//echo 'Note: messageClassExtension decoding is supported so far!';
	exit('Note: messageClassExtension decoding is supported so far!');
}

//construct output string
$tab='    ';
$tabs = $tab;
$rrc_out='UL-CCCH-Message : {';
$rrc_out_end='}';

//read 1 bites to check the message type of c1
$c1_type_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);

switch ($c1_type_bit){
	case '0':
		rrcConnectionReestablishmentRequest();
		break;
	case '1':
		rrcConnectionRequest();
		break;
	default:
		break;
}

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

//procedure end here.

//function start

function rrcConnectionReestablishmentRequest(){
}

function rrcConnectionRequest(){
	global $rrc_msg_bin,$rrc_out,$rrc_out_end,$tabs,$tab;
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'RRCConnectionRequest : {' ;
	$tabs=$tabs . $tab;
	$rrc_out=$rrc_out . "\n" . $tabs . 'criticalExtensions {';
	$criticalExtensions_choice_bit=substr($rrc_msg_bin,0,1);
	$rrc_msg_bin=substr($rrc_msg_bin,1);
	if($criticalExtensions_choice_bit=='0'){
		//
		$tabs=$tabs . $tab;
		$rrc_out=$rrc_out . "\n". $tabs . 'rrcConnectionRequest-r8 {';
		//ue-Identity	
		$tabs=$tabs . $tab;
		$rrc_out=$rrc_out . "\n". $tabs . 'ue-Identity {';
		$InitialUE_Identity_choice_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		if($InitialUE_Identity_choice_bit=='0'){
			//s-TMSI
			$tabs=$tabs . $tab;
			$rrc_out=$rrc_out . "\n". $tabs . 'S-TMSI  {';
			//mmec
			//read 8 bits
			$mmec_bit=substr($rrc_msg_bin,0,8);
			$rrc_msg_bin=substr($rrc_msg_bin,8);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'mmec ' . $mmec_bit . ','; 
			//m-TMSI
			//32 bit
			$m_TMSI_bit=substr($rrc_msg_bin,0,32);
			$rrc_msg_bin=substr($rrc_msg_bin,32);
			$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'm-TMSI ' . $m_TMSI_bit ; 
			
			$rrc_out=$rrc_out . "\n". $tabs . '}';
			$tabs=substr($tabs,0,-4);
		}
		if($InitialUE_Identity_choice_bit=='1'){
			//randomValue
			$randomValue_bit=substr($rrc_msg_bin,0,40);
			$rrc_msg_bin=substr($rrc_msg_bin,40);
			$tabs=$tabs . $tab;
			$rrc_out=$rrc_out . "\n". $tabs . 'randomValue ' . $randomValue_bit;			
			$tabs=substr($tabs,0,-4);
		}
		$rrc_out=$rrc_out . "\n". $tabs . '},';
		$tabs=substr($tabs,0,-4);
		//establishmentCause	
		//read 3 bits
		$establishmentCause_bit=substr($rrc_msg_bin,0,3);
		$rrc_msg_bin=substr($rrc_msg_bin,3);
		$establishmentCause_value=establishmentCause($establishmentCause_bit);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'establishmentCause ' . $establishmentCause_value . ',';
		//spare	
		$spare_bit=substr($rrc_msg_bin,0,1);
		$rrc_msg_bin=substr($rrc_msg_bin,1);
		$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'spare ' . $spare_bit ;
		
		$rrc_out=$rrc_out . "\n". $tabs . '}';
		$tabs=substr($tabs,0,-4);
	}
	if($criticalExtensions_choice_bit=='1'){
		$tabs=$tabs . $tab;
		$rrc_out=$rrc_out . "\n". $tabs . 'criticalExtensionsFuture {';
		//
		$rrc_out=$rrc_out . "\n". $tabs . '}';
		$tabs=substr($tabs,0,-4);
	}
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
	//close
	$rrc_out=$rrc_out . "\n" . $tabs . '}';
	$tabs=substr($tabs,0,-4);
}

function establishmentCause($in){
	switch ($in){
		case '000':
			$ret='emergency';
			break;
		case '001':
			$ret='highPriorityAccess';
			break;
		case '010':
			$ret='mt-Access';
			break;
		case '011':
			$ret='mo-Signalling';
			break;
		case '100':
			$ret='mo-Data';
			break;
		case '101':
			$ret='delayTolerantAccess-v1020';
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