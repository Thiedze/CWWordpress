<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 15.10.2016
 * Time: 11:41
 */
class Export_XLS {

	private $db;

	private $abc;

	public function __construct($_db) {
		$this->db = $_db;
		$this->abc = range('A','Z');
	}

	/**
	 * Exportiert die T-Shirt-Liste
	 */
	public function export_shirts(){

		$shirtlist = $this->db->get_results("SELECT wcs.name,wcs.size,count(wcus.user_id) as anzahl 
								FROM wp_cw_user_shirt wcus 
							    LEFT JOIN wp_cw_shirt wcs ON wcus.shirt_id=wcs.id 
							GROUP BY wcs.name,wcs.size
							ORDER BY wcs.name,wcs.size");

		$phpx = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$phpx->getProperties()
				->setTitle('T-Shirts Campuswoche');

		$phpx->setActiveSheetIndex(0);
		$sheet = $phpx->getActiveSheet();
		$count = 1;

		$sheet->setCellValue('A1',"Bezeichnung",true)->getStyle()->getFont()->setBold(true);
		$sheet->setCellValue('B1',"Größe",true)->getStyle()->getFont()->setBold(true);
		$sheet->setCellValue('C1',"Menge",true)->getStyle()->getFont()->setBold(true);

		if($shirtlist){
			foreach ($shirtlist as $shirt){
				$count++;

				if($count%2 == 1){
					$sheet->getStyle('A'.$count.':C'.$count)->applyFromArray(
						['fill' => [
							'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
							'color' => ['argb' => 'FFDDDDDD'],
						]]
					);
				}

				$sheet->setCellValue('A'.$count,$shirt->name);
				$sheet->setCellValue('B'.$count,$shirt->size);
				$sheet->setCellValue('C'.$count,$shirt->anzahl);
			}
		}

		$this->download_file($phpx,"Campuswoche_Shirts.xlsx");

	}

	public function export_kurs_teilnehmer(){
		$kurse = get_all_kurse();
		$first = true;

		$phpx = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$phpx->getProperties()
		     ->setTitle('Teilnehmer Kurse Campuswoche');

		foreach($kurse as $kurs){
			if($first){
				$sheet = $phpx->getActiveSheet();
				$first = false;
			}else{
				$sheet = $phpx->createSheet();
			}

			$sheet->setTitle(substr($kurs->getName(),0,20));

			$sheet->mergeCells('A1:H1')->setCellValue('A1',$kurs->getName())->getStyle()->getFont()->setBold(true)->setSize(13);

			$sheet->setCellValue('A2',"Nachname",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('B2',"Vorname",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('C2',"Email",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('D2',"Alter",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('E2',"(Hoch-)Schule",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('F2',"Sonstiges",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('G2',"Registrierdatum",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('H2',"Teilnahme bezahlt?",true)->getStyle()->getFont()->setBold(true);

			$teilnehmer = get_all_teilnehmer_by_kurs($kurs->getId());

			$count = 2;
			if($teilnehmer) {
				foreach ( $teilnehmer as $teil ) {
					$count ++;

					if($count%2 == 1){
						$sheet->getStyle('A'.$count.':H'.$count)->applyFromArray(
							['fill' => [
								'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'color' => ['argb' => 'FFDDDDDD'],
							]]
						);
					}

					$sheet->setCellValue( 'A' . $count, $teil->getNachname() );
					$sheet->setCellValue( 'B' . $count, $teil->getVorname() );
					$sheet->setCellValue( 'C' . $count, $teil->getEmail() );
					$sheet->setCellValue( 'D' . $count, calc_age( $teil->getGeb() ) );
					$sheet->setCellValue( 'E' . $count, $teil->getSchule() );
					$sheet->setCellValue( 'F' . $count, $teil->getSonstiges() );
					$sheet->setCellValue( 'G' . $count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( strtotime( $teil->getRegdate() ) ) );
					$sheet->getStyle( 'G' . $count )
					      ->getNumberFormat()
					      ->setFormatCode( 'dd.mm.yyyy HH:mm:ss' );
					$sheet->setCellValue( 'H' . $count, ($teil->getPayed() == 1 ? "Ja" : "Nein") );
				}
			}

			$sheet->getPageSetup()->setFitToWidth()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

			foreach ( $this->abc as $item ) {
				$sheet->getColumnDimension($item)->setAutoSize(true);
			}

		}

		$this->download_file($phpx,"Campuswoche_Kurse_Teilnehmer.xlsx");

	}

	public function export_teilnehmer(){
		$teilnehmer = get_all_teilnehmer();

		if($teilnehmer){

			$phpx = new PhpOffice\PhpSpreadsheet\Spreadsheet();
			$phpx->getProperties()
			     ->setTitle('Teilnehmer Campuswoche');

			$sheet = $phpx->getActiveSheet();
			$sheet->setTitle('Teilnehmer Campuswoche');

			$sheet->setCellValue('A1',"Nachname",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('B1',"Vorname",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('C1',"Email",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('D1',"Adresse",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('E1',"PLZ",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('F1',"Ort",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('G1',"Geburtsdatum",true)->getStyle()->getFont()->setBold(true);
            $sheet->setCellValue('H1',"Alter",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('I1',"(Hoch-)Schule",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('J1',"Kurs",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('K1',"Shirt",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('L1',"Essen",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('M1',"Sonstiges",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('N1',"Aufmerksam durch:",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('O1',"Registrierdatum",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('P1',"Teilnahme bezahlt?",true)->getStyle()->getFont()->setBold(true);
			$sheet->setCellValue('Q1',"T-Shirt bezahlt?",true)->getStyle()->getFont()->setBold(true);

			$count = 1;

			foreach ($teilnehmer as $teil){
				$count++;

				if($count%2 == 1){
					$sheet->getStyle('A'.$count.':Q'.$count)->applyFromArray(
						['fill' => [
							'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
							'color' => ['argb' => 'FFDDDDDD'],
						]]
					);
				}

				$sheet->setCellValue('A'.$count,$teil->getNachname());
				$sheet->setCellValue('B'.$count,$teil->getVorname());
				$sheet->setCellValue('C'.$count,$teil->getEmail());
				$sheet->setCellValue('D'.$count,$teil->getStr());
				$sheet->setCellValue('E'.$count,$teil->getPlz());
				$sheet->setCellValue('F'.$count,$teil->getOrt());
				$sheet->setCellValue('G'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($teil->getGeb())));

				$sheet->getStyle('G'.$count)
					->getNumberFormat()
					->setFormatCode('dd.mm.yyyy');

                $sheet->setCellValue('H'.$count,calc_age($teil->getGeb()));
				$sheet->setCellValue('I'.$count,$teil->getSchule());
				$sheet->setCellValue('J'.$count,$teil->getKurs()->getName());
				$sheet->setCellValue('K'.$count,$teil->getTshirt()->getName()." ".$teil->getTshirt()->getSize());
				$sheet->setCellValue('L'.$count,$teil->getEssen());
				$sheet->setCellValue('M'.$count,$teil->getSonstiges());
				$sheet->setCellValue('N'.$count,$teil->getGotit());

				$sheet->setCellValue('O'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($teil->getRegdate())));

				$sheet->getStyle('O'.$count)
				      ->getNumberFormat()
				      ->setFormatCode('dd.mm.yyyy HH:mm:ss');

				$sheet->setCellValue('P'.$count,($teil->getPayed() == 1 ? "Ja" : "Nein"));

				if($teil->getTshirt()->getName() == "") {
					$sheet->setCellValue( 'Q' . $count, " " );
				}else{
					$sheet->setCellValue( 'Q' . $count, ($teil->getShirtPayed() == 1 ? "Ja": "Nein"));
				}

				$sheet->getPageSetup()->setFitToWidth()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

			}

			foreach ( $this->abc as $item ) {
				$sheet->getColumnDimension($item)->setAutoSize(true);
			}

			$this->download_file($phpx,"Campuswoche_Teilnehmer.xlsx");
		}
	}


	private function download_file($excel,$filename){

		$dir = wp_upload_dir();

		//Als erstes wird die scheiß Datei erstellt, dann kann man auch die Größe ermitteln
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
		$writer->save($dir['basedir'].'/'.$filename);
		$size = filesize($dir['basedir'].'/'.$filename);

		//Nachdem wir die Größe haben, killen wir die Datei (die braucht kein Mensch)
		unlink($dir['basedir'].'/'.$filename);

		//Dann den Cache löschen, damit kein Rotz mit in der Datei landet
		ob_clean();

		//Jetzt den Header setzen und dann die Datei raushauen
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' .$size);
		header('Cache-Control: max-age=1');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

		$writer->save('php://output');
	}

}