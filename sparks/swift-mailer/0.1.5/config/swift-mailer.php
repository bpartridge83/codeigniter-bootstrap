<?php
/*

@todo UPDATE THIS DOCUMENTATION!!!

Set the transport type.  Setting this option to a valid option
will engage 'abstraction mode'.  Once abstraction mode is engaged
the transport and message will be created automatically.

The transport will be created based on the configuration specific
to this transport type below.

The message created will have no recipients, senders, or message
content and will need to be set by the developer at run-time.

Currently, abstraction mode supports 'smtp', 'sendmail', and 'mail'.
Set the transport type to FALSE in order to disable abstraction mode.

I recommend that you do not use 'mail'. To back up my opinion here is
a quote from SwiftMailer documentation:

In my experience – and others’ – the mail() function is not particularly predictable, or helpful.
Quite notably, the mail() function behaves entirely differently between Linux and Windows servers. On linux it uses sendmail, but on Windows it uses SMTP.
In order for the mail() function to even work at all php.ini needs to be configured correctly, specifying the location of sendmail or of an SMTP server.
The problem with mail() is that it “tries” to simplify things to the point that it actually makes things more complex due to poor interface design. The developers of Swift Mailer have gone to a lot of effort to make the Mail Transport work with a reasonable degree of consistency.

Serious drawbacks when using this Transport are:

    Unpredictable message headers
    Lack of feedback regarding delivery failures
    Lack of support for several plugins that require real-time delivery feedback

It’s a last resort, and we say that with a passion!

*/
$config['swift_mailer_transport_type'] = FALSE;

// SMTP configuration - this is completely ignored unless the
// transport type is set to smtp.

$config['swift_mailer_smtp_email_address'] = 'from_email_address';
$config['swift_mailer_smtp_outgoing_server'] = 'smtp.server.com';
$config['swift_mailer_smtp_outgoing_server_port'] = 25;
$config['swift_mailer_smtp_username'] = 'smtp_username';
$config['swift_mailer_smtp_password'] = 'smtp_passwsord';

// starttls is also called ssl
$config['swift_mailer_smtp_use_starttls'] = FALSE;

// Sendmail configuration - this is completely ignored unless the
// transport type is set to sendmail

$config['swift_mailer_sendmail_path'] = '/usr/sbin/sendmail';
$config['swift_mailer_sendmail_parameters'] = '-bs';