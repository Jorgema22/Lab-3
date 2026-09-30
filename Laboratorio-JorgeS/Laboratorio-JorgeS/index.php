<?php include 'includes/header.php'; ?>

<main class="container my-4 flex-grow-1">
    <section class="mx-auto" style="max-width: 460px;">
        <h1 class="h4 fw-bold text-center mb-3">Formulario de Registro de Aspirantes</h1>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <!-- enctype multipart/form-data es obligatorio para poder subir la foto -->
                <form action="procesar.php" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold small">Nombre (Requerido):</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                               placeholder="Ej: Sofía" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="apellido" class="form-label fw-bold small">Apellido (Requerido):</label>
                        <input type="text" class="form-control" id="apellido" name="apellido"
                               placeholder="Ej: Pérez" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="identificacion" class="form-label fw-bold small">Identificación (Requerido):</label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion"
                               placeholder="Ej: 8-123-456" maxlength="20" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label fw-bold small">Fecha de Nacimiento (Requerido):</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>

                    <div class="mb-3">
                        <span class="form-label fw-bold small d-block">Sexo (Requerido):</span>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="sexo" id="sexo_h" value="Hombre" required>
                            <label class="btn btn-outline-secondary flex-fill" for="sexo_h">Hombre</label>

                            <input type="radio" class="btn-check" name="sexo" id="sexo_m" value="Mujer">
                            <label class="btn btn-outline-secondary flex-fill" for="sexo_m">Mujer</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="foto" class="form-label fw-bold small">Fotografía del Aspirante (png, jpg, jpeg, gif, webp):</label>
                        <input type="file" class="form-control" id="foto" name="foto"
                               accept=".png,.jpg,.jpeg,.gif,.webp" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrar Aspirante</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
