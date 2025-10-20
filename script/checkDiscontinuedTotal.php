<?
require_once("class/Debugger.php");
require_once("class/manufacturing_country.php");
require_once("class/validator.php");
require_once("class/helper_functions.php");
$output = new debugger();
$manufacturing_country = new manufacturing_country();
$validator = new validator();
$remote_dir = "Z:/Inventory/2025/Discontinued/";
$China = $Taiwan = $Crotia = $India = $Indonesia = $Italy = $Malaysia = $Thailand = $USA = $notFound = 0;
$ACME = $Coaster = $FOA = $Poundex = $Lilola = $SR = 0;
$countryTotal = $vendorTotal = 0;

if (is_dir($remote_dir)) {
	$output->info("Loop through each file in $remote_dir...");
	$dir = array_diff(scandir($remote_dir), array('..', '.'));
	foreach ($dir as $key => $file) {
		if(is_dir("$file")) {
			$output->info("$file is a drectory, do nothing...");
		} else {
			$output->info("$file is an inventory file...");
			$remote_file = $remote_dir . $file;
			$input = fopen($remote_file, "r");
			if ($input) {
				while(!feof($input)) {
					$sku = trim(fgets($input));
					if (!empty($sku)) {
						if ($validator->check_sku($sku)) {
							$sku = clean_up($sku);
							list($code, $item_no) = array_pad(explode("-", $sku, 2), 2, null);
							if (preg_match('/Wayfair|Beyond|Amazon Local|Amazon VC|Houzz/', $code)) {
								// Platform name, skip
							} else {
								$origin = $manufacturing_country->get_origin($code, $item_no);
								$result = "$sku\t$origin";
								switch ($origin) {
									case "Taiwan":
										$Taiwan++;
										break;
									case "China":
										$China++;
										break;
									case "Malaysia":
										$Malaysia++;
										break;
									case "India":
										$India++;
										break;
									case "Indonesia":
										$Indonesia++;
										break;
									case "Italy":
										$Italy++;
										break;
									case "Crotia":
										$Crotia++;
										break;
									case "Thailand":
										$Thailand++;
										break;
									case "United States":
										$USA++;
										break;
									case "":
										$notFound++;
										break;
								}
								switch ($code) {
									case "AC":
										$ACME++;
										break;
									case "CO":
										$Coaster++;
										break;
									case "FA":
										$FOA++;
										break;
									case "PDEX":
										$Poundex++;
										break;
									case "LHF":
										$Lilola++;
										break;	
									case "SR":
										$SR++;
										break;
								}
							}
						} else {
							$result = "$sku\tInvalid";
						}
						$output->info("$result");
					}
				}
			}

			fclose($input);			
		}		
	}
	$countryTotal = $China + $Crotia + $India + $Indonesia + $Italy + $Malaysia + $Taiwan + $Thailand + $USA;
	$vendorTotal = $ACME + $Coaster + $FOA + $Poundex + $Lilola;
	$output->info("China: $China, Crotia: $Crotia, India: $India, Indonesia: $Indonesia, Italy: $Italy, Malaysia: $Malaysia, Taiwan: $Taiwan, Thailand: $Thailand, USA: $USA, Not Found: $notFound, Total: $countryTotal");
	$output->info("ACME: $ACME, Coaster: $Coaster, FOA: $FOA, Poundex: $Poundex, Lilola: $Lilola, SR: $SR, Total: $vendorTotal");
} else {
	$output->error("$remote_dir is not a directory!");
}
?>
