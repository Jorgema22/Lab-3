<?php
// procesar.php - Backend: valida, estandariza, guarda la foto y muestra el resultado

/* ---------- Funciones auxiliares ---------- */

// Saneamiento: quita espacios y etiquetas HTML/PHP (el escape con htmlspecialchars se hace al mostrar)
function limpiar(string $valor): string {
    return trim(strip_tags(trim($valor)));
}

// Escapa caracteres especiales al imprimir en pantalla (previene XSS)
function e(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// Formato Tipo Título respetando tildes: "sofia" -> "Sofia", "ÁNGEL" -> "Ángel"
function tipoTitulo(string $valor): string {
    return mb_convert_case(mb_strtolower($valor, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}

/* ---------- Variables ---------- */
$errores = [];
$exito   = false;
$datos   = [];
$fotoB64 = '';

// Solo se acepta el método POST (no se puede entrar escribiendo la URL)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/* ---------- 1. Recibir y sanear ---------- */
$nombre         = limpiar($_POST['nombre'] ?? '');
$apellido       = limpiar($_POST['apellido'] ?? '');
$identificacion = limpiar($_POST['identificacion'] ?? '');
$fechaNac       = trim($_POST['fecha_nacimiento'] ?? '');
$sexo           = trim($_POST['sexo'] ?? '');

/* ---------- 2. Campos no vacíos ---------- */
if ($nombre === '')         $errores[] = 'El nombre es obligatorio.';
if ($apellido === '')       $errores[] = 'El apellido es obligatorio.';
if ($identificacion === '') $errores[] = 'La identificación es obligatoria.';
if ($fechaNac === '')       $errores[] = 'La fecha de nacimiento es obligatoria.';
if (!in_array($sexo, ['Hombre', 'Mujer'], true)) $errores[] = 'Debe seleccionar el sexo.';

/* ---------- 3. Estandarizar textos ---------- */
$nombre         = tipoTitulo($nombre);
$apellido       = tipoTitulo($apellido);
$identificacion = mb_strtoupper($identificacion, 'UTF-8');

/* ---------- 4. Calcular edad y validar rango 18 - 70 ---------- */
$edad = null;
if ($fechaNac !== '') {
    $fecha = DateTime::createFromFormat('Y-m-d', $fechaNac);
    $valida = $fecha && $fecha->format('Y-m-d') === $fechaNac;

    if (!$valida) {
        $errores[] = 'La fecha de nacimiento no es válida.';
    } elseif ($fecha > new DateTime('today')) {
        $errores[] = 'La fecha de nacimiento no puede ser futura.';
    } else {
        $edad = $fecha->diff(new DateTime('today'))->y;
        if ($edad < 18 || $edad > 70) {
            $errores[] = "La edad ($edad años) debe estar entre 18 y 70 años.";
        }
    }
}

/* ---------- 5. Validar y guardar la foto ---------- */
$permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$carpeta    = __DIR__ . '/uploaded_files/';

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
    $errores[] = 'Debe seleccionar una fotografía.';
} elseif ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Error al subir la fotografía (código ' . $_FILES['foto']['error'] . ').';
} else {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $permitidas, true)) {
        $errores[] = 'Extensión no permitida. Use: ' . implode(', ', $permitidas) . '.';
    } elseif (@getimagesize($_FILES['foto']['tmp_name']) === false) {
        // Verifica que el contenido realmente sea una imagen (no solo la extensión)
        $errores[] = 'El archivo no es una imagen válida.';
    } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        $errores[] = 'La imagen no puede superar los 2 MB.';
    }
}

/* ---------- 6. Si todo está bien, guardar ---------- */
if (empty($errores)) {
    // Nombre nuevo y aleatorio: evita sobrescribir y nombres maliciosos
    $nombreArchivo = bin2hex(random_bytes(8)) . '.' . $ext;
    $destino = $carpeta . $nombreArchivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        $exito = true;
        $datos = compact('nombre', 'apellido', 'identificacion', 'fechaNac', 'edad', 'sexo', 'nombreArchivo');

        // La carpeta está protegida (.htaccess), así que mostramos la foto leyéndola desde el servidor
        $mime    = mime_content_type($destino);
        $fotoB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($destino));
    } else {
        $errores[] = 'No se pudo guardar la fotografía en el servidor.';
    }
}
?>
<?php include 'includes/header.php'; ?>

<main class="container my-4 flex-grow-1">
    <section class="mx-auto" style="max-width: 560px;">

        <?php if ($exito): ?>
            <div class="alert alert-success">¡Aspirante registrado correctamente!</div>

            <div class="card shadow-sm">
                <div class="row g-0">
                    <div class="col-md-4 text-center p-3">
                        <img src="<?php echo $fotoB64; ?>" class="img-fluid rounded" alt="Fotografía del aspirante">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h2 class="h5 card-title"><?php echo e($datos['nombre'] . ' ' . $datos['apellido']); ?></h2>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Identificación:</strong> <?php echo e($datos['identificacion']); ?></li>
                                <li><strong>Fecha de nacimiento:</strong> <?php echo e($datos['fechaNac']); ?></li>
                                <li><strong>Edad:</strong> <?php echo $datos['edad']; ?> años</li>
                                <li><strong>Sexo:</strong> <?php echo e($datos['sexo']); ?></li>
                                <li><strong>Archivo guardado:</strong> <?php echo e($datos['nombreArchivo']); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h2 class="h6">No se pudo completar el registro:</h2>
                <ul class="mb-0">
                    <?php foreach ($errores as $msg): ?>
                        <li><?php echo e($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-primary mt-3">Volver al formulario</a>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
