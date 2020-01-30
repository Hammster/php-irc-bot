<?php

// Prevent PHP from stopping the script after 30 sec
set_time_limit(0);

// Change these values!
$channel  = '#openra';
$nickname = 'HammsterBot';
// $password = 'secret';
$master   = 'Hammster';

// Opening the socket to the Rizon network
$socket = fsockopen("irc.freenode.net", 6667);

// Send auth info
// fputs($socket, "PASS " . $password . "\n");
fputs($socket, "USER " . $nickname . " 0 * :" . $master . "'s Bot\n");
fputs($socket, "NICK " . $nickname . "\n");

// Join channel
fputs($socket, "JOIN " . $channel . "\n");

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
                fputs($socket, "PRIVMSG " . $channel . " :whazzup? \n");
            }
            if ($command == ":!time") {
                fputs($socket, "PRIVMSG " . $channel . " :" . date(DATE_RFC2822) . " \n");
            }
            if ($command == ":!help") {
                fputs($socket, "PRIVMSG " . $channel . " :Hambot phpIRCbot v0.1 commands. \n");
                fputs($socket, "PRIVMSG " . $channel . " :!alive?, !time, !slave, !chucknorris, !meme !meat \n");
            }
            if ($command == ":!slave") {
                
                $parts = explode("!", $ex[0]);
                $user  = substr($parts['0'], 1);
                
                if ($user == $master)
                    fputs($socket, "PRIVMSG " . $channel . " :Yes master! \n");
                else
                    fputs($socket, "PRIVMSG " . $channel . " :get lost " . $user . " you filthy infidel! \n");
            }
            if ($command == ":!test") {
                fputs($socket, "PRIVMSG " . $channel . " :value0 " . $ex[0] . ", value1 " . $ex[1] . ",value2 " . $ex[2] . ",value3 " . $ex[3] . "\n");
            }
            if ($command == ":!chucknorris") {
                $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random', true));
                fputs($socket, "PRIVMSG " . $channel . " :" . $joke->value->joke . " \n");
            }
            if ($command == ":!meme") {
                $meme = file_get_contents('http://api.automeme.net/text?lines=1');
                fputs($socket, "PRIVMSG " . $channel . " :" . $meme . " \n");
            }
            if ($command == ":!meat") {
                $meat = file_get_contents('http://baconipsum.com/api/?type=all-meat&sentences=1');
                $meat = explode(" ", $meat);
                $meat = substr($meat['0'], 2);
                fputs($socket, "PRIVMSG " . $channel . " :" . $meat . " \n");
            }
        }
    }
}
?>
