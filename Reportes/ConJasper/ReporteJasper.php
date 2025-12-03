<?php
$javaPath = __DIR__ . '/../../.java/jdk-8/bin/java';
// Incluir la clase JasperPHP
require_once 'JasperPHP.php';

try {
    // Inicializar instancia
    $jasper = new JasperPHP();
    
    // Configurar credenciales de base de datos
    $jasper->setDbConfig(
        user: 'root',
        pass: '',
        host: 'localhost',
        name: 'cuartouta',
        port: 3306,
        driver: 'com.mysql.cj.jdbc.Driver',
        jdbcDir: 'lib'
    );

    // Rutas de entrada y salida
    $input = '../../Reportes/ConJasper/plantillas/Reporte_Estudiante.jrxml';
    $output = '../../Reportes/ConJasper/reportes_salida/Reporte_Estudiante';

    // Procesar el reporte
    if ($jasper->process($input, $output)) {
        // Si el PDF se generó correctamente
        $pdfFile = $output . '.pdf';
        
        if (file_exists($pdfFile)) {
            // Enviar al navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($pdfFile) . '"');
            header('Content-Length: ' . filesize($pdfFile));
            readfile($pdfFile);
            exit;
        } else {
            echo '<div style="background:#fff3cd;border:1px solid #ffc107;padding:15px;border-radius:4px;margin:20px;">';
            echo '<h3>⚠️ Error: PDF no generado</h3>';
            echo '<p>El reporte se procesó pero el PDF no se encontró en: ' . $pdfFile . '</p>';
            echo '</div>';
        }
    } else {
        // Si hubo error en la generación
        echo '<div style="background:#f8d7da;border:1px solid #f5c6cb;padding:15px;border-radius:4px;margin:20px;font-family:monospace;color:#721c24;">';
        echo '<h3>❌ Error al generar el reporte</h3>';
        echo '<p style="margin-bottom:10px;"><strong>Comando ejecutado:</strong></p>';
        echo '<pre style="background:#fff;padding:10px;border-left:3px solid #f5c6cb;overflow-x:auto;">' . 
             htmlspecialchars($jasper->getLastCommand()) . '</pre>';
        echo '<p style="margin-top:10px;"><strong>Salida del sistema:</strong></p>';
        echo '<pre style="background:#fff;padding:10px;border-left:3px solid #f5c6cb;overflow-x:auto;">' . 
             htmlspecialchars($jasper->getLastOutput()) . '</pre>';
        echo '</div>';

        // Sugerencias de solución
        echo '<div style="background:#d1ecf1;border:1px solid #bee5eb;padding:15px;border-radius:4px;margin:20px;color:#0c5460;">';
        echo '<h4>📋 Checklist de solución:</h4>';
        echo '<ul>';
        echo '<li>✓ Verifica que Java esté instalado: ejecuta <code>java -version</code> en CMD</li>';
        echo '<li>✓ Revisa que <code>jasperstarter.jar</code> existe en la carpeta <code>lib/</code></li>';
        echo '<li>✓ Descarga el MySQL JDBC Driver (mysql-connector-java.jar) en la carpeta <code>lib/</code></li>';
        echo '<li>✓ Asegúrate que la plantilla existe en: <code>' . $input . '</code></li>';
        echo '<li>✓ Verifica que la carpeta <code>reportes_salida/</code> tenga permisos de escritura</li>';
        echo '<li>✓ Comprueba la conexión a la BD: usuario, contraseña, nombre de BD</li>';
        echo '</ul>';
        echo '</div>';
    }

} catch (Exception $e) {
    echo '<div style="background:#f8d7da;border:1px solid #f5c6cb;padding:15px;border-radius:4px;margin:20px;color:#721c24;">';
    echo '<h3>❌ Excepción: ' . htmlspecialchars($e->getMessage()) . '</h3>';
    echo '</div>';
}

?>