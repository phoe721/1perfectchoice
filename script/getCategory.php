<?
/* Initialization */
require_once('functions.php');
require_once('class/queues.php');
$q = new queues;

// Put Request Into Queue
if(isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$input_file = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $input_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $input_file";
	$qid = $q->create_queue($command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$input_file = $argv[2];
	prepare($uid);

	log_status("Looking up match...");
	$file1 = fopen($input_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$line = fgets($file1);
			$line = trim($line);
			$type = get_type($line);
			$output = $line . "\t" . $type;
			log_result($output);
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
}

function get_type($str) {
	$type = "";
	$str = strtolower($str);
	$str = str_replace("set of", "", $str);
	if (preg_match('/\bset\b/', $str)) {
		if (preg_match('/(dining|counter height)/', $str)) {
			$type = "kitchen-and-dining-room-sets";
		} else if (preg_match('/patio dinning/', $str)) {
			$type = "patio-dining-sets";
		} else if (preg_match('/office/', $str)) {
			$type = "office-desks";
		} else if (preg_match('/tray table/', $str)) {
			$type = "lap-desks";
		} else if (preg_match('/chair/', $str)) {
			$type = "living-room-chairs";
		} else if (preg_match('/vanity/', $str)) {
			$type = "vanities";
		} else if (preg_match('/(occasional|coffee end|coffee|end|table)/', $str)) {
			$type = "living-room-table-sets";
		} else if (preg_match('/(sofa|loveseat|sectional)/', $str)) {
			$type = "sofas";
		}
	} else if (preg_match('/\b(table|tables)\b/', $str)) {
		if (preg_match('/(coffee|cocktail|tea)/', $str)) {
			$type = "coffee-tables";
		} else if (preg_match('/(nightstand|night stand)/', $str)) {
			$type = "nightstands";
		} else if (preg_match('/(end|side|stand|accent|magazine|snack)/', $str)) {
			$type = "end-tables";
		} else if (preg_match('/(lobby|console|sofa|corner|narrow|entry)/', $str)) {
			$type = "sofa-tables";
		} else if (preg_match('/(dining|counter height|gathering)/', $str)) {
			$type = "dining-tables";
		} else if (preg_match('/game/', $str)) {
			$type = "game-tables";
		} else if (preg_match('/tray/', $str)) {
			$type = "folding-tables";
		} else if (preg_match('/bar/', $str)) {
			$type = "bar-tables";
		} else if (preg_match('/nesting/', $str)) {
			$type = "nesting-tables";
		} else if (preg_match('/lamp/', $str)) {
			$type = "table-lamps";
		} else if (preg_match('/computer/', $str)) {
			$type = "computer-desks";
		} else if (preg_match('/vanity/', $str)) {
			$type = "vanities";
		}
	} else if (preg_match('/(sofa|loveseat|love seat|settee|sectional|wedge)/', $str)) {
		if (preg_match('/bed/', $str)) {
			$type = "sofas";
		} else {
			$type = "sofas";
		}
	} else if (preg_match('/\b(chair|chairs)\b/', $str)) {
		if (preg_match('/office/', $str)) {
			$type = "desk-chairs";
		} else if (preg_match('/rocking/', $str)) {
			$type = "living-room-chairs";
		} else if (preg_match('/accent/', $str)) {
			$type = "living-room-chairs";
		} else if (preg_match('/side/', $str)) {
			$type = "dining-chairs";
		} else if (preg_match('/dining/', $str)) {
			$type = "dining-chairs";
		} else {
			$type = "living-room-chairs";
		}
	} else if (preg_match('/\bbar\b/', $str)) {
		if (preg_match('/(small|stand)/', $str)) {
			$type = "bar-tables";
		} else if (preg_match('/towel/', $str)) {
			$type = "towel-bars";
		} else if (preg_match('/stool/', $str)) {
			$type = "barstools";
		}
	} else if (preg_match('/\btv\b/', $str)) {
		if (preg_match('/console/', $str)) {
			$type = "television-stands";
		} else if (preg_match('/stand/', $str)) {
			$type = "television-stands";
		} else if (preg_match('/unit/', $str)) {
			$type = "home-entertainment-centers";
		} else if (preg_match('/cabinet/', $str)) {
			$type = "home-entertainment-centers";
		}
	} else if (preg_match('/\bdesk\b/', $str)) {
		if (preg_match('/computer/', $str)) {
			$type = "computer-desks";
		} else if (preg_match('/(rectangular|office)/', $str)) {
			$type = "office-desks";
		} else if (preg_match('/writing/', $str)) {
			$type = "home-office-desks";
		} else {
			$type = "office-desks";
		}
	} else if (preg_match('/\bbench\b/', $str)) {
		if (preg_match('/storage/', $str)) {
			$type = "storage-benches";
		} else if (preg_match('/vanity/', $str)) {
			$type = "vanity-benches";
		} else {
			$type = "storage-benches";
		}
	} else if (preg_match('/\bcabinet\b/', $str)) {
		if (preg_match('/storage/', $str)) {
			$type = "storage-cabinets";
		} else if (preg_match('/wine/', $str)) {
			$type = "wine-cabinets";
		} else {
			$type = "storage-cabinets";
		}
	} else if (preg_match('/\bcart\b/', $str)) {
		if (preg_match('/kitchen/', $str)) {
			$type = "kitchen-islands-and-carts";
		} else if (preg_match('/serving/', $str)) {
			$type = "serving-carts";
		}
	} else if (preg_match('/\b(rack|racks)\b/', $str)) {
		if (preg_match('/coat/', $str)) {
			$type = "coat-stands";
		} else if (preg_match('/wine/', $str)) {
			$type = "wall-mounted-wine-racks";
		} else if (preg_match('/storage/', $str)) {
			$type = "general-purpose-storage-racks";
		} else if (preg_match('/baker/', $str)) {
			$type = "bakery-racks";
		} else if (preg_match('/shoe organizer/', $str)) {
			$type = "free-standing-shoe-racks";
		} else if (preg_match('/(shelf|shelves)/', $str)) {
			$type = "standing-shelf-units";
		}
	} else if (preg_match('/\blamp\b/', $str)) {
		if (preg_match('/floor/', $str)) {
			$type = "floor-lamps";
		} else if (preg_match('/wall/', $str)) {
			$type = "wall-sconces";
		}
	} else if (preg_match('/\b(shelf|shelves)\b/', $str)) {
		if (preg_match('/wine/', $str)) {
			$type = "wine-cabinets";
		} else if (preg_match('/antique/', $str)) {
			$type = "desktop-shelves";
		} else if (preg_match('/book/', $str)) {
			$type = "bookcases";
		} else {
			$type = "standing-shelf-units";
		}
	} else if (preg_match('/\bceiling\b/', $str)) {
		if (preg_match('/light/', $str)) {
			$type = "close-to-ceiling-light-fixtures";
		} else if (preg_match('/lamp/', $str)) {
	 		$type = "close-to-ceiling-light-fixtures";
		}
	} else if (preg_match('/\bmirror\b/', $str)) {
		if (preg_match('/frame/', $str)) {
			$type = "wall-mounted-mirrors";
		} else {
			$type = "wall-mounted-mirrors";
		}
	} else if (preg_match('/\bstand\b/', $str)) {
		if (preg_match('/flower/', $str)) {
			$type = "plant-stands";
		} else if (preg_match('/coat/', $str)) {
			$type = "coat-stands";
		} else if (preg_match('/night/', $str)) {
			$type = "nightstands";
		}
	} else if (preg_match('/\bhutch\b/', $str)) {
		if (preg_match('/computer/', $str)) {
			$type = "computer-hutches";
		} else {
			$type = "hutch-furniture-attachments";
		}
	} else if (preg_match('/\bholder\b/', $str)) {
		if (preg_match('/wine/', $str)) {
			$type = "tabletop-wine-racks";
		}
	} else if (preg_match('/btower\b/', $str)) {
		if (preg_match('/(media|TV)/', $str)) {
			$type = "audio-video-media-cabinets";
		}
	} else if (preg_match('/stool/', $str)) {
		$type = "barstools";
	} else if (preg_match('/nightstand/', $str)) {
		$type = "nightstands";
	} else if (preg_match('/\bseating\b/', $str)) {
		$type = "living-room-chairs";
	} else if (preg_match('/\bchaise\b/', $str)) {
		$type = "living-room-chaise-lounges";
	} else if (preg_match('/\bvanity\b/', $str)) {
		$type = "vanities";
	} else if (preg_match('/\brecliner\b/', $str)) {
		$type = "living-room-chairs";
	} else if (preg_match('/\bfuton\b/', $str)) {
		$type = "futon-mattresses";
	} else if (preg_match('/(media console | entertainment (center|unit) | entertainment wall unit)/', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/office suites/', $str)) {
		$type = "office-desks";
	} else if (preg_match('/\bheadboard\b/', $str)) {
		$type = "headboards";
	} else if (preg_match('/\bottoman\b/', $str)) {
		$type = "storage-ottomans";
	} else if (preg_match('/\bfireplace\b/', $str)) {
		$type = "ventless-fireplaces";
	} else if (preg_match('/\b(bed|trundle)\b/', $str)) {
		$type = "beds";
	} else if (preg_match('/\bcanopy\b/', $str)) {
		$type = "bed-frame-draperies";
	} else if (preg_match('/\barmoire\b/', $str)) {
		$type = "bedroom-armoires";
	} else if (preg_match('/\bchest\b/', $str)) {
		$type = "chests-of-drawers";
	} else if (preg_match('/\b(dressers|dresser|wardrobe)\b/', $str)) {
		$type = "dressers";
	} else if (preg_match('/\bdog house\b/', $str)) {
		$type = "dog-houses";
	} else if (preg_match('/(panel screen|room divider)/', $str)) {
		$type = "panel-screens";
	} else if (preg_match('/\btrunk\b/', $str)) {
		$type = "storage-cabinets";
	} else if (preg_match('/\bchandelier\b/', $str)) {
		$type = "chandeliers";
	} else if (preg_match('/\bside pier\b/', $str)) {
		$type = "standing-shelf-units";
	} else if (preg_match('/(sideboard|buffet)/', $str)) {
		$type = "sideboards";
	} else if (preg_match('/lateral file/', $str)) {
		$type = "lateral-file-cabinets";
	} else if (preg_match('/(head|sculpture)/', $str)) {
		$type = "wall-sculptures";
	} else if (preg_match('/\bmattress\b/', $str)) {
		$type = "mattresses";
	} else if (preg_match('/magazine holder/', $str)) {
		$type = "magazine-holders";
	} else if (preg_match('/\b(bookcase|bookshelf|book shelf)\b/', $str)) {
		$type = "bookcases";
	} else if (preg_match('/\bvase\b/', $str)) {
		$type = "vases";
	} else if (preg_match('/\beasel\b/', $str)) {
		$type = "artists-easels";
	} else if (preg_match('/\bframe\b/', $str)) {
		$type = "wall-and-table-top-frames";
	} else if (preg_match('/\bplate\b/', $str)) {
		$type = "dinner-plates";
	} else if (preg_match('/\bbowl\b/', $str)) {
		$type = "serving-bowls";
	} else if (preg_match('/\bcurtain\b/', $str)) {
		$type = "window-treatment-panels";
	} else if (preg_match('/\brug\b/', $str)) {
		$type = "area-rugs";
	} else if (preg_match('/\bplanter\b/', $str)) {
		$type = "standing-planters";
	} else if (preg_match('/bath mat/', $str)) {
		$type = "bathmats";
	} else if (preg_match('/(vetrine|glass argentiere)/', $str)) {
		$type = "curio-cabinets";
	} else if (preg_match('/kids organize closet/', $str)) {
		$type = "childrens-furniture";
	} else if (preg_match('/wall art/', $str)) {
		$type = "paintings";
	} else if (preg_match('/\bserver\b/', $str)) {
		$type = "sideboards";
	}

	return $type;
}
?>
