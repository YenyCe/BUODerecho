-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: escuela
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  `status` enum('activo','baja') NOT NULL DEFAULT 'activo',
  `motivo_baja` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_alumno`),
  KEY `id_grupo` (`id_grupo`),
  KEY `fk_alumno_carrera` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (1,'Campos Silva Luis Antonio',1,1,'activo',NULL),(2,'García Lázaro Iván Alexander',1,1,'activo',NULL),(3,'García Lázaro José Manuel',1,1,'activo',NULL),(4,'García Matías Luis Antonio',1,1,'activo',NULL),(5,'García Méndez Idali Adriana',1,1,'activo',NULL),(6,'Guerrero Carrasco Emilio De Jesús',1,1,'activo',NULL),(7,'Hernández Soriano Caleb',1,1,'activo',NULL),(8,'López López Daniel Gilberto',1,1,'activo',NULL),(9,'Lucas Matadamas Vanesa',1,1,'activo',NULL),(10,'Miguel Ortega Sandra Nidia',1,1,'activo',NULL),(11,'Osorio Ramírez Karol Sinaí',1,1,'activo',NULL),(12,'Pérez Guzmán Karen Linette',1,1,'activo',NULL),(13,'Ramos Sumano Stefany Alin',1,1,'activo',NULL),(14,'Sánchez Martínez Karla Denisse',1,1,'activo',NULL),(15,'Solís Velásquez Ytzel Yamilet',NULL,1,'baja','Se retiró del grupo'),(17,'Caballero Luján Sol',2,1,'activo',NULL),(18,'Campos Silva Luis Antonio',2,1,'activo',NULL),(19,'Días Ríos Geymmy Lawmy',2,1,'activo',NULL),(20,'García Lázaro Iván Alexander',2,1,'activo',NULL),(21,'García Lázaro José Manuel',2,1,'activo',NULL),(22,'García Matías Luis Antonio',2,1,'activo',NULL),(23,'García Méndez Idali Adriana',2,1,'activo',NULL),(24,'Gualberto Herrera Zurisadai',2,1,'activo',NULL),(25,'Guerrero Carrasco Emilio De Jesús',2,1,'activo',NULL),(26,'Hernández Soriano Caleb',2,1,'activo',NULL),(27,'López López Daniel Gilberto',2,1,'activo',NULL),(28,'Miguel Ortega Sandra Nidia',2,1,'activo',NULL),(29,'Osorio Ramírez Karol Sinaí',2,1,'activo',NULL),(30,'Pérez Guzmán Karen Linette',2,1,'activo',NULL),(31,'Ramos Sumano Stefany Alin',2,1,'activo',NULL),(32,'Rodríguez Altamirano Uriel',2,1,'activo',NULL),(33,'Sánchez Martínez Karla Denisse',2,1,'activo',NULL),(34,'Solís Velásquez Ytzel Yamilet',2,1,'activo',NULL),(35,'Velásquez Cortes Ashley Getsemaní',2,1,'activo',NULL),(37,'Lucas Matadamas Vanesa',2,1,'activo',NULL),(38,'aa',5,2,'activo',NULL),(39,'Velásquez Cortes Ashley Getsemaní',NULL,1,'baja',NULL),(40,'yeby',NULL,1,'baja','x');
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_alumnos_insert
BEFORE INSERT ON alumnos
FOR EACH ROW
BEGIN
    DECLARE carrera_grupo INT;

    -- Solo asignar id_carrera si no es baja y tiene grupo
    IF NEW.status != 'baja' AND NEW.id_grupo IS NOT NULL THEN
        SELECT id_carrera INTO carrera_grupo
        FROM grupos
        WHERE id_grupo = NEW.id_grupo;

        SET NEW.id_carrera = carrera_grupo;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_alumnos_update
BEFORE UPDATE ON alumnos
FOR EACH ROW
BEGIN
    DECLARE carrera_grupo INT;

    -- Solo asignar id_carrera si no es baja y tiene grupo
    IF NEW.status != 'baja' AND NEW.id_grupo IS NOT NULL THEN
        SELECT id_carrera INTO carrera_grupo
        FROM grupos
        WHERE id_grupo = NEW.id_grupo;

        SET NEW.id_carrera = carrera_grupo;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `clave` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (1,'Derecho','DERECHO2025'),(2,'Teología','TEOLOGIA2025'),(3,'Médico cirujano','MED2025'),(4,'Enfermería','ENFER2025'),(5,'Psicología','PSICO2025');
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docentes`
--

DROP TABLE IF EXISTS `docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_carrera` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_docente`),
  KEY `fk_docente_carrera` (`id_carrera`),
  CONSTRAINT `fk_docente_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docentes`
--

LOCK TABLES `docentes` WRITE;
/*!40000 ALTER TABLE `docentes` DISABLE KEYS */;
INSERT INTO `docentes` VALUES (1,'MTRA. CORAL DEL CARMEN ','ORDAZ FUENTES  ','CORALCARMEN@gmail.com','951124780',1),(2,'Carlos Alberto','Moreno Alcántara','carlos.moreno@escuela.local','951478545',1),(3,'María del Carmen','Avendaño Rito','maria.avendano@escuela.local','111111',1),(4,'DR. JOSÉ ANTONIO ','ALVAREZ HERNÁNDEZ  ','prueba@gmail.com','951254',1),(5,'MTRO. ADRIÁN ','QUIROGA AVENDAÑO ','aa@gmail.com','951478545',1),(6,'MTRO. ISAÍAS','HERNÁNDEZ SANTIAGO  ','aa@gmail.com','95114578924',1),(7,'MTRO. PEDRO ','CELESTINO GUZMAN  ','p@gmail.com','951000000',1),(9,'nuevos ','vvv','QQ@GMAIL.COM','11111',4),(10,'NUEVOS CMABIOS','QQ','aa@gmail.com','951478545',1);
/*!40000 ALTER TABLE `docentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  `id_semestre` int(11) NOT NULL,
  `id_carrera` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_grupo`),
  KEY `id_semestre` (`id_semestre`),
  KEY `fk_grupos_carreras` (`id_carrera`),
  CONSTRAINT `fk_grupos_carreras` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`),
  CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos`
--

LOCK TABLES `grupos` WRITE;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` VALUES (1,'1ER SEMESTRE',1,1),(2,'3er semestre',2,1),(3,'5to semestre',3,1),(4,'primer semestre psic',1,5),(5,'grupo de tecnologia',2,2),(6,'G-Medicina',3,3);
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horario_dias`
--

DROP TABLE IF EXISTS `horario_dias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horario_dias` (
  `id_dia` int(11) NOT NULL AUTO_INCREMENT,
  `id_horario` int(11) NOT NULL,
  `dia` enum('L','M','X','J','V') NOT NULL,
  PRIMARY KEY (`id_dia`),
  KEY `id_horario` (`id_horario`),
  CONSTRAINT `horario_dias_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario_dias`
--

LOCK TABLES `horario_dias` WRITE;
/*!40000 ALTER TABLE `horario_dias` DISABLE KEYS */;
INSERT INTO `horario_dias` VALUES (10,1,'L'),(11,1,'M'),(21,3,'L'),(22,5,'L'),(23,5,'M'),(25,7,'L'),(35,2,'L'),(36,2,'X'),(37,2,'V');
/*!40000 ALTER TABLE `horario_dias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `horario_texto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_docente` (`id_docente`),
  KEY `id_materia` (`id_materia`),
  KEY `id_grupo` (`id_grupo`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`),
  CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,1,1,1,1,'LUNES DE 07:00 A 08:50, MARTES DE 07:00 A 07:50'),(2,2,1,2,2,'LUNES DE 11:00 A 11:50, MIÉRCOLES DE 13:00 A 13:50, VIERNES DE 12:00 A 12:50 HRS '),(3,3,1,3,3,'LUNES DE 12:00 A 14:50 HRS   '),(5,2,1,4,2,'LUNES DE 07:00 A 08:50, MARTES DE 07:00 A 07:50'),(7,4,1,2,2,'LUNES DE 12:00 A 14:50 HRS');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `horas_semana` int(11) DEFAULT NULL,
  `horas_semestre` int(11) DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  PRIMARY KEY (`id_materia`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'Teoría Económica','BUOLD07',3,72,1),(2,'Cultura General del Abogado','BUOLD 08',3,54,1),(3,'Metodología para la Investigación y Redacción Jurídica','BUOLD 03 ',3,54,1),(4,'materia prueba','prue228',10,50,1),(5,'materia prueba psicilogia','pssii3',2,20,5),(6,'psicilogia','pssii3222',2,20,5),(7,'xxxxx','xx1x1x1',1,12,2),(8,'mental','ment22',1,20,5),(9,'QWQWQWQW','QWQWQ',2,20,3);
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parciales`
--

DROP TABLE IF EXISTS `parciales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parciales` (
  `id_parcial` int(11) NOT NULL AUTO_INCREMENT,
  `id_carrera` int(11) NOT NULL,
  `numero_parcial` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id_parcial`),
  KEY `fk_parcial_carrera` (`id_carrera`),
  CONSTRAINT `fk_parcial_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parciales`
--

LOCK TABLES `parciales` WRITE;
/*!40000 ALTER TABLE `parciales` DISABLE KEYS */;
INSERT INTO `parciales` VALUES (1,1,2,'2025-09-15','2025-10-17'),(2,1,1,'2025-08-18','2025-09-12'),(4,5,4,'2025-12-19','2026-01-10'),(5,5,5,'2026-01-02','2026-02-07'),(6,3,1,'2026-03-12','2026-04-12'),(7,1,3,'2025-12-19','2026-01-09'),(8,1,4,'2025-12-18','2026-01-02');
/*!40000 ALTER TABLE `parciales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semestres`
--

DROP TABLE IF EXISTS `semestres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semestres` (
  `id_semestre` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `id_carrera` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_semestre`),
  KEY `fk_semestre_carrera` (`id_carrera`),
  CONSTRAINT `fk_semestre_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semestres`
--

LOCK TABLES `semestres` WRITE;
/*!40000 ALTER TABLE `semestres` DISABLE KEYS */;
INSERT INTO `semestres` VALUES (1,1,NULL),(2,3,NULL),(3,5,NULL),(4,7,NULL);
/*!40000 ALTER TABLE `semestres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesiones_clase`
--

DROP TABLE IF EXISTS `sesiones_clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sesiones_clase` (
  `id_sesion` int(11) NOT NULL AUTO_INCREMENT,
  `id_horario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id_sesion`),
  KEY `id_horario` (`id_horario`),
  CONSTRAINT `sesiones_clase_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesiones_clase`
--

LOCK TABLES `sesiones_clase` WRITE;
/*!40000 ALTER TABLE `sesiones_clase` DISABLE KEYS */;
/*!40000 ALTER TABLE `sesiones_clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','coordinador') NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `fk_usuario_carrera` (`id_carrera`),
  CONSTRAINT `fk_usuario_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador General','admin1@local','admin','$2y$10$gaPQhy4DXJ06uYJ9cdodUuh5ytXtVVAgEzEp5R66ibRn0ByPctsim','admin',1,1),(2,'Coordinador Derecho','coord_derecho@local','coord_derecho','$2y$10$Bp14c3V6BuF4RWM/.xkAcefYLqqOyma0RaLnXeCzw17AKlA7eERgu','coordinador',1,1),(3,'Coordinador Enfermería','coord_enfermeria@local','coord_enfermeria','$2y$10$v5m1NWhyR6TPVvqRZ4kjfOszH7TsNtb24gy8QX3493LwH.mRL/Do6','coordinador',4,1),(5,'Coordinador Psicología','coord_psicologia@local','coord_psicologia','$2y$10$dVTV7IlhWJdCVmWIFBHaBeXM0c.jg2m7VLxjBikQsNETEbv8BSDm6','coordinador',5,1),(6,'Coordinador Medicina','coord_medicina@local','coord_medicina','$2y$10$YTDEG4RyguakS/PSCBf1EeKnYZLm8TYLNV5ZNdMObPJL5ah7.dQEa','coordinador',3,1),(7,'cordi derecho 2','coord_derecho2@local','coord_derecho2','$2y$10$PTosKOm9kzTuisL0OuOMvethS.J765WgVpon4IPk6Nn1jM4H8CZjy','coordinador',1,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'escuela'
--

--
-- Dumping routines for database 'escuela'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-12 12:15:56
