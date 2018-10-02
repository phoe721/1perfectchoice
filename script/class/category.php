<?
/* Initialization */
require_once("debugger.php");

class category {
	private $output;
	private $category;

	public function __construct() {
		$this->output = new debugger;
	}

	public function get_category($str) {
		$str = strtolower($str);
		$str = str_replace("set of", "", $str);
		if (preg_match('/\bset\b/', $str)) {
			if (preg_match('/(dining|counter height)/', $str)) {
				$this->category = "kitchen-and-dining-room-sets";
			} else if (preg_match('/patio dinning/', $str)) {
				$this->category = "patio-dining-sets";
			} else if (preg_match('/office/', $str)) {
				$this->category = "office-desks";
			} else if (preg_match('/tray table/', $str)) {
				$this->category = "lap-desks";
			} else if (preg_match('/chair/', $str)) {
				$this->category = "living-room-chairs";
			} else if (preg_match('/vanity/', $str)) {
				$this->category = "vanities";
			} else if (preg_match('/(occasional|coffee end|coffee|end|table)/', $str)) {
				$this->category = "living-room-table-sets";
			} else if (preg_match('/(sofa|loveseat|sectional)/', $str)) {
				$this->category = "sofas";
			}
		} else if (preg_match('/\b(table|tables)\b/', $str)) {
			if (preg_match('/(coffee|cocktail|tea)/', $str)) {
				$this->category = "coffee-tables";
			} else if (preg_match('/(nightstand|night stand)/', $str)) {
				$this->category = "nightstands";
			} else if (preg_match('/(end|side|stand|accent|magazine|snack)/', $str)) {
				$this->category = "end-tables";
			} else if (preg_match('/(lobby|console|sofa|corner|narrow|entry)/', $str)) {
				$this->category = "sofa-tables";
			} else if (preg_match('/(dining|counter height|gathering)/', $str)) {
				$this->category = "dining-tables";
			} else if (preg_match('/game/', $str)) {
				$this->category = "game-tables";
			} else if (preg_match('/tray/', $str)) {
				$this->category = "folding-tables";
			} else if (preg_match('/bar/', $str)) {
				$this->category = "bar-tables";
			} else if (preg_match('/nesting/', $str)) {
				$this->category = "nesting-tables";
			} else if (preg_match('/lamp/', $str)) {
				$this->category = "table-lamps";
			} else if (preg_match('/computer/', $str)) {
				$this->category = "computer-desks";
			} else if (preg_match('/vanity/', $str)) {
				$this->category = "vanities";
			}
		} else if (preg_match('/(sofa|loveseat|love seat|settee|sectional|wedge)/', $str)) {
			if (preg_match('/bed/', $str)) {
				$this->category = "sofas";
			} else {
				$this->category = "sofas";
			}
		} else if (preg_match('/\b(chair|chairs)\b/', $str)) {
			if (preg_match('/office/', $str)) {
				$this->category = "desk-chairs";
			} else if (preg_match('/rocking/', $str)) {
				$this->category = "living-room-chairs";
			} else if (preg_match('/accent/', $str)) {
				$this->category = "living-room-chairs";
			} else if (preg_match('/side/', $str)) {
				$this->category = "dining-chairs";
			} else if (preg_match('/dining/', $str)) {
				$this->category = "dining-chairs";
			} else {
				$this->category = "living-room-chairs";
			}
		} else if (preg_match('/\bbar\b/', $str)) {
			if (preg_match('/(small|stand)/', $str)) {
				$this->category = "bar-tables";
			} else if (preg_match('/towel/', $str)) {
				$this->category = "towel-bars";
			} else if (preg_match('/stool/', $str)) {
				$this->category = "barstools";
			}
		} else if (preg_match('/\btv\b/', $str)) {
			if (preg_match('/console/', $str)) {
				$this->category = "television-stands";
			} else if (preg_match('/stand/', $str)) {
				$this->category = "television-stands";
			} else if (preg_match('/unit/', $str)) {
				$this->category = "home-entertainment-centers";
			} else if (preg_match('/cabinet/', $str)) {
				$this->category = "home-entertainment-centers";
			}
		} else if (preg_match('/\bdesk\b/', $str)) {
			if (preg_match('/computer/', $str)) {
				$this->category = "computer-desks";
			} else if (preg_match('/(rectangular|office)/', $str)) {
				$this->category = "office-desks";
			} else if (preg_match('/writing/', $str)) {
				$this->category = "home-office-desks";
			} else {
				$this->category = "office-desks";
			}
		} else if (preg_match('/\bbench\b/', $str)) {
			if (preg_match('/storage/', $str)) {
				$this->category = "storage-benches";
			} else if (preg_match('/vanity/', $str)) {
				$this->category = "vanity-benches";
			} else {
				$this->category = "storage-benches";
			}
		} else if (preg_match('/\bcabinet\b/', $str)) {
			if (preg_match('/storage/', $str)) {
				$this->category = "storage-cabinets";
			} else if (preg_match('/wine/', $str)) {
				$this->category = "wine-cabinets";
			} else {
				$this->category = "storage-cabinets";
			}
		} else if (preg_match('/\bcart\b/', $str)) {
			if (preg_match('/kitchen/', $str)) {
				$this->category = "kitchen-islands-and-carts";
			} else if (preg_match('/serving/', $str)) {
				$this->category = "serving-carts";
			}
		} else if (preg_match('/\b(rack|racks)\b/', $str)) {
			if (preg_match('/coat/', $str)) {
				$this->category = "coat-stands";
			} else if (preg_match('/wine/', $str)) {
				$this->category = "wall-mounted-wine-racks";
			} else if (preg_match('/storage/', $str)) {
				$this->category = "general-purpose-storage-racks";
			} else if (preg_match('/baker/', $str)) {
				$this->category = "bakery-racks";
			} else if (preg_match('/shoe organizer/', $str)) {
				$this->category = "free-standing-shoe-racks";
			} else if (preg_match('/(shelf|shelves)/', $str)) {
				$this->category = "standing-shelf-units";
			}
		} else if (preg_match('/\blamp\b/', $str)) {
			if (preg_match('/floor/', $str)) {
				$this->category = "floor-lamps";
			} else if (preg_match('/wall/', $str)) {
				$this->category = "wall-sconces";
			}
		} else if (preg_match('/\b(shelf|shelves)\b/', $str)) {
			if (preg_match('/wine/', $str)) {
				$this->category = "wine-cabinets";
			} else if (preg_match('/antique/', $str)) {
				$this->category = "desktop-shelves";
			} else if (preg_match('/book/', $str)) {
				$this->category = "bookcases";
			} else {
				$this->category = "standing-shelf-units";
			}
		} else if (preg_match('/\bceiling\b/', $str)) {
			if (preg_match('/light/', $str)) {
				$this->category = "close-to-ceiling-light-fixtures";
			} else if (preg_match('/lamp/', $str)) {
		 		$this->category = "close-to-ceiling-light-fixtures";
			}
		} else if (preg_match('/\bmirror\b/', $str)) {
			if (preg_match('/frame/', $str)) {
				$this->category = "wall-mounted-mirrors";
			} else {
				$this->category = "wall-mounted-mirrors";
			}
		} else if (preg_match('/\bstand\b/', $str)) {
			if (preg_match('/flower/', $str)) {
				$this->category = "plant-stands";
			} else if (preg_match('/coat/', $str)) {
				$this->category = "coat-stands";
			} else if (preg_match('/night/', $str)) {
				$this->category = "nightstands";
			}
		} else if (preg_match('/\bhutch\b/', $str)) {
			if (preg_match('/computer/', $str)) {
				$this->category = "computer-hutches";
			} else {
				$this->category = "hutch-furniture-attachments";
			}
		} else if (preg_match('/\bholder\b/', $str)) {
			if (preg_match('/wine/', $str)) {
				$this->category = "tabletop-wine-racks";
			}
		} else if (preg_match('/btower\b/', $str)) {
			if (preg_match('/(media|TV)/', $str)) {
				$this->category = "audio-video-media-cabinets";
			}
		} else if (preg_match('/stool/', $str)) {
			$this->category = "barstools";
		} else if (preg_match('/nightstand/', $str)) {
			$this->category = "nightstands";
		} else if (preg_match('/\bseating\b/', $str)) {
			$this->category = "living-room-chairs";
		} else if (preg_match('/\bchaise\b/', $str)) {
			$this->category = "living-room-chaise-lounges";
		} else if (preg_match('/\bvanity\b/', $str)) {
			$this->category = "vanities";
		} else if (preg_match('/\brecliner\b/', $str)) {
			$this->category = "living-room-chairs";
		} else if (preg_match('/\bfuton\b/', $str)) {
			$this->category = "futon-mattresses";
		} else if (preg_match('/(media console | entertainment (center|unit) | entertainment wall unit)/', $str)) {
			$this->category = "home-entertainment-centers";
		} else if (preg_match('/office suites/', $str)) {
			$this->category = "office-desks";
		} else if (preg_match('/\bheadboard\b/', $str)) {
			$this->category = "headboards";
		} else if (preg_match('/\bottoman\b/', $str)) {
			$this->category = "storage-ottomans";
		} else if (preg_match('/\bfireplace\b/', $str)) {
			$this->category = "ventless-fireplaces";
		} else if (preg_match('/\b(bed|trundle)\b/', $str)) {
			$this->category = "beds";
		} else if (preg_match('/\bcanopy\b/', $str)) {
			$this->category = "bed-frame-draperies";
		} else if (preg_match('/\barmoire\b/', $str)) {
			$this->category = "bedroom-armoires";
		} else if (preg_match('/\bchest\b/', $str)) {
			$this->category = "chests-of-drawers";
		} else if (preg_match('/\b(dressers|dresser|wardrobe)\b/', $str)) {
			$this->category = "dressers";
		} else if (preg_match('/\bdog house\b/', $str)) {
			$this->category = "dog-houses";
		} else if (preg_match('/(panel screen|room divider)/', $str)) {
			$this->category = "panel-screens";
		} else if (preg_match('/\btrunk\b/', $str)) {
			$this->category = "storage-cabinets";
		} else if (preg_match('/\bchandelier\b/', $str)) {
			$this->category = "chandeliers";
		} else if (preg_match('/\bside pier\b/', $str)) {
			$this->category = "standing-shelf-units";
		} else if (preg_match('/(sideboard|buffet)/', $str)) {
			$this->category = "sideboards";
		} else if (preg_match('/lateral file/', $str)) {
			$this->category = "lateral-file-cabinets";
		} else if (preg_match('/(head|sculpture)/', $str)) {
			$this->category = "wall-sculptures";
		} else if (preg_match('/\bmattress\b/', $str)) {
			$this->category = "mattresses";
		} else if (preg_match('/magazine holder/', $str)) {
			$this->category = "magazine-holders";
		} else if (preg_match('/\b(bookcase|bookshelf|book shelf)\b/', $str)) {
			$this->category = "bookcases";
		} else if (preg_match('/\bvase\b/', $str)) {
			$this->category = "vases";
		} else if (preg_match('/\beasel\b/', $str)) {
			$this->category = "artists-easels";
		} else if (preg_match('/\bframe\b/', $str)) {
			$this->category = "wall-and-table-top-frames";
		} else if (preg_match('/\bplate\b/', $str)) {
			$this->category = "dinner-plates";
		} else if (preg_match('/\bbowl\b/', $str)) {
			$this->category = "serving-bowls";
		} else if (preg_match('/\bcurtain\b/', $str)) {
			$this->category = "window-treatment-panels";
		} else if (preg_match('/\brug\b/', $str)) {
			$this->category = "area-rugs";
		} else if (preg_match('/\bplanter\b/', $str)) {
			$this->category = "standing-planters";
		} else if (preg_match('/bath mat/', $str)) {
			$this->category = "bathmats";
		} else if (preg_match('/(vetrine|glass argentiere)/', $str)) {
			$this->category = "curio-cabinets";
		} else if (preg_match('/kids organize closet/', $str)) {
			$this->category = "childrens-furniture";
		} else if (preg_match('/wall art/', $str)) {
			$this->category = "paintings";
		} else if (preg_match('/\bserver\b/', $str)) {
			$this->category = "sideboards";
		}
		
		$this->output->notice("Category: $this->category for $str!");	
		return $this->category;
	}
}
?>
