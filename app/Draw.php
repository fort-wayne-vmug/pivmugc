<?php

class Draw {
	private $title = 'PiVMUGc Drawing';
	private $content = 'draw.htm';
	private $layout = 'layout-default.htm';

  function DefaultDisplay($f3) {

    $mapper = new DB\SQL\Mapper($f3->get('db'), 'admins');
		$auth = new Auth($mapper, array('id'=>'username','pw'=>'password'));

		// callback function because password is stored as an md5 hash.
		function chkauth($pw) {
		    return md5($pw);
		}

    if($auth->basic('chkauth')) {
        $f3->set('title',$this->title);

        if ($f3->get('SESSION.message_type') == 'success') {
            $f3->set('jsnoty','jsnoty-success.htm');
            $f3->set('jsnotymsg',$f3->get('SESSION.message'));
        }
        elseif ($f3->get('SESSION.message_type') == 'failure') {
            $f3->set('jsnoty','jsnoty-error.htm');
            $f3->set('jsnotymsg',$f3->get('SESSION.message'));
        }

        $f3->clear('SESSION.message_type');
        $f3->clear('SESSION.message');

        $f3->set('content',$this->content);
        echo \Template::instance()->render($this->layout);
      }

  }
	
	function RandomName($f3) {
	// get random checked in user information
	$result = $f3->get('db')->exec('SELECT * FROM guests WHERE timestamp NOT NULL AND drawing = "1" AND drawn = "0" ORDER BY Random()  LIMIT 1')[0];
	$f3->set('SESSION.message_type','success');
	$f3->set('SESSION.message',$result['firstname'].' '.$result['lastname'].' from: '.$result['company']);
	$f3->get('db')->exec(
		'UPDATE guests SET drawn = :drawn WHERE id = :id', array(
		  ':drawn'=> "1",
		  ':id'=> $result['id']
		 )
	);
	$f3->reroute('/draw');
	}

	function ResetDrawnFlag($f3) {
	// Set drawn to false for the checked in users with drawn set to true
	$f3->get('db')->exec('UPDATE guests SET drawn = "0" WHERE drawing = "1" AND drawn = "1"');
	$f3->set('SESSION.message_type','success');
	$f3->set('SESSION.message', 'Drawn Flags Reset');
	$f3->reroute('/draw');
	}

	function VendorDrawing($f3) {
	// get random name for Vendor drawing
	$result = $f3->get('bd')->exec('SELECT * FROM guests WHERE timestamp NOT NULL AND vendordrawing = "1" AND vendordrawn = "0" ORDER BY Random() LIMIT 1')[0];
	$f3->set('SESSION.message_type','success');
	$f3->set('SESSION.message',$result['firstname'].' '.$result['lastname'].' from: '.$result['company']);
	$f3->get('db')->exec(
		'UPDATE guests SET vendordrawn = :vendordrawn WHERE id = :id', array(
			':vendordrawn'=> "1",
			':id'=> $result['id']
		)
	);
	$f3->reroute('/draw');
	}
}
?>
