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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/*
|==========================================================================
| ScoreReportExport
|
| Entry point — memilih mode export:
|   'summary'  → satu sheet (rekap per siswa)
|   'sessions' → multi-sheet (Harian | UTS | UAS)
|==========================================================================
*/

class ScoreReportExport implements WithMultipleSheets
{
    public function __construct(
        protected Collection|array $rows,
        protected array            $meta        = [],
        protected string           $exportType  = 'summary',
    ) {}

    public function sheets(): array
    {
        if ($this->exportType === 'sessions') {
            $sheets = [];

            $typeMap = [
                'daily'      => 'Harian',
                'assignment' => 'Tugas',
                'mid_exam'   => 'UTS',
                'final_exam' => 'UAS',
            ];

            foreach ($typeMap as $typeKey => $typeLabel) {
                $typeRows = $this->rows[$typeKey] ?? collect();
                if ($typeRows->isNotEmpty()) {
                    $sheets[] = new ScoreSessionSheet($typeRows, $this->meta, $typeKey);
                }
            }

            return $sheets ?: [new ScoreSummarySheet(collect(), $this->meta)];
        }

        return [new ScoreSummarySheet($this->rows, $this->meta)];
    }
}

/*
|==========================================================================
| ScoreSummarySheet — Rekap Per Siswa
|==========================================================================
*/

class ScoreSummarySheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    private const COLOR_HEADER_BG   = '1E3A8A';
    private const COLOR_HEADER_FONT = 'FFFFFF';
    private const COLOR_ROW_ALT     = 'EFF6FF';
    private const COLOR_BELOW_KKM   = 'FEE2E2';
    private const COLOR_PASS_KKM    = 'ECFDF5';
    private const LAST_COL          = 'I';

    private const ROW_TITLE      = 1;
    private const ROW_INFO       = 2;
    private const ROW_HEADER     = 3;
    private const ROW_DATA_START = 4;

    private int $rowNumber = 0;

    public function __construct(
        protected Collection $rows,
        protected array      $meta = [],
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Harian (avg)',
            'UTS',
            'UAS',
            'Nilai Akhir',
            'KKM',
            'Status KKM',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['nis'],
            $row['full_name'],
            $row['avg_harian'],
            $row['avg_uts'],
            $row['avg_uas'],
            $row['final_score'],
            $row['kkm'],
            $row['kkm_status'],
        ];
    }

    public function title(): string
    {
        return 'Rekap Per Siswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16,
            'C' => 30,
            'D' => 14,
            'E' => 12,
            'F' => 12,
            'G' => 14,
            'H' => 10,
            'I' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $last     = self::LAST_COL;
                $dataRows = $this->rows->count();

                $sheet->insertNewRowBefore(1, 2);

                $headerRow   = self::ROW_HEADER;
                $dataStart   = self::ROW_DATA_START;
                $lastDataRow = $dataStart + $dataRows - 1;

                $this->_writeMeta($sheet);
                $this->_styleHeader($sheet, $headerRow, $last);
                $this->_styleBordersAndAlignment($sheet, $headerRow, $last, $lastDataRow, $dataStart);
                $this->_centerColumns($sheet, ['A', 'D', 'E', 'F', 'G', 'H', 'I'], $dataStart, $lastDataRow);
                $this->_colorRows($sheet, $dataRows, $dataStart, $last);

                $sheet->freezePane("A{$dataStart}");
                $sheet->setAutoFilter("A{$headerRow}:{$last}{$headerRow}");

                $this->_setRowHeights($sheet, $headerRow, $dataStart, $lastDataRow);
                $this->_setFont($sheet, $last, $lastDataRow);
                $this->_setPrintSetup($sheet);
            },
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    private function _writeMeta(Worksheet $sheet): void
    {
        $last = self::LAST_COL;

        // Baris 1 — judul
        $sheet->mergeCells("A1:{$last}1");
        $sheet->setCellValue('A1', $this->meta['title'] ?? 'Laporan Nilai Siswa');
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

        // Baris 2 — info
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

        if (!empty($this->meta['grade']))      $parts[] = "Kelas: {$this->meta['grade']}";
        if (!empty($this->meta['subject']))    $parts[] = "Mapel: {$this->meta['subject']}";
        if (!empty($this->meta['semester']))   $parts[] = "Semester: {$this->meta['semester']}";
        if (!empty($this->meta['score_type'])) $parts[] = "Tipe: {$this->meta['score_type']}";
        if (!empty($this->meta['kkm']))        $parts[] = "KKM: {$this->meta['kkm']}";
        if (!empty($this->meta['teacher']))    $parts[] = "Guru: {$this->meta['teacher']}";

        $parts[] = 'Dicetak: ' . now()->translatedFormat('d F Y H:i');

        return implode('   ·   ', $parts);
    }

    private function _styleHeader(Worksheet $sheet, int $headerRow, string $last): void
    {
        $sheet->getStyle("A{$headerRow}:{$last}{$headerRow}")->applyFromArray([
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
    }

    private function _styleBordersAndAlignment(
        Worksheet $sheet,
        int $headerRow,
        string $last,
        int $lastDataRow,
        int $dataStart,
    ): void {
        $sheet->getStyle("A{$headerRow}:{$last}{$lastDataRow}")->applyFromArray([
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
    }

    private function _centerColumns(Worksheet $sheet, array $cols, int $dataStart, int $lastDataRow): void
    {
        foreach ($cols as $col) {
            $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastDataRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function _colorRows(Worksheet $sheet, int $dataRows, int $dataStart, string $last): void
    {
        for ($i = 0; $i < $dataRows; $i++) {
            $row    = $dataStart + $i;
            $status = $sheet->getCell("I{$row}")->getValue(); // kolom Status KKM

            $color = match (true) {
                str_contains((string) $status, 'bawah') => self::COLOR_BELOW_KKM,
                str_contains((string) $status, 'Lulus') => self::COLOR_PASS_KKM,
                default                                  => $i % 2 === 0 ? 'FFFFFF' : self::COLOR_ROW_ALT,
            };

            $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF' . $color],
                ],
            ]);
        }
    }

    private function _setRowHeights(Worksheet $sheet, int $headerRow, int $dataStart, int $lastDataRow): void
    {
        $sheet->getRowDimension(self::ROW_TITLE)->setRowHeight(28);
        $sheet->getRowDimension(self::ROW_INFO)->setRowHeight(16);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);
        for ($r = $dataStart; $r <= $lastDataRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(20);
        }
    }

    private function _setFont(Worksheet $sheet, string $last, int $lastDataRow): void
    {
        $sheet->getStyle("A1:{$last}{$lastDataRow}")
            ->getFont()
            ->setName('Segoe UI')
            ->setSize(10);
    }

    private function _setPrintSetup(Worksheet $sheet): void
    {
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
    }
}

/*
|==========================================================================
| ScoreSessionSheet — Daftar Sesi per Tipe (Harian / UTS / UAS)
| Satu instance per tipe nilai, dipakai untuk multi-sheet export
|==========================================================================
*/

class ScoreSessionSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    private const COLOR_HEADER_BG   = '1E3A8A';
    private const COLOR_HEADER_FONT = 'FFFFFF';
    private const COLOR_ROW_ALT     = 'EFF6FF';
    private const LAST_COL          = 'G';

    private const ROW_TITLE      = 1;
    private const ROW_INFO       = 2;
    private const ROW_HEADER     = 3;
    private const ROW_DATA_START = 4;

    private int $rowNumber = 0;

    public function __construct(
        protected Collection $sessions,
        protected array      $meta      = [],
        protected string     $scoreType = 'harian',
    ) {}

    public function collection(): Collection
    {
        // Flatten: 1 row per student per session
        return $this->sessions->flatMap(function ($session) {
            return $session->scoreDetails->map(function ($detail) use ($session) {
                return (object) [
                    'score_date'  => $session->score_date?->format('d/m/Y') ?? '-',
                    'title'       => $session->title,
                    'score_type'  => strtoupper($session->score_type),
                    'full_name'   => $detail->student?->full_name ?? '-',
                    'nis'         => $detail->student?->nis        ?? '-',
                    'score'       => $detail->score,
                    'max_score'   => $detail->max_score,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Judul Sesi',
            'Tipe',
            'Nama Siswa',
            'NIS',
            'Nilai',
            'Nilai Max',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $row->score_date,
            $row->title,
            $row->score_type,
            $row->full_name,
            $row->nis,
            $row->score,
            $row->max_score,
        ];
    }

    public function title(): string
    {
        return match ($this->scoreType) {
            'mid_exam'   => 'UTS',
            'final_exam' => 'UAS',
            'assignment' => 'Tugas',
            default      => 'Harian',
        };
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 28,
            'C' => 10,
            'D' => 30,
            'E' => 16,
            'F' => 10,
            'G' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $last     = self::LAST_COL;
                $dataRows = $this->collection()->count();

                $sheet->insertNewRowBefore(1, 2);

                $headerRow   = self::ROW_HEADER;
                $dataStart   = self::ROW_DATA_START;
                $lastDataRow = $dataStart + $dataRows - 1;

                $this->_writeMeta($sheet);

                // Header style
                $sheet->getStyle("A{$headerRow}:{$last}{$headerRow}")->applyFromArray([
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

                // Border seluruh tabel
                $sheet->getStyle("A{$headerRow}:{$last}{$lastDataRow}")->applyFromArray([
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

                // Center: Tanggal, Tipe, NIS, Nilai, Nilai Max
                foreach (['A', 'C', 'E', 'F', 'G'] as $col) {
                    $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastDataRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Alternating row colors
                for ($i = 0; $i < $dataRows; $i++) {
                    $row   = $dataStart + $i;
                    $color = $i % 2 === 0 ? 'FFFFFF' : self::COLOR_ROW_ALT;
                    $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF' . $color],
                        ],
                    ]);
                }

                $sheet->freezePane("A{$dataStart}");
                $sheet->setAutoFilter("A{$headerRow}:{$last}{$headerRow}");

                // Row heights
                $sheet->getRowDimension(self::ROW_TITLE)->setRowHeight(28);
                $sheet->getRowDimension(self::ROW_INFO)->setRowHeight(16);
                $sheet->getRowDimension($headerRow)->setRowHeight(24);
                for ($r = $dataStart; $r <= $lastDataRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(20);
                }

                // Font
                $sheet->getStyle("A1:{$last}{$lastDataRow}")
                    ->getFont()->setName('Segoe UI')->setSize(10);

                // Print
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
            },
        ];
    }

    private function _writeMeta(Worksheet $sheet): void
    {
        $last      = self::LAST_COL;
        $typeLabel = match ($this->scoreType) {
            'mid_exam'   => 'UTS',
            'final_exam' => 'UAS',
            'assignment' => 'Tugas',
            default      => 'Harian',
        };

        $sheet->mergeCells("A1:{$last}1");
        $sheet->setCellValue('A1', ($this->meta['title'] ?? 'Laporan Nilai') . " — {$typeLabel}");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['argb' => 'FF1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells("A2:{$last}2");
        $sheet->setCellValue('A2', $this->_buildInfoString($typeLabel));
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

    private function _buildInfoString(string $typeLabel): string
    {
        $parts = [];

        if (!empty($this->meta['grade']))    $parts[] = "Kelas: {$this->meta['grade']}";
        if (!empty($this->meta['subject']))  $parts[] = "Mapel: {$this->meta['subject']}";
        if (!empty($this->meta['semester'])) $parts[] = "Semester: {$this->meta['semester']}";
        $parts[] = "Tipe: {$typeLabel}";
        if (!empty($this->meta['kkm']))      $parts[] = "KKM: {$this->meta['kkm']}";
        if (!empty($this->meta['teacher']))  $parts[] = "Guru: {$this->meta['teacher']}";
        $parts[] = 'Dicetak: ' . now()->translatedFormat('d F Y H:i');

        return implode('   ·   ', $parts);
    }
}