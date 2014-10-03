<?php
 
// Prevent PHP from stopping the script after 30 sec
set_time_limit(0);

$channel = '#openra';

// Opening the socket to the Rizon network
$socket = fsockopen("irc.freenode.net", 6667);
 
// Send auth info
fputs($socket,"USER HammsterBot hammst3r.de HammsterBot :Hammster bot\n");
fputs($socket,"NICK HammsterBot\n");
 
// Join channel
fputs($socket,"JOIN ".$channel."\n");
 
// Force an endless while
while(1) {
 
	// Continue the rest of the script here
	while($data = fgets($socket, 128)) {
 
		echo $data;
		flush();
 
		// Separate all data
		$ex = explode(' ', $data);
 
		// Send PONG back to the server
		if($ex[0] == "PING"){
			fputs($socket, "PONG ".$ex[1]."\n");
		}
 
		// executes chat command
        if($ex[0] != 'PING' && ISSET($ex[3])){
            $command = str_replace(array(chr(10), chr(13)), '', $ex[3]);
            if ($command == ":!alive?") {
                fputs($socket, "PRIVMSG ".$channel." :whazzup? \n");
            }
            if ($command == ":!time") {
                fputs($socket, "PRIVMSG ".$channel." :".date(DATE_RFC2822)." \n");
            }
            if ($command == ":!help") {
                fputs($socket, "PRIVMSG ".$channel." :Hambot phpIRCbot v0.1 commands !alive?, !time \n");
            }
            if ($command == ":!slave") {
                
                $parts = explode("!",$ex[0]); 
                //break the string up around the "/" character in $mystring 

                $user = substr($parts['0'], 1);
                //grab the first part 
                
                if($user == "Hammster")
                    fputs($socket, "PRIVMSG ".$channel." :Yes MASTER! \n");
                else
                    fputs($socket, "PRIVMSG ".$channel." :get lost ".$user." you filthy infidel! \n");
            }
            if ($command == ":!test") {
                fputs($socket, "PRIVMSG ".$channel." :value0 ".$ex[0].", value1 ".$ex[1].",value2 ".$ex[2].",value3 ".$ex[3]."\n");
            }
        }
	}
}
 
?>