# Taller Aspirantes – Laboratorio #3

Sistema de registro de aspirantes (HTML5, Bootstrap 5.3.8 y PHP).

## Descripción
Formulario que recibe nombre, apellido, identificación, fecha de nacimiento, sexo y fotografía.
El backend valida los datos, estandariza los textos, calcula la edad (18 a 70 años) y guarda la foto
en una carpeta protegida, sin usar base de datos.

## Estructura
```
Taller-Aspirantes/
├── includes/
│   ├── header.php      (metadatos, navbar y migas de pan dinámicas)
│   └── footer.php      (footer con enlaces y año dinámico)
├── uploaded_files/     (fotos; protegida con .htaccess)
├── index.php           (formulario)
└── procesar.php        (validación y guardado)
```

## Requisitos
- WAMP/XAMPP con Apache y PHP 8+ (extensión `mbstring` activa)
- `mod_authz_core` de Apache habilitado (viene por defecto)

## Instalación y uso
1. Copiar la carpeta a `C:\wamp64\www\Taller-Aspirantes\`
2. Iniciar WAMP y abrir `http://localhost/Taller-Aspirantes/index.php`
3. Llenar el formulario y presionar **Registrar Aspirante**

## Seguridad aplicada
- `trim()`, `strip_tags()` y `htmlspecialchars()` contra XSS
- Validación de extensión y de contenido real de la imagen (`getimagesize`)
- Nombre de archivo aleatorio y límite de 2 MB
- `.htaccess` en `uploaded_files/` que bloquea el acceso desde el navegador

## Autor
Nombre del estudiante – Grupo – UTP
