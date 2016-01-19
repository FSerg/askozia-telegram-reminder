<?php

require('phpagi.php');

    function GetVarChannnel($agi, $_varName){
        $v = $agi->get_variable($_varName);
        if(!$v['result'] == 0){
            $agi->verbose($_varName.' ---> '.$v['data'], 10);
            return $v['data'];
        }
        else{
            $agi->verbose($_varName.' not set', 10);
            return "";
        }
    } // GetVarChannnel($_agi, $_varName)

    $agi = new AGI();
    $number = GetVarChannnel($agi, "CALLERID(num)");
    $mytime = GetVarChannnel($agi, "STRFTIME(${EPOCH},,%Y.%m.%d-%H:%M:%S)");
    // $telegram_bot =
    // $chat_id = 

    $textMessage = "Пропущенный звонок: ".$number." (".$mytime.")";
    $content=file_get_contents("https://api.telegram.org/bot124384686:AAF_WfHdzZT0fqOgSIwXRb63eLtO3IxUelI/sendMessage?chat_id=-10651220&text=".$textMessage,true);

?>​
