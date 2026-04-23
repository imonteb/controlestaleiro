<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class ImportTemplateController extends Controller
{
    private array $templates = [
        'colaboradores' => [
            'filename' => 'modelo_colaboradores.xlsx',
            'headers' => ['numero_colaborador', 'nome', 'apelido', 'designacao_cargo', 'telefone'],
            'example' => ['C-001', 'João', 'Silva', 'Eletricista', '+351 912 000 000'],
        ],
        'veiculos' => [
            'filename' => 'modelo_veiculos.xlsx',
            'headers' => ['matricula', 'marca', 'modelo'],
            'example' => ['MA-00-AA', 'Ford', 'Transit'],
        ],
        'peps' => [
            'filename' => 'modelo_peps.xlsx',
            'headers' => ['nome', 'localizacao', 'tipo_trabalho'],
            'example' => ['P.016.047/001', 'Funchal', 'BT'],
        ],
        'epis' => [
            'filename' => 'modelo_epis.xlsx',
            'headers' => ['nome', 'codigo', 'tipo', 'tamanho', 'descricao', 'ca', 'unidade', 'riscos', 'stock_minimo'],
            'example' => ['CAPACETE DE PROTEÇÃO', 'EPI-001', 'individual', 'Universal', 'Capacete ABS', 'CA-12345', 'unidade', 'Queda em altura,Projeção de partículas', '5'],
        ],
        'epi_rececoes' => [
            'filename' => 'modelo_recepcoes_epi.xlsx',
            'headers' => ['epi', 'codigo', 'quantidade', 'tamanho', 'data', 'fornecedor', 'fatura', 'observacoes'],
            'example' => ['CAPACETE DE PROTEÇÃO', 'EPI-001', '10', 'M', '2026-03-08', 'Fornecedor ABC', 'FAT-123', 'Lote novo'],
        ],
        'ferramentas' => [
            'filename' => 'modelo_ferramentas.xlsx',
            'headers' => ['designacao', 'referencia', 'marca', 'modelo', 'num_serie', 'estado', 'periodicidade', 'documentacao', 'familia', 'localizacao', 'data_verificacao'],
            'example' => ['Berbequim Percussão', 'FER-001', 'Bosch', 'GBH 2-28', 'SN123456', 'Apto', '12', 'Manual', 'Elétricas', 'Armazém A', '2025-01-10'],
        ],
        'extintores' => [
            'filename' => 'modelo_extintores.xlsx',
            'headers' => ['num_serie', 'tipo', 'tamanho', 'estado', 'data_verificacao', 'proxima_revisao', 'matricula'],
            'example' => ['EXT-9988', 'Pó Químico', '6kg', 'Conforme', '2025-01-10', '2026-01-10', 'AA-00-BB'],
        ],
        'saude_kits' => [
            'filename' => 'modelo_kits_saude.xlsx',
            'headers' => ['designacao', 'identificador', 'matricula'],
            'example' => ['Kit Primeiros Socorros Viatura 1', 'KSA-001', 'BB-11-CC'],
        ],
        'materiais' => [
            'filename' => 'modelo_materiais.xlsx',
            'headers' => ['codigo', 'nome', 'categoria', 'unidade', 'descricao'],
            'example' => ['ELE-001', 'Cabo 2.5mm²', 'Eléctrico', 'MTS', 'Cabo de cobre flexível'],
        ],
    ];

    public function __invoke(Request $request, string $tipo): Response
    {
        abort_if(! isset($this->templates[$tipo]), 404);

        $config = $this->templates[$tipo];
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Header row styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '09143b']],
        ];

        foreach ($config['headers'] as $colIndex => $header) {
            $col = $colIndex + 1;
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->setValue($header);
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $headerRange = 'A1:'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($config['headers'])).'1';
        $sheet->getStyle($headerRange)->applyFromArray($headerStyle);

        // Example row
        foreach ($config['example'] as $colIndex => $value) {
            $sheet->getCellByColumnAndRow($colIndex + 1, 2)->setValue($value);
        }

        $sheet->getStyle('A2:'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($config['example'])).'2')
            ->getFont()->setItalic(true)->getColor()->setRGB('888888');

        $writer = new Xlsx($spreadsheet);
        $filename = $config['filename'];

        // Aseguramos que no haya nada en el buffer que pueda corromper el binario
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Usar un archivo temporal con extensión .xlsx ayuda a la detección de formato
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $tempFileXlsx = $tempFile.'.xlsx';

        $writer->save($tempFileXlsx);

        return response()->download($tempFileXlsx, $filename)->deleteFileAfterSend(true);
    }
}
