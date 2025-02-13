<?php

use App\Http\Controllers\ScanPdfController;
use Illuminate\Support\Facades\Route;
use mikehaertl\pdftk\Pdf;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('pdf', [App\Http\Controllers\ScanPdfController::class, 'index']);

Route::get('/greeting', [ScanPdfController::class, 'index']);

Route::get('/data', function () {
    $pdf = new Pdf('pdf/molde/molde.pdf');

    $data = $pdf->getData();
    if ($data === false) {
        $error = $pdf->getError();
    }

    // Get form data fields
    $pdf = new Pdf('pdf/molde/molde.pdf');
    $data = $pdf->getDataFields();
    if ($data === false) {
        $error = $pdf->getError();
    }

    dd($data);
});

// Route::get('/super', function () {
//     $pdf = new Pdf('pdf/molde/molde.pdf');
//     $data = [
//         "Número de convenio" => "02330962",
//         "Cuenta de cargo" => "0121442716",
//         "Nombre de la empresa" => "GALGA CAPITAL SAPI DE CV",
//         "Divisa del convenio" => "MXP",
//         "Código de operación" => "ABONO A CUENTAS INTERBANCARIA (6)",
//         "Clave del Proveedor" => "014",
//         "Nombre del proveedor" => "ANAKAREN PRECIADO ESPINOZA",
//         "Cuenta del proveedor" => "002320700750125925",
//         "Referencia" => "INTERESANAKARENPE",
//         "Referencia numérica" => "18102024",
//         "Importe del pago" => "0.01 MXP",
//         "Concepto de pago" => "INTERES ANA LILIA FM",
//         "Fecha de operación" => "21/10/2024",
//         "Fecha de alta" => "21/10/2024",
//         "Hora de procesamiento" => "14:07:26",
//         "Tipo de confirmación" => "MAIL",
//         "Estatus del pago" => "PAGADO",
//         "Número de operación" => "014065179",
//         "Clave de rastreo" => "CIE-0100241021826595",
//         "fecha" => "22-Oct-2024 / 01:04:16 HRS",
//     ];

//     $pdf->fillForm($data);
//     return $pdf->send();
// });

Route::get('/super', function () {
    // Supongamos que tienes un arreglo con los datos de varias personas
    $personas = [
        [
            "Número de convenio"      => "02330962",
            "Cuenta de cargo"         => "0121442716",
            "Nombre de la empresa"    => "GALGA CAPITAL SAPI DE CV",
            "Divisa del convenio"     => "MXP",
            "Código de operación"     => "ABONO A CUENTAS INTERBANCARIA (6)",
            "Clave del Proveedor"     => "014",
            "Nombre del proveedor"    => "ANAKAREN PRECIADO ESPINOZA",
            "Cuenta del proveedor"    => "002320700750125925",
            "Referencia"              => "INTERESANAKARENPE",
            "Referencia numérica"     => "18102024",
            "Importe del pago"        => "0.01 MXP",
            "Concepto de pago"        => "INTERES ANA LILIA FM",
            "Fecha de operación"      => "21/10/2024",
            "Fecha de alta"           => "21/10/2024",
            "Hora de procesamiento"   => "14:07:26",
            "Tipo de confirmación"    => "MAIL",
            "Estatus del pago"        => "PAGADO",
            "Número de operación"     => "014065179",
            "Clave de rastreo"        => "CIE-0100241021826595",
            "fecha"                   => "22-Oct-2024 / 01:04:16 HRS",
        ],
        [
            "Número de convenio"      => "02330963",
            "Cuenta de cargo"         => "0121442717",
            "Nombre de la empresa"    => "EMPRESA EJEMPLO S.A.",
            "Divisa del convenio"     => "MXP",
            "Código de operación"     => "ABONO A CUENTAS INTERBANCARIA (6)",
            "Clave del Proveedor"     => "015",
            "Nombre del proveedor"    => "PROVEEDOR EJEMPLO",
            "Cuenta del proveedor"    => "002320700750125926",
            "Referencia"              => "INTERESPROVEEDOREJEMPLO",
            "Referencia numérica"     => "18102025",
            "Importe del pago"        => "0.02 MXP",
            "Concepto de pago"        => "INTERES PROVEEDOR EJEMPLO",
            "Fecha de operación"      => "21/10/2024",
            "Fecha de alta"           => "21/10/2024",
            "Hora de procesamiento"   => "14:07:27",
            "Tipo de confirmación"    => "MAIL",
            "Estatus del pago"        => "PAGADO",
            "Número de operación"     => "014065180",
            "Clave de rastreo"        => "CIE-0100241021826596",
            "fecha"                   => "22-Oct-2024 / 01:04:17 HRS",
        ],
    ];

    // Ruta donde se guardarán los PDFs dentro del storage de Laravel
    $rutaDestino = storage_path('app/pdfs');

    // Verificamos si la carpeta existe, si no, la creamos
    if (!file_exists($rutaDestino)) {
        mkdir($rutaDestino, 0755, true);
    }

    foreach ($personas as $indice => $data) {
        // Creamos una instancia de Pdf utilizando el molde (template) del PDF
        $pdf = new Pdf('pdf/molde/molde.pdf');
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
});
