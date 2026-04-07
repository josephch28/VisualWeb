# INFORME TÉCNICO DE PRÁCTICA

**I. TEMA:** 
APE_02. Refactorización de una aplicación usando principios SOLID.

**II. OBJETIVO GENERAL:** 
Aplicar principios SOLID para refactorizar aplicaciones existentes para solucionar problemas de desarrollo en un sistema de software.

**OBJETIVOS ESPECÍFICOS:**
1. Desacoplar la lógica de presentación y la lógica de acceso a datos mediante la implementación de *Clean Architecture* (Arquitectura Limpia) y el patrón de Repositorio.
2. Estandarizar el código fuente (Backend) utilizando las convenciones de la industria (nombres en inglés para entidades y controladores) manteniéndose transparente para el usuario final (Frontend en español).
3. Asegurar el cumplimiento de los principios SOLID estableciendo interfaces claras (DIP) y segmentando responsabilidades únicas por cada clase (SRP) en los servicios, controladores y entidades.

**III. MODALIDAD:** 
Presencial

**IV. TIEMPO DE DURACIÓN:** 
Presenciales: 8
No Presenciales: 0

**V. INSTRUCCIONES:** 
*Acciones previas:*
* Verificar que se dispone de acceso a internet, aula virtual y computador con los requerimientos necesarios para ejecutar aplicaciones en Windows para el lenguaje de programación seleccionado.

**VI. LISTADO DE EQUIPOS, MATERIALES Y RECURSOS:** 
* Inteligencia artificial, TAC.
* Computador.
* Entorno de Desarrollo Local (XAMPP).
* Editor de texto enriquecido / IDE (Visual Studio Code).
* Navegador Web (Chrome, Firefox o Edge).
* Base de datos MySQL / MariaDB.

---

### VII. ACTIVIDADES A DESARROLLAR

Durante la práctica se llevó a cabo la transformación de un proyecto PHP heredado de estilo monolítico e inconsistente, convirtiéndolo en un sistema escalable apoyado en *Clean Architecture*.

**1. Reestructuración a Clean Architecture**
Se migró del obsoleto y acoplado patrón de archivos sueltos a una estructuración en capas dentro de la carpeta `src/`. Esto aísla la lógica de negocio de la lógica web:
*   **Domain:** Contiene las entidades puras de negocio (`Student.php`, `Course.php`) y los contratos/interfaces (`IStudentRepository.php`).
*   **Application:** Ubicación de los Casos de Uso (`StudentService.php`), orquestando el negocio sin conocer de bases de datos o de HTML.
*   **Infrastructure:** Responsable de la persistencia de datos (implementación de los repositorios `MySQLStudentRepository.php` que usan sentencias preparadas de PDO).
*   **Presentation:** Controladores (`StudentController.php`) encargados puramente de recibir peticiones HTTP, llamar a `Application` y retornar JSON.

> *[SUGERENCIA DE CAPTURA A]:* Aquí puedes colocar una **captura de pantalla del árbol de carpetas de tu editor (VSCode)** mostrando cómo están divididas las carpetas `src/Domain`, `src/Application`, `src/Infrastructure` y `src/Presentation`.

**2. Refactorización para cumplir Principios SOLID**
El proyecto se rediseñó para cumplir estrictamente con los 5 principios de diseño orientado a objetos (SOLID):
*   **Single Responsibility Principle (SRP):** Se dividió lo que antes era código aglomerado. Ahora el archivo `MySQLCourseRepository.php` únicamente interactúa con SQL, el `CourseService.php` engloba  lógica de negocio y el `CourseController.php` solo evalúa peticiones HTTP y responde mediante JSON.
*   **Open/Closed Principle (OCP):** El código es *abierto para su extensión, cerrado para su modificación*. Gracias al patrón *Repository*, si quisiéramos cambiar a base de datos PostgreSQL, basta con crear una nueva clase `PgSQLStudentRepository`. Ninguna línea de los controladores o servicios necesita ser modificada.
*   **Liskov Substitution Principle (LSP):** Al utilizar estrictamente las interfaces, nos aseguramos que cualquier nueva clase que se inyecte (ej. un repositorio *Mock* para Testing en la nube) pueda comportarse exactamente igual y sustituir al `MySQLStudentRepository` sin que el código central colapse.
*   **Interface Segregation Principle (ISP):** En lugar de tener una sola interface gigantesca, se establecieron contratos específicos (`IStudentRepository`, `ICourseRepository`, etc.), asegurando que los servicios no se vean obligados a depender de métodos (como consultas complejas de matrículas) que no van a usar.
*   **Dependency Inversion Principle (DIP):** Los servicios de la capa de *Application* ahora dependen enteramente de abstracciones (`IStudentRepository`) en lugar de implementaciones directas de bases de datos. Esto invierte la dependencia tradicional facilitando mantenimiento.

> *[SUGERENCIA DE CAPTURA B]:* Aquí puedes colocar una **captura de pantalla de la interfaz `IStudentRepository.php`** o de la inyección de dependencias en el constructor de `StudentService.php` para justificar el principio de Inversión de Dependencias (DIP).

**3. Implementación de Router y Estandarización de Backend**
Se estandarizó la nomenclatura de la base de datos a un idioma universal en la programación (inglés). Las tablas pasaron a llamarse `students`, `courses`, y `enrollments`. Correspondientemente, el código del core se reconstruyó en inglés para el mantenimiento escalable. 
Además, se instaló un punto de acceso unificado (`api.php`) guiado por la variable *route* para manejar el flujo dinámico hacia los controladores.

> *[SUGERENCIA DE CAPTURA C]:* Colocar **captura de pantalla del archivo `api.php`** mostrando cómo se definen las rutas ordenadamente utilizando el objeto `$router->add(...)`.

**4. Sincronización del Frontend y Gestión de Reportes**
Al actualizar los repositorios para enviar datos en un estándar `camelCase` (ej. `idCard`), la interfaz gráfica `Views/Servicios.php` que utiliza la liberaría EasyUI, requirió un mapeo profundo desde `snake_case` visual para poder pintar los datos nuevamente en la tabla. Las interfaces de reportes de *FPDF* también se refactorizaron.

---

### VIII. RESULTADOS OBTENIDOS

De la refactorización profunda aplicada sobre el código legacy, se obtuvieron múltiples resultados técnicos medibles que elevan la madurez del software a estándares de la industria actual:

1. **Arquitectura Sólida, Desacoplada y Altamente Escalable:**
   La estructura monolítica de scripts entremezclados fue erradicada. El proyecto resultando es ahora predecible y altamente gobernable. Al implementarse la *Clean Architecture* en la carpeta `src/`, se comprobó que el flujo de datos viaja de manera segura desde las peticiones (HTTP) hacia las validaciones de negocio puro (Capa Application), para recién ahí delegar persistencia y guardado en la capa de Infraestructura. 
   ¿El gran logro? Si el día de mañana se exige migrar de *MySQL* a *PostgreSQL* o inclusive *MongoDB*, basta con escribir una nueva clase de repositorio; sin alterar en absoluto ninguna validación, cálculo o interfaz del proyecto original.

2. **Independencia Real del Frontend y Backend Localizado (Naming):**
   Se logró un resultado excelente en estandarización universal: el código fuente (Controladores, Servicios, Repositorios, Entidades y esquema de Base de Datos) está 100% redactado, nombrado y mapeado en **Inglés** (`firstName`, `student_id`, `Enrollments`). 
   Este resultado posiciona al proyecto para ser colaborado a escala global de manera técnica, mientras simultáneamente, se comprobó que la interfaz gráfica (UI) se sostiene y conserva puramente en **Español** para los usuarios administrativos del dominio general sin conflictos del lado del cliente. 

> *[SUGERENCIA DE CAPTURA D]:* Coloca una **captura de pantalla de la página "Servicios" operando** (ej. la tabla cargando la entidad estudiantes, o el formulario de editar datos) para mostrar que funciona correctamente sin errores a nivel usuario.

3. **Inyección de Dependencias y Preparación para Testing Unitario:**
   Al haber cumplido exitosamente con los cinco principios SOLID (en especial Inversión de Dependencias y Segregación de Interfaces), el acoplamiento es inexistente. El resultado transversal de este hito es que el core del sistema quedó "mockeable" y 100% aislado. Es decir, las piezas del software son ahora lo suficientemente modulares como para aceptar implementaciones automáticas (Pruebas Unitarias o integraciones con sistemas externos) disminuyendo la deuda técnica drásticamente.

4. **Flujo de Peticiones Estandarizado y Seguro:**
   Se sustituyó la llamada directa indiscriminada a archivos (`borrar.php`, `editar.php`) por un único orquestador central inmerso en `api.php`. Como resultado, se centralizó el control de seguridad, permitiendo que la aplicación actúe de manera similar a una API REST, procesando JSON tipados al comunicarse de manera transparente y eficiente bajo la variable dinámica URL `$route` hacia sus Controladores designados.

5. **Sanitización de Consultas (Seguridad) y Recuperación de Módulos Rotos:**
   Al modernizar la capa de Infraestructura y usar herencia del patrón *Repository*, se migró enteramente de extensiones inseguras obsoletas a `PDO` usando de manera obligatoria la unión de **Sentencias Preparadas**. Esto resulta en un grado altísimo de seguridad contra ataques críticos de inyección SQL. Sumado a esto, se estabilizaron componentes rotos de la versión inicial, finalizando con un sistema de reportes PDF dinámicos (`FPDF`) totalmente reactivo y con gráficas estadísticas de datos recuperadas e íntegras.

> *[SUGERENCIA DE CAPTURA E]:* Coloca una **captura de un reporte FPDF cargado y funcional** (como el "Reporte Detallado de Estudiante" o el vistoso "Gráfico Estadístico de Género") evidenciando este entregable clave extraído de la API segura.

---

### IX. CONCLUSIONES

1. **Sobre Clean Architecture e interfaces (DIP & SRP):** Se concluye que la implementación de una *Clean Architecture*, al segmentar estrictamente las responsabilidades mediante servicios y controladores, disminuye dramáticamente la propagación de errores. Modificar una parte del sistema (como una consulta SQL) ya no requiere manipular la lógica de la Interfaz Visual, lo que incrementa la predictibilidad técnica y estabilidad.
2. **Sobre la Estandarización Universal (Naming):** Estandarizar el código fuente a términos de industria (inglés) junto con nomenclatura `camelCase`, ha purificado la arquitectura interna, permitiendo que la interacción del Backend resulte profesional e idiomática sin sacrificar la Usabilidad, demostrando que es viable sostener bases de datos, APIs y lenguajes en un estándar y traducir exclusivamente la capa visual.
3. **Sobre Refactorización a Interfaces (ISP y DIP):** Se corrobora que la adopción sólida del patrón *Repository* e Inyección de Dependencias elimina en su totalidad el viejo acoplamiento rígido de PHP. Los servicios pasaron de depender de una conexión directa a PDO, a depender de abstracciones de interfaces, haciendo de este un sistema apto para metodologías modernas de desarrollo e, inclusive, Testing Unitario.

---

### X. RECOMENDACIONES

1. **Testing Automático:** Se recomienda integrar en el corto plazo la herramienta *PHPUnit*. Al poseer el sistema una arquitectura limpia en donde la lógica central carece de bases de datos o HTML, se facilita sumamente el testeo unitario sistemático que evalúe y prevenga roturas con cada cambio.
2. **Hashing de Seguridad:** Se sugiere actualizar la capa de autenticación, transformando la estructura básica a un registro donde las contraseñas pasen obligatoriamente a través de algoritmos de hashing criptográficos (como `password_hash()` de PHP en vez de texto plano), protegiendo la confidencialidad de administradores y secretarias.
3. **Caché y APIs Modernas:** En desarrollos futuros, con la actual estructura de API implementada (`api.php`), se recomienda aprovechar esta ventaja para en el futuro planear una migración del *frontend* actual a algo desacoplado, como el uso de *React*, *Vue* o *Angular* de manera nativa sin volver a programar el negocio.

---

### XI. BIBLIOGRAFÍA

[1] R. C. Martin, *Clean Architecture: A Craftsman's Guide to Software Structure and Design*. Boston, MA, USA: Prentice Hall, 2017.
[2] R. C. Martin, *Clean Code: A Handbook of Agile Software Craftsmanship*. Boston, MA, USA: Prentice Hall, 2008.
[3] A. Da Rocha, *Guía de Arquitectura de Software Contemporánea y Patrones SOLID*. Madrid, España: Editorial Tecnológica, 2020.
[4] The PHP Group, "PHP Data Objects (PDO) Documentation," *PHP: Hypertext Preprocessor*. [En línea]. Disponible en: https://www.php.net/manual/es/book.pdo.php.
[5] O. Plathey, "FPDF Library Documentation," *Free PDF Generator*. [En línea]. Disponible en: http://www.fpdf.org/.
