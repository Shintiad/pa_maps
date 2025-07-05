<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasusPenyakitExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return array_keys($this->data->first() ?? []);
    }
    
    // Format numerik untuk kolom terjangkit dan meninggal
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,  // Kolom Jumlah Terjangkit
            'F' => NumberFormat::FORMAT_NUMBER,  // Kolom Jumlah Meninggal
        ];
    }

    // Implementasi lebar kolom yang tepat dengan interface WithColumnWidths
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 12,  // Tahun
            'C' => 25,  // Kecamatan
            'D' => 30,  // Nama Penyakit
            'E' => 20,  // Jumlah Terjangkit
            'F' => 15,  // Jumlah Meninggal
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Atur autosizing kolom sebagai alternatif jika columnWidths tidak bekerja dengan baik
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        
        return [
            // Style untuk header (baris pertama)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style untuk seluruh tabel
            'A1:F' . ($this->data->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}