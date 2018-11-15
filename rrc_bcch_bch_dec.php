<?php
include('rrc_fun.php');
//$rrc_msg="6E 98 00";
$rrc_msg=$_POST["hexStr"];

//replace blank in this string
$rrc_msg=str_replace(" ","",$rrc_msg);
//convert hex string to binary string
$rrc_msg_bin=my_hex2bin($rrc_msg);

//
//construct output string
$tab='    ';
$rrc_out='BCCH-BCH-Message : {';
$rrc_out_end='}';
$tabs=$tab;

//
$rrc_out=$rrc_out . "\n" . $tabs . 'message : MasterInformationBlock{';
$rrc_out_end="\n" . $tab . '}' . "\n" . $rrc_out_end;

//dl-Bandwidth
$dl_Bandwidth_bit=substr($rrc_msg_bin,0,3);
$rrc_msg_bin=substr($rrc_msg_bin,3);
$dl_Bandwidth_value=dl_Bandwidth($dl_Bandwidth_bit);
$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'dl-Bandwidth ' . $dl_Bandwidth_value . ',';

//phich-Config
$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'PHICH-Config  {';
//phich-Duration
$phich_Duration_bit=substr($rrc_msg_bin,0,1);
$rrc_msg_bin=substr($rrc_msg_bin,1);
$phich_Duration_value=phich_Duration($phich_Duration_bit);
$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'phich-Duration ' . $phich_Duration_value . ',';

//phich-Resource
$phich_Resource_bit=substr($rrc_msg_bin,0,2);
$rrc_msg_bin=substr($rrc_msg_bin,2);
$phich_Resource_value=phich_Resource($phich_Resource_bit);
$rrc_out=$rrc_out . "\n" . $tabs . str_repeat($tab,2) . 'phich-Resource ' . $phich_Resource_value;

$rrc_out=$rrc_out . "\n" . $tabs . $tab . '},';
//systemFrameNumber
//read 8 bits
$systemFrameNumber_bit=substr($rrc_msg_bin,0,8);
$rrc_msg_bin=substr($rrc_msg_bin,8);
$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'systemFrameNumer ' . $systemFrameNumber_bit . ',';
//spare
//read 10 bits
$rrc_out=$rrc_out . "\n" . $tabs . $tab . 'spare ' . substr($rrc_msg_bin,0,10);

//output RRC decoding message text
echo $rrc_out;
echo "\n";
echo $rrc_out_end;

?>