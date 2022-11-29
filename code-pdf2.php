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
add_action('crme_fpdf_customerorderacknowledgement', 'crmeb_fpdf_customerorderacknowledgement');

Function crmeb_fpdf_customerorderacknowledgement(){
	Global $wpdb;
	extract($_REQUEST);
	echo crmeb_fpdf_customerorderacknowledgementspdf($OrderIdfr, $Output);
}

Function crmeb_fpdf_customerorderacknowledgementspdf($OrderIdfr, $Output){
	Global $wpdb;
	extract($_REQUEST);
	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/ISA-QF-13-02 Customer Acknowledgement Issue 19.pdf'); 
	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1)); 	

	$Order = New Order();
	$Order->Get($OrderIdfr);

	$Attendance = New Attendance();
	$Attendance->GetbyOrder($OrderIdfr);

	$Event = New Event();
	$Event->Get($Attendance->EventIdfr);

	$Course = New Course();
	$Course->Get($Event->CourseIdfr);

	$Method = New Method();
	$Method->Get($Course->MethodIdfr);

	$Student = New Student();
	$Student->Get($Order->StudentIdfr);

	$Company = New Company();
	$Company->Get($Student->CompanyIdfr);

	$sqlcount = "SELECT COUNT(*) FROM tblQualification WHERE OrderIdfr = $OrderIdfr";

	$QualificationCount = $wpdb->get_var($sqlcount);
	
	//student name
	$StudentName = $Student->FirstName.' '.$Student->LastName;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(65,25);
	$pdf->Cell(25,10,$StudentName,0,0,'L');
	
	//Company Name
	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(65,43);
	$pdf->Cell(25,10,$Company->CompanyName,0,0,'L');
	
	//Employer address
	$Address = '';
	if (!empty($Company->Address1)){
		$Address.=$Company->Address1.', ';

	}
	if (!empty($Company->Address2)){
		$Address.=$Company->Address2.', ';

	}
	if (!empty($Company->Address3)){
		$Address.=$Company->Address3.', ';

	}
	if (!empty($Company->Address4)){
		$Address.=$Company->Address4.', ';

	}

	$Address.=$Company->Postcode;


	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(65,54);
	$pdf->MultiCell(120,4,$Address,0,'L');
	
	//Name and contact details of lvl 3
	$Lvl3Name = $Company->MainContact;
	$Lvl3Details = $Company->Email;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(65,68.5);
	$pdf->Cell(25,10,$Lvl3Name,0,0,'L');
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(65,72);
	$pdf->Cell(25,10,$Lvl3Details,0,0,'L');
	
	//Exam type

	IF (is_null($Attendance->EventIdfr)){
		$TrainingType = 'Exam Only';
	}else{
		$TrainingType = 'Training & Exam';
	}
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(65,89);
	$pdf->Cell(25,10,$TrainingType,0,0,'L');
	
	//Training and exam type
	$TrainingType = $Attendance->Type;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(65,97);
	$pdf->Cell(25,10,$TrainingType,0,0,'L');
	
	//Location
	$Location = $Event->Location;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(65,105);
	$pdf->Cell(25,10,$Location,0,0,'L');
	
	//Dates of Attendance
	IF (!is_null($Attendance->EventIdfr)){
		$Dates = date("d M",strtotime($Event->StartDate)).' to '.date("d M Y",strtotime($Event->EndDate));
		$pdf->SetFont('Arial','','10');
		$pdf->SetXY(65,37);
		$pdf->MultiCell(100,4,$Dates);
	}
	
	//Dates of Last Written Practice
	$LatestWrittenPractice = $Company->WrittenPractice;
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(65,64);
	$pdf->MultiCell(100,4,$LatestWrittenPractice);
	
	//Qualification System
	$QualificationSystem = "EN4179";
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(170,89);
	$pdf->Cell(25,10,$QualificationSystem,0,0,'L');
	
	//Method
	$MethodDescription = $Method->Method;
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(65,83);
	$pdf->MultiCell(100,5,$MethodDescription,0,'L');
	
	//NDT Level
	$Level = $Attendance->Level;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(170,97);
	$pdf->Cell(25,10,$Level,0,0,'L');
	

	IF ($QualificationCount >=1){

		$sql = "SELECT * FROM tblQualification WHERE Level='Level 3 Basic' and OrderIdfr = $OrderIdfr";
		
		//Level3 Basic Required
		$Level3Basic = $wpdb->get_row($sql);
		if ($wpdb->num_rows>0){
			$Level3BasicRequired = 'YES';
			$BasicMethods = $Level3Basic->Level3BasicMethods;
		}Else{
			$Level3BasicRequired = 'NO';
			$BasicMethods = 'N/A';
		}
			$pdf->SetFont('Arial','','12');
			$pdf->SetXY(80,122);
			$pdf->Cell(25,10,$Level3BasicRequired,0,0,'L');
		
		//Level3 Basic Methods
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(170,122);
		$pdf->Cell(25,10,$BasicMethods,0,0,'L');	

		$sql = "SELECT * FROM tblQualification WHERE OrderIdfr = $OrderIdfr";

		$Qualification = $wpdb->get_row($sql);


		//General Exam Format
		$Level = $Qualification->Level;
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,131);
		$pdf->Cell(25,10,$Level,0,0,'L');

		//Specific Exam Format
		$Specifications = $Qualification->Specifications;
		$sql = "SELECT * FROM tblSpecification where SpecificationIdfr in ($Specifications)";
		$SpecificExamFormats = $wpdb->get_results($sql);
		If ($wpdb->num_rows>0){
		$ExamFormat .="Yes \r\n";
		foreach($SpecificExamFormats as $SpecificExamFormat){
			$ExamFormat .= $SpecificExamFormat->Specification.' | ';
		}
		}else{
		$ExamFormat ='No';
		}
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,142);
		$pdf->MultiCell(120,5,$ExamFormat,0,'L');
		
		//Product Technology Questions
		$ProductTechnologyQuestions = $Qualification->ProductTechnologyQuestions;
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,181);
		$pdf->Cell(25,10,$ProductTechnologyQuestions,0,0,'L');
		
		//Number Of Questions
		If ($Qualification->ProductTechnologyQuestions =='No'){
			$NumberOfQuestions = 'N/A';
		}else{
			$NumberOfQuestions = $Qualification->NumberOfQuestions;
		}
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(140,181);
		$pdf->Cell(25,10,$NumberOfQuestions,0,0,'L');
		
		//Practical Techniques
		$Techniques = $Qualification->Techniques;
		$sql = "SELECT * FROM tblTechnique where TechniqueIdfr in ($Techniques)";
		$PracticalTechniques = $wpdb->get_results($sql);
		foreach($PracticalTechniques as $PracticalTechnique){
			$Technique .= $PracticalTechnique->Technique." | ";
		}
		$pdf->SetFont('Arial','','10');
		$pdf->SetXY(80,190);
		$pdf->MultiCell(130,5,$Technique,0,'L');
		
		//Product/Material/Form Type
		$ProductMaterial = $Method->ProductMaterial;
		$pdf->SetFont('Arial','','8');
		$pdf->SetXY(80,200.5);
		//$pdf->Cell(25,10,$ProductMaterial,0,0,'L');
		$pdf->MultiCell(120,2.5,$ProductMaterial,0,'L');
		
		//Level 2 Written Instruction
		$L2Written='No';
		$sql = "SELECT * FROM tblQualification WHERE Level='Level 2 Written Instruction' and OrderIdfr = $OrderIdfr";
		$Level2Written = $wpdb->get_row($sql);
		if ($wpdb->num_rows>0){
			$L2Written='Yes';
		}

		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,207);
		$pdf->Cell(25,10,$L2Written,0,0,'L');
		
		//Level 3 Procedure
		$L3Procedure='No';
		$sql = "SELECT * FROM tblQualification WHERE Level='Level 3 Procedure' and OrderIdfr = $OrderIdfr";
		$Level3Procedure = $wpdb->get_row($sql);
		if ($wpdb->num_rows>0){
			$L3Procedure='Yes';
		}

		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(180,207);
		$pdf->Cell(25,10,$L3Procedure,0,0,'L');
		
		//use of Customer equipment
		$UseOfCustomersEquipment = $Qualification->UseOfCustomersEquipment;
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,215);
		$pdf->Cell(25,10,$UseOfCustomersEquipment,0,0,'L');


		//L2 Practical for L3 Required
		$L2PracticalForL3Required='No';
		$sql = "SELECT * FROM tblQualification WHERE Level='L2 Practical for L3' and OrderIdfr = $OrderIdfr";
		
		$L2PracticalForL3 = $wpdb->get_row($sql);
		if ($wpdb->num_rows>0){
			$L2PracticalForL3Required='Yes';
		}

		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(180,215);
		$pdf->Cell(25,10,$L2PracticalForL3Required,0,0,'L');
		
		//Chief Examiners Review Date
		if (is_null($Order-> ChiefExaminersReview)){
			$ChiefExaminersReview = 'To be Confirmed';
		}else{
			$ChiefExaminersReview = date("d/m/Y",strtotime($Order-> ChiefExaminersReview));
		}
		$pdf->SetFont('Arial','','12');
		$pdf->SetXY(80,222);
		$pdf->Cell(25,10,$ChiefExaminersReview,0,0,'L');
	}
	
$DocumentName = 'ISA Customer Acknowledgement';
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

