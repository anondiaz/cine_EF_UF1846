USE `cine`;

-- select personajes
SELECT pe.nombre, pe.apellido, pr.profesion, ge.genero, oscars, fecha_nacimiento
FROM people pe
JOIN profesion pr
ON pe.profesion = pr.id_profesion
JOIN genero ge
ON pe.genero = ge.id_genero
ORDER BY fecha_nacimiento
;

-- select profesion
SELECT pe.nombre, pe.apellido, pr.profesion, pe.oscars, pe.fecha_nacimiento
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                WHERE pr.profesion = 'actuacion'
                ORDER BY pe.apellido DESC
;

-- select fecha nacimiento
SELECT pe.apellido, pr.profesion, ge.genero, pe.oscars
                FROM people pe
                JOIN profesion pr
                ON pe.profesion = pr.id_profesion
                JOIN genero ge
                ON pe.genero = ge.id_genero
                WHERE pe.fecha_nacimiento = :fecha
                ORDER BY pe.fecha_nacimiento DESC
;