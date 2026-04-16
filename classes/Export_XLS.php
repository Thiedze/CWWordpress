<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

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

	public function export_kurs_teilnehmer() {
		$kurse = get_all_kurse(true);
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

			$sheetTitle = substr( preg_replace( '/[\\\\\/\*\?\:\[\]]/', '', $kurs->getName() ), 0, 31 );
			$sheet->setTitle( $sheetTitle ?: 'Kurs' );

			$sheet->mergeCells( 'A1:K1' )->setCellValue( 'A1', $kurs->getName() )->getStyle( 'A1' )->getFont()->setBold( true )->setSize( 13 );

			$sheet->setCellValue( 'A2', "Nachname", null )->getStyle( 'A2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'B2', "Vorname", null )->getStyle( 'B2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'C2', "Email", null )->getStyle( 'C2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'D2', "Alter", null )->getStyle( 'D2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'E2', "(Hoch-)Schule/Arbeitsstätte", null )->getStyle( 'E2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'F2', "Sonstiges", null )->getStyle( 'F2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'G2', "Registrierdatum", null )->getStyle( 'G2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'H2', "Schüler:in/Student:in", null )->getStyle( 'H2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'I2', "Gesamtbetrag", null )->getStyle( 'I2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'J2', "Teilnahme bezahlt?", null )->getStyle( 'J2' )->getFont()->setBold( true );
			$sheet->setCellValue( 'K2', "Kursleiter:in", null )->getStyle( 'K2' )->getFont()->setBold( true );

			$teilnehmer = get_all_teilnehmer_by_kurs( $kurs->getId() );

			$count = 2;
			if ( $teilnehmer ) {
				foreach ( $teilnehmer as $teil ) {
					$count ++;

					if ( $count % 2 == 1 ) {
						$sheet->getStyle( 'A'.$count.':K'.$count )->getFill()->setFillType( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFE0E0E0' );
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
					$sheet->setCellValue( 'K'.$count, ( $teil->getIsCourseLeader() ? "Ja" : "Nein" ) );

					$sheet->getStyle('H'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('I'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('J'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('K'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				}
			}

			$sheet->setAutoFilter('A2:K2');

			$sheet->getPageSetup()->setFitToWidth( 0 )->setOrientation( \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );

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
			$sheet->setCellValue( 'I1', "(Hoch-)Schule/Arbeitsstätte", null )->getStyle('I1')->getFont()->setBold( true );
			$sheet->setCellValue( 'J1', "Kurs", null )->getStyle('J1')->getFont()->setBold( true );
			$sheet->setCellValue( 'K1', "Essen", null )->getStyle('K1')->getFont()->setBold( true );
			$sheet->setCellValue( 'L1', "Sonstiges", null )->getStyle('L1')->getFont()->setBold( true );
			$sheet->setCellValue( 'M1', "Aufmerksam durch:", null )->getStyle('M1')->getFont()->setBold( true );
			$sheet->setCellValue( 'N1', "Registrierdatum", null )->getStyle('N1')->getFont()->setBold( true );
			$sheet->setCellValue( 'O1', "Schüler:in/Student:in", null )->getStyle('O1')->getFont()->setBold( true );
			$sheet->setCellValue( 'P1', "Gesamtbetrag", null )->getStyle('P1')->getFont()->setBold( true );
			$sheet->setCellValue( 'Q1', "Teilnahme bezahlt?", null )->getStyle('Q1')->getFont()->setBold( true );
			$sheet->setCellValue( 'R1', "Kursleiter:in", null )->getStyle('R1')->getFont()->setBold( true );

			$count = 1;

			foreach ( $teilnehmer as $teil ) {
				$count ++;

				if ( $count % 2 == 1 ) {
					$sheet->getStyle( 'A'.$count.':R'.$count )->getFill()->setFillType( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFE0E0E0' );
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
				$sheet->setCellValue( 'K'.$count, $teil->getEssen() );
				$sheet->setCellValue( 'L'.$count, $teil->getSonstiges() );
				$sheet->setCellValue( 'M'.$count, $teil->getGotit() );

				$sheet->setCellValue( 'N'.$count, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( strtotime( $teil->getRegdate() ) ) );

				$sheet->getStyle( 'N'.$count )
				      ->getNumberFormat()
				      ->setFormatCode( 'dd.mm.yyyy HH:mm:ss' );

				$sheet->setCellValue( 'O'.$count, ( $teil->get_paytype() == 1 ? 'Ja' : 'Nein' ) );
				$sheet->setCellValue( 'P'.$count, $teil->get_to_pay() );
				$sheet->setCellValue( 'Q'.$count, ( $teil->getPayed() == 1 ? "Ja" : "Nein" ) );
				$sheet->setCellValue( 'R'.$count, ( $teil->getIsCourseLeader() ? "Ja" : "Nein" ) );

				$sheet->getStyle('O'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('P'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('Q'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('R'.$count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

				$sheet->setAutoFilter('A1:R1');

				$sheet->getPageSetup()->setFitToWidth(0)->setOrientation( \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );

			}

			foreach ( $this->abc as $item ) {
				$sheet->getColumnDimension( $item )->setAutoSize( true );
			}

			$this->download_file( $phpx, "Campuswoche_Teilnehmer.xlsx" );
		}
	}


	public function export_teilnehmer_csv() {
		$teilnehmer = get_all_teilnehmer();
		if ( ! $teilnehmer ) {
			return;
		}

		$rows   = [];
		$rows[] = [ 'Nachname', 'Vorname', 'Email', 'Adresse', 'PLZ', 'Ort', 'Geburtsdatum', 'Alter', '(Hoch-)Schule/Arbeitsstätte', 'Kurs', 'Essen', 'Sonstiges', 'Aufmerksam durch:', 'Registrierdatum', 'Schüler:in/Student:in', 'Gesamtbetrag', 'Teilnahme bezahlt?', 'Kursleiter:in' ];

		foreach ( $teilnehmer as $teil ) {
			$rows[] = [
				$teil->getNachname(),
				$teil->getVorname(),
				$teil->getEmail(),
				$teil->getStr(),
				$teil->getPlz(),
				$teil->getOrt(),
				date( 'd.m.Y', strtotime( $teil->getGeb() ) ),
				calc_age( $teil->getGeb() ),
				$teil->getSchule(),
				$teil->getKurs()->getName(),
				$teil->getEssen(),
				$teil->getSonstiges(),
				$teil->getGotit(),
				date( 'd.m.Y H:i:s', strtotime( $teil->getRegdate() ) ),
				( $teil->get_paytype() == 1 ? 'Ja' : 'Nein' ),
				$teil->get_to_pay(),
				( $teil->getPayed() == 1 ? 'Ja' : 'Nein' ),
				( $teil->getIsCourseLeader() ? 'Ja' : 'Nein' ),
			];
		}

		$this->download_csv( $rows, 'Campuswoche_Teilnehmer.csv' );
	}

	public function export_kurs_teilnehmer_csv() {
		$kurse  = get_all_kurse( true );
		$rows   = [];
		$rows[] = [ 'Kurs', 'Nachname', 'Vorname', 'Email', 'Alter', '(Hoch-)Schule/Arbeitsstätte', 'Sonstiges', 'Registrierdatum', 'Schüler:in/Student:in', 'Gesamtbetrag', 'Teilnahme bezahlt?', 'Kursleiter:in' ];

		foreach ( $kurse as $kurs ) {
			$teilnehmer = get_all_teilnehmer_by_kurs( $kurs->getId() );
			if ( $teilnehmer ) {
				foreach ( $teilnehmer as $teil ) {
					$rows[] = [
						$kurs->getName(),
						$teil->getNachname(),
						$teil->getVorname(),
						$teil->getEmail(),
						calc_age( $teil->getGeb() ),
						$teil->getSchule(),
						$teil->getSonstiges(),
						date( 'd.m.Y H:i:s', strtotime( $teil->getRegdate() ) ),
						( $teil->get_paytype() == 1 ? 'Ja' : 'Nein' ),
						$teil->get_to_pay(),
						( $teil->getPayed() == 1 ? 'Ja' : 'Nein' ),
						( $teil->getIsCourseLeader() ? 'Ja' : 'Nein' ),
					];
				}
			}
		}

		$this->download_csv( $rows, 'Campuswoche_Kurse_Teilnehmer.csv' );
	}

	private function download_csv( array $rows, string $filename ) {
		ob_start();
		$handle = fopen( 'php://output', 'w' );
		fwrite( $handle, "\xEF\xBB\xBF" ); // UTF-8 BOM für Excel
		foreach ( $rows as $row ) {
			fputcsv( $handle, $row, ';' );
		}
		fclose( $handle );
		$content = ob_get_clean();

		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . strlen( $content ) );
		header( 'Cache-Control: max-age=0' );
		header( 'Pragma: public' );

		echo $content;
		exit();
	}

	private function download_file( $excel, $filename ) {

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $excel, 'Xlsx' );

		// Dateiinhalt in eigenem Buffer erzeugen
		ob_start();
		$writer->save( 'php://output' );
		$content = ob_get_clean();

		// Alle offenen WordPress-Buffer leeren, damit kein HTML in die Datei gelangt
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . strlen( $content ) );
		header( 'Cache-Control: max-age=0' );
		header( 'Pragma: public' );

		echo $content;
		exit();
	}

}