# VeterinariaWebPhp
# Proyecto Laravel Livewire Starter Kit

Este proyecto utiliza Laravel 12, Livewire y PowerGrid para la gestión de proveedores.

---

## Requisitos

- **PHP:** 8.2 o superior  
- **Composer:** 2.x  
- **Node.js:** 18.x o superior (para Vite y assets)  
- **npm:** 9.x o superior  

Extensiones PHP necesarias:

- `mbstring`
- `openssl`
- `pdo`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `gd` (opcional para imágenes)

---

## Instalación

1. Clonar el repositorio:

```bash
git clone <URL_DEL_REPOSITORIO>
cd <NOMBRE_DEL_PROYECTO>
```

2. Copiar archivo de configuración:
```
cp .env.example .env
```

3. Instalar dependencias de PHP:
```
composer install
```

4. Generar la clave de Laravel:
```
php artisan key:generate
```

5. Configurar las credenciales de la bd en el archivo .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1 // Localhostt
DB_PORT=3306
DB_DATABASE=veterinaria // Nombre de la base de datos
DB_USERNAME=
DB_PASSWORD=
```

6. Ejecutar migraciones de la base de datos
```
php artisan migrate
```

7. Levantar el servidor de desarrollo:
```
php artisan serve
```
8. Levantar el compilador de assets (Vite):
```
npm run dev
```
