<?php
require 'vendor/autoload.php';

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 15.10.2016
 * Time: 11:41
 */
class Export_XLS {

	private $db;

	private $abc;

	public function __construct( $_db ) {
		$this->db  = $_db;
		$this->abc = range( 'A', 'Z' );
	}

	/**
	 * Exportiert die T-Shirt-Liste
	 */
	public function export_shirts() {

		$shirtlist = $this->db->get_results( "SELECT wcs.name,wcs.size,count(wcus.user_id) as anzahl 
								FROM wp_cw_user_shirt wcus 
							    LEFT JOIN wp_cw_shirt wcs ON wcus.shirt_id=wcs.id 
							GROUP BY wcs.name,wcs.size
							ORDER BY wcs.name,wcs.size" );

		$phpx = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$phpx->getProperties()->setTitle( 'T-Shirts Campuswoche' );

		$sheet = $phpx->getActiveSheet();
		$count = 1;

		$sheet->setCellValue( 'A1', "Bezeichnung", null )->getStyle('A1')->getFont()->setBold( true );
		$sheet->setCellValue( 'B1', "Größe", null )->getStyle('B1')->getFont()->setBold( true );
		$sheet->setCellValue( 'C1', "Menge", null )->getStyle('C1')->getFont()->setBold( true );

		if ( $shirtlist ) {
			foreach ( $shirtlist as $shirt ) {
				$count ++;

				if ( $count % 2 == 1 ) {
					$sheet->getStyle( 'A'.$count.':C'.$count )->getFill()->setFillType( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFE0E0E0' );
				}

				$sheet->setCellValue( 'A'.$count, $shirt->name );
				$sheet->setCellValue( 'B'.$count, $shirt->size );
				$sheet->setCellValue( 'C'.$count, $shirt->anzahl );
			}
		}

		$this->download_file( $phpx, "Campuswoche_Shirts.xlsx" );

	}

	public function export_kurs_teilnehmer() {
		$kurse = get_all_kurse();
		$first = true;

		$phpx   = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$phpx->getProperties()->setTitle( 'Teilnehmer Kurse Campuswoche' );

		foreach ( $kurse as $kurs ) {
			if ( $first ) {
				$sheet = $phpx->getActiveSheet();
				$first = false;
			} else {
				$sheet = $phpx->createSheet();
			}

			$sheet->setTitle( substr( $kurs->getName(), 0, 20 ) );

			$sheet->mergeCells( 'A1:J1' )->setCellValue( 'A1', $kurs->getName() )->getStyle( 'A1' )->getFont()->setBold( true )->setSize( 13 );

			$sheet->setCellValue( 'A2', "Nachname", null )->getStyle( 'A2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'B2', "Vorname", null )->getStyle( 'B2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'C2', "Email", null )->getStyle( 'C2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'D2', "Alter", null )->getStyle( 'D2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'E2', "(Hoch-)Schule", null )->getStyle( 'E2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'F2', "Sonstiges", null )->getStyle( 'F2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'G2', "Registrierdatum", null )->getStyle( 'G2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'H2', "Schüler/Student", null )->getStyle( 'H2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'I2', "Gesamtbetrag", null )->getStyle( 'I2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'J2', "Teilnahme bezahlt?", null )->getStyle( 'J2' )->getFont()->setBold( true );

			$teilnehmer = get_all_teilnehmer_by_kurs( $kurs->getId() );

			$count = 2;
			if ( $teilnehmer ) {
				foreach ( $teilnehmer as $teil ) {
					$count ++;

					if ( $count % 2 == 1 ) {
						$sheet->getStyle( 'A'.$count.':J'.$count )->getFill()->setFillType( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFE0E0E0' );
					}

					$sheet->setCellValue( 'A'.$count, $teil->getNachname() );
					$sheet->setCellValue( 'B'.$count, $teil->getVorname() );
					$sheet->setCellValue( 'C'.$count, $teil->getEmail() );
					$sheet->setCellValue( 'D'.$count, calc_age( $teil->getGeb() ) );
					$sheet->setCellValue( 'E'.$count, $teil->getSchule() );
					$sheet->setCellValue( 'F'.$count, $teil->getSonstiges() );
					$sheet->setCellValue( 'G'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( strtotime( $teil->getRegdate() ) ) );
					$sheet->getStyle( 'G'.$count )
					      ->getNumberFormat()
					      ->setFormatCode( 'dd.mm.yyyy HH:mm:ss' );
					$sheet->setCellValue( 'H'.$count, ( $teil->get_paytype() == 1 ? 'Ja' : 'Nein' ) );
					$sheet->setCellValue( 'I'.$count, $teil->get_to_pay() );
					$sheet->setCellValue( 'J'.$count, ( $teil->getPayed() == 1 ? "Ja" : "Nein" ) );

					$sheet->getStyle('H'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('I'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('J'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				}
			}

			$sheet->setAutoFilter('A2:J2');

			$sheet->getPageSetup()->setFitToWidth( null )->setOrientation( \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );

			foreach ( $this->abc as $item ) {
				$sheet->getColumnDimension( $item )->setAutoSize( true );
			}

		}

		$this->download_file( $phpx, "Campuswoche_Kurse_Teilnehmer.xlsx" );

	}

	public function export_teilnehmer() {
		$teilnehmer = get_all_teilnehmer();

		if ( $teilnehmer ) {

			$phpx = new PhpOffice\PhpSpreadsheet\Spreadsheet();
			$phpx->getProperties()
			     ->setTitle( 'Teilnehmer Campuswoche' );

			$sheet = $phpx->getActiveSheet();
			$sheet->setTitle( 'Teilnehmer Campuswoche' );

			$sheet->setCellValue( 'A1', "Nachname", null )->getStyle('A1')->getFont()->setBold( true );
			$sheet->setCellValue( 'B1', "Vorname", null )->getStyle('B1')->getFont()->setBold( true );
			$sheet->setCellValue( 'C1', "Email", null )->getStyle('C1')->getFont()->setBold( true );
			$sheet->setCellValue( 'D1', "Adresse", null )->getStyle('D1')->getFont()->setBold( true );
			$sheet->setCellValue( 'E1', "PLZ", null )->getStyle('E1')->getFont()->setBold( true );
			$sheet->setCellValue( 'F1', "Ort", null )->getStyle('F1')->getFont()->setBold( true );
			$sheet->setCellValue( 'G1', "Geburtsdatum", null )->getStyle('G1')->getFont()->setBold( true );
			$sheet->setCellValue( 'H1', "Alter", null )->getStyle('H1')->getFont()->setBold( true );
			$sheet->setCellValue( 'I1', "(Hoch-)Schule", null )->getStyle('I1')->getFont()->setBold( true );
			$sheet->setCellValue( 'J1', "Kurs", null )->getStyle('J1')->getFont()->setBold( true );
			$sheet->setCellValue( 'K1', "Shirt", null )->getStyle('K1')->getFont()->setBold( true );
			$sheet->setCellValue( 'L1', "Essen", null )->getStyle('L1')->getFont()->setBold( true );
			$sheet->setCellValue( 'M1', "Sonstiges", null )->getStyle('M1')->getFont()->setBold( true );
			$sheet->setCellValue( 'N1', "Aufmerksam durch:", null )->getStyle('N1')->getFont()->setBold( true );
			$sheet->setCellValue( 'O1', "Registrierdatum", null )->getStyle('O1')->getFont()->setBold( true );
			$sheet->setCellValue( 'P1', "Schüler/Student", null )->getStyle('P1')->getFont()->setBold( true );
			$sheet->setCellValue( 'Q1', "Gesamtbetrag", null )->getStyle('Q1')->getFont()->setBold( true );
			$sheet->setCellValue( 'R1', "Teilnahme bezahlt?", null )->getStyle('R1')->getFont()->setBold( true );
			$sheet->setCellValue( 'S1', "T-Shirt bezahlt?", null )->getStyle('S1')->getFont()->setBold( true );

			$count = 1;

			foreach ( $teilnehmer as $teil ) {
				$count ++;

				if ( $count % 2 == 1 ) {
					$sheet->getStyle( 'A'.$count.':P'.$count )->getFill()->setFillType( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFE0E0E0' );
				}

				$sheet->setCellValue( 'A'.$count, $teil->getNachname() );
				$sheet->setCellValue( 'B'.$count, $teil->getVorname() );
				$sheet->setCellValue( 'C'.$count, $teil->getEmail() );
				$sheet->setCellValue( 'D'.$count, $teil->getStr() );
				$sheet->setCellValue( 'E'.$count, $teil->getPlz() );
				$sheet->setCellValue( 'F'.$count, $teil->getOrt() );
				$sheet->setCellValue( 'G'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( strtotime( $teil->getGeb() ) ) );

				$sheet->getStyle( 'G'.$count )
				      ->getNumberFormat()
				      ->setFormatCode( 'dd.mm.yyyy' );

				$sheet->setCellValue( 'H'.$count, calc_age( $teil->getGeb() ) );
				$sheet->setCellValue( 'I'.$count, $teil->getSchule() );
				$sheet->setCellValue( 'J'.$count, $teil->getKurs()->getName() );
				$sheet->setCellValue( 'K'.$count, $teil->getTshirt()->getName()." ".$teil->getTshirt()->getSize() );
				$sheet->setCellValue( 'L'.$count, $teil->getEssen() );
				$sheet->setCellValue( 'M'.$count, $teil->getSonstiges() );
				$sheet->setCellValue( 'N'.$count, $teil->getGotit() );

				$sheet->setCellValue( 'O'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( strtotime( $teil->getRegdate() ) ) );

				$sheet->getStyle( 'O'.$count )
				      ->getNumberFormat()
				      ->setFormatCode( 'dd.mm.yyyy HH:mm:ss' );


				$sheet->setCellValue( 'P'.$count, ( $teil->get_paytype() == 1 ? 'Ja' : 'Nein' ) );
				$sheet->setCellValue( 'Q'.$count, $teil->get_to_pay() );


				$sheet->setCellValue( 'R'.$count, ( $teil->getPayed() == 1 ? "Ja" : "Nein" ) );

				if ( $teil->getTshirt()->getName() == "" ) {
					$sheet->setCellValue( 'S'.$count, " " );
				} else {
					$sheet->setCellValue( 'S'.$count, ( $teil->getShirtPayed() == 1 ? "Ja" : "Nein" ) );
				}

				$sheet->getStyle('P'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('Q'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('R'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('S'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

				$sheet->setAutoFilter('A1:S1');

				$sheet->getPageSetup()->setFitToWidth(null)->setOrientation( \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );

			}

			foreach ( $this->abc as $item ) {
				$sheet->getColumnDimension( $item )->setAutoSize( true );
			}

			$this->download_file( $phpx, "Campuswoche_Teilnehmer.xlsx" );
		}
	}


	private function download_file( $excel, $filename ) {

		$dir = wp_upload_dir();

		//Als erstes wird die scheiß Datei erstellt, dann kann man auch die Größe ermitteln
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $excel, 'Xlsx' );
		$writer->save( $dir['basedir'].'/'.$filename );
		$size = filesize( $dir['basedir'].'/'.$filename );

		//Nachdem wir die Größe haben, killen wir die Datei (die braucht kein Mensch)
		unlink( $dir['basedir'].'/'.$filename );

		//Dann den Cache löschen, damit kein Rotz mit in der Datei landet
		ob_clean();

		//Jetzt den Header setzen und dann die Datei raushauen
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="'.$filename.'"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: '.$size );
		header( 'Cache-Control: max-age=1' );
		header( 'Cache-Control: cache, must-revalidate' );
		header( 'Pragma: public' );

		$writer->save( 'php://output' );
	}

}