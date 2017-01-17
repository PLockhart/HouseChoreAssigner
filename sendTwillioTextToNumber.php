<?php
//sends a message to a mobile number
function sendMessageToNumber($number, $message, $client, $myNumber) {
    if ($number != "" && $number != NULL && strlen($number) >= 10) {
        $client->account->messages->create(array( 
            'To' => $number, 
            'From' => $myNumber, 
            'Body' => $message,   
        ));
    }
    else {
        
        echo "Cannot send number to null or blank number";
    }
}