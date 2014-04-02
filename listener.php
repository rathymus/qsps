/**
 * Suitable to run from a cron-job, this file regularly
 * polls a mailbox for incoming items.
 * Using the ImapMailbox project from https://github.com/barbushin/php-imap/
 */

include_once "ImapMailbox.php";

define('USER_KEYSTORE', '/users/'); //! User certificates folder
define('USER_KEYSTORE_SALT', 'n3w7QZDBkaETPqDP97CNTLjH');
define('SIG_FILE_NAME', "signature.asc");
define('ATTACHMENTS_DIR', "/attachments/"); //! Local(?) attachment download dir

define('QSPS_IMAP_SERVER', ''); //! eg. imap.gmail.com
define('QSPS_IMAP_PORT', '993');
define('QSPS_IMAP_USER', '');
define('QSPS_IMAP_PASS', '');
define('QSPS_IMAP_ENC', 'utf-8');
define('QSPS_IMAP_MBOX', 'INBOX');



function qsps_listen(){
	/*
	  Ideally there should be a dedicated address, ie. qsps@example.com
	  but you can get away with using a dedicated mailbox
	*/

	$query ="{".QSPS_IMAP_SERVER.":".QSPS_IMAP_PORT."}".QSPS_IMAP_MBOX;
	$mbox  =new ImapMalbox($query,
						   QSPS_IMAP_USER,
						   QSPS_IMAP_PASS,
						   ATTACHMENTS_DR,
						   QSPS_IMAP_ENC);

	$msgs  =$mbox->searchMailBox("UNSEEN"); //Search in unread messages

	if(count($msgs)==0)
		return NULL; //no new emails

	$ret=qsps_verify_mails($msgs);

	//Mark the rest of the emails as "read"
	$mbox->markMailsAsRead($ret);
}


/**
 * Validity checks on each unread mail.
 * Mails which failed the test are removed
 * from the return array.
 * @param $m Array containing email IDs
 * @param $att Attachments folder
 * @return array of mail IDs which passed the test
 */
function qsps_verify_mails($m){
	$c=count($m);
	$ret=array();
	for($i=0; $i<$c; $i++){
		if(!in_array(SIG_FILE_NAME, $m->getAttachments()))
			continue;
		$sig=@file_get_contents($att.SIG_FILE_NAME);
		if($sig===NULL)
			die("Sig not found in ".ATTACHMENTS_DIR.SIG_FILE_NAME);

		$dt=$m[$i]->textPlain;

		//get user key. Cert finelame is hash(salt+user email)
		$pkey=openssl_pkey_get_public(
			USER_KEYSTORE.sha1(USER_KEYSTORE_SALT.$m->fromAddress)."crt");

		if($pkey===NULL)
			die("No such user ".$m->fromAddress);

		/*!
		  QSPS uses sign-then-encrypt so the signature actually
		  validates to the encrypted text. Presently the file gets
		  decrypted twice, once here and once in processing. A
		  more civilized way is needed.
		*/

		//TODO: Handle encryption

		//moment of truth
		if(openssl_verify($dt, $sig, $pkey)==1)
			array_push($ret, $m[$i]);
	}
	return $ret;
}