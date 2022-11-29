<?php
/*
TEMPLATE NAMES:
	Level 3 Cert Template
	Results Notice Template
	Training Cert Template
*/
use setasign\Fpdi\Fpdi;

require_once('fpdf181/fpdf.php'); 
require_once('fpdi2/src/autoload.php');
add_action('crme_fpdf_joininginstructions', 'crmeb_fpdf_joininginstructions');

Function crmeb_fpdf_joininginstructions(){
	Global $wpdb;
	extract($_REQUEST);
	echo crmeb_fpdf_joininginstructionspdf($OrderIdfr, $Output);
}

Function crmeb_fpdf_joininginstructionspdf($OrderIdfr, $Output){
	Global $wpdb;
	extract($_REQUEST);
	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/ISA-QF-00-15 Student Advance Information Template.pdf'); 
	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1)); 	
	
	$Order = New Order();
	$Order->Get($OrderIdfr);
	
	$Student = New Student();
	$Student->Get($Order->StudentIdfr);
	
	$Company = New Company();
	$Company->Get($Student->CompanyIdfr);
	
	$Attendance = New Attendance();
	$Attendance->GetbyOrder($OrderIdfr);
	
	$Event = New Event();
	$Event->Get($Attendance->EventIdfr);
	
	$Course = New Course();
	$Course->Get($Event->CourseIdfr);

	$Method = New Method();
	$Method->Get($Course->MethodIdfr);
	
	
	//Student Name
	$StudentName = $Student->FirstName.' '.$Student->LastName;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(35,89.5);
	$pdf->Cell(25,10,$StudentName,0,0,'L');

	IF (is_null($Attendance->EventIdfr)){
		$TrainingType = 'Exam Only';
	}else{
		$TrainingType = 'Training & Exam';
	}

	
	//Dates of Attendance
	$Dates = date("d M",strtotime($Event->StartDate)).' to '.date("d M Y",strtotime($Event->EndDate));
	
	//Qualification System
	$QualificationSystem = "EN4179";
	
	//Method
	$MethodDescription = $Method->Method;

	//NDT Level
	$Level = $Attendance->Level;

	$CourseDetails = "$QualificationSystem - $Level:$MethodDescription - $TrainingType - $Dates.";

	$pdf->SetFont('Arial','B','10');
	$pdf->SetXY(23,98);
	$pdf->Cell(25,10,$CourseDetails,0,0,'L');

$DocumentName = 'Joininginstructions';
switch($Output){
	case 'view' :
		$pdf->Output('I',"$DocumentName.pdf");
		break;
	case 'string' :
		$PDFString=$pdf->Output('S');
		return $PDFString;
		break;
	default:
		$pdf->Output("/nas/content/live/isandt/wp-content/uploads/PDF/$DocumentName.pdf", 'F');
		$Date = date("Y/m/d h:i:s");
		$html =  "<h1>$DocumentName</h1>";
		$html .= '<a href="https://isandt.crmengine.co.uk//wp-content/uploads/PDF/'.$DocumentName.'.pdf?'.$Date.'" target="pdf">View Document</a><br>';
		return $html;
		break;
}



}

