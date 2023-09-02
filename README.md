## Aplicacion Web para EISA

## Instalacion

1. Verificar que tiene instalado PHP8.0, la ultima version LTS de npm y una version estable de Composer.
2. Copiar el contenido del archivo .env.example en un nuevo archivo llamado .env
3. Abrir una terminal en la carpeta raiz del proyecto
4. Correr el comando 'composer install'
5. Correr el comando 'npm install'
6. Crear una base de datos vacia para el proyecto, puede ponerle cualquier nombre que desee
7. Ejecutar las migraciones de Laravel con el comando 'php artisan migrate'
8. Ejecutar el seeder de Laravel con el comando 'php artisan db:seed'
9. Ejecutar el servidor local con el comando 'php artisan serve'. Si tiene MacOS y desea usar Laravel Valet, tambien es posible
10. Si el puerto 8000 no esta en uso, artisan servira el proyecto en http://localhost:8000/admin (si el puerto esta en uso, le asignara el siguiente disponible, es decir, 8001. Y asi sucesivamente si ese tambien esta en uso)
11. Use las siguientes credenciales para entrar: admin@email.com - admin12345
