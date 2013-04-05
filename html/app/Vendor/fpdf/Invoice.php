<?php
	class Invoice extends FPDF{
		// Current column
		var $col = 0;
		// Ordinate of column start
		var $y0;
		function SetCol($col){
			// Set position at a given column
			$this->col = $col;
			$x = 10+$col*65;
			$this->SetLeftMargin($x);
			$this->SetX($x);
		}
		
		function PrintLineCells($cells = array()){
			$this->Ln();
			for($i = 0; $i < count($cells); $i++){
				$this->SetCol($i);
				$this->Cell(0,5,$cells[$i]);
			}
		}
		
		function Table($header, $data, $subtotal, $total){
			// Colors, line width and bold font
			$this->SetFillColor(100,100,100);
			$this->SetTextColor(0);
			$this->SetDrawColor(0,0,0);
			$this->SetLineWidth(.3);
			$this->SetFont('','B');
			// Header
			$w = array(20, 15, 40, 45,40);
			for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
			$this->Ln();
			// Color and font restoration
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
			$this->SetFont('');
			// Data
			$fill = false;
			foreach($data as $row)
			{
				$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
				$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
				$this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
				$this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
				$this->Cell($w[4],6,$row[4],'LR',0,'R',$fill);
				$this->Ln();
				$fill = !$fill;
			}
			
			// Closing line
				$this->Cell($w[0],6,'','LRT',0,'L',$fill);
				$this->Cell($w[1],6,'','LRT',0,'L',$fill);
				$this->Cell($w[2],6,'','LRT',0,'R',$fill);
				$this->Cell($w[3],6,'Total: ','LRT',0,'L',$fill);
				$this->Cell($w[4],6,$total,'LRT',0,'R',$fill);
				$this->Ln();
				$fill = !$fill;
				
			$this->Cell(array_sum($w),0,'','T');
		}
	}
	?>	