<?
function grab_vendor_data() {
	global $img_dir, $product;
	$query = get_vendor_query_url();
	foreach($product['skus'] as $index => $sku) {
		$queryURL = $query . $sku; 
		$product['vendorImage'][$index] = $product['vendorCode'] . '-' . $sku . '-2.jpg';
		$filename = $img_dir . $product['vendorImage'][$index]; 
		if (!file_exists($filename)) {
			$page = file_get_html($queryURL);
			if (!($page->find('p.note-msg', 0))) {
				// Get product dimension
				$product['dimension'][$index] = '';
				$dimension = $page->find('div.pull-left p', 0)->plaintext;
				if (isset($dimension)) {
					$dimension = trim(preg_replace('/Dimensions \:/', '', $dimension));
					$dimension = trim(preg_replace('/N\/A/', '', $dimension));
					$product['dimension'][$index] = $dimension;
				}
				
				// Get product weight	
				$product['weight'][$index] = '';
				$package = $page->find('div.pull-left p', 1)->plaintext;
				if (isset($package)) {
					$package = trim(strip_tags(preg_replace('/Package \:/', '', $package)));
					$pieces = explode('/', $package);
					if (isset($pieces[3])) {
						$weight = preg_replace('/LBS/i', '', $pieces[3]);
						$product['weight'][$index] = $weight;
					}
				}
				
				// Grab image
				$link = $page->find('div.description-detail h1 a', 0)->href;
				$page2 = file_get_html($link);
				if (isset($page2)) {
					if ($page2->find('#zoom1', 0)) {
						$imgURL = $page2->find('#zoom1', 0)->href;
						$result = download($imgURL, $filename);
					}
				}
			}
		}
	}

	// Get vendor image names
	if (isset($product['vendorImage'])) {
		$product['vendorImages'] = implode("\t", $product['vendorImage']); 
		unset($product['vendorImage']);
	}

	// Get dimensions
	if (isset($product['dimension'])) {
		$product['dimensions'] = implode("\t", $product['dimension']);
		unset($product['dimension']);
	}

	// Get weights
	if (isset($product['weight'])) {
		$product['weights'] = implode("\t", $product['weight']);
		unset($product['weight']);
	}
}
?>
