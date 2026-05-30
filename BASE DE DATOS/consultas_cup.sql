-- ============================================================
--  CONSULTAS SQL — Sistema CUP (FICCT - UAGRM)
--  Motor: PostgreSQL 15+
--  Gestión: 1-2026
--  Organizadas por módulo del sistema (según casos de uso)
-- ============================================================


-- ============================================================
--  MÓDULO 1: AUTENTICACIÓN (CU01, CU02, CU04)
-- ============================================================

-- CU01: Iniciar sesión — verificar credenciales por email
SELECT u.id_usuario,
       u.ci,
       u.nombre,
       u.apellido,
       u.email,
       u.password_hash,
       u.activo,
       r.nombre_rol
FROM   usuario u
JOIN   rol r ON r.id_rol = u.id_rol
WHERE  u.email = 'ana.flores@gmail.com'
  AND  u.activo = TRUE;

-- CU01: Registrar evento de login en bitácora
INSERT INTO bitacora_auditoria (id_usuario, accion, tabla_afectada, descripcion, ip_origen)
VALUES (8, 'LOGIN', NULL, 'Inicio de sesión exitoso', '190.121.44.12');

-- CU02: Cerrar sesión — registrar logout en bitácora
INSERT INTO bitacora_auditoria (id_usuario, accion, tabla_afectada, descripcion, ip_origen)
VALUES (8, 'LOGOUT', NULL, 'Cierre de sesión', '190.121.44.12');

-- CU04: Cambiar contraseña — actualizar hash
UPDATE usuario
SET    password_hash = '$2a$12$NuevoHashBcrypt',
       updated_at    = NOW()
WHERE  id_usuario = 8
  AND  activo = TRUE;

-- CU01: Bloquear cuenta tras N intentos fallidos (verificación de estado)
SELECT activo FROM usuario WHERE email = 'ana.flores@gmail.com';

-- Consulta de auditoría: últimos 10 intentos de login de un usuario
SELECT accion, descripcion, ip_origen, fecha_hora
FROM   bitacora_auditoria
WHERE  id_usuario = 8
  AND  accion IN ('LOGIN','LOGOUT')
ORDER  BY fecha_hora DESC
LIMIT  10;


-- ============================================================
--  MÓDULO 2: GESTIÓN DE CUENTAS RBAC (CU05, CU31)
-- ============================================================

-- CU05: Listar todos los usuarios con su rol
SELECT u.id_usuario,
       u.ci,
       u.nombre || ' ' || u.apellido AS nombre_completo,
       u.email,
       r.nombre_rol,
       u.activo,
       u.created_at
FROM   usuario u
JOIN   rol r ON r.id_rol = u.id_rol
ORDER  BY r.nombre_rol, u.apellido;

-- CU05: Crear nuevo usuario con rol asignado
INSERT INTO usuario (ci, nombre, apellido, email, password_hash, id_rol)
VALUES ('9012345', 'Nuevo', 'Usuario', 'nuevo@ficct.edu.bo', '$2a$12$HashNuevo', 2);

-- CU05: Actualizar rol de un usuario (cambio de permisos)
UPDATE usuario
SET    id_rol     = 1,
       updated_at = NOW()
WHERE  id_usuario = 5;

-- CU05: Desactivar cuenta (baja lógica, nunca DELETE)
UPDATE usuario
SET    activo     = FALSE,
       updated_at = NOW()
WHERE  id_usuario = 10;

-- CU05: Reactivar cuenta
UPDATE usuario
SET    activo     = TRUE,
       updated_at = NOW()
WHERE  id_usuario = 10;

-- CU05: Listar usuarios por rol específico
SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.activo
FROM   usuario u
JOIN   rol r ON r.id_rol = u.id_rol
WHERE  r.nombre_rol = 'DOCENTE'
ORDER  BY u.apellido;

-- CU31: Importar cuentas masivas desde Excel/CSV
--       (simulación de inserción masiva con ON CONFLICT para idempotencia)
INSERT INTO usuario (ci, nombre, apellido, email, password_hash, id_rol)
VALUES
  ('9111111', 'Pedro',  'Mamani',  'pmamani@ficct.edu.bo',  '$2a$12$Hash001', 2),
  ('9222222', 'Sandra', 'Cuellar', 'scuellar@ficct.edu.bo', '$2a$12$Hash002', 2),
  ('9333333', 'Jorge',  'Vaca',    'jvaca@ficct.edu.bo',    '$2a$12$Hash003', 2)
ON CONFLICT (email) DO UPDATE
  SET nombre     = EXCLUDED.nombre,
      apellido   = EXCLUDED.apellido,
      updated_at = NOW();

-- CU31: Verificar cuántos usuarios se importaron en el último lote
SELECT COUNT(*) AS total_importados
FROM   bitacora_auditoria
WHERE  accion = 'INSERT'
  AND  tabla_afectada = 'usuario'
  AND  fecha_hora >= NOW() - INTERVAL '1 hour';


-- ============================================================
--  MÓDULO 3: REGISTRO DE POSTULANTES (CU07, CU08)
-- ============================================================

-- CU08: Registrar nuevo postulante (se hace junto con el usuario en transacción)
BEGIN;
  INSERT INTO usuario (ci, nombre, apellido, email, password_hash, id_rol)
  VALUES ('8999999', 'Mario', 'Chávez', 'mario.chavez@gmail.com', '$2a$12$HashPos', 3)
  RETURNING id_usuario;

  -- Usar el id_usuario retornado en la siguiente INSERT
  INSERT INTO postulante (id_usuario, ci, nombre, apellido, fecha_nacimiento, telefono, colegio_origen)
  VALUES (currval('usuario_id_usuario_seq'), '8999999', 'Mario', 'Chávez', '2007-03-10', '70399999', 'U.E. La Salle');
COMMIT;

-- CU08: Verificar si el CI ya está registrado (evitar duplicados)
SELECT COUNT(*) FROM postulante WHERE ci = '8999999';

-- CU08: Ver perfil completo de un postulante
SELECT p.id_postulante,
       p.ci,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       p.fecha_nacimiento,
       EXTRACT(YEAR FROM AGE(p.fecha_nacimiento)) AS edad,
       p.telefono,
       p.colegio_origen,
       u.email,
       u.activo,
       p.created_at
FROM   postulante p
JOIN   usuario u ON u.id_usuario = p.id_usuario
WHERE  p.id_postulante = 1;

-- CU08: Listar todos los postulantes registrados
SELECT p.id_postulante,
       p.ci,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       p.colegio_origen,
       u.email,
       p.created_at
FROM   postulante p
JOIN   usuario u ON u.id_usuario = p.id_usuario
ORDER  BY p.apellido, p.nombre;

-- CU07: Registrar pago de inscripción
INSERT INTO pago (id_postulante, monto, concepto, estado_pago, codigo_comprobante)
VALUES (1, 150.00, 'Inscripción CUP Gestión 1-2026', 'PENDIENTE', NULL);

-- CU07: Confirmar pago (respuesta de pasarela externa)
UPDATE pago
SET    estado_pago        = 'CONFIRMADO',
       codigo_comprobante = 'TIGO-20260315-000099',
       fecha_pago         = NOW()
WHERE  id_pago = 1;

-- CU07: Verificar si un postulante ya pagó
SELECT estado_pago, codigo_comprobante, fecha_pago
FROM   pago
WHERE  id_postulante = 1
  AND  estado_pago = 'CONFIRMADO';

-- CU07: Historial de pagos de un postulante
SELECT id_pago, monto, concepto, estado_pago, codigo_comprobante, fecha_pago
FROM   pago
WHERE  id_postulante = 1
ORDER  BY fecha_pago DESC;

-- CU08: Subir documento del postulante
INSERT INTO documento (id_postulante, tipo_documento, nombre_archivo, ruta_archivo)
VALUES (1, 'CI', 'ci_mario_chavez.pdf', '/uploads/docs/ci/ci_mario_chavez.pdf');

-- CU08: Verificar documentos pendientes de un postulante
SELECT tipo_documento, nombre_archivo, estado_verificacion, fecha_subida
FROM   documento
WHERE  id_postulante = 1
ORDER  BY fecha_subida;

-- CU08: Verificar/rechazar documento (Admin)
UPDATE documento
SET    estado_verificacion = 'VERIFICADO'
WHERE  id_documento = 1;

-- CU08: Postulantes con documentación incompleta
SELECT p.id_postulante,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       COUNT(d.id_documento) FILTER (WHERE d.estado_verificacion = 'PENDIENTE') AS docs_pendientes,
       COUNT(d.id_documento) FILTER (WHERE d.estado_verificacion = 'RECHAZADO') AS docs_rechazados
FROM   postulante p
LEFT   JOIN documento d ON d.id_postulante = p.id_postulante
GROUP  BY p.id_postulante, p.nombre, p.apellido
HAVING COUNT(d.id_documento) FILTER (WHERE d.estado_verificacion IN ('PENDIENTE','RECHAZADO')) > 0
ORDER  BY p.apellido;


-- ============================================================
--  MÓDULO 4: EXÁMENES Y NOTAS (CU13, CU15, CU16)
-- ============================================================

-- CU13: Listar postulantes de un grupo para registrar notas (vista del docente)
SELECT p.id_postulante,
       p.ci,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       gp.fecha_asignacion
FROM   grupo_postulante gp
JOIN   postulante p ON p.id_postulante = gp.id_postulante
WHERE  gp.id_grupo = 1
ORDER  BY p.apellido, p.nombre;

-- CU13: Registrar notas de un postulante en una materia
INSERT INTO evaluacion (id_postulante, id_materia, nota1, nota2, nota3, estado)
VALUES (1, 1, 80.00, 75.00, 82.00, 'PENDIENTE')
ON CONFLICT (id_postulante, id_materia) DO UPDATE
  SET nota1            = EXCLUDED.nota1,
      nota2            = EXCLUDED.nota2,
      nota3            = EXCLUDED.nota3,
      fecha_evaluacion = NOW();

-- CU13: Verificar que una nota esté en rango (validación en DB como respaldo)
--       La restricción CHECK ya lo garantiza, pero se puede consultar:
SELECT id_evaluacion, nota1, nota2, nota3, promedio
FROM   evaluacion
WHERE  id_postulante = 1 AND id_materia = 1;

-- CU15: Consultar promedio calculado (GENERATED ALWAYS AS STORED)
--       El promedio se actualiza solo; no requiere UPDATE manual.
SELECT e.id_evaluacion,
       p.nombre || ' ' || p.apellido AS postulante,
       m.nombre_materia,
       e.nota1, e.nota2, e.nota3,
       e.promedio,
       e.estado
FROM   evaluacion e
JOIN   postulante p ON p.id_postulante = e.id_postulante
JOIN   materia    m ON m.id_materia    = e.id_materia
WHERE  e.id_postulante = 1
ORDER  BY m.nombre_materia;

-- CU15: Calcular promedio global de un postulante entre todas las materias
SELECT p.id_postulante,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       ROUND(AVG(e.promedio), 2)     AS promedio_global
FROM   evaluacion e
JOIN   postulante p ON p.id_postulante = e.id_postulante
WHERE  e.id_postulante = 1
GROUP  BY p.id_postulante, p.nombre, p.apellido;

-- CU16: Actualizar estado de aprobación según promedio (>= 60 = APROBADO)
UPDATE evaluacion
SET    estado = CASE WHEN promedio >= 60 THEN 'APROBADO' ELSE 'REPROBADO' END
WHERE  id_postulante = 1;

-- CU16: Actualizar estado de TODAS las evaluaciones en lote (cierre de período)
UPDATE evaluacion
SET    estado = CASE WHEN promedio >= 60 THEN 'APROBADO' ELSE 'REPROBADO' END
WHERE  estado = 'PENDIENTE';

-- CU13: Vista completa de notas de un grupo con promedios
SELECT p.nombre || ' ' || p.apellido AS postulante,
       m.sigla,
       e.nota1, e.nota2, e.nota3,
       e.promedio,
       e.estado
FROM   evaluacion e
JOIN   postulante     p  ON p.id_postulante = e.id_postulante
JOIN   materia        m  ON m.id_materia    = e.id_materia
JOIN   grupo_postulante gp ON gp.id_postulante = e.id_postulante
WHERE  gp.id_grupo = 1
ORDER  BY p.apellido, m.sigla;

-- CU13: Verificar que no se ingresen más de 3 notas por materia
--       (la tabla tiene nota1, nota2, nota3 — estructura fija; restricción en diseño)
SELECT COUNT(*) FROM evaluacion
WHERE  id_postulante = 1 AND id_materia = 1;


-- ============================================================
--  MÓDULO 5: ALGORITMO DE ADMISIÓN (CU17)
-- ============================================================

-- CU17: Calcular puntaje total de cada postulante (promedio de promedios por materia)
SELECT p.id_postulante,
       p.nombre || ' ' || p.apellido      AS nombre_completo,
       ROUND(AVG(e.promedio), 2)          AS puntaje_total,
       CASE WHEN AVG(e.promedio) >= 60
            THEN 'APROBADO' ELSE 'REPROBADO' END AS resultado
FROM   evaluacion e
JOIN   postulante p ON p.id_postulante = e.id_postulante
GROUP  BY p.id_postulante, p.nombre, p.apellido
ORDER  BY puntaje_total DESC;

-- CU17: Actualizar puntaje_total y estado en POSTULACION
UPDATE postulacion po
SET    puntaje_total = sub.puntaje_total,
       estado        = CASE WHEN sub.puntaje_total >= 60 THEN 'APROBADO' ELSE 'REPROBADO' END
FROM (
    SELECT id_postulante, ROUND(AVG(promedio), 2) AS puntaje_total
    FROM   evaluacion
    GROUP  BY id_postulante
) sub
WHERE  po.id_postulante = sub.id_postulante;

-- CU17: Asignar carrera según opción 1 (si hay cupos disponibles)
UPDATE postulacion
SET    id_carrera_asignada = id_carrera_opcion1,
       estado              = 'ADMITIDO'
WHERE  estado = 'APROBADO'
  AND  id_carrera_opcion1 IN (
      SELECT id_carrera FROM carrera WHERE cupos_disponibles > 0
  );

-- CU17: Si no hay cupos en opción 1, intentar con opción 2
UPDATE postulacion
SET    id_carrera_asignada = id_carrera_opcion2,
       estado              = 'ADMITIDO'
WHERE  estado = 'APROBADO'
  AND  id_carrera_asignada IS NULL
  AND  id_carrera_opcion2 IN (
      SELECT id_carrera FROM carrera WHERE cupos_disponibles > 0
  );

-- CU17: Decrementar cupos al confirmar admisión
UPDATE carrera
SET    cupos_disponibles = cupos_disponibles - 1
WHERE  id_carrera IN (
    SELECT id_carrera_asignada
    FROM   postulacion
    WHERE  estado = 'ADMITIDO'
      AND  id_carrera_asignada IS NOT NULL
);

-- CU17: Listado final de admitidos con carrera asignada
SELECT p.nombre || ' ' || p.apellido AS postulante,
       po.puntaje_total,
       c1.sigla                       AS opcion_1,
       c2.sigla                       AS opcion_2,
       ca.nombre_carrera              AS carrera_asignada,
       po.estado
FROM   postulacion po
JOIN   postulante p   ON p.id_postulante   = po.id_postulante
JOIN   carrera    c1  ON c1.id_carrera     = po.id_carrera_opcion1
LEFT   JOIN carrera c2 ON c2.id_carrera   = po.id_carrera_opcion2
LEFT   JOIN carrera ca ON ca.id_carrera   = po.id_carrera_asignada
ORDER  BY po.puntaje_total DESC;


-- ============================================================
--  MÓDULO 6: GRUPOS (CU19, CU20)
-- ============================================================

-- CU19: Calcular número de grupos necesarios — CEIL(total_aprobados / 80)
SELECT CEIL(COUNT(*)::NUMERIC / 80) AS grupos_necesarios
FROM   postulacion
WHERE  estado IN ('APROBADO', 'ADMITIDO');

-- CU19: Registrar nuevos grupos calculados
INSERT INTO grupo (nombre_grupo, capacidad_maxima, aula, turno)
VALUES ('A-MAÑ-2026', 80, 'Aula 101 - FICCT', 'MAÑANA'),
       ('B-TAR-2026', 80, 'Aula 205 - FICCT', 'TARDE');

-- CU20: Asignar postulantes a grupos (distribución ordenada por puntaje DESC)
--       Asigna grupo según número de fila / capacidad
INSERT INTO grupo_postulante (id_grupo, id_postulante)
SELECT g.id_grupo,
       sub.id_postulante
FROM (
    SELECT po.id_postulante,
           ROW_NUMBER() OVER (ORDER BY po.puntaje_total DESC) AS fila
    FROM   postulacion po
    WHERE  po.estado IN ('APROBADO','ADMITIDO')
) sub
JOIN grupo g ON g.id_grupo = CEIL(sub.fila::NUMERIC / 80)
ON CONFLICT (id_grupo, id_postulante) DO NOTHING;

-- CU20: Ver distribución de postulantes por grupo
SELECT g.nombre_grupo,
       g.turno,
       COUNT(gp.id_postulante) AS total_asignados,
       g.capacidad_maxima,
       g.capacidad_maxima - COUNT(gp.id_postulante) AS cupos_restantes
FROM   grupo g
LEFT   JOIN grupo_postulante gp ON gp.id_grupo = g.id_grupo
WHERE  g.activo = TRUE
GROUP  BY g.id_grupo, g.nombre_grupo, g.turno, g.capacidad_maxima
ORDER  BY g.nombre_grupo;

-- CU20: Ver listado de postulantes de un grupo específico
SELECT g.nombre_grupo,
       p.ci,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       po.puntaje_total,
       gp.fecha_asignacion
FROM   grupo_postulante gp
JOIN   grupo      g  ON g.id_grupo        = gp.id_grupo
JOIN   postulante p  ON p.id_postulante   = gp.id_postulante
JOIN   postulacion po ON po.id_postulante = gp.id_postulante
WHERE  gp.id_grupo = 1
ORDER  BY po.puntaje_total DESC;


-- ============================================================
--  MÓDULO 7: GESTIÓN DE DOCENTES (CU21, CU22)
-- ============================================================

-- CU21: Registrar nuevo docente (transacción con usuario)
BEGIN;
  INSERT INTO usuario (ci, nombre, apellido, email, password_hash, id_rol)
  VALUES ('4999999', 'Elena', 'Soria', 'esoria@ficct.edu.bo', '$2a$12$DocHashNew', 2)
  RETURNING id_usuario;

  INSERT INTO docente (id_usuario, especialidad, titulo_academico, telefono)
  VALUES (currval('usuario_id_usuario_seq'), 'Estadística Aplicada', 'MAESTRIA', '70399888');
COMMIT;

-- CU21: Validar que el docente tenga título requerido (MAESTRIA o superior)
SELECT id_docente, titulo_academico
FROM   docente
WHERE  id_docente = 1
  AND  titulo_academico IN ('MAESTRIA','DOCTORADO');

-- CU21: Listar todos los docentes activos con sus datos
SELECT d.id_docente,
       u.ci,
       u.nombre || ' ' || u.apellido AS nombre_completo,
       d.especialidad,
       d.titulo_academico,
       d.telefono,
       u.email,
       d.activo
FROM   docente d
JOIN   usuario u ON u.id_usuario = d.id_usuario
WHERE  d.activo = TRUE
ORDER  BY u.apellido;

-- CU22: Asignar docente a grupo-materia
INSERT INTO asignacion_docente (id_docente, id_grupo, id_materia)
VALUES (1, 1, 1)
ON CONFLICT (id_grupo, id_materia) DO UPDATE
  SET id_docente       = EXCLUDED.id_docente,
      fecha_asignacion = NOW(),
      activo           = TRUE;

-- CU22: Ver carga horaria de un docente
SELECT d.id_docente,
       u.nombre || ' ' || u.apellido AS docente,
       g.nombre_grupo,
       g.turno,
       m.nombre_materia,
       m.horas_semana,
       gm.horario,
       ad.fecha_asignacion
FROM   asignacion_docente ad
JOIN   docente  d ON d.id_docente = ad.id_docente
JOIN   usuario  u ON u.id_usuario = d.id_usuario
JOIN   grupo    g ON g.id_grupo   = ad.id_grupo
JOIN   materia  m ON m.id_materia = ad.id_materia
LEFT   JOIN grupo_materia gm ON gm.id_grupo = ad.id_grupo AND gm.id_materia = ad.id_materia
WHERE  ad.id_docente = 1 AND ad.activo = TRUE
ORDER  BY g.nombre_grupo, m.nombre_materia;

-- CU22: Docentes sin asignación (para detectar sin carga horaria)
SELECT d.id_docente,
       u.nombre || ' ' || u.apellido AS docente,
       d.especialidad
FROM   docente d
JOIN   usuario u ON u.id_usuario = d.id_usuario
WHERE  d.activo = TRUE
  AND  d.id_docente NOT IN (
      SELECT DISTINCT id_docente FROM asignacion_docente WHERE activo = TRUE
  );

-- CU22: Grupos sin docente asignado en alguna materia
SELECT g.nombre_grupo,
       m.nombre_materia
FROM   grupo   g
CROSS  JOIN materia m
WHERE  g.activo = TRUE AND m.activo = TRUE
  AND  NOT EXISTS (
      SELECT 1 FROM asignacion_docente ad
      WHERE  ad.id_grupo   = g.id_grupo
        AND  ad.id_materia = m.id_materia
        AND  ad.activo     = TRUE
  )
ORDER  BY g.nombre_grupo, m.nombre_materia;


-- ============================================================
--  MÓDULO 8: DASHBOARD KPIs (CU29)
-- ============================================================

-- CU29: KPI 1 — Total de postulantes inscritos
SELECT COUNT(*) AS total_inscritos FROM postulante;

-- CU29: KPI 2 — Total de aprobados
SELECT COUNT(*) AS total_aprobados
FROM   postulacion
WHERE  estado IN ('APROBADO', 'ADMITIDO');

-- CU29: KPI 3 — Total de reprobados
SELECT COUNT(*) AS total_reprobados
FROM   postulacion
WHERE  estado = 'REPROBADO';

-- CU29: KPI 4 — Total de grupos habilitados
SELECT COUNT(*) AS total_grupos FROM grupo WHERE activo = TRUE;

-- CU29: KPI 5 — Total de admitidos con carrera asignada
SELECT COUNT(*) AS total_admitidos
FROM   postulacion
WHERE  estado = 'ADMITIDO';

-- CU29: KPI 6 — Total recaudado en pagos confirmados
SELECT SUM(monto) AS total_recaudado_bs
FROM   pago
WHERE  estado_pago = 'CONFIRMADO';

-- CU29: Todos los KPIs en una sola consulta (para el panel principal)
SELECT
    (SELECT COUNT(*) FROM postulante)                                          AS total_inscritos,
    (SELECT COUNT(*) FROM postulacion WHERE estado IN ('APROBADO','ADMITIDO')) AS total_aprobados,
    (SELECT COUNT(*) FROM postulacion WHERE estado = 'REPROBADO')              AS total_reprobados,
    (SELECT COUNT(*) FROM grupo WHERE activo = TRUE)                           AS total_grupos,
    (SELECT COUNT(*) FROM postulacion WHERE estado = 'ADMITIDO')               AS total_admitidos,
    (SELECT COALESCE(SUM(monto),0) FROM pago WHERE estado_pago = 'CONFIRMADO') AS total_recaudado_bs;

-- CU29: Gráfico — Rendimiento promedio por materia
SELECT m.nombre_materia,
       ROUND(AVG(e.promedio), 2)                                    AS promedio_general,
       COUNT(*) FILTER (WHERE e.estado = 'APROBADO')                AS aprobados,
       COUNT(*) FILTER (WHERE e.estado = 'REPROBADO')               AS reprobados,
       ROUND(COUNT(*) FILTER (WHERE e.estado = 'APROBADO')::NUMERIC
             / NULLIF(COUNT(*),0) * 100, 1)                         AS porcentaje_aprobacion
FROM   evaluacion e
JOIN   materia m ON m.id_materia = e.id_materia
GROUP  BY m.id_materia, m.nombre_materia
ORDER  BY m.nombre_materia;

-- CU29: Gráfico — Distribución de postulantes por carrera de opción 1
SELECT c.sigla,
       c.nombre_carrera,
       COUNT(po.id_postulacion)  AS total_postulaciones,
       c.cupos_totales,
       c.cupos_disponibles
FROM   carrera c
LEFT   JOIN postulacion po ON po.id_carrera_opcion1 = c.id_carrera
GROUP  BY c.id_carrera, c.sigla, c.nombre_carrera, c.cupos_totales, c.cupos_disponibles
ORDER  BY total_postulaciones DESC;

-- CU29: Gráfico — Postulantes por colegio de origen (top 10)
SELECT colegio_origen,
       COUNT(*) AS total
FROM   postulante
WHERE  colegio_origen IS NOT NULL
GROUP  BY colegio_origen
ORDER  BY total DESC
LIMIT  10;

-- CU29: Gráfico — Evolución de inscripciones por día
SELECT DATE(created_at)           AS fecha,
       COUNT(*)                   AS inscripciones_del_dia,
       SUM(COUNT(*)) OVER (ORDER BY DATE(created_at)) AS acumulado
FROM   postulante
GROUP  BY DATE(created_at)
ORDER  BY fecha;

-- CU29: Ranking de postulantes por puntaje (top 10)
SELECT ROW_NUMBER() OVER (ORDER BY po.puntaje_total DESC) AS puesto,
       p.nombre || ' ' || p.apellido AS nombre_completo,
       po.puntaje_total,
       ca.sigla AS carrera_asignada,
       po.estado
FROM   postulacion po
JOIN   postulante p   ON p.id_postulante   = po.id_postulante
LEFT   JOIN carrera ca ON ca.id_carrera    = po.id_carrera_asignada
ORDER  BY po.puntaje_total DESC
LIMIT  10;


-- ============================================================
--  MÓDULO 9: AUDITORÍA Y BITÁCORA
-- ============================================================

-- Consultar toda la bitácora ordenada por fecha
SELECT ba.id_bitacora,
       u.nombre || ' ' || u.apellido AS usuario,
       r.nombre_rol,
       ba.accion,
       ba.tabla_afectada,
       ba.descripcion,
       ba.ip_origen,
       ba.fecha_hora
FROM   bitacora_auditoria ba
LEFT   JOIN usuario u ON u.id_usuario = ba.id_usuario
LEFT   JOIN rol     r ON r.id_rol     = u.id_rol
ORDER  BY ba.fecha_hora DESC
LIMIT  50;

-- Filtrar bitácora por tipo de acción
SELECT * FROM bitacora_auditoria
WHERE  accion = 'LOGIN'
ORDER  BY fecha_hora DESC;

-- Filtrar bitácora por rango de fechas
SELECT ba.id_bitacora,
       u.nombre || ' ' || u.apellido AS usuario,
       ba.accion,
       ba.descripcion,
       ba.ip_origen,
       ba.fecha_hora
FROM   bitacora_auditoria ba
LEFT   JOIN usuario u ON u.id_usuario = ba.id_usuario
WHERE  ba.fecha_hora BETWEEN '2026-03-01' AND '2026-04-30'
ORDER  BY ba.fecha_hora DESC;

-- Acciones por usuario (resumen)
SELECT u.nombre || ' ' || u.apellido AS usuario,
       ba.accion,
       COUNT(*) AS total
FROM   bitacora_auditoria ba
JOIN   usuario u ON u.id_usuario = ba.id_usuario
GROUP  BY u.id_usuario, u.nombre, u.apellido, ba.accion
ORDER  BY usuario, ba.accion;

-- IPs con más actividad (detección de accesos sospechosos)
SELECT ip_origen,
       COUNT(*) AS total_accesos,
       COUNT(DISTINCT id_usuario) AS usuarios_distintos
FROM   bitacora_auditoria
WHERE  accion = 'LOGIN'
GROUP  BY ip_origen
ORDER  BY total_accesos DESC;


-- ============================================================
--  MÓDULO 10: REPORTES Y CONSULTAS GENERALES DEL SISTEMA
-- ============================================================

-- Reporte general: estado completo de cada postulante
SELECT p.ci,
       p.nombre || ' ' || p.apellido           AS nombre_completo,
       p.colegio_origen,
       pg.estado_pago,
       po.puntaje_total,
       po.estado                               AS estado_postulacion,
       ca.sigla                                AS carrera_asignada,
       g.nombre_grupo                          AS grupo_asignado
FROM   postulante p
LEFT   JOIN pago pg ON pg.id_postulante = p.id_postulante
                    AND pg.estado_pago  = 'CONFIRMADO'
LEFT   JOIN postulacion po ON po.id_postulante  = p.id_postulante
LEFT   JOIN carrera ca     ON ca.id_carrera     = po.id_carrera_asignada
LEFT   JOIN grupo_postulante gp ON gp.id_postulante = p.id_postulante
LEFT   JOIN grupo g            ON g.id_grupo    = gp.id_grupo
ORDER  BY p.apellido, p.nombre;

-- Reporte de notas completo por postulante y materia
SELECT p.nombre || ' ' || p.apellido AS postulante,
       m.nombre_materia,
       e.nota1, e.nota2, e.nota3,
       e.promedio,
       e.estado
FROM   evaluacion e
JOIN   postulante p ON p.id_postulante = e.id_postulante
JOIN   materia    m ON m.id_materia    = e.id_materia
ORDER  BY p.apellido, p.nombre, m.nombre_materia;

-- Parámetros del sistema
SELECT clave, valor, descripcion, updated_at
FROM   parametro_sistema
ORDER  BY clave;

-- Actualizar un parámetro del sistema
UPDATE parametro_sistema
SET    valor      = '65',
       updated_at = NOW()
WHERE  clave = 'nota_minima_aprobacion';

-- Consulta de carreras con cupos
SELECT sigla, nombre_carrera, cupos_totales, cupos_disponibles,
       cupos_totales - cupos_disponibles AS cupos_ocupados,
       ROUND((cupos_totales - cupos_disponibles)::NUMERIC / cupos_totales * 100, 1) AS porcentaje_ocupacion
FROM   carrera
WHERE  activo = TRUE
ORDER  BY sigla;

-- Materias activas del CUP
SELECT id_materia, nombre_materia, sigla, horas_semana
FROM   materia
WHERE  activo = TRUE
ORDER  BY nombre_materia;

-- Estado del período de admisión activo
SELECT id_periodo, nombre, fecha_inicio, fecha_fin, estado,
       fecha_fin - CURRENT_DATE AS dias_restantes
FROM   periodo_admision
WHERE  estado = 'ACTIVO';

-- ============================================================
--  FIN DE CONSULTAS
-- ============================================================
