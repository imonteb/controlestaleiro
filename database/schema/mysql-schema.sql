/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `colaborador_id` bigint unsigned DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'geral',
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_expiracao` datetime DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_colaborador_id_foreign` (`colaborador_id`),
  CONSTRAINT `app_notifications_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asignacion_colaborador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_colaborador` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asignacion_id` bigint unsigned NOT NULL,
  `colaborador_id` bigint unsigned NOT NULL,
  `rol_en_equipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipo_tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'principal' COMMENT 'principal o auxiliar',
  `es_jefe` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignacion_colaborador_asignacion_id_foreign` (`asignacion_id`),
  KEY `asignacion_colaborador_colaborador_id_foreign` (`colaborador_id`),
  CONSTRAINT `asignacion_colaborador_asignacion_id_foreign` FOREIGN KEY (`asignacion_id`) REFERENCES `asignaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignacion_colaborador_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asignacion_vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_vehiculo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asignacion_id` bigint unsigned NOT NULL,
  `vehiculo_id` bigint unsigned NOT NULL,
  `equipo_tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'principal' COMMENT 'principal o auxiliar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignacion_vehiculo_asignacion_id_foreign` (`asignacion_id`),
  KEY `asignacion_vehiculo_vehiculo_id_foreign` (`vehiculo_id`),
  CONSTRAINT `asignacion_vehiculo_asignacion_id_foreign` FOREIGN KEY (`asignacion_id`) REFERENCES `asignaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignacion_vehiculo_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asignaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `pep_id` bigint unsigned DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'estaleiro',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_hora_evento` datetime DEFAULT NULL,
  `descripcion_evento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_taller` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_entrada_taller` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignaciones_pep_id_foreign` (`pep_id`),
  CONSTRAINT `asignaciones_pep_id_foreign` FOREIGN KEY (`pep_id`) REFERENCES `peps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auto_socorro_kits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_socorro_kits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `veiculo_id` bigint unsigned DEFAULT NULL,
  `designacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `needs_restock` tinyint(1) NOT NULL DEFAULT '0',
  `identificador_kit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locacion_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auto_socorro_kits_qr_code_token_unique` (`qr_code_token`),
  KEY `auto_socorro_kits_locacion_id_foreign` (`locacion_id`),
  KEY `auto_socorro_kits_veiculo_id_foreign` (`veiculo_id`),
  CONSTRAINT `auto_socorro_kits_locacion_id_foreign` FOREIGN KEY (`locacion_id`) REFERENCES `locaciones` (`id`),
  CONSTRAINT `auto_socorro_kits_veiculo_id_foreign` FOREIGN KEY (`veiculo_id`) REFERENCES `vehiculos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avisos_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avisos_tv` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'azul',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `ordem` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colaboradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboradores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_colaborador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `denominacion_cargo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `visible_en_dashboard` tinyint(1) NOT NULL DEFAULT '1',
  `motivo_baja` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `colaboradores_numero_colaborador_unique` (`numero_colaborador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dias_publicados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dias_publicados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `activo_tv` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dias_publicados_fecha_unique` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ordem` int NOT NULL DEFAULT '0',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emergency_procedures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_procedures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'geral',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ordem` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epi_ajustes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epi_ajustes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `epi_item_id` bigint unsigned NOT NULL,
  `talla` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_sistema` int NOT NULL,
  `stock_real` int NOT NULL,
  `diferencia` int NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ajustado_por` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_ajustes_epi_item_id_foreign` (`epi_item_id`),
  KEY `epi_ajustes_ajustado_por_foreign` (`ajustado_por`),
  CONSTRAINT `epi_ajustes_ajustado_por_foreign` FOREIGN KEY (`ajustado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `epi_ajustes_epi_item_id_foreign` FOREIGN KEY (`epi_item_id`) REFERENCES `epi_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epi_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epi_entregas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `epi_item_id` bigint unsigned NOT NULL,
  `colaborador_id` bigint unsigned DEFAULT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  `talla` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valores_personalizados` json DEFAULT NULL,
  `fecha_entrega` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `estado` enum('entregue','devolvido','danificado','perdido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'entregue',
  `firma` longtext COLLATE utf8mb4_unicode_ci,
  `firma_devolucion` longtext COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `entregado_por` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_entregas_entregado_por_foreign` (`entregado_por`),
  KEY `epi_entregas_epi_item_id_estado_index` (`epi_item_id`,`estado`),
  KEY `epi_entregas_colaborador_id_fecha_entrega_index` (`colaborador_id`,`fecha_entrega`),
  CONSTRAINT `epi_entregas_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`),
  CONSTRAINT `epi_entregas_entregado_por_foreign` FOREIGN KEY (`entregado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `epi_entregas_epi_item_id_foreign` FOREIGN KEY (`epi_item_id`) REFERENCES `epi_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epi_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epi_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `talla` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('individual','coletivo') COLLATE utf8mb4_unicode_ci DEFAULT 'individual',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `requiere_talla` tinyint(1) NOT NULL DEFAULT '0',
  `tallas_disponibles` json DEFAULT NULL,
  `campos_personalizados` json DEFAULT NULL,
  `riscos` json DEFAULT NULL,
  `ca_numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_total` int NOT NULL DEFAULT '0',
  `stock_por_talla` json DEFAULT NULL,
  `unidade` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidade',
  `stock_minimo` int unsigned NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `motivo_baja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epi_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epi_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `colaborador_id` bigint unsigned NOT NULL,
  `epi_item_id` bigint unsigned NOT NULL,
  `tamanho` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantidade` int NOT NULL DEFAULT '1',
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `motivo_colaborador` text COLLATE utf8mb4_unicode_ci,
  `motivo_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_pedidos_colaborador_id_foreign` (`colaborador_id`),
  KEY `epi_pedidos_epi_item_id_foreign` (`epi_item_id`),
  CONSTRAINT `epi_pedidos_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epi_pedidos_epi_item_id_foreign` FOREIGN KEY (`epi_item_id`) REFERENCES `epi_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epi_recepciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epi_recepciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `epi_item_id` bigint unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL,
  `talla` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_recepciones_epi_item_id_foreign` (`epi_item_id`),
  KEY `epi_recepciones_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `epi_recepciones_epi_item_id_foreign` FOREIGN KEY (`epi_item_id`) REFERENCES `epi_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epi_recepciones_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `extintores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extintores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `num_serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_agente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanho` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Conforme',
  `needs_restock` tinyint(1) NOT NULL DEFAULT '0',
  `data_verificacao` date DEFAULT NULL,
  `proxima_revisao` date DEFAULT NULL,
  `veiculo_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locacion_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extintores_qr_code_token_unique` (`qr_code_token`),
  KEY `extintores_veiculo_id_foreign` (`veiculo_id`),
  KEY `extintores_locacion_id_foreign` (`locacion_id`),
  CONSTRAINT `extintores_locacion_id_foreign` FOREIGN KEY (`locacion_id`) REFERENCES `locaciones` (`id`),
  CONSTRAINT `extintores_veiculo_id_foreign` FOREIGN KEY (`veiculo_id`) REFERENCES `vehiculos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ferramenta_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferramenta_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ferramenta_id` bigint unsigned NOT NULL,
  `colaborador_id` bigint unsigned DEFAULT NULL,
  `veiculo_id` bigint unsigned DEFAULT NULL,
  `data_verificacao` date DEFAULT NULL,
  `proxima_verificacao` date DEFAULT NULL,
  `verificado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_registo_verificacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checklist` json DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `folha_id` int DEFAULT NULL,
  `apto` tinyint(1) NOT NULL DEFAULT '1',
  `num_registo_seq` int DEFAULT NULL,
  `manutencao_tipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conclusao` text COLLATE utf8mb4_unicode_ci,
  `assinatura_path` longtext COLLATE utf8mb4_unicode_ci,
  `unidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_movimento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'verificacao',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ferramenta_logs_ferramenta_id_foreign` (`ferramenta_id`),
  KEY `ferramenta_logs_colaborador_id_foreign` (`colaborador_id`),
  KEY `ferramenta_logs_veiculo_id_foreign` (`veiculo_id`),
  CONSTRAINT `ferramenta_logs_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`),
  CONSTRAINT `ferramenta_logs_ferramenta_id_foreign` FOREIGN KEY (`ferramenta_id`) REFERENCES `ferramentas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ferramenta_logs_veiculo_id_foreign` FOREIGN KEY (`veiculo_id`) REFERENCES `vehiculos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ferramentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferramentas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referencia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `familia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periodicidade_meses` int NOT NULL DEFAULT '12',
  `tipo_documentacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Manual',
  `estado_operacional` enum('Apto','Não Apto','Condicionado','Abate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Apto',
  `talla` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ferramentas_referencia_unique` (`referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guia_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guia_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guia_transporte_id` bigint unsigned NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unidade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'und',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guia_items_guia_transporte_id_foreign` (`guia_transporte_id`),
  CONSTRAINT `guia_items_guia_transporte_id_foreign` FOREIGN KEY (`guia_transporte_id`) REFERENCES `guia_transportes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guia_transportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guia_transportes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `local_carga_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_carga_morada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_carga_localidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_carga_cpostal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `matricula` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_morada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_localidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_cpostal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `requerente_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `numero_oficial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_recusa` text COLLATE utf8mb4_unicode_ci,
  `data_emissao` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `processed_by_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guia_transportes_user_id_foreign` (`user_id`),
  KEY `guia_transportes_processed_by_id_foreign` (`processed_by_id`),
  KEY `guia_transportes_status_index` (`status`),
  KEY `guia_transportes_numero_oficial_index` (`numero_oficial`),
  KEY `guia_transportes_requerente_id_index` (`requerente_id`),
  CONSTRAINT `guia_transportes_processed_by_id_foreign` FOREIGN KEY (`processed_by_id`) REFERENCES `users` (`id`),
  CONSTRAINT `guia_transportes_requerente_id_foreign` FOREIGN KEY (`requerente_id`) REFERENCES `colaboradores` (`id`),
  CONSTRAINT `guia_transportes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `incident_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incident_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `colaborador_id` bigint unsigned NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_hora` datetime NOT NULL,
  `localizacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incident_reports_colaborador_id_foreign` (`colaborador_id`),
  CONSTRAINT `incident_reports_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `legal_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `legal_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conteudo` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `versao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultima_revisao` date NOT NULL,
  `publicada` tinyint(1) NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `legal_pages_slug_unique` (`slug`),
  KEY `legal_pages_updated_by_foreign` (`updated_by`),
  CONSTRAINT `legal_pages_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `locaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locaciones_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `peps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locacion_id` bigint unsigned DEFAULT NULL,
  `tipo_trabajo_id` bigint unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `motivo_baja` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `peps_locacion_id_foreign` (`locacion_id`),
  KEY `peps_tipo_trabajo_id_foreign` (`tipo_trabajo_id`),
  CONSTRAINT `peps_locacion_id_foreign` FOREIGN KEY (`locacion_id`) REFERENCES `locaciones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peps_tipo_trabajo_id_foreign` FOREIGN KEY (`tipo_trabajo_id`) REFERENCES `tipos_trabajo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saude_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saude_itens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'un',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saude_kit_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saude_kit_itens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kit_id` bigint unsigned NOT NULL,
  `saude_item_id` bigint unsigned NOT NULL,
  `data_validade` date DEFAULT NULL,
  `quantidade` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saude_kit_itens_kit_id_foreign` (`kit_id`),
  KEY `saude_kit_itens_saude_item_id_foreign` (`saude_item_id`),
  CONSTRAINT `saude_kit_itens_kit_id_foreign` FOREIGN KEY (`kit_id`) REFERENCES `auto_socorro_kits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saude_kit_itens_saude_item_id_foreign` FOREIGN KEY (`saude_item_id`) REFERENCES `saude_itens` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `signature_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signature_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature_data` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `signable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signable_id` bigint unsigned NOT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `signature_requests_token_unique` (`token`),
  KEY `signature_requests_signable_type_signable_id_index` (`signable_type`,`signable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipos_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_trabajo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_trabajo_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `login_at` timestamp NOT NULL,
  `last_activity_at` timestamp NOT NULL,
  `logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_sessions_user_id_foreign` (`user_id`),
  KEY `user_sessions_session_id_index` (`session_id`),
  KEY `user_sessions_last_activity_at_index` (`last_activity_at`),
  KEY `user_sessions_logout_at_index` (`logout_at`),
  CONSTRAINT `user_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` json DEFAULT NULL,
  `signature` longtext COLLATE utf8mb4_unicode_ci,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_driver_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_driver_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint unsigned NOT NULL,
  `colaborador_id` bigint unsigned NOT NULL,
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_driver_logs_vehicle_id_started_at_index` (`vehicle_id`,`started_at`),
  KEY `vehicle_driver_logs_user_id_started_at_index` (`colaborador_id`,`started_at`),
  KEY `vehicle_driver_logs_colaborador_id_started_at_index` (`colaborador_id`,`started_at`),
  CONSTRAINT `vehicle_driver_logs_colaborador_id_foreign` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_driver_logs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `motivo_baja` text COLLATE utf8mb4_unicode_ci,
  `matricula` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehiculos_matricula_unique` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_08_14_170933_add_two_factor_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_02_27_213002_create_locacions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_02_27_213002_create_tipo_trabajos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_02_27_213003_create_peps_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_02_27_222551_create_colaboradores_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_02_27_222636_create_vehiculos_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_02_27_223024_create_asignaciones_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_02_27_223032_create_asignacion_colaboradores_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_02_27_223033_create_asignacion_vehiculos_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_02_28_073636_add_equipo_tipo_to_asignacion_pivots_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_02_28_194953_add_activo_to_records_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_02_28_194954_add_activo_to_peps_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_02_28_194954_add_activo_to_vehiculos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_02_28_194955_add_role_to_users_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_02_28_210404_remove_unique_from_peps_nombre',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_02_28_235428_add_visible_en_dashboard_to_colaboradores_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_03_01_121320_add_evento_fields_to_asignaciones_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_03_01_193719_create_dias_publicados_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_03_03_000001_create_avisos_tv_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_03_03_202953_add_imagem_to_avisos_tv_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_03_08_200001_convert_role_to_json_on_users_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_03_08_200002_create_epi_items_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_03_08_200003_create_epi_recepciones_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_03_08_200004_create_epi_entregas_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_03_08_200005_create_epi_ajustes_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_03_08_200006_add_unidade_to_epi_items_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_03_10_222618_add_stock_columns_to_epi_items_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_03_11_203945_add_firma_devolucion_to_epi_entregas_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_03_12_194700_fix_operador_role_name',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_03_13_000000_add_talla_to_epi_items',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_03_14_065820_expand_inventory_for_modular_tracking',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_03_14_073000_fix_epi_items_tipo_enum',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_03_14_075911_create_ferramentas_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_03_14_075918_create_extintores_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_03_14_075927_create_saude_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_03_14_080003_cleanup_modular_fields_from_epi_tables',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_03_14_183555_add_preventive_and_status_fields_to_ferramentas_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_03_15_101539_add_checklist_and_grouping_to_ferramenta_logs_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_03_15_131258_add_unidade_to_ferramenta_logs_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_03_14_190533_make_ferramentas_referencia_unique',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_03_15_135519_add_observacoes_to_ferramenta_logs_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_03_16_185757_add_technical_fields_to_users_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_03_17_061043_create_signature_requests_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_03_17_061536_add_signature_data_to_signature_requests_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_03_18_191331_change_user_agent_to_text_in_signature_requests_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_03_18_192558_add_metadata_to_signature_requests_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_03_19_205526_add_pwa_fields_to_assets_tables',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_03_19_205533_create_vehicle_driver_logs_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_03_20_051308_add_pin_to_colaboradores_and_refactor_driver_logs',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_03_20_060256_create_epi_pedidos_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_03_21_111909_create_app_notifications_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_03_21_112525_create_emergency_contacts_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_03_21_112526_create_emergency_procedures_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_03_21_112526_create_incident_reports_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_03_21_160931_add_logo_to_emergency_contacts_table',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_03_21_170109_create_guia_transportes_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_03_21_170110_create_guia_items_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_03_21_175347_add_status_and_numero_oficial_to_guia_transportes_table',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_03_21_175803_add_requerente_to_guia_transportes_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_03_21_180150_add_colaborador_id_to_app_notifications_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_03_21_210041_upgrade_signature_columns_to_longtext',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_03_22_134905_add_subtitulo_and_ordem_to_emergency_procedures',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_03_22_200000_add_terms_accepted_at_to_colaboradores_table',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_03_22_205837_create_legal_pages_table',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_03_23_000000_create_user_sessions_table',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_03_23_054401_update_privacy_policy_auditing_disclosure',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_03_24_070018_add_cpostal_fields_to_guia_transportes_table',47);
