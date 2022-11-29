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

add_action('crme_fpdf_sponsorletter', 'crmeb_fpdf_sponsorletter');

Function crmeb_fpdf_sponsorletter(){
	Global $wpdb;
	extract($_REQUEST);
	echo crmeb_fpdf_sponsorletterpdf($OrderIdfr, $Output);
}

Function crmeb_fpdf_sponsorletterpdf($OrderIdfr, $Output){

	Global $wpdb;
	extract($_REQUEST);
	$pdf = new FPDI();
	$pdf->setSourceFile('/nas/content/live/isandt/wp-content/uploads/Templates/LetterHead.pdf'); 
	$pdf->AddPage(); 
	$pdf->useTemplate($pdf->importPage(1)); 
	
	$pdf->SetLeftMargin(25);
	$pdf->SetRightMargin(30);
	
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

	//student name
	$StudentName = "Dear $Student->FirstName $Student->LastName, ";
	$pdf->SetFont('Arial','B','9');
	$pdf->SetXY(25,80);
	$pdf->Write(5, $StudentName);


	//Employer Name
	$Name =$Company->CompanyName;
	$pdf->SetFont('Arial','','9');
	$pdf->SetXY(25,70);
	$pdf->MultiCell(100,5,$Name,0,'L');
	
	//Employer address

	$Address = is_null($Company->Address1) ? '' : $Company->Address1.', ' ;
	$Address .= is_null($Company->Address2) ? '' : $Company->Address2.', ' ;
	$Address .= is_null($Company->Address3) ? '' : $Company->Address3.', ' ;
	$Address .= is_null($Company->Address4) ? '' : $Company->Address4.', ' ;
	$Address .= is_null($Company->Postcode) ? '' : $Company->Postcode.'.' ;
	
	$pdf->SetFont('Arial','','9');
	$pdf->SetXY(25,75);
	$pdf->MultiCell(100,5,$Address,0,'L');
	
	//Date
	$Date = date("j/n/Y",strtotime($Order->Date));
	$pdf->SetFont('Arial','','9');
	$pdf->SetXY(25,65);
	$pdf->Write(5, $Date);
	
	//Passport
	$Passport = "Passport Number: $Student->PassportNumber";
	$pdf->SetFont('Arial','B','9');
	$pdf->SetXY(75,85);
	$pdf->Write(5, $Passport);
	
		
	//Dates of Attendance
	$Dates = date("d F",strtotime($Event->StartDate)).' to '.date("d F Y",strtotime($Event->EndDate));

	
	//Qualification System
	$QualificationSystem = "EN4179";

	
	//Method
	$MethodDescription = $Method->Method;

	
	//NDT Level
	$Level = $Attendance->Level;

	
	$CourseDetails = "We are pleased to confirm your acceptance to attend our facilities for $Level in $MethodDescription methods on the following dates $Dates at International School of Aerospace NDT Ltd.";

	$pdf->SetFont('Arial','','9');
	$pdf->SetXY(25,90);
	$pdf->Write(5, $CourseDetails);
	
	$pdf->SetXY(25,110);
	$pdf->Write(5, "International School of Aerospace NDT Ltd is accredited by the British Institute of Non-Destructive Testing and  is on the UKBA's Sponsor's Register.

By accepting this offer of study, you agree to meet the UKBA requirements before and while visiting the UK.  Usually, these requirements will be monitored and reported by International School of Aerospace NDT Ltd and in particular you are reminded that: 

-	You must arrive on the date the training establishment expects you. If you cannot arrive on that date for any reason you must contact us as we are required to report this to the UKBA.

-	You must enrol on your course on the given date. If you cannot enrol on the given date for any reason you must contact us as we are required to report this to the UKBA.

-	You must attend the course fully and completely.  If you cannot attend the course fully and completely for any reason you must contact us as we are required to report this to the UKBA.

-	You must leave the country on completion of the course. If you cannot leave the country on completion of the course for any reason you must contact us as we are required to report this to the UKBA.

Should you breach, or be at risk of breaching, the UKBA requirements we will attempt to contact you but please be aware that the matter must be reported whether or not we have made contact with you.  It is in your own best interests to stay in contact with us.

Should you have any queries on this or any other matter, either before you arrive or while you are here, please contact Sarah Parker or Wayne Thompson at 00441603 260148 or info@isandt.co.uk.

We look forward to welcoming you to International School of Aerospace NDT Ltd and in the meantime, wish you a safe journey.

Best wishes 

Sarah Parker
School Administrator
");
	
	
$DocumentName = 'Sponsor Letter';
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

