<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../../pdo_bind_connection.php';

// 1) En la ruta /api/personajes deben aparecer estos datos de cada personaje:
//  -- nombre
//  -- apellido
//  -- profesión (en texto: actuación , dirección, etc.)
//  -- género (en texto, "mujer", "hombre", ...)
//  -- oscars
//  -- fecha_nacimiento 
// ordenados per fecha de fecha_nacimiento

if (!$_GET) {
    $query = "SELECT pe.nombre, pe.apellido, pr.profesion, ge.genero, oscars, fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                ORDER BY fecha_nacimiento
                ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "No se encontraron personajes"]);
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
    $genero = $_GET['genero'];
    $validGeneros = ['hombre', 'mujer', 'otro'];

    if (!in_array($genero, $validGeneros)) {
       http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de género no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    $query = "SELECT pe.nombre, pe.apellido, pr.profesion, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE ge.genero = :genero
                ORDER BY pe.apellido DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':genero', $genero);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else if ($genero == 'hombre' || $genero == 'mujer' || $genero == 'otro' && empty($result)) {
        // echo json_encode($result);
        http_response_code(400);
        echo json_encode(["codigo" => 400, "mensaje" => "No se han encontrado personajes con este género"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
    $profesion = $_GET['profesion'];
    $validProfesiones = ['actor', 'director', 'compositor'];

    if (!in_array($profesion, $validProfesiones)) {
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de profesión no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    $query = "SELECT pe.nombre, pe.apellido, pr.profesion, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                WHERE pr.profesion = :profesion
                ORDER BY pe.apellido DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else if ($profesion == 'actor' || $profesion == 'director' || $profesion == 'compositor' && empty($result)) {
        http_response_code(400);
        echo json_encode(["codigo" => 400, "mensaje" => "No se han encontrado personajes con esta profesión"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
    $fecha = $_GET['fecha'];

    $query = "SELECT pe.apellido, pr.profesion, ge.genero, pe.oscars
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE pe.fecha_nacimiento = :fecha
                ORDER BY pe.fecha_nacimiento DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["codigo" => 404, "mensaje" => "Ningún personaje encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
    $genero = $_GET['genero'];
    $profesion = $_GET['profesion'];
    $validGeneros = ['hombre', 'mujer', 'otro'];
    $validProfesiones = ['actor', 'director', 'compositor'];

    if (!in_array($genero, $validGeneros) || !in_array($profesion, $validProfesiones)) {
        http_response_code(404); // Bad Request
        echo json_encode(["codigo" => 404, "mensaje" => "El valor de género o profesión no es válido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    $query = "SELECT pe.nombre, pe.apellido, pr.profesion, ge.genero, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE ge.genero = :genero AND pr.profesion = :profesion
                ORDER BY pe.nombre DESC, pe.apellido DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["codigo" => 404, "mensaje" => "Ningún personaje encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}