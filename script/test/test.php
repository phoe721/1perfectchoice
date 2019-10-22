<?
require_once("../init.php");
$names = array("AC-00452"=>"SMRX1163", "AC-19557EK"=>"SMRX1212", "AC-20953"=>"SMRX1094", "AC-21003"=>"SMRX1095", "AC-21124CK"=>"SMRX1254", "AC-21127EK"=>"SMRX1254", "AC-21130Q"=>"SMRX1187", "AC-21606EK"=>"SMRX1258", "AC-21683"=>"SMRX1099", "AC-22704"=>"SMRX1078", "AC-23367EK"=>"SMRX1269", "AC-23373"=>"SMRX1102", "AC-23793"=>"SMRX1105", "AC-23887EK"=>"SMRX1272", "AC-23897"=>"SMRX1107", "AC-23907EK"=>"SMRX1216", "AC-23923"=>"SMRX1108", "AC-23983"=>"SMRX1110", "AC-24590Q"=>"SMRX1204", "AC-24837EK"=>"SMRX1227", "AC-24840Q"=>"SMRX1205", "AC-25793"=>"SMRX1117", "AC-25837EK"=>"SMRX1230", "AC-25844CK"=>"SMRX1231", "AC-25847EK"=>"SMRX1231", "AC-25853"=>"SMRX1118", "AC-25900Q"=>"SMRX1176", "AC-25963"=>"SMRX1120", "AC-26044CK"=>"SMRX1233", "AC-26047EK"=>"SMRX1233", "AC-26093"=>"SMRX1121", "AC-26107EK"=>"SMRX1235", "AC-26113"=>"SMRX1122", "AC-26218"=>"SMRX1123", "AC-26243"=>"SMRX1124", "AC-26260Q"=>"SMRX1206", "AC-26263"=>"SMRX1125", "AC-26283"=>"SMRX1126", "AC-26770Q"=>"SMRX1208", "AC-26773"=>"SMRX1128", "AC-26935"=>"SMRX1130", "AC-27047EK"=>"SMRX1238", "AC-27067EK"=>"SMRX1239", "AC-30243"=>"SMRX1134", "AC-30323"=>"SMRX1135", "AC-30365"=>"SMRX1136", "AC-30538"=>"SMRX1143", "AC-30653"=>"SMRX1149", "AC-30698"=>"SMRX1152", "AC-30763"=>"SMRX1154", "AC-36093"=>"SMRX1155", "AC-81447"=>"SMRX1164", "AC-81850"=>"SMRX1157", "CO-100026"=>"SMRX1055", "CO-100889N"=>"SMRX1056", "CO-100958"=>"SMRX1073", "CO-102310"=>"SMRX1045", "CO-105252"=>"SMRX1057", "CO-1137"=>"SMRX1066", "CO-120341"=>"PPCP1701", "CO-120451"=>"PPCP1702", "CO-120767"=>"SMRX1051", "CO-130071"=>"SMRX1067", "CO-200423"=>"SMRX1068", "CO-200703"=>"SMRX1062", "CO-200711Q"=>"SMRX1069", "CO-460056W"=>"SMRX1059", "CO-460407"=>"SMRX1050", "CO-500948"=>"PPCP1934", "CO-500986"=>"PPCP2036", "CO-700497"=>"SMRX1071", "CO-704989"=>"SMRX1063", "CO-800513"=>"PPCP1659", "CO-801373"=>"SMRX1060", "CO-801549"=>"SMRX1072", "CO-900803"=>"PPCP2039", "CO-902098"=>"SMRX1054", "CO-902169"=>"SMRX1061", "CO-950171"=>"SMRX1065", "PDEX-F2200"=>"SMRX1043", "PDEX-F3054"=>"SMRX1042", "PDEX-F3087"=>"SMRX1041", "PDEX-F6505"=>"SMRX1040", "PDEX-F6542"=>"SMRX1039", "PDEX-F6891"=>"SMRX1038", "PDEX-F6934"=>"SMRX1037", "PDEX-F6939"=>"SMRX1036", "PDEX-F6989"=>"SMRX1035", "PDEX-F7320"=>"SMRX1034", "PDEX-F9231T"=>"SMRX1033", "PDEX-F9375T"=>"SMRX1032");


$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(IMG));
$it->rewind();
while ($it->valid()) {
	if (!$it->isDot()) {
		$filePath = $it->key();
		$fileName = $it->getSubPathName();
		if (preg_match('/.*\.jpg/', $fileName)) {	
			$name = basename($fileName, ".jpg");	
			$newName = $names[$name] . ".jpg";
			echo "File name: $fileName\n";
			echo "New name: $newName\n";
			$newPath = dirname($filePath) . "/" . $newName;
			echo "New Path: $newPath\n";
			if (rename($filePath, $newPath)) {
				echo "File renamed OK!\n";
			} else {
				echo "File renamed fail!\n";
			}
		}
	}
	$it->next();
}
?>
