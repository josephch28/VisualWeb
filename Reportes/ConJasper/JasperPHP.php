<?php

class JasperPHP {
    
    private string $javaPath;
    private string $jasperStarterJar;
    private string $jdbcDir;
    private string $mainClass;
    private string $dbUser = '';
    private string $dbPass = '';
    private string $dbHost = '';
    private string $dbName = '';
    private int $dbPort = 3306;
    private string $dbDriver = 'com.mysql.cj.jdbc.Driver';
    private ?string $lastCommand = null;
    private ?string $lastOutput = null;

    public function __construct() {
        // Detectar ruta Java automáticamente
        $this->javaPath = $this->findJavaPath();
        
        if ($this->javaPath === null) {
            throw new Exception('Java no encontrado en el sistema. Asegúrate de tener Java instalado.');
        }

        // Rutas relativas al proyecto
        $this->jasperStarterJar = __DIR__ . '/lib/jasperstarter.jar';
        $this->jdbcDir = __DIR__ . '/lib';
        $this->mainClass = 'de.cenote.jasperstarter.App';
    }

    /**
     * Busca la ruta de Java en el sistema
     */
    private function findJavaPath(): ?string {
        // En Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Primero intenta usar Java 8 local del proyecto
            $localJava = __DIR__ . '/../../.java/jdk-8/bin/java.exe';
            if (file_exists($localJava)) {
                return '"' . $localJava . '"';
            }
            
            $javaExe = exec('where java 2>nul', $output, $returnCode);
            if ($returnCode === 0 && !empty($javaExe)) {
                return '"' . trim($javaExe) . '"';
            }
            // Fallback a rutas comunes en Windows
            $commonPaths = [
                'C:\\Program Files\\Java\\jdk*/bin/java.exe',
                'C:\\Program Files (x86)\\Java\\jdk*/bin/java.exe',
                'C:\\Program Files\\Java\\jre*/bin/java.exe',
            ];
            foreach ($commonPaths as $pattern) {
                $matches = glob($pattern);
                if (!empty($matches)) {
                    return '"' . $matches[0] . '"';
                }
            }
        } else {
            // En Linux/Mac
            // Primero intenta usar Java 8 local del proyecto
            $localJava = __DIR__ . '/../../.java/jdk-8/bin/java';
            if (file_exists($localJava)) {
                return $localJava;
            }
            
            $javaPath = exec('which java 2>/dev/null', $output, $returnCode);
            if ($returnCode === 0 && !empty($javaPath)) {
                return $javaPath;
            }
        }
        
        return null;
    }

    /**
     * Configurar credenciales de base de datos
     */
    public function setDbConfig(
        string $user,
        string $pass,
        string $host,
        string $name,
        int $port = 3306,
        string $driver = 'com.mysql.cj.jdbc.Driver',
        string $jdbcDir = null
    ): self {
        $this->dbUser = $user;
        $this->dbPass = $pass;
        $this->dbHost = $host;
        $this->dbName = $name;
        $this->dbPort = $port;
        $this->dbDriver = $driver;

        if ($jdbcDir !== null) {
            $this->jdbcDir = $jdbcDir;
        }

        return $this;
    }

    /**
     * Procesar reporte y generar PDF
     */
    public function process(
        string $input,
        string $output,
        array $formats = ['pdf'],
        array $parameters = []
    ): bool {
        // Validar que el archivo de entrada existe
        if (!file_exists($input)) {
            $this->lastOutput = "Error: El archivo de plantilla no existe: $input";
            return false;
        }

        // Validar que jasperstarter.jar existe
        if (!file_exists($this->jasperStarterJar)) {
            $this->lastOutput = "Error: jasperstarter.jar no encontrado en: {$this->jasperStarterJar}";
            return false;
        }

        // Validar que jdbc-dir existe
        if (!is_dir($this->jdbcDir)) {
            $this->lastOutput = "Error: Directorio JDBC no encontrado: {$this->jdbcDir}";
            return false;
        }

        // Crear directorio de salida si no existe
        $outputDir = dirname($output);
        if (!is_dir($outputDir)) {
            if (!mkdir($outputDir, 0755, true)) {
                $this->lastOutput = "Error: No se pudo crear el directorio de salida: $outputDir";
                return false;
            }
        }

        // Detectar separador de classpath según el SO
        $classpathSeparator = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? ';' : ':';
        
        // Construir el classpath incluyendo el JDBC driver
        $classpath = $this->jasperStarterJar . $classpathSeparator . $this->jdbcDir . '/*';

        // Construir el comando
        $formatsStr = implode(',', $formats);
        $cmd = $this->javaPath
            . ' -cp "' . $classpath . '"'
            . ' ' . $this->mainClass
            . ' process'
            . ' "' . $input . '"'
            . ' -o "' . $output . '"'
            . ' -f ' . $formatsStr
            . ' -t mysql'
            . ' -u "' . $this->dbUser . '"';

        if (!empty($this->dbPass)) {
            $cmd .= ' -p "' . $this->dbPass . '"';
        }

        $cmd .= ' -H "' . $this->dbHost . '"'
            . ' -n "' . $this->dbName . '"'
            . ' --db-port ' . $this->dbPort
            . ' --db-driver "' . $this->dbDriver . '"'
            . ' --jdbc-dir "' . $this->jdbcDir . '"';

        // Agregar parámetros del reporte
        foreach ($parameters as $key => $value) {
            $escapedValue = str_replace('"', '\\"', $value);
            $cmd .= ' -P ' . $key . '="' . $escapedValue . '"';
        }

        // Ejecutar comando
        $this->lastCommand = $cmd;
        $this->lastOutput = shell_exec($cmd . ' 2>&1');

        // Verificar que el PDF se generó correctamente
        $pdfFile = $output . '.pdf';
        if (file_exists($pdfFile) && filesize($pdfFile) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Obtener el último comando ejecutado (para debugging)
     */
    public function getLastCommand(): ?string {
        return $this->lastCommand;
    }

    /**
     * Obtener la salida del último comando (mensajes de error)
     */
    public function getLastOutput(): ?string {
        return $this->lastOutput;
    }
}
?>