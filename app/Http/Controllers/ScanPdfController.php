<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;
use mikehaertl\pdftk\Pdf as PdftkPdf;

use TCPDF;

class ScanPdfController extends Controller
{
    public function index()
    {
        $parser = new Parser();
        $pdf = $parser->parseFile('pdf/main/221024298437.pdf'); // Reemplaza con tu archivo
        $text = $pdf->getText();

        // Separar los comprobantes usando "COMPROBANTE" como delimitador
        $comprobantes = explode("COMPROBANTE", $text);
        $datosFinales = [];

        foreach ($comprobantes as $comprobante) {
            if (trim($comprobante) == "") continue; // Saltar bloques vacíos

            // Extraer la fecha (formato: 22-Oct-2024)
            preg_match('/(\d{2}-[A-Za-z]{3}-\d{4})/', $comprobante, $fecha);
            // Extraer la hora (formato: 01:04:16)
            preg_match('/(\d{2}:\d{2}:\d{2})/', $comprobante, $hora);

            // Combinar la fecha y la hora si existen
            $fecha_formateada = isset($fecha[1]) ? $fecha[1] : '';
            if (isset($hora[1])) {
                $fecha_formateada .= ' / ' . $hora[1] . ' HRS';
            }

            // Extraer el resto de los datos
            preg_match('/Número de convenio\s*(\d+)/', $comprobante, $numero_convenio);
            preg_match('/Nombre de la empresa\s*([\w\s]+?)(?=\s*Divisa del convenio)/', $comprobante, $empresa);
            preg_match('/Cuenta de cargo\s*(\d+)/', $comprobante, $cuenta_cargo);
            preg_match('/Divisa del convenio\s*([\w]+)/', $comprobante, $divisa);
            preg_match('/Código de operación\s*([\w\s\(\)]+?)(?=\s*Clave del Proveedor)/', $comprobante, $codigo_operacion);
            preg_match('/Nombre del proveedor\s*([\w\s]+?)(?=\s*Cuenta del proveedor)/', $comprobante, $proveedor);
            preg_match('/Referencia\s*([\w\d]+)/', $comprobante, $referencia);
            preg_match('/Fecha de alta\s*(\d{2}\/\d{2}\/\d{4})/', $comprobante, $fecha_alta);
            preg_match('/Tipo de confirmación\s*([\w]+)/', $comprobante, $tipo_confirmacion);
            preg_match('/Estatus del pago\s*([\w]+)/', $comprobante, $estatus_pago);
            preg_match('/Clave del Proveedor\s*(\d+)/', $comprobante, $clave_proveedor);
            preg_match('/Cuenta del proveedor\s*(\d+)/', $comprobante, $cuenta_proveedor);
            preg_match('/Importe del pago\s*([\d\.MXP]+)/', $comprobante, $importe);
            preg_match('/Fecha de operación\s*(\d{2}\/\d{2}\/\d{4})/', $comprobante, $fecha_operacion);
            preg_match('/Número de operación\s*(\d+)/', $comprobante, $numero_operacion);

            preg_match('/Referencia numérica\s*([\w\d]+)/', $comprobante, $referencia_numerica);
            preg_match('/Hora de procesamiento\s*(\d{2}:\d{2}:\d{2})/', $comprobante, $hora_procesamiento);
            preg_match('/Concepto de pago\s*(.+?)(?=\s*(?:Referencia numérica|Hora de procesamiento|Fecha de alta|$))/s', $comprobante, $concepto_pago);
            preg_match('/Clave de rastreo\s*([\w\-]+)/', $comprobante, $clave_rastreo);

            $datosFinales[] = [
                'fecha'             => $fecha_formateada,
                'Número de convenio'   => $numero_convenio[1] ?? '',
                'Nombre de la empresa'           => trim($empresa[1] ?? ''),
                'Cuenta de cargo'      => $cuenta_cargo[1] ?? '',
                'Divisa del convenio'            => $divisa[1] ?? '',
                'Código de operación'  => trim($codigo_operacion[1] ?? ''),
                'Nombre del proveedor'         => trim($proveedor[1] ?? ''),
                'Referencia'        => $referencia[1] ?? '',
                'Fecha de alta'        => $fecha_alta[1] ?? '',
                'Tipo de confirmación' => $tipo_confirmacion[1] ?? '',
                'Estatus del pago'      => $estatus_pago[1] ?? '',
                'Clave del Proveedor'   => $clave_proveedor[1] ?? '',
                'Cuenta del proveedor'  => $cuenta_proveedor[1] ?? '',
                'Importe del pago'           => $importe[1] ?? '',
                'Fecha de operación'   => $fecha_operacion[1] ?? '',
                'Número de operación'  => $numero_operacion[1] ?? '',

                'Referencia numérica' => $referencia_numerica[1] ?? '',
                'Hora de procesamiento'  => $hora_procesamiento[1] ?? '',
                'Concepto de pago'      => trim($concepto_pago[1] ?? ''),
                'Clave de rastreo'      => $clave_rastreo[1] ?? '',
            ];
        }

        // Ruta donde se guardarán los PDFs dentro del storage de Laravel
        $rutaDestino = storage_path('app/pdfs');

        // Verificamos si la carpeta existe, si no, la creamos
        if (!file_exists($rutaDestino)) {
            mkdir($rutaDestino, 0755, true);
        }

        foreach ($datosFinales as $indice => $data) {
            // Creamos una instancia de Pdf utilizando el molde (template) del PDF
            $pdf = new PdftkPdf('pdf/molde/molde.pdf');
            $pdf->fillForm($data);

            // Generamos un nombre único para el PDF (puedes basarlo en un ID, nombre o un índice)
            $nombreArchivo = "pdf_{$indice}_" . time() . ".pdf";
            $rutaCompleta = $rutaDestino . '/' . $nombreArchivo;

            // Guardamos el PDF en la ruta indicada
            if ($pdf->saveAs($rutaCompleta)) {
                echo "PDF generado: {$nombreArchivo}<br>";
            } else {
                echo "Error al generar el PDF para la persona con índice {$indice}.<br>";
            }
        }

        return "Proceso completado.";


        // Imprimir el resultado en formato JSON para visualizarlo mejor
        return response()->json($datosFinales);
    }
}
