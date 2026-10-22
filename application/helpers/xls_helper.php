<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

if (!function_exists('xls_download')) {
	/**
	 * Stream a simple one-sheet .xls (BIFF) file to the browser and exit.
	 *
	 * @param string $filename   download file name
	 * @param string $title      worksheet title
	 * @param array  $columns    list of ['title' => string, 'width' => number]
	 * @param array  $rows       list of row value arrays, in column order
	 */
	function xls_download($filename, $title, array $columns, array $rows)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle($title);

		$lastCol = '';
		foreach (array_values($columns) as $i => $col) {
			$lastCol = chr(ord('A') + $i);
			$sheet->setCellValue($lastCol . '1', $col['title']);
			$sheet->getColumnDimension($lastCol)->setWidth($col['width']);
		}
		$header = $sheet->getStyle('A1:' . $lastCol . '1')->getFont();
		$header->setBold(true);
		$header->setSize(12);
		$sheet->getRowDimension(1)->setRowHeight(20);

		$r = 2;
		foreach ($rows as $row) {
			foreach (array_values($row) as $i => $value) {
				$sheet->setCellValue(chr(ord('A') + $i) . $r, $value);
			}
			$r++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

		(new Xls($spreadsheet))->save('php://output');
		exit;
	}
}
