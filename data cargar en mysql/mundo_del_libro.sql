-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla mundo_del_libro.books
CREATE TABLE IF NOT EXISTS `books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` smallint unsigned DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `stock_total` int unsigned NOT NULL DEFAULT '1',
  `stock_disponible` int unsigned NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `books_category_id_foreign` (`category_id`),
  CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.books: ~7 rows (aproximadamente)
INSERT INTO `books` (`id`, `titulo`, `autor`, `descripcion`, `imagen`, `anio`, `category_id`, `stock_total`, `stock_disponible`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Cien años de soledad', 'Gabriel García Márquez', 'Imagina un pueblo perdido en medio de la selva donde los muertos regresan a conversar porque se sienten solos, una mujer asciende al cielo mientras tiende las sábanas y una lluvia interminable dura casi cinco años: ese lugar es Macondo, el escenario de Cien años de soledad, la obra maestra de Gabriel García Márquez. Más que una simple historia, es un viaje hipnótico a través de siete generaciones de la familia Buendía, atrapadas en un laberinto de pasiones prohibidas, guerras imposibles, inventos mágicos y el temor a una antigua profecía que amenaza con condenar su linaje. Con un ritmo envolvente donde lo sobrenatural ocurre como algo cotidiano, este libro te atrapa desde su primera línea para demostrarte que el destino, el amor y la memoria pueden ser tan fascinantes como inevitables, retándote a descifrar un enigma que lleva más de un siglo escrito.', 'baab5938-0c6f-4de6-ae0b-07da5fbfd978.jpg', 1967, 1, 3, 3, 1, '2026-10-31 11:10:25', '2026-11-18 20:48:56'),
	(2, 'Drácula', 'Bram Stoker', 'Imagina viajar por negocios a un castillo aislado entre las montañas de Transilvania, descubrir que tu anfitrión no se refleja en los espejos, trepa por los muros como un lagarto y planea desatar una plaga de muertos vivientes en el corazón de Londres: esa es la pesadilla en la que te sumerge Drácula, la novela gótica de Bram Stoker publicada en 1897. Contada de forma adictiva a través de diarios íntimos, cartas, telegramas y recortes de periódico como si estuvieras leyendo los archivos secretos de una investigación real, la historia sigue al joven abogado Jonathan Harker, a su prometida Mina Murray, a su amiga Lucy Westenra y al astuto profesor Abraham Van Helsing en una carrera desesperada contra el tiempo para cazar al vampiro más icónico de la literatura. Entre tormentas en alta mar, manicomios, criptas polvorientas y el choque entre la ciencia moderna y las supersticiones ancestrales, este libro te atrapará con su suspenso psicológico y terror atmosférico desde la primera página.', '9c4fe427-0182-484b-bac4-c762c1b4aef7.jpg', 1897, 3, 2, 2, 1, '2026-10-31 11:10:25', '2026-11-18 21:02:45'),
	(3, 'Orgullo y prejuicio', 'Jane Austen', 'Imagina una época donde el futuro y la supervivencia económica de cinco hermanas dependen por completo de lograr un buen matrimonio, pero la segunda de ellas, la ingeniosa y rebelde Elizabeth Bennet, se niega a casarse sin amor y choca frontalmente contra el hombre más rico, reservado y aparentemente arrogante de la región, el señor Fitzwilliam Darcy. Publicada en 1813 por Jane Austen, Orgullo y prejuicio es una brillante comedia romántica y sátira social que te atrapa desde su icónica primera frase con bailes de salón, chismes familiares, cartas reveladoras, escándalos amorosos y duelos verbales cargados de ironía. A medida que Elizabeth y Darcy se ven obligados a superar sus propios errores de juicio —el orgullo de clase de él y los prejuicios precipitados de ella—, esta novela demuestra por qué sigue siendo la historia definitiva del tropo "de enemigos a amantes", ofreciendo una mirada aguda y divertida sobre el amor verdadero, las presiones sociales y la valentía de mantener los propios principios.', 'bc820d1b-9cb1-46cf-9a47-a12456075454.jpg', 1813, 2, 2, 2, 1, '2026-10-31 11:10:25', '2026-11-18 21:08:24'),
	(4, 'Fundación', 'Isaac Asimov', '¿Imaginas que un brillante matemático pudiera predecir el futuro de toda la humanidad con ecuaciones estadísticas y descubriera que el colosal Imperio Galáctico está condenado a caer en treinta mil años de barbarie y oscuridad? Ese es el punto de partida de Fundación, la obra maestra de la ciencia ficción publicada en 1951 por Isaac Asimov, donde el científico Hari Seldon utiliza la "psicohistoria" para crear un plan secreto: reunir a las mentes más brillantes de la galaxia en un planeta remoto llamado Términus con el fin de preservar el conocimiento humano y reducir esa era de caos a solo mil años. Sin embargo, los miembros de esta Fundación pronto descubren que están rodeados de reinos hostiles y que deberán sobrevivir a crisis políticas, religiosas y económicas utilizando únicamente su ingenio, la ciencia y la diplomacia en lugar de la fuerza bruta, demostrando que la violencia es el último recurso del incompetente en una fascinante partida de ajedrez cósmica que inspiró sagas como Star Wars y Dune.', 'c88ab2f3-18c9-4af6-a230-14966d432b48.jpg', 1951, 4, 2, 2, 1, '2026-10-31 11:10:25', '2026-11-18 21:06:28'),
	(5, 'Clean Code', 'Robert C. Martin', '¿Alguna vez has abierto un proyecto que programaste hace apenas tres meses y has sentido que lo escribió un completo desconocido en un idioma imposible de descifrar? Clean Code de Robert C. Martin es el manual definitivo que te enseña a dar el salto de simplemente "hacer que el programa funcione" a convertirte en un verdadero artesano del software cuyo código da gusto leer y mantener. A través de ejemplos reales, casos prácticos de rescate de código desastroso y consejos directos sin rodeos, descubrirás cómo nombrar variables con precisión quirúrgica, crear funciones pequeñas que hagan una sola cosa a la perfección, eliminar comentarios redundantes y blindar tus proyectos contra errores mediante pruebas limpias. Leer este libro no solo te ahorrará horas de frustración depurando código caótico en tus tareas universitarias, sino que te dará las herramientas profesionales que las grandes empresas tecnológicas buscan desde el primer día.', '6921ddaa-044d-4b5d-bb41-72c67f07cb08.jpg', 2008, 6, 1, 1, 1, '2026-10-31 11:10:25', '2026-11-18 20:59:49'),
	(6, 'El Principito', 'Antoine de Saint-Exupéry', '¿Qué pasaría si tu avión sufriera una avería en medio del desierto del Sahara y, al amanecer, apareciera frente a ti un misterioso niño de cabellos dorados que dice venir de un diminuto asteroide pidiéndote que le dibujes un cordero? Así comienza El Principito, la inolvidable novela corta publicada en 1943 por Antoine de Saint-Exupéry, en la que acompañarás a este joven viajero interplanetario mientras relata sus encuentros con los absurdos adultos que habitan distintos planetas —como un rey sin súbditos, un vanidoso, un hombre de negocios que cuenta estrellas o un geógrafo que nunca explora— hasta llegar a la Tierra. A través de sus conversaciones con una rosa orgullosa, una serpiente enigmática y un sabio zorro que le revela el secreto de "domesticar" y ver con el corazón, este libro aparentemente sencillo esconde una profunda reflexión sobre la amistad, el amor, la soledad y el peligro de olvidar al niño que fuimos al crecer, convirtiéndose en una lectura rápida, emotiva y llena de frases que te acompañarán toda la vida.', '284c506d-de8a-45b7-af3b-33b7a3d94a1f.jpg', 1943, 7, 3, 3, 1, '2026-10-31 11:10:25', '2026-11-18 21:09:30');

-- Volcando estructura para tabla mundo_del_libro.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.cache: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.cache_locks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.categories: ~7 rows (aproximadamente)
INSERT INTO `categories` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Novela', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(2, 'Romance', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(3, 'Terror', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(4, 'Ciencia ficción', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(5, 'Historia', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(6, 'Técnico', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(7, 'Infantil', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(8, 'Anime', '2026-11-01 02:06:08', '2026-11-01 02:06:08');

-- Volcando estructura para tabla mundo_del_libro.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
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

-- Volcando datos para la tabla mundo_del_libro.job_batches: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.loans
CREATE TABLE IF NOT EXISTS `loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `book_id` bigint unsigned NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_limite` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prestado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loans_user_id_foreign` (`user_id`),
  KEY `loans_book_id_foreign` (`book_id`),
  CONSTRAINT `loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.loans: ~0 rows (aproximadamente)
INSERT INTO `loans` (`id`, `user_id`, `book_id`, `fecha_prestamo`, `fecha_limite`, `fecha_devolucion`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 3, 6, '2026-11-18', '2026-11-25', '2026-11-18', 'devuelto', '2026-11-18 21:09:16', '2026-11-18 21:09:30');

-- Volcando estructura para tabla mundo_del_libro.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.migrations: ~0 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_01_01_000001_create_roles_table', 1),
	(5, '2025_01_01_000002_add_role_to_users_table', 1),
	(6, '2025_01_01_000003_create_categories_table', 1),
	(7, '2025_01_01_000004_create_books_table', 1),
	(8, '2025_01_01_000005_create_loans_table', 1),
	(9, '2026_11_18_140242_add_imagen_to_books_table', 2),
	(10, '2026_11_18_144100_add_descripcion_to_books_table', 3);

-- Volcando estructura para tabla mundo_del_libro.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mundo_del_libro.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.roles: ~2 rows (aproximadamente)
INSERT INTO `roles` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'administrador', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(2, 'bibliotecario', '2026-10-31 11:10:25', '2026-10-31 11:10:25'),
	(3, 'lector', '2026-10-31 11:10:25', '2026-10-31 11:10:25');

-- Volcando estructura para tabla mundo_del_libro.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
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

-- Volcando datos para la tabla mundo_del_libro.sessions: ~1 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('JqTW18rONjbsQ4agEUnG6YpTIfFYRK8qe2veks1B', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ2am9KbEQ1MGkxbTJwdnQ3QVhjOFFsMVc3MjU2NjNJMEJkdnZteW1FIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xpYnJvcyIsInJvdXRlIjoibGlicm9zLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImU5ZTIzMzA0MDQ1NWM4N2ZlMTM0MWRhMDE0ZmExMDY4NGNiMmY4Y2Q0ZjQ5MGJiZTcxYjZlMjU5OTZjNjA1OTkifQ==', 1795014617);

-- Volcando estructura para tabla mundo_del_libro.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla mundo_del_libro.users: ~3 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@biblioteca.test', NULL, '$2y$12$GVE/X99qGCD/k9cAXr8OuOHjhkglhbFKsiyIGG.jAH3o4nJrWlw3C', 1, 1, NULL, '2026-10-31 11:10:25', '2026-11-18 20:11:43'),
	(2, 'Bibliotecario Demo', 'bibliotecario@biblioteca.test', NULL, '$2y$12$6YxWOxC4qDHXpMm9uSlLj.i0sdU03ju7.PCUPvW0c71hFRLKIhY/e', 2, 1, NULL, '2026-10-31 11:10:25', '2026-11-18 20:11:53'),
	(3, 'Lector Demo', 'lector@biblioteca.test', NULL, '$2y$12$oKGANHHTwxuHF2xTHDOo4OBqS9sKGxlLbltrA33gOp1ZfBSqP9eHq', 3, 1, NULL, '2026-10-31 11:10:25', '2026-11-18 20:49:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
