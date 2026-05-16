<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Expense;
use App\Models\Transaction;
use DateTime;
use Illuminate\Http\Request;
use ZipArchive;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);

        $incomeChart  = [];
        $expenseChart = [];
        $labels       = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = DateTime::createFromFormat('!m', $m)->format('M');

            $incomeChart[] = (float) Transaction::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total');

            $expenseChart[] = (float) Expense::where('branch_id', $branchId)
                ->where('status', 'verified')
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $m)
                ->sum('amount');
        }

        $totalIncome = Transaction::where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total');

        $totalExpense = Expense::where('branch_id', $branchId)
            ->where('status', 'verified')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $totalProfit      = $totalIncome - $totalExpense;
        $totalTransaction = Transaction::where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $transactions = Transaction::where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('kasir', 'items')
            ->latest()
            ->get();

        $expenseByCategory = Expense::where('branch_id', $branchId)
            ->where('status', 'verified')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $criticalStocks = BranchStock::where('branch_id', $branchId)
            ->where('stock', '<=', 5)
            ->whereHas('menu', fn ($query) => $query->where('stock_type', 'kuantitas_jadi'))
            ->with('menu')
            ->get();

        return view('admin.reports.index', compact(
            'month',
            'year',
            'branchId',
            'labels',
            'incomeChart',
            'expenseChart',
            'totalIncome',
            'totalExpense',
            'totalProfit',
            'totalTransaction',
            'transactions',
            'expenseByCategory',
            'criticalStocks'
        ));
    }

    public function export(Request $request)
    {
        $branchId  = auth()->user()->branch_id;
        $month     = $request->get('month', now()->month);
        $year      = $request->get('year', now()->year);
        $monthName = DateTime::createFromFormat('!m', $month)->format('F');

        $transactions = Transaction::where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('kasir')
            ->latest()
            ->get();

        $expenses = Expense::where('branch_id', $branchId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with('createdBy')
            ->latest()
            ->get();

        $totalIncome = (float) $transactions->sum('total');
        $totalExpense = (float) $expenses->sum('amount');

        $sheets = [
            'Ringkasan' => [
                ['Laporan ELCO', "{$monthName} {$year}"],
                ['Total Pemasukan', $totalIncome],
                ['Total Pengeluaran', $totalExpense],
                ['Laba Bersih', $totalIncome - $totalExpense],
            ],
            'Pemasukan' => array_merge(
                [['No', 'Invoice', 'Kasir', 'Subtotal', 'Diskon', 'Total', 'Metode Bayar', 'Tanggal']],
                $transactions->values()->map(fn ($trx, $i) => [
                    $i + 1,
                    $trx->invoice_number,
                    $trx->kasir->name ?? '-',
                    (float) $trx->subtotal,
                    (float) $trx->discount_amount,
                    (float) $trx->total,
                    strtoupper($trx->payment_method),
                    $trx->created_at->format('d/m/Y H:i'),
                ])->all()
            ),
            'Pengeluaran' => array_merge(
                [['No', 'Judul', 'Kategori', 'Jumlah', 'Tanggal', 'Status', 'Dicatat Oleh', 'Bukti']],
                $expenses->values()->map(fn ($exp, $i) => [
                    $i + 1,
                    $exp->title,
                    ucfirst($exp->category),
                    (float) $exp->amount,
                    $exp->expense_date->format('d/m/Y'),
                    ucfirst($exp->status),
                    $exp->createdBy->name ?? '-',
                    $exp->receipt ? url('storage/' . $exp->receipt) : '-',
                ])->all()
            ),
        ];

        $path = storage_path("app/Laporan_ELCO_{$monthName}_{$year}.xlsx");
        $this->writeXlsx($path, $sheets);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    private function writeXlsx(string $path, array $sheets): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml(count($sheets)));
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml(array_keys($sheets)));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml(count($sheets)));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        $index = 1;
        foreach ($sheets as $rows) {
            $zip->addFromString("xl/worksheets/sheet{$index}.xml", $this->sheetXml($rows));
            $index++;
        }

        $zip->close();
    }

    private function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $xml .= '<row r="' . ($rowIndex + 1) . '">';
            foreach (array_values($row) as $colIndex => $value) {
                $cell = $this->cellName($colIndex + 1, $rowIndex + 1);
                if (is_numeric($value)) {
                    $xml .= '<c r="' . $cell . '"><v>' . $value . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cell . '" t="inlineStr"><is><t>'
                        . htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8')
                        . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }

        return $xml . '</sheetData></worksheet>';
    }

    private function cellName(int $column, int $row): string
    {
        $name = '';
        while ($column > 0) {
            $mod = ($column - 1) % 26;
            $name = chr(65 + $mod) . $name;
            $column = intdiv($column - $mod, 26);
        }

        return $name . $row;
    }

    private function contentTypesXml(int $sheetCount): string
    {
        $sheets = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $sheets .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $sheets
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(array $sheetNames): string
    {
        $sheets = '';
        foreach ($sheetNames as $index => $name) {
            $sheetId = $index + 1;
            $sheets .= '<sheet name="' . htmlspecialchars($name, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '" sheetId="' . $sheetId . '" r:id="rId' . $sheetId . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheets . '</sheets></workbook>';
    }

    private function workbookRelsXml(int $sheetCount): string
    {
        $rels = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }
        $rels .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rels . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }
}
