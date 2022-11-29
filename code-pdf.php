<?php
/*
TEMPLATE NAMES:
	Level 3 Basics Cert Template
	Level 3 Cert Template
	Results Notice Template
	Training Cert Template
	crmeb_fpdf_customerorderacknowledgementqualification
*/
use setasign\Fpdi\Fpdi;

require_once('fpdf181/fpdf.php'); 
require_once('fpdi2/src/autoload.php');
add_action('crme_fpdf_default', 'crmeb_fpdf_default');
add_action('crme_fpdf_certificate', 'crmeb_fpdf_certificate');
add_action('crme_fpdf_trainingcertificate', 'crmeb_fpdf_trainingcertificate');
add_action('crme_fpdf_templatebuilder', 'crmeb_fpdf_templatebuilder');
add_action('crme_fpdf_customerorderacknowledgementqualification', 'crmeb_fpdf_customerorderacknowledgementqualification');

Function crmeb_fpdf_default(){
	Echo '<a href="/?entity=fpdf&action=templatebuilder">Build Template Grid</a><br>';
	Echo '<a href="/?entity=fpdf&action=certificate&QualificationIdfr=1">Build Certificate</a><br>';
	Echo '<a href="/?entity=fpdf&action=customerorderacknowledgement&AttendanceIdfr=1">Customer Order Acknowledgement</a><br>';
	Echo '<a href="/?entity=fpdf&action=customerorderacknowledgementqualification&QualificationIdfr=1">Customer Order Acknowledgementqualification</a><br>';
}

Function crmeb_fpdf_trainingcertificate(){
	Global $wpdb;
	extract($_REQUEST);

crmeb_fpdf_trainingcertificatepdf($AttendanceIdfr, $Output);

}

Function crmeb_fpdf_trainingcertificatepdf($AttendanceIdfr, $Output){

	Global $wpdb;
	extract($_REQUEST);



	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/Blank_Certificate.pdf'); 

	$pdf->SetAutoPageBreak(true, .5);

	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1)); 

	$Attendance = New Attendance();
	$Attendance->Get($AttendanceIdfr);

	$Student = New Student();
	$Student->Get($Attendance->StudentIdfr);

	$Company = New Company();
	$Company->Get($Student->CompanyIdfr);

	$Event = New Event();
	$Event->Get($Attendance->EventIdfr);

	$Course = New Course();
	$Course->Get($Event->CourseIdfr);

	$Staff = New Staff();
	$Staff->Get($Event->InstructorIdfr);

	//Header
	$HeaderText='This Training Certificate is awarded to';
	$pdf->SetFont('Arial', '', '18');
	$pdf->SetXY(35,60);
	$pdf->Cell(140,10,$HeaderText,0,0,'C');

	//Student Name
	$StudentName = $Student->FirstName.' '.$Student->LastName;

	$pdf->SetFont('Arial', '', '20');
	$pdf->SetXY(35,70);
	$pdf->Cell(140,10,$StudentName,0,0,'C');

	
	//Company Name & address
	$Address = $Company->CompanyName.', ';
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


	$pdf->SetFont('Arial','','8');
	$pdf->SetXY(35,75);
	$pdf->Cell(140,10,$Address,0,0,'C');



    $legal="In recognition of having successfully completed a BINDT approved NDT educational training course at International School of Areospace NDT Ltd using the $Company->CompanyName Written Practice $Company->WrittenPractice. The course satisfies the requirements of the NAS 410 and EN4179 certification schemes in the subject of";

	$pdf->SetFont('Arial', '', '7');
	$pdf->SetXY(15,85);
	$pdf->MultiCell(180,3,$legal,0,'C');

	$Line=100;
	//Sector 
	$Title="Sector:";
	$Value="Aerospace";
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(50,$Line);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$Line=$Line+5;
	//Techniques 
	$Title="Technique(s):";
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');

	$Techniques = $Attendance->Techniques;
	$sql = "SELECT * FROM tblTechnique where TechniqueIdfr in ($Techniques)";
	$PracticalTechniques = $wpdb->get_results($sql);
		foreach($PracticalTechniques as $PracticalTechnique){
			$Value=$PracticalTechnique->Technique;
			$pdf->SetXY(50,$Line);
			$pdf->Cell(30,10,$Value,0,0,'L');
			$Line=$Line+5;
		}

	$Line=$Line+5;
	//Specification 
	$Title="Specification(s):";
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');

	$Specifications = $Attendance->Specifications;
	$sql = "SELECT * FROM tblSpecification where SpecificationIdfr in ($Specifications)";
	$PracticalSpecifications = $wpdb->get_results($sql);
		foreach($PracticalSpecifications as $PracticalSpecification){
			$Value=$PracticalSpecification->Specification;
			$pdf->SetXY(50,$Line);
			$pdf->Cell(30,10,$Value,0,0,'L');
			$Line=$Line+5;
		}



	$RLine=100;
	//Course Dates 
	$Title="Course Dates:";
	$StartDate = date("d/m/Y",strtotime($Event->StartDate));
	If (is_null($Event->EndDate)){
		$EndDate = "";
	}else{
		$EndDate = ' - '.date("d/m/Y",strtotime($Event->EndDate));
	}

	$Value=$StartDate.$EndDate;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(150,$RLine);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$RLine=$RLine+5;
	//Course Duration (hours) 
	$Title="Course Duration (hours):";
	$Value=$Course->Duration;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(150,$RLine);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$RLine=$RLine+5;
	//Training Location
	$Title="Training Location:";
	$Value=$Event->Location;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(150,$RLine+2.5);
	$pdf-> MultiCell(50,5,$Value,0,'L');

	$Line=170;
	//Training Course Test Results
	$Header = "Training Course Test Results";
	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(70,$Line);
	$pdf->Cell(30,10,$Header,0,0,'L');

	$Line=$Line+5;
	$Results = $Line;
	//Theory: 
	$Title="Theory:";
	$Value=$Attendance->Theory;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(50,$Line);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$Line=$Line+5;
	//Practical:: 
	$Title="Practical:";
	$Value=$Attendance->Practical;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(50,$Line);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$Line=$Line+5;
	//Overall Training:: 
	$Title="Overall Training:";
	$Value="Pass";
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(50,$Line);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$Line=$Line+5;
	//Certificate Number: 
	$Title="Certificate Number:";
	$Value=$Attendance->AttendanceIdfr;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(20,$Line);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(50,$Line);
	$pdf->Cell(30,10,$Value,0,0,'L');


	$RLine =$Results;
	//Course Instructor
	$Title="Course Instructor:";
	$Value=$Staff->FirstName.' '.$Staff->LastName;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(150,$RLine);
	$pdf->Cell(30,10,$Value,0,0,'L');

	$RLine=$RLine+5;
	//Instructor’s Signature:
	$Title="Instructor's Signature:";

	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->Image($Staff->Signature,150,$RLine+2,0,15,'PNG');

	$RLine=$RLine+15;
	//Date of Issue:
	$CertDate = date("d/m/Y",strtotime($Attendance->CertDate));
	$Title="Date of Issue:";
	$Value=$CertDate;
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(120,$RLine);
	$pdf->Cell(30,10,$Title,0,0,'R');
	$pdf->SetXY(150,$RLine);
	$pdf->Cell(30,10,$Value,0,0,'L');

	//Footer
	$Form="ISA Form Certificate of Training, ISA-QF-00-20 Issue 6";
	$FormFooter1="This Training Certificate may be used as evidence of eligibility for NDT certification and that the stated candidate has successfully passed the NDT Training Course";
	$FormFooter2="THIS IS NOT A CERTIFICATION EXAMINATION";

	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(35,270);
	$pdf->Cell(140,10,$Form,0,0,'C');

	$pdf->SetFont('Arial', '', '8');
	$pdf->SetXY(35,275);
	$pdf->Cell(140,10,$FormFooter1,0,0,'C');

	$pdf->SetFont('Arial', '', '8');
	$pdf->SetXY(35,280);
	$pdf->Cell(140,10,$FormFooter2,0,0,'C');


$DocumentName = 'Training Certificate';
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

Function crmeb_fpdf_certificate(){
	Global $wpdb;
	extract($_REQUEST);

crmeb_fpdf_certificatepdf($QualificationIdfr, $Output);

}

Function crmeb_fpdf_certificatepdf($QualificationIdfr, $Output){
	Global $wpdb;
	extract($_REQUEST);
	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/Blank_Certificate.pdf'); 
	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1)); 

	$Qualification = New Qualification();
	$Qualification->Get($QualificationIdfr);

	$Level = $Qualification->Level;
	$Type = $Qualification->ExamType;
	$ExamDate = date("d/m/Y",strtotime($Qualification->ExamDate));

	$Method = New Method();
	$Method->Get($Qualification->MethodIdfr);

	$Student = New Student();
	$Student->Get($Qualification->StudentIdfr);
	$StudentName = $Student->FirstName.' '.$Student->LastName;

	$Staff = New Staff();
	$Staff->Get($Qualification->ExaminerIdfr);
	
	$Company = New Company();
	$Company->Get($Student->CompanyIdfr);
	$CompanyName = $Company->CompanyName;
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
	$WrittenPractice = $Company->WrittenPractice;
	
	$sql = "Select * from tblResult where QualificationIdfr = $QualificationIdfr Order By ResultTypeIdfr";
	$Results = $wpdb->get_results($sql);
	
	$IssueDate = date("d/m/Y");
	//Certify
	
	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(88,60);
	$pdf->Cell(25,10,'This is to certify that examinations have been administered for',0,0,'C');
	$pdf->SetXY(88,65);
	$pdf->Cell(25,10,'Qualification on behalf of ',0,0,'C');
	
	//Company Name
	$pdf->SetFont('Arial', '', '20');
	$pdf->SetXY(88,73);
	$pdf->Cell(25,10,$CompanyName,0,0,'C');
	
	//Company Address
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(88,80);
	$pdf->Cell(25,10,$Address,0,0,'C');
	
	//Company Written Practice
	$pdf->SetFont('Arial', '', '8');
	$pdf->SetXY(88,89.5);
	$pdf->Cell(25,10,"In accordance with $CompanyName written practice $WrittenPractice requirements for Training, Examination,",0,0,'C');
	$pdf->SetXY(88,95);
	$pdf->Cell(25,10,"Qualification and Certification of NDT Personnel to EN4179 and/or NAS410. ",0,0,'C');
	

 
	
	//Candidate Name
	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(88,105);
	$pdf->Cell(10,10,"Candidate Name:",0,0,'R');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(104,105);
	$pdf->Write(10,$StudentName);
	
	//NDT Method

	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(88,111);
	$pdf->Cell(10,10,"NDT Method:",0,0,'R');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(104,111);
	$pdf->Write(10,$Method->Method);
	
	//Level

	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(88,117);
	$pdf->Cell(10,10,"Level:",0,0,'R');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(104,117);
	$pdf->Write(10,$Level);
	
	//Examination Type

	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(88,123);
	$pdf->Cell(10,10,"Examination Type:",0,0,'R');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(104,123);
	$pdf->Write(10,$Type);
	
	//Examination Date

	$pdf->SetFont('Arial', 'B', '12');
	$pdf->SetXY(88,129);
	$pdf->Cell(10,10,"Examination Date:",0,0,'R');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(104,129);
	$pdf->Write(10,$ExamDate);


	//Specific to
	$sql = "SELECT Specification FROM tblSpecification where SpecificationIdfr In ($Qualification->Specifications)";
	$QualificationSpecifications = $wpdb->get_results($sql);
	
	
	If ($wpdb->num_rows>0){

	
	foreach($QualificationSpecifications as $QualificationSpecification){
		$specs .= $QualificationSpecification->Specification.', ';
	}

		$pdf->SetFont('Arial', 'B', '8');
		$pdf->SetXY(40, 137);

		$specs = substr($specs, 0, -2);

		$pdf-> MultiCell(130,4,"Specific to: $specs");
	}


//Results


	
	$ResultX = 24;
	$PassGradeX = 123;
	$GradeX = 148;

	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY($ResultX, 150);
	$pdf->Cell(10,10,"Results",0,0,'L');

	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY($PassGradeX, 150);
	$pdf->Cell(10,10,"Mimimum",0,0,'L');
	$pdf->SetXY($PassGradeX, 155);
	$pdf->Cell(10,10,"Pass Grade",0,0,'L');

	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY($GradeX, 150);
	$pdf->Cell(10,10,"Grade",0,0,'L');
	$pdf->SetXY($GradeX, 155);
	$pdf->Cell(10,10,"Achieved",0,0,'L');

	
	$ResultY = 165;
	foreach ($Results as $Result){
		$ResultType = New ResultType();
		$ResultType->Get($Result->ResultTypeIdfr);
		$ResultDescription = $ResultType->Description;
		if(!is_null($Result->TechniqueIdfr)){
			$Technique = New Technique();
			$Technique->Get($Result->TechniqueIdfr);
			$ResultDescription = $ResultType->Description.':'.utf8_decode($Technique->Technique);
		}
		
		//Result
		$pdf->SetFont('Arial', '', '11');
		$pdf->SetXY($ResultX,$ResultY);
		$pdf->MultiCell(100,5,$ResultDescription,0,'L');
		$CellY = $pdf->GetY();
		//Minimum Pass Grade
		$pdf->SetXY($PassGradeX,$ResultY);
		$pdf->Write(5,'('.$ResultType->MinimumPassGrade.'%)');
		//Grade Achieved
		If ($Result->Result >= $ResultType->MinimumPassGrade){
			$Grade=$Result->Result.'%';
		}else{
			$Grade=$Result->Result.'% (Fail)';
		}
		$pdf->SetXY($GradeX,$ResultY);
		$pdf->Write(5,$Grade);
		
		$ResultY = $CellY + 1;
	}

	//Limited to
	$sql = "SELECT Technique FROM tblTechnique where TechniqueIdfr In ($Qualification->Techniques)";
	$QualificationTechniques = $wpdb->get_results($sql);
	
	
	If ($wpdb->num_rows>0){

	
	foreach($QualificationTechniques as $QualificationTechnique){
		$techs .= $QualificationTechnique->Technique.', ';
	}

		$pdf->SetFont('Arial', 'B', '8');
		$pdf->SetXY(40, 230);

		$techs = substr($techs, 0, -2);

		$pdf-> MultiCell(110,4,"Limited to: $techs");
	}


	//Signature
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY(50, 240);
	$pdf->Cell(10,10,"Signature :",0,0,'L');

	$pdf->Image($Staff->Signature,70, 236);

	//Examiner
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY(50, 248);
	$pdf->Cell(10,10,"Examiner :",0,0,'L');

	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(70, 248);
	$pdf->Cell(10,10,$Staff->FirstName.' '.$Staff->LastName,0,0,'L');

	
	//Document Number
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY(135, 260.4);
	$pdf->Cell(10,10,"Document No:",0,0,'L');

	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(163,260.4);
	$pdf->Write(10,$QualificationIdfr);
	
	//Issue Date
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetXY(135, 264.9);
	$pdf->Cell(10,10,"Date of Issue: ",0,0,'L');
	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(163,264.9);
	$pdf->Write(10,$IssueDate);

	
$DocumentName = 'Exam Certificate';
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

Function crmeb_fpdf_templatebuilder(){
Global $wpdb;
extract($_REQUEST);

$Template = 'Blank_Certificate';

$pdf = new FPDI();
 $pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/'.$Template.'.pdf'); 
 $pdf->AddPage(); 
 $pdf->useTemplate($pdf->importPage(1)); 

//X
 $pdf->SetFont('Arial', '', '10');
 for($i=10; $i<200; $i = $i+10){
 $pdf->SetXY($i,1);
 $pdf->Write(10,$i);
 }
 
 //Y
 $pdf->SetFont('Arial', '', '10');
 for($i=10; $i<270; $i = $i+10){
 $pdf->SetXY(1,$i);
 $pdf->Write(10,$i);
 }

 for($y=10; $y<270; $y = $y+5){
	for($x=10; $x<200; $x = $x+5){
		$pdf->SetXY($x,$y);
		$pdf->Write(10,'.');
	}
 }

 $pdf->Output('/nas/content/live/isandt/wp-content/uploads/TemplateHelper/'.$Template.'Helper.pdf', 'F');
 Echo '<a href="https://isandt.wpengine.com/wp-content/uploads/TemplateHelper/'.$Template.'Helper.pdf" target="pdf">View Document</a>';
}

Function crmeb_fpdf_testpdf(){
	$pdf = new FPDI(); 
	$pdf->AddPage();
	
	$pdf->SetFont('Arial', '', '10');
	$pdf->SetXY(10,20);
	$pdf->Write(10,'This is a test pdf');
	
	$pdf->Output('/nas/content/live/isandt/wp-content/uploads/OrderAcknowledgements/Test.pdf', 'F');
}

Function crmeb_fpdf_customerorderacknowledgementqualification($QualifictionIdfr){
	Global $wpdb;
	extract($_REQUEST);
	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/ISA-QF-13-02 Customer Order Acknowledgement.pdf'); 
	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1));
	$Qualification = New Qualification();
	$Qualification->Get($QualificationIdfr);
	
	$Student = New Student();
	$Student->Get($Qualification->StudentIdfr);

	$Company = New Company();
	$Company->Get($Student->CompanyIdfr);
		
	
	//student name
	$Name = $Student->FirstName.' '.$Student->LastName;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,32);
	$pdf->Cell(25,10,$Name,0,0,'L');
	
	//Company Name
	$pdf->SetFont('Arial', '', '12');
	$pdf->SetXY(67.5,41);
	$pdf->Cell(25,10,$Company->CompanyName,0,0,'L');
	
	//Employer address
	$Address = $Company->Address1.', '.$Company->Address2.', '.$Company->Address3.', '.$Company->Address4.', '.$Company->Postcode;
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(67.5,48);
	$pdf->MultiCell(50,5,$Address,0,'L');
	
	//Name and contact details of lvl 3
	$Lvl3Name = $Company->MainContact;
	$Lvl3Details = $Company->Email;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,56);
	$pdf->Cell(25,10,$Lvl3Name,0,0,'L');
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(67.5,61);
	$pdf->Cell(25,10,$Lvl3Details,0,0,'L');
	
	//Training and exam type
	$TrainingType = $Qualification->ExamType;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,67);
	$pdf->Cell(25,10,$TrainingType,0,0,'L');
	
	//Status
	$Status = "Booked";
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,77);
	$pdf->Cell(25,10,$Status,0,0,'L');
	
	//Location
	$Location = "Placeholder";
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,86);
	$pdf->Cell(25,10,$Location,0,0,'L');
	
	//Dates of Attendance
	$Dates = "N/A";
	$pdf->SetFont('Arial','','11');
	$pdf->SetXY(158,32);
	$pdf->Cell(25,10,$Dates,0,0,'L');
	
	//Dates of Last Written Practice
	$LatestWrittenPractice = date("d/m/Y",strtotime($Company->WrittenPractice));
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,41);
	$pdf->Cell(25,10,$LatestWrittenPractice,0,0,'L');
	
	//Qualification System
	$QualificationSystem = "EN4179";
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,56);
	$pdf->Cell(25,10,$QualificationSystem,0,0,'L');
	
	//Method
	$Method = $Qualification->NDTMethod;
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(158,67);
	$pdf->Cell(25,10,$Method,0,0,'L');
	
	//NDT Level
	$Level = $Qualification->Level;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,77);
	$pdf->Cell(25,10,$Level,0,0,'L');
	
	//Level3 Basic Required
	$Level3BasicRequired = $Qualification->Level3BasicRequired;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,104);
	$pdf->Cell(25,10,$Level3BasicRequired,0,0,'L');
	
	//Level3 Basic Methods
	$BasicMethods = $Qualification->Level3BasicMethods;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,104);
	$pdf->Cell(25,10,$BasicMethods,0,0,'L');
	
	//General Exam Format
	$Level = $Qualification->Level;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,113);
	$pdf->Cell(25,10,$Level,0,0,'L');

	//Specific Exam Format
	$sql = "Select Specification from tblQualificationSpecification where QualificationIdfr = $Qualification->QualificationIdfr";
	$SpecificExamFormats = $wpdb->get_results($sql);
	foreach($SpecificExamFormats as $SpecificExamFormat){
		$ExamFormat .= $SpecificExamFormat->Specification.' | ';
	}
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,125);
	$pdf->MultiCell(130,5,$ExamFormat,0,'L');
	
	//Product Technology Questions
	$ProductTechnologyQuestions = $Qualification->ProductTechnologyQuestions;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,149);
	$pdf->Cell(25,10,$ProductTechnologyQuestions,0,0,'L');
	
	//Number Of Questions
	$NumberOfQuestions = $Qualification->NumberOfQuestions;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(115,149);
	$pdf->Cell(25,10,$NumberOfQuestions,0,0,'L');
	
	//Practical Techniques
	$sql = "Select Technique from tblTechnique, tblQualificationTechnique where tblTechnique.TechniqueIdfr = tblQualificationTechnique.TechniqueIdfr and QualificationIdfr = $Qualification->QualificationIdfr";
	$PracticalTechniques = $wpdb->get_results($sql);
	foreach($PracticalTechniques as $PracticalTechnique){
		$Technique .= $PracticalTechnique->Technique." | ";
	}
	$pdf->SetFont('Arial','','10');
	$pdf->SetXY(67.5,162);
	$pdf->MultiCell(130,5,$Technique,0,'L');
	
	//Product/Material/Form Type
	$ProductType = "Placeholder";
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(67.5,169);
	$pdf->Cell(25,10,$ProductType,0,0,'L');
	
	//Level 2 Written Instruction
	$Level2WrittenInstruction = $Qualification->Level2WrittenInstruction;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(91,179);
	$pdf->Cell(25,10,$Level2WrittenInstruction,0,0,'L');
	
	//Level 3 Procedure
	$Level3Procedure = $Qualification->Level3Procedure;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,179);
	$pdf->Cell(25,10,$Level3Procedure,0,0,'L');
	
	//use of Customer equipment
	$UseOfCustomersEquipment = $Qualification->UseOfCustomersEquipment;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(91,191);
	$pdf->Cell(25,10,$UseOfCustomersEquipment,0,0,'L');
	
	//L2 Practical for L3
	$L2PracticalForL3 = $Qualification->L2PracticalForL3;
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(158,191);
	$pdf->Cell(25,10,$L2PracticalForL3,0,0,'L');
	
	//Examiners Review Date
	$ExaminersReviewDate = date("d/m/Y",strtotime($Qualification->ExaminersReviewDate));
	$pdf->SetFont('Arial','','12');
	$pdf->SetXY(91,200);
	$pdf->Cell(25,10,$ExaminersReviewDate,0,0,'L');
		
	

	
	$pdf->Output('/nas/content/live/isandt/wp-content/uploads/OrderAcknowledgements/ISA-QF-13-02 Customer Order Acknowledgement Qualification-'.$Name.'.pdf', 'F');
	Echo "<h1>Qualification Acknowledgement: $StudentName</h1>";
	$Date = date("Y/m/d h:i:s");
	Echo '<a href="https://isandt.wpengine.com/wp-content/uploads/OrderAcknowledgements/ISA-QF-13-02 Customer Order Acknowledgement Qualification-'.$Name.'.pdf?'.$Date.'" target="pdf">View Document</a><br>';
	Echo '<a href="/?entity=mailer&action=acknowledgementqualificationemail&QualificationIdfr='.$QualificationIdfr.'">Email Acknowledgement to '.$Lvl3Details.'</a><br>';
	Echo "<a href = '/?entity=qualification&action=edit&QualificationIdfr=$QualificationIdfr'>Back</a>";
}

