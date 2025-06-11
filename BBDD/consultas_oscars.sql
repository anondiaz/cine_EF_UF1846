USE `cine`;
SELECT pe.apellido, pr.profesion, ge.genero, pe.oscars
                FROM people pe
                JOIN profesion pr ON pe.profesion = pr.id_profesion
                JOIN genero ge ON pe.genero = ge.id_genero
                WHERE pe.oscars > 0
                ORDER BY pe.oscars ASC, pe.apellido ASC
;                