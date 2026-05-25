<?php

namespace App\Exports\Teacher\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */

    private const COLOR_HEADER_BG   = '1E3A8A';
    private const COLOR_HEADER_FONT = 'FFFFFF';
    private const COLOR_ROW_ALT     = 'EFF6FF';
    private const COLOR_PRESENT     = 'ECFDF5';
    private const COLOR_PERMISSION  = 'DBEAFE';
    private const COLOR_SICK        = 'FEF9C3';
    private const COLOR_ABSENT      = 'FEE2E2';
    private const COLOR_LATE        = 'FEF3C7';
    private const LAST_COL          = 'G';

    // Row positions — satu tempat, tidak tersebar
    private const ROW_TITLE     = 1;
    private const ROW_INFO      = 2;
    private const ROW_HEADER    = 3; // headings dari WithHeadings
    private const ROW_DATA_START = 4; // data mulai sini

    private int $rowNumber = 0;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct(
        protected Collection $rows,
        protected array      $meta = []
    ) {}

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return $this->rows;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADINGS
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS',
            'Status',
            'Keterangan',
            'Notifikasi',
            'Pertemuan ke-',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING
    |--------------------------------------------------------------------------
    */

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['student_name'],
            $row['nis'],
            $row['status_label'],
            $row['note']        ?? '',
            $row['notified_at'] ?? '-',
            $row['meeting_number'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SHEET TITLE
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        $grade   = $this->meta['grade']   ?? '';
        $subject = $this->meta['subject'] ?? '';
        return substr("Presensi {$grade} {$subject}", 0, 31);
    }

    /*
    |--------------------------------------------------------------------------
    | COLUMN WIDTHS
    |--------------------------------------------------------------------------
    */

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 16,
            'D' => 14,
            'E' => 34,
            'F' => 18,
            'G' => 14,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STYLES — dikosongkan, semua styling di AfterSheet
    |--------------------------------------------------------------------------
    */

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $last     = self::LAST_COL;
                $dataRows = $this->rows->count();

                // ── Struktur row setelah insertNewRowBefore ───────────────
                // WithHeadings & data sudah ada di row 1, 2, 3, ...
                // Kita insert 2 baris di atas agar:
                //   row 1 = title
                //   row 2 = info
                //   row 3 = header (dari WithHeadings)
                //   row 4+ = data
                $sheet->insertNewRowBefore(1, 2);

                $headerRow   = self::ROW_HEADER;   // 3
                $dataStart   = self::ROW_DATA_START; // 4
                $lastDataRow = $dataStart + $dataRows - 1;

                // 1. Meta (title + info)
                $this->_writeMeta($sheet);

                // 2. Header style
                $sheet->getStyle("A{$headerRow}:{$last}{$headerRow}")
                    ->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'size'  => 10,
                            'color' => ['argb' => 'FF' . self::COLOR_HEADER_FONT],
                        ],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF' . self::COLOR_HEADER_BG],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFD1D5DB'],
                            ],
                        ],
                    ]);

                // 3. Border & alignment seluruh tabel (header + data)
                $sheet->getStyle("A{$headerRow}:{$last}{$lastDataRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFE5E7EB'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);

                // 4. Center alignment kolom No, Status, Pertemuan
                foreach (['A', 'D', 'G'] as $col) {
                    $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastDataRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // 5. Warna per status
                $this->_colorRows($sheet, $dataRows, $dataStart);

                // 6. Freeze pane tepat di bawah header
                $sheet->freezePane("A{$dataStart}");

                // 7. Auto filter di baris header
                $sheet->setAutoFilter("A{$headerRow}:{$last}{$headerRow}");

                // 8. Row heights
                $sheet->getRowDimension(self::ROW_TITLE)->setRowHeight(28);
                $sheet->getRowDimension(self::ROW_INFO)->setRowHeight(16);
                $sheet->getRowDimension($headerRow)->setRowHeight(24);
                for ($r = $dataStart; $r <= $lastDataRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(20);
                }

                // 9. Font seluruh sheet
                $sheet->getStyle("A1:{$last}{$lastDataRow}")
                    ->getFont()
                    ->setName('Segoe UI')
                    ->setSize(10);

                // 10. Print setup
                $sheet->getPageSetup()
                    ->setOrientation(
                        \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                    )
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
            },
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Tulis baris 1 (title) dan baris 2 (info sesi).
     * insertNewRowBefore sudah dipanggil di registerEvents — tidak perlu di sini.
     */
    private function _writeMeta(Worksheet $sheet): void
    {
        $last = self::LAST_COL;

        // Baris 1 — judul
        $sheet->mergeCells("A1:{$last}1");
        $sheet->setCellValue('A1', $this->meta['title'] ?? 'Laporan Presensi Siswa');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['argb' => 'FF' . self::COLOR_HEADER_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Baris 2 — info sesi
        $sheet->mergeCells("A2:{$last}2");
        $sheet->setCellValue('A2', $this->_buildInfoString());
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size'   => 9,
                'italic' => true,
                'color'  => ['argb' => 'FF64748B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFBFDBFE'],
                ],
            ],
        ]);
    }

    private function _buildInfoString(): string
    {
        $parts = [];

        if (!empty($this->meta['grade']))   $parts[] = "Kelas: {$this->meta['grade']}";
        if (!empty($this->meta['subject'])) $parts[] = "Mapel: {$this->meta['subject']}";
        if (!empty($this->meta['date']))    $parts[] = "Tanggal: {$this->meta['date']}";
        if (!empty($this->meta['meeting'])) $parts[] = "Pertemuan ke-{$this->meta['meeting']}";
        if (!empty($this->meta['teacher'])) $parts[] = "Guru: {$this->meta['teacher']}";

        $parts[] = 'Dicetak: ' . now()->translatedFormat('d F Y H:i');

        return implode('   ·   ', $parts);
    }

    /**
     * Warna baris berdasarkan status di kolom D.
     *
     * @param  int  $dataStart  — row pertama data (setelah insert meta)
     */
    private function _colorRows(Worksheet $sheet, int $dataRows, int $dataStart): void
    {
        $last = self::LAST_COL;

        $statusColorMap = [
            'Hadir'     => self::COLOR_PRESENT,
            'Izin'      => self::COLOR_PERMISSION,
            'Sakit'     => self::COLOR_SICK,
            'Alpha'     => self::COLOR_ABSENT,
            'Terlambat' => self::COLOR_LATE,
        ];

        for ($i = 0; $i < $dataRows; $i++) {
            $row    = $dataStart + $i; // pakai $dataStart, bukan hardcode
            $status = $sheet->getCell("D{$row}")->getValue();
            $color  = $statusColorMap[$status]
                      ?? ($i % 2 === 0 ? 'FFFFFF' : self::COLOR_ROW_ALT);

            $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF' . $color],
                ],
            ]);
        }
    }
}