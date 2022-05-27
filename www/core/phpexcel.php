<?
function GetListCell($sheet, $from, $to, $empty=false, $calc=false){
	list($fromC,$fromR) = SplitCell($from);
	list($toC,$toR) = SplitCell($to);
	
	$data = array();
	for($i=Col2Index($fromC);$i<=Col2Index($toC);$i++){
		for($j=$fromR;$j<=$toR;$j++){
			$n = Index2Col($i).$j;
			$c = $sheet->getCell($n);
			$v = $c->getValue();
			if($calc && substr($v,0,1)=='=') $v = $c->getCalculatedValue();
			//echo "Cell $c = $v<br>";
			
			if(!$empty && empty($v)) continue;
			$data[$n] = $v;
		}
	}
	
	return $data;
}

function Col2Index($name){
	$index = PHPExcel_Cell::columnIndexFromString($name);
	return $index;
}

function Index2Col($index){
	$col = PHPExcel_Cell::stringFromColumnIndex($index - 1);
	return $col;
}


function SplitCell($cell){
	$cell = str_replace('$','',$cell);
	return preg_split('/(?<=[a-zA-Z])(?=\d+)/',$cell);
}

function SheetToHTML($sheet, $calc=false) {
	echo '<table border="1" style="border-collapse:collapse">';
	foreach ($sheet->getRowIterator() as $row) {
		echo '<tr>';
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell) {
      $v = $cell->getValue();
      if($calc && substr($v,0,1)=='=') $v = $cell->getCalculatedValue();
      
			//$objv = $cell->getDataValidation();
			//$formula = $objv->getFormula1();
			//if(!empty($formula)) $v .= ' <--  <em>'.$formula.'</em>';
			
			if($v=='') $v = '&nbsp;';
			echo '<td>'.$v.'</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}
?>