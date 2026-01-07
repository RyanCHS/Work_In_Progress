<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        
        <link
            href="https://cdn.jsdelivr.net/npm/@mdi/font@7.x/css/materialdesignicons.min.css"
            rel="stylesheet"
            />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/icon?family=Material+Icons"
            />
            <!-- Material Symbols -->
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0"
            />
        <title>Laravel Vue</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
       
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
