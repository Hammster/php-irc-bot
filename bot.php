<?php

// Prevent PHP from stopping the script after 30 sec
set_time_limit(0);

// Change these values!
$channels  = array('#openra', '#another_channel', '#another_channel2');
$nickname = 'HammsterBot';
// $password = 'secret';
$master   = 'Hammster';

// Opening the socket to the Rizon network
$socket = fsockopen("irc.freenode.net", 6667);

// Send auth info
// fputs($socket, "PASS " . $password . "\n");
fputs($socket, "NICK " . $nickname . "\n");
fputs($socket, "USER " . $nickname . " 0 * :" . $master . "'s Bot\n");

// Join channel
foreach($channels as $channel) {
	fputs($socket, "JOIN " . $channel . "\n");
}
// Force an endless while
while (1) {
    // Continue the rest of the script here
    while ($data = fgets($socket, 128)) {
        echo $data;
        flush();
        
        // Separate all data
        $ex = explode(' ', $data);
        
        // Send PONG back to the server
        if ($ex[0] == "PING") {
            fputs($socket, "PONG " . $ex[1] . "\n");
        }
        
        // executes chat command
        if ($ex[0] != 'PING' && ISSET($ex[3])) {
            $command = str_replace(array(
                chr(10),
                chr(13)
            ), '', $ex[3]);
            if ($command == ":!alive?") {
                fputs($socket, "PRIVMSG " . $ex[2] . " :whazzup? \n");
            }
            if ($command == ":!time") {
                fputs($socket, "PRIVMSG " . $ex[2] . " :" . date(DATE_RFC2822) . " \n");
            }
            if ($command == ":!help") {
                fputs($socket, "PRIVMSG " . $ex[2] . " :Hambot phpIRCbot v0.1 commands. \n");
                fputs($socket, "PRIVMSG " . $ex[2] . " :!alive?, !time, !slave, !chucknorris, !meme !meat \n");
            }
            if ($command == ":!slave") {
                
                $parts = explode("!", $ex[0]);
                $user  = substr($parts['0'], 1);
                
                if ($user == $master)
                    fputs($socket, "PRIVMSG " . $ex[2] . " :Yes master! \n");
                else
                    fputs($socket, "PRIVMSG " . $ex[2] . " :get lost " . $user . " you filthy infidel! \n");
            }
            if ($command == ":!test") {
                fputs($socket, "PRIVMSG " . $ex[2] . " :value0 " . $ex[0] . ", value1 " . $ex[1] . ",value2 " . $ex[2] . ",value3 " . $command . "\n");
            }
            if ($command == ":!chucknorris") {
                $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random', true));
                fputs($socket, "PRIVMSG " . $ex[2] . " :" . $joke->value->joke . " \n");
            }
            if ($command == ":!meme") {
                $meme = file_get_contents('http://api.automeme.net/text?lines=1');
                fputs($socket, "PRIVMSG " . $ex[2] . " :" . $meme . " \n");
            }
            if ($command == ":!meat") {
                $meat = file_get_contents('http://baconipsum.com/api/?type=all-meat&sentences=1');
                $meat = explode(" ", $meat);
                $meat = substr($meat['0'], 2);
                fputs($socket, "PRIVMSG " . $ex[2] . " :" . $meat . " \n");
            }
        }
    }
}
?>
