--
-- Base de dades: `cine`
--
CREATE DATABASE IF NOT EXISTS `cine`;
USE `cine`;

-- --------------------------------------------------------

--
-- Estructura de la taula `genero`
--

CREATE TABLE `genero` (
  `id_genero` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `genero` varchar(10) NOT NULL
) ;

--
-- Bolcament de dades per a la taula `genero`
--

INSERT INTO `genero` (`id_genero`, `genero`) VALUES
(1, 'mujer'),
(2, 'hombre'),
(5, 'otro');

-- --------------------------------------------------------

--
-- Estructura de la taula `people`
--

CREATE TABLE `people` (
  `id_people` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `profesion` int(2) NOT NULL,
  `genero` int(1) NOT NULL,
  `oscars` int(2) NOT NULL,
  `fecha_nacimiento` varchar(10) NOT NULL
) ;

--
-- Bolcament de dades per a la taula `people`
--

INSERT INTO `people` (`id_people`, `nombre`, `apellido`, `profesion`, `genero`, `oscars`, `fecha_nacimiento`) VALUES
(1, 'Katharine', 'Hepburn', 2, 1, 4, '1907'),
(2, 'James', 'Stewart', 2, 2, 2, '1908'),
(3, 'John', 'Ford', 1, 1, 4, '1894'),
(4, 'Cary', 'Grant', 2, 2, 1, '1908'),
(5, 'Henry', 'Fonda', 2, 2, 2, '1905'),
(6, 'Billy', 'Wilder', 1, 2, 6, '1906'),
(7, 'Marilyn', 'Monroe', 2, 1, 0, '1926'),
(8, 'Shirley', 'MacLaine', 2, 1, 1, '1934'),
(9, 'Alfred', 'Hitchcock', 1, 2, 0, '1899'),
(10, 'Nino', 'Rota', 3, 2, 1, '1911'),
(11, 'John', 'Barry', 3, 2, 4, '1933'),
(12, 'Sean', 'Connery', 2, 2, 1, '1930'),
(13, 'Ingrid', 'Bergman', 2, 1, 2, '1915'),
(14, 'Audrey', 'Hepburn', 2, 1, 1, '1929'),
(15, 'Grace', 'Kelly', 2, 1, 1, '1929'),
(17, 'Meryl', 'Streep', 2, 1, 3, '1949'),
(18, 'John', 'Williams', 3, 2, 6, '1932'),
(19, 'Arthur', 'Rubinstein', 3, 2, 1, '1887');

-- --------------------------------------------------------

--
-- Estructura de la taula `profesion`
--

CREATE TABLE `profesion` (
  `id_profesion` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `profesion` varchar(25) NOT NULL
);

--
-- Bolcament de dades per a la taula `profesion`
--

INSERT INTO `profesion` (`id_profesion`, `profesion`) VALUES
(1, 'director'),
(2, 'actor'),
(3, 'compositor');

--
-- Restriccions per a la taula `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `fk_genero` FOREIGN KEY (`genero`) REFERENCES `genero` (`id_genero`),
  ADD CONSTRAINT `fk_profesion` FOREIGN KEY (`profesion`) REFERENCES `profesion` (`id_profesion`);

