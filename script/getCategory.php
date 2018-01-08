<?
/* Initialization */
require_once('functions.php');

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
	$qid = create_queue($uid, $command);

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
	if (preg_match('/sofa bed/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/coffee table/i', $str)) {
		$type = "coffee-tables";
	} else if (preg_match('/occasional set/i', $str)) {
		$type = "coffee-tables";
	} else if (preg_match('/coffee\/end set/i', $str)) {
		$type = "coffee-tables";
	} else if (preg_match('/end table/i', $str)) {
		$type = "end-tables";
	} else if (preg_match('/side table/i', $str)) {
		$type = "end-tables";
	} else if (preg_match('/stand table/i', $str)) {
		$type = "end-tables";
	} else if (preg_match('/lobby table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/console table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/sofa table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/corner table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/accent table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/narrow table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/game table/i', $str)) {
		$type = "game-tables";
	} else if (preg_match('/entry table/i', $str)) {
		$type = "sofa-tables";
	} else if (preg_match('/tray table/i', $str)) {
		$type = "folding-tables";
	} else if (preg_match('/sofa/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/loveseat/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/love seat/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/recliner/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/futon/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/settee/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/sectional/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/wedge/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/office chair/i', $str)) {
		$type = "adjustable-home-desk-chairs";
	} else if (preg_match('/rocking chair/i', $str)) {
		$type = "nursery-rocking-chairs";
	} else if (preg_match('/accent chair/i', $str)) {
		$type = "living-room-chairs";
	} else if (preg_match('/chaise/i', $str)) {
		$type = "living-room-chaise-lounges";
	} else if (preg_match('/chair/i', $str)) {
		$type = "sofas";
	} else if (preg_match('/bar table/i', $str)) {
		$type = "bar-tables";
	} else if (preg_match('/small bar/i', $str)) {
		$type = "bar-tables";
	} else if (preg_match('/table top/i', $str)) {
		$type = "bar-tables";
	} else if (preg_match('/stool/i', $str)) {
		$type = "barstools";
	} else if (preg_match('/tv console/i', $str)) {
		$type = "television-stands";
	} else if (preg_match('/tv stand/i', $str)) {
		$type = "television-stands";
	} else if (preg_match('/media tower/i', $str)) {
		$type = "audio-video-media-cabinets";
	} else if (preg_match('/tv console/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/tv unit/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/tv cabinet/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/entertainment center/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/entertainment unit/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/media console/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/entertainment wall unit/i', $str)) {
		$type = "home-entertainment-centers";
	} else if (preg_match('/computer desk/i', $str)) {
		$type = "computer-desks";
	} else if (preg_match('/computer .* desk/i', $str)) {
		$type = "computer-desks";
	} else if (preg_match('/rectangular desk/i', $str)) {
		$type = "office-desks";
	} else if (preg_match('/office desk/i', $str)) {
		$type = "office-desks";
	} else if (preg_match('/desk/i', $str)) {
		$type = "office-desks";
	} else if (preg_match('/office set/i', $str)) {
		$type = "office-desks";
	} else if (preg_match('/office suites/i', $str)) {
		$type = "office-desks";
	} else if (preg_match('/storage bench/i', $str)) {
		$type = "storage-benches";
	} else if (preg_match('/headboard/i', $str)) {
		$type = "headboards";
	} else if (preg_match('/ottoman/i', $str)) {
		$type = "storage-ottomans";
	} else if (preg_match('/fireplace/i', $str)) {
		$type = "ventless-fireplaces";
	} else if (preg_match('/dining table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/counter height table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/cocktail table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/gathering table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/tea table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/nesting table/i', $str)) {
		$type = "nesting-tables";
	} else if (preg_match('/table/i', $str)) {
		$type = "dining-tables";
	} else if (preg_match('/side chair/i', $str)) {
		$type = "dining-chairs";
	} else if (preg_match('/dining set/i', $str)) {
		$type = "kitchen-and-dining-room-sets";
	} else if (preg_match('/counter height set/i', $str)) {
		$type = "kitchen-and-dining-room-sets";
	} else if (preg_match('/bed/i', $str)) {
		$type = "beds";
	} else if (preg_match('/trundle/i', $str)) {
		$type = "beds";
	} else if (preg_match('/canopy/i', $str)) {
		$type = "bed-frame-draperies";
	} else if (preg_match('/armoire/i', $str)) {
		$type = "bedroom-armoires";
	} else if (preg_match('/chest/i', $str)) {
		$type = "chests-of-drawers";
	} else if (preg_match('/wardrobe/i', $str)) {
		$type = "dressers";
	} else if (preg_match('/dresser/i', $str)) {
		$type = "dressers";
	} else if (preg_match('/night stand/i', $str)) {
		$type = "nightstands";
	} else if (preg_match('/nightstand/i', $str)) {
		$type = "nightstands";
	} else if (preg_match('/bench/i', $str)) {
		$type = "vanity-benches";
	} else if (preg_match('/vanity bench/i', $str)) {
		$type = "vanity-benches";
	} else if (preg_match('/vanity/i', $str)) {
		$type = "vanities";
	} else if (preg_match('/dog house/i', $str)) {
		$type = "dog-houses";
	} else if (preg_match('/coat rack/i', $str)) {
		$type = "coat-stands";
	} else if (preg_match('/coat stand/i', $str)) {
		$type = "coat-stands";
	} else if (preg_match('/kitchen cart/i', $str)) {
		$type = "kitchen-islands-and-carts";
	} else if (preg_match('/serving cart/i', $str)) {
		$type = "serving-carts";
	} else if (preg_match('/panel screen/i', $str)) {
		$type = "panel-screens";
	} else if (preg_match('/wine rack/i', $str)) {
		$type = "wall-mounted-wine-racks";
	} else if (preg_match('/wine cabinet/i', $str)) {
		$type = "wine-cabinets";
	} else if (preg_match('/wine shelf/i', $str)) {
		$type = "wine-cabinets";
	} else if (preg_match('/trunk/i', $str)) {
		$type = "storage-cabinets";
	} else if (preg_match('/storage cabinet/i', $str)) {
		$type = "storage-cabinets";
	} else if (preg_match('/cabinet/i', $str)) {
		$type = "storage-cabinets";
	} else if (preg_match('/chandelier/i', $str)) {
		$type = "chandeliers";
	} else if (preg_match('/table lamp/i', $str)) {
		$type = "table-lamps";
	} else if (preg_match('/floor lamp/i', $str)) {
		$type = "floor-lamps";
	} else if (preg_match('/wall lamp/i', $str)) {
		$type = "wall-sconces";
	} else if (preg_match('/shelve/i', $str)) {
		$type = "standing-shelf-units";
	} else if (preg_match('/side pier/i', $str)) {
		$type = "standing-shelf-units";
	} else if (preg_match('/shelf rack/i', $str)) {
		$type = "standing-shelf-units";
	} else if (preg_match('/sideboard/i', $str)) {
		$type = "sideboards";
	} else if (preg_match('/buffet/i', $str)) {
		$type = "sideboards";
	} else if (preg_match('/lateral file/i', $str)) {
		$type = "lateral-file-cabinets";
	} else if (preg_match('/accents .* head/i', $str)) {
		$type = "wall-sculptures";
	} else if (preg_match('/sculpture/i', $str)) {
		$type = "wall-sculptures";
	} else if (preg_match('/mattress/i', $str)) {
		$type = "mattresses";
	} else if (preg_match('/ceiling .* light/i', $str)) {
		$type = "close-to-ceiling-light-fixtures";
	} else if (preg_match('/ceiling .* lamp/i', $str)) {
		$type = "close-to-ceiling-light-fixtures";
	} else if (preg_match('/frame mirror/i', $str)) {
		$type = "wall-mounted-mirrors";
	} else if (preg_match('/mirror/i', $str)) {
		$type = "wall-mounted-mirrors";
	} else if (preg_match('/magazine holder/i', $str)) {
		$type = "magazine-holders";
	} else if (preg_match('/bookcase/i', $str)) {
		$type = "bookcases";
	} else if (preg_match('/bookshelf/i', $str)) {
		$type = "bookcases";
	} else if (preg_match('/book shelf/i', $str)) {
		$type = "bookcases";
	} else if (preg_match('/computer hutch/i', $str)) {
		$type = "computer-hutches";
	} else if (preg_match('/hutch/i', $str)) {
		$type = "hutch-furniture-attachments";
	} else if (preg_match('/baker\'s rack/i', $str)) {
		$type = "bakery-racks";
	} else if (preg_match('/storage rack/i', $str)) {
		$type = "general-purpose-storage-racks";
	} else if (preg_match('/flower stand/i', $str)) {
		$type = "plant-stands";
	} else if (preg_match('/antique shelf/i', $str)) {
		$type = "desktop-shelves";
	} else if (preg_match('/vase/i', $str)) {
		$type = "vases";
	} else if (preg_match('/easel/i', $str)) {
		$type = "artists-easels";
	} else if (preg_match('/frame/i', $str)) {
		$type = "wall-and-table-top-frames";
	} else if (preg_match('/plate/i', $str)) {
		$type = "dinner-plates";
	} else if (preg_match('/bowl/i', $str)) {
		$type = "serving-bowls";
	} else if (preg_match('/pen/i', $str)) {
		$type = "ballpoint-pens";
	} else if (preg_match('/towel bar/i', $str)) {
		$type = "towel-bars";
	} else if (preg_match('/curtain/i', $str)) {
		$type = "window-treatment-panels";
	} else if (preg_match('/rug/i', $str)) {
		$type = "area-rugs";
	} else if (preg_match('/planter/i', $str)) {
		$type = "standing-planters";
	} else if (preg_match('/bath mat/i', $str)) {
		$type = "bathmats";
	} else if (preg_match('/patio dinning set/i', $str)) {
		$type = "patio-dining-sets";
	} else if (preg_match('/vetrine/i', $str)) {
		$type = "curio-cabinets";
	} else if (preg_match('/glass argentiere/i', $str)) {
		$type = "curio-cabinets";
	}

	return $type;
}
?>
