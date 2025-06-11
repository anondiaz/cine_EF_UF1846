<?php
// Establecemos la cabecera de respuesta para JSON
// y la codificación UTF-8
header('Content-Type: application/json; charset=utf-8');

// Incluimos el archivo de conexión a la base de datos
require_once '../../pdo_bind_connection.php';

// 5) En la ruta /api/oscars 
// hay que mostrar los datos de cada personaje que obtuvo al menos un oscar (los demás no)
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- género (en texto, "mujer", "hombre", ...)
//  -- oscars
// ordenados per cantidad de oscars en forma ascendente y después por apellido

if (!$_GET) {
    // Realizamos la consulta a la base de datos
    // para obtener los personajes que tienen al menos un Oscar
    $queryOscars = "SELECT pe.apellido, pr.profesion, ge.genero, pe.oscars
                FROM people pe
                JOIN profesion pr ON pe.profesion = pr.id_profesion
                JOIN genero ge ON pe.genero = ge.id_genero
                WHERE pe.oscars > 0
                ORDER BY pe.oscars ASC, pe.apellido ASC";
    $stmt = $pdo->prepare($queryOscars);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado);
    } else {
        // Si no hay resultados, devolvemos un error 404
        // y un mensaje en formato JSON
        http_response_code(404); // Not Found
        echo json_encode(["codigo" => 404, "mensaje" => "No se encontraron personajes con oscars"]);
    }
}
