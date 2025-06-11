<?php
// Establecemos el tipo de contenido a JSON
// y la codificación a UTF-8
header('Content-Type: application/json; charset=utf-8');

// Incluimos el archivo de conexión a la base de datos
require_once '../../pdo_bind_connection.php';

// Generamos arrays de géneros y profesiones válidos
// $validGeneros = ['hombre', 'mujer', 'otro'];
// $validProfesiones = ['actuacion', 'direccion', 'musica', 'actor', 'director', 'compositor'];

// Consultamos los géneros desde la base de datos para evitar confusiones
// y asegurar que los valores son correctos
$queryGeneros = "SELECT genero FROM genero";
$stmt = $pdo->prepare($queryGeneros);
$stmt->execute();
$selectGeneros = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selectGeneros = array_column($selectGeneros, 'genero');
$validGeneros = $selectGeneros;
// print_r($validGeneros);

// Consultamos las profesiones desde la base de datos para evitar confusiones
// y asegurar que los valores son correctos
$queryProfesiones = "SELECT profesion FROM profesion";
$stmt = $pdo->prepare($queryProfesiones);
$stmt->execute();
$selectProfesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selectProfesiones = array_column($selectProfesiones, 'profesion');
$validProfesiones = $selectProfesiones;
// print_r($validProfesiones);


// 1) En la ruta /api/personajes deben aparecer estos datos de cada personaje:
//  -- nombre
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- género (en texto, "mujer", "hombre", ...)
//  -- oscars
//  -- fecha_nacimiento 
// ordenados per fecha de fecha_nacimiento

if (!$_GET) {
    // Si no hay parámetros en la URL, obtenemos todos los personajes
    // y los ordenamos por fecha de nacimiento en orden ascendente
    $queryPersonajes = "SELECT pe.nombre, pe.apellido, pr.profesion, ge.genero, oscars, fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                ORDER BY fecha_nacimiento
                ASC";
    $stmt = $pdo->prepare($queryPersonajes);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado);
        exit();
    } else {
        // Si no hay resultados, devolvemos un error 404
        // y un mensaje en formato JSON
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "No se encontraron personajes"]);
        exit();
    }
}

// 6) En la ruta /api/personajes?genero=X&profesion=Y,
// donde X es "hombre", "mujer", u "otro", e Y es "actuacion", "direccion", o "musica",
// deben aparecer estos datos de cada personaje:
//  -- nombre
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- género (en texto, "mujer", "hombre", ...)
//  -- oscars
//  -- fecha_nacimiento 
// ordenados per fecha de nombre en forma descendente, y apellido descendente
// NOTA: si no hay ningún personaje con el género y/o profesión indicados, hay que mostrar:
// { "codigo": 404, "mensaje": "Ningún personaje encontrado" }

if (isset($_GET['genero']) && isset($_GET['profesion'])) {
    // Si hay parámetros 'genero' y 'profesion' en la URL, los obtenemos
    $genero = $_GET['genero'];
    $profesion = $_GET['profesion'];

    // Verificamos si el género y la profesión son válidos
    // Si no son válidos, devolvemos un error 404
    // y un mensaje en formato JSON
    if (!in_array($genero, $validGeneros) || !in_array($profesion, $validProfesiones)) {
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de género o profesión no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    // Si el género y la profesión son válidos, realizamos la consulta a la base de datos
    // para obtener los personajes con ese género y profesión
    $queryGeneroProf = "SELECT pe.nombre, pe.apellido, pr.profesion, ge.genero, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE ge.genero = :genero AND pr.profesion = :profesion
                ORDER BY pe.nombre DESC, pe.apellido DESC";
    $stmt = $pdo->prepare($queryGeneroProf);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else {
        // Si no hay resultados, devolvemos un error 404
        // y un mensaje en formato JSON
        http_response_code(404); // Not Found
        echo json_encode(["codigo" => 404, "mensaje" => "Ningún personaje encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}


// 2) En la ruta /api/personajes?genero=X (donde X es "hombre", "mujer", u "otro"),
//  deben aparecer estos datos de cada personaje:
//  -- nombre
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- oscars
//  -- fecha_nacimiento 
// ordenados per apellido en forma descendente
// NOTA: si X no fuera un valor de los permitidos se debe mostrar un JSON con el error:
// { "codigo": 404, "mensaje": "El valor de genero no es válido" }

if (isset($_GET['genero'])) {
    // Si hay un parámetro 'genero' en la URL, lo obtenemos
    $genero = $_GET['genero'];

    // Verificamos si el género es válido
    // Si no es válido, devolvemos un error 404
    if (!in_array($genero, $validGeneros)) {
       http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de género no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    // Si el género es válido, realizamos la consulta a la base de datos
    // para obtener los personajes con ese género
    // y los ordenamos por apellido en orden descendente
    $queryGenero = "SELECT pe.nombre, pe.apellido, pr.profesion, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE ge.genero = :genero
                ORDER BY pe.apellido DESC";
    $stmt = $pdo->prepare($queryGenero);
    $stmt->bindParam(':genero', $genero);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else if (in_array($genero, $validGeneros) && empty($resultado)) {
        // echo json_encode($result);
        // Si no hay resultados, devolvemos un error 400
        // y un mensaje en formato JSON
        http_response_code(404);
        echo json_encode(["codigo" => 404, "mensaje" => "No se han encontrado personajes con este género"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}


// 3) En la ruta /api/personajes?profesion=X (donde X es "actuacion", "direccion", u "musica"),
//  deben aparecer estos datos de cada personaje:
//  -- nombre
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- oscars
//  -- fecha_nacimiento 
// ordenados per apellido en forma descendente
// NOTA: si X no fuera un valor de los permitidos se debe mostrar un JSON con el error:
// { "codigo": 404, "mensaje": "El valor de profesión no es válido" }

if (isset($_GET['profesion'])) {
    // Si hay un parámetro 'profesion' en la URL, lo obtenemos
    $profesion = $_GET['profesion'];

    // Verificamos si la profesión es válida
    if (!in_array($profesion, $validProfesiones)) {
        // Si no es válida, devolvemos un error 404
        // y un mensaje en formato JSON
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de profesión no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    // Si la profesión es válida, realizamos la consulta a la base de datos
    // para obtener los personajes con esa profesión
    // y los ordenamos por apellido en orden descendente
    $queryProfesion = "SELECT pe.nombre, pe.apellido, pr.profesion, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                WHERE pr.profesion = :profesion
                ORDER BY pe.apellido DESC";
    $stmt = $pdo->prepare($queryProfesion);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else if (in_array($profesion, $validProfesiones) && empty($resultado)) {
        // Si no hay resultados, devolvemos un error 400
        // y un mensaje en formato JSON
        http_response_code(404);
        echo json_encode(["codigo" => 404, "mensaje" => "No se han encontrado personajes con esta profesión"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}



// 4) En la ruta /api/personajes?fecha=X (donde X es el año de nacimiento),
// hay que mostrar los datos de cada personaje que nació en ese año
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- género (en texto, "mujer", "hombre", ...)
//  -- oscars
// ordenados per fecha_nacimiento en forma descendente
// NOTA: si no hay ningún personaje que haya nacido ese año hay que mostrar:
// { "codigo": 404, "mensaje": "Ningún personaje encontrado" }

if (isset($_GET['fecha'])) {
    // Si hay un parámetro 'fecha' en la URL, lo obtenemos
    $fecha = $_GET['fecha'];

    // Verificamos si la fecha es un año válido
    if (!preg_match('/^\d{4}$/', $fecha)) {
        // Si no es un año válido, devolvemos un error 404 
        // y un mensaje en formato JSON
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de fecha no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    // Realizamos la consulta a la base de datos
    // para obtener los personajes que nacieron en ese año
    $queryFecha = "SELECT pe.apellido, pr.profesion, ge.genero, pe.oscars
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE pe.fecha_nacimiento = :fecha
                ORDER BY pe.fecha_nacimiento DESC";
    $stmt = $pdo->prepare($queryFecha);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    if ($resultado) {
        // Si hay resultados, los devolvemos en formato JSON
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else {
        // Si no hay resultados, devolvemos un error 404
        // y un mensaje en formato JSON
        http_response_code(404); // Not Found
        echo json_encode(["codigo" => 404, "mensaje" => "Ningún personaje encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}
