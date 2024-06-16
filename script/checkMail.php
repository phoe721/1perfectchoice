<?php

// Define email server connection parameters
$hostname = '{imap.example.com:993/imap/ssl}INBOX'; // Update with your IMAP server details
$username = 'your_email@example.com'; // Update with your email address
$password = 'your_password'; // Update with your email password

// Connect to the mailbox
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to the mail server: ' . imap_last_error());

// Search emails in the mailbox
$emails = imap_search($inbox, 'ALL');

if ($emails) {
    // Sort emails in descending order
    rsort($emails);

    // Loop through each email
    foreach ($emails as $email_number) {
        // Fetch the email overview
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        // Fetch the email structure
        $structure = imap_fetchstructure($inbox, $email_number);

        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $part = $structure->parts[$i];
                if ($part->ifdparameters) {
                    foreach ($part->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $filename = $object->value;
                            $message = imap_fetchbody($inbox, $email_number, $i+1);
                            // Decode the message if needed
                            if ($part->encoding == 3) { // 3 = BASE64
                                $message = base64_decode($message);
                            } elseif ($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                $message = quoted_printable_decode($message);
                            }
                            // Save the attachment
                            $fp = fopen($filename, 'w+');
                            fwrite($fp, $message);
                            fclose($fp);
                        }
                    }
                }
            }
        }
    }
} else {
    echo 'No emails found';
}

// Close the connection
imap_close($inbox);
?>
