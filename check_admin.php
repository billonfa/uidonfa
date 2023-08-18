<?php
    $admin = file_get_contents('https://api.telegram.org/bot6028695435:AAHvIOlH6R-nW0HI2RJcNmH3pBpgvfYyJog/getChatAdministrators?chat_id=-811627697');
    $admin = json_decode($admin)->result;
    // echo json_encode($admin);
    // die();
    foreach($admin as $key => $a) {
        echo ($a->user->id) . "\n";
        // die();
    }
?>