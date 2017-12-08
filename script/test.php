<?	
/* Initialization */
require_once("functions.php");

$url = "https://www.flatfair.com/4-pcs-outdoor-resin-frame-sofa-set-435-by-poundex.html";
$page = file_get_html($url);
if (isset($page)) {
	set_page($page);
	get_fields2();
	var_dump($product);
	echo "Finish Processing page: " . $url;
}

function get_fields2() {
	global $page, $product;
	if ($page->find('table.data', 0)) {
		$tmp = preg_replace('#<[^>]+>#', ' ', $page->find('table.data', 0)->innertext);
		$tmp = preg_replace('/\s\s+/', ' ', $tmp);
		$data = explode(' ', trim($tmp));
		var_dump($data);
		for ($i = 0; $i < count($data); $i++) {
			switch($data[$i]) {
				case 'MPN':
						$product['sku'] = str_pad($data[$i+1], 5, "0", STR_PAD_LEFT);
						break;
				case 'Brand':
						$product['vendor'] = '';
						for ($j = $i+1; $data[$j] != "Collection"; $j++) {
							$product['vendor'] .= $data[$j] . ' ';
						}
						$product['vendor'] = trim($product['vendor']);
						break;
				case 'Length':
						$product['length'] = $data[$i+1];
						break;
				case 'Width':
						$product['width'] = $data[$i+1];
						break;
				case 'Height':
						$product['height'] = $data[$i+1];
						break;
				case 'Material':
						$product['material'] = '';
						for ($j = $i+1; isset($data[$j]) && $data[$j] != "Finish" && $data[$j] != "Color" && $data[$j] != "Shipping"; $j++) {
							$product['material'] .= $data[$j] . ' ';
						}
						$product['material'] = trim($product['material']);
						break;
				case 'Finish':
						$product['color'] = $data[$i+1];
						break;
				case 'Color':
						$product['color'] = $data[$i+1];
						break;
				case 'Shipping':
						$product['freeShipping'] = $data[$i+1];
						if($product['freeShipping'] == 'Yes') {
							$product['shipping'] = 0;
						} else {
							$product['shipping'] = '';
						}
						break;
			}
		}
	}
}
?>
