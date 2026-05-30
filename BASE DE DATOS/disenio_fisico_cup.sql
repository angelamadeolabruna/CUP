-- ============================================================
--  DISEÑO FÍSICO — Sistema CUP (FICCT - UAGRM)
--  Motor: PostgreSQL 15+
--  Gestión: 1-2026
-- ============================================================

-- ============================================================
--  EXTENSIONES
-- ============================================================
CREATE EXTENSION IF NOT EXISTS "pgcrypto";   -- gen_random_uuid(), crypt()
CREATE EXTENSION IF NOT EXISTS "pg_trgm";    -- búsqueda full-text en nombres

-- ============================================================
--  1. ROL
-- ============================================================
CREATE TABLE rol (
    id_rol        SERIAL          PRIMARY KEY,
    nombre_rol    VARCHAR(50)     NOT NULL UNIQUE,
    descripcion   VARCHAR(255),
    created_at    TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE  rol              IS 'Roles RBAC del sistema (Administrador, Docente, Postulante).';
COMMENT ON COLUMN rol.nombre_rol   IS 'Nombre único del rol, p.ej. ADMIN, DOCENTE, POSTULANTE.';

-- ============================================================
--  2. USUARIO
-- ============================================================
CREATE TABLE usuario (
    id_usuario     SERIAL          PRIMARY KEY,
    ci             VARCHAR(20)     NOT NULL UNIQUE,
    nombre         VARCHAR(100)    NOT NULL,
    apellido       VARCHAR(100)    NOT NULL,
    email          VARCHAR(150)    NOT NULL UNIQUE,
    password_hash  VARCHAR(255)    NOT NULL,
    id_rol         INT             NOT NULL REFERENCES rol(id_rol)
                                   ON UPDATE CASCADE ON DELETE RESTRICT,
    activo         BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_usuario_rol    ON usuario(id_rol);
CREATE INDEX idx_usuario_email  ON usuario(email);

COMMENT ON TABLE  usuario              IS 'Cuenta de acceso al sistema. Cada persona (docente o postulante) tiene exactamente un usuario.';
COMMENT ON COLUMN usuario.ci           IS 'Cédula de identidad boliviana, sin extensión departamental.';
COMMENT ON COLUMN usuario.password_hash IS 'Hash bcrypt generado con pgcrypto.crypt(). Nunca almacenar texto plano.';
COMMENT ON COLUMN usuario.id_rol       IS 'FK al rol RBAC asignado.';

-- ============================================================
--  3. BITACORA_AUDITORIA
-- ============================================================
CREATE TABLE bitacora_auditoria (
    id_bitacora     BIGSERIAL       PRIMARY KEY,
    id_usuario      INT             REFERENCES usuario(id_usuario)
                                    ON UPDATE CASCADE ON DELETE SET NULL,
    accion          VARCHAR(50)     NOT NULL
                                    CHECK (accion IN ('LOGIN','LOGOUT','INSERT','UPDATE','DELETE','EXPORT')),
    tabla_afectada  VARCHAR(100),
    descripcion     TEXT,
    ip_origen       INET,
    fecha_hora      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_bitacora_usuario   ON bitacora_auditoria(id_usuario);
CREATE INDEX idx_bitacora_fecha     ON bitacora_auditoria(fecha_hora DESC);

COMMENT ON TABLE  bitacora_auditoria           IS 'Registro inmutable de todas las acciones relevantes del sistema (trazabilidad RBAC).';
COMMENT ON COLUMN bitacora_auditoria.ip_origen  IS 'Tipo INET de PostgreSQL; soporta IPv4 e IPv6 sin conversión.';
COMMENT ON COLUMN bitacora_auditoria.accion     IS 'Conjunto cerrado de acciones auditables.';

-- ============================================================
--  4. PARAMETRO_SISTEMA  (no modelado en MDJ; inferido del doc)
-- ============================================================
CREATE TABLE parametro_sistema (
    id_parametro    SERIAL          PRIMARY KEY,
    clave           VARCHAR(100)    NOT NULL UNIQUE,
    valor           TEXT            NOT NULL,
    descripcion     VARCHAR(255),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE parametro_sistema IS 'Parámetros configurables en caliente: cupo_min_grupo, nota_aprobacion, etc.';

-- ============================================================
--  5. PERIODO_ADMISION
-- ============================================================
CREATE TABLE periodo_admision (
    id_periodo   SERIAL          PRIMARY KEY,
    nombre       VARCHAR(100)    NOT NULL,
    fecha_inicio DATE            NOT NULL,
    fecha_fin    DATE            NOT NULL,
    estado       VARCHAR(20)     NOT NULL DEFAULT 'PLANIFICADO'
                                 CHECK (estado IN ('PLANIFICADO','ACTIVO','CERRADO')),
    created_at   TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    CONSTRAINT ck_periodo_fechas CHECK (fecha_fin > fecha_inicio)
);

COMMENT ON TABLE  periodo_admision        IS 'Convocatoria académica del CUP. Solo puede existir un período ACTIVO a la vez (controlado en aplicación).';
COMMENT ON COLUMN periodo_admision.estado IS 'Ciclo de vida del período.';

-- ============================================================
--  6. CARRERA
-- ============================================================
CREATE TABLE carrera (
    id_carrera        SERIAL          PRIMARY KEY,
    nombre_carrera    VARCHAR(150)    NOT NULL,
    sigla             VARCHAR(10)     NOT NULL UNIQUE,
    cupos_totales     INT             NOT NULL CHECK (cupos_totales > 0),
    cupos_disponibles INT             NOT NULL CHECK (cupos_disponibles >= 0),
    activo            BOOLEAN         NOT NULL DEFAULT TRUE,
    CONSTRAINT ck_cupos CHECK (cupos_disponibles <= cupos_totales)
);

COMMENT ON TABLE  carrera                   IS 'Carreras de la FICCT disponibles para postulación.';
COMMENT ON COLUMN carrera.sigla             IS 'Código corto único, p.ej. ING-SIS, ING-IND.';
COMMENT ON COLUMN carrera.cupos_disponibles IS 'Se decrementa automáticamente al confirmar admisión (trigger o lógica de negocio).';

-- ============================================================
--  7. POSTULANTE
-- ============================================================
CREATE TABLE postulante (
    id_postulante    SERIAL          PRIMARY KEY,
    id_usuario       INT             NOT NULL UNIQUE
                                     REFERENCES usuario(id_usuario)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    ci               VARCHAR(20)     NOT NULL UNIQUE,
    nombre           VARCHAR(100)    NOT NULL,
    apellido         VARCHAR(100)    NOT NULL,
    fecha_nacimiento DATE            NOT NULL,
    telefono         VARCHAR(20),
    colegio_origen   VARCHAR(200),
    created_at       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_postulante_usuario ON postulante(id_usuario);

COMMENT ON TABLE  postulante            IS 'Datos académicos y personales del aspirante al CUP.';
COMMENT ON COLUMN postulante.id_usuario IS 'Relación 1-a-1 con USUARIO (UNIQUE). El usuario tiene rol POSTULANTE.';

-- ============================================================
--  8. POSTULACION
-- ============================================================
CREATE TABLE postulacion (
    id_postulacion      SERIAL          PRIMARY KEY,
    id_postulante       INT             NOT NULL REFERENCES postulante(id_postulante)
                                        ON UPDATE CASCADE ON DELETE RESTRICT,
    id_carrera_opcion1  INT             NOT NULL REFERENCES carrera(id_carrera)
                                        ON UPDATE CASCADE ON DELETE RESTRICT,
    id_carrera_opcion2  INT             REFERENCES carrera(id_carrera)
                                        ON UPDATE CASCADE ON DELETE SET NULL,
    puntaje_total       NUMERIC(5,2)    CHECK (puntaje_total BETWEEN 0 AND 100),
    estado              VARCHAR(20)     NOT NULL DEFAULT 'PENDIENTE'
                                        CHECK (estado IN ('PENDIENTE','APROBADO','REPROBADO','ADMITIDO')),
    id_carrera_asignada INT             REFERENCES carrera(id_carrera)
                                        ON UPDATE CASCADE ON DELETE SET NULL,
    fecha_postulacion   TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    CONSTRAINT ck_opciones_distintas CHECK (id_carrera_opcion1 <> id_carrera_opcion2)
);

CREATE INDEX idx_postulacion_postulante ON postulacion(id_postulante);
CREATE INDEX idx_postulacion_estado     ON postulacion(estado);

COMMENT ON TABLE  postulacion                   IS 'Solicitud formal de ingreso al CUP. Registra las dos opciones de carrera y el resultado final.';
COMMENT ON COLUMN postulacion.puntaje_total      IS 'Promedio ponderado calculado por CU17 (Algoritmo Admisión).';
COMMENT ON COLUMN postulacion.id_carrera_asignada IS 'Carrera asignada al concluir el algoritmo de admisión (CU17).';

-- ============================================================
--  9. DOCUMENTO
-- ============================================================
CREATE TABLE documento (
    id_documento         SERIAL          PRIMARY KEY,
    id_postulante        INT             NOT NULL REFERENCES postulante(id_postulante)
                                         ON UPDATE CASCADE ON DELETE CASCADE,
    tipo_documento       VARCHAR(50)     NOT NULL
                                         CHECK (tipo_documento IN ('CI','CERTIFICADO_BACHILLER','FOTO','COMPROBANTE_PAGO','OTRO')),
    nombre_archivo       VARCHAR(255)    NOT NULL,
    ruta_archivo         VARCHAR(500)    NOT NULL,
    estado_verificacion  VARCHAR(20)     NOT NULL DEFAULT 'PENDIENTE'
                                         CHECK (estado_verificacion IN ('PENDIENTE','VERIFICADO','RECHAZADO')),
    fecha_subida         TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_documento_postulante ON documento(id_postulante);

COMMENT ON TABLE  documento                      IS 'Archivos digitales requeridos durante la inscripción del postulante.';
COMMENT ON COLUMN documento.ruta_archivo          IS 'Ruta relativa en el sistema de archivos o URL del bucket de almacenamiento en nube.';
COMMENT ON COLUMN documento.estado_verificacion   IS 'Estado de revisión manual por el Administrador.';

-- ============================================================
-- 10. PAGO
-- ============================================================
CREATE TABLE pago (
    id_pago            SERIAL          PRIMARY KEY,
    id_postulante      INT             NOT NULL REFERENCES postulante(id_postulante)
                                       ON UPDATE CASCADE ON DELETE RESTRICT,
    monto              NUMERIC(8,2)    NOT NULL CHECK (monto > 0),
    concepto           VARCHAR(150)    NOT NULL,
    estado_pago        VARCHAR(20)     NOT NULL DEFAULT 'PENDIENTE'
                                       CHECK (estado_pago IN ('PENDIENTE','CONFIRMADO','RECHAZADO')),
    codigo_comprobante VARCHAR(100)    UNIQUE,
    fecha_pago         TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_pago_postulante ON pago(id_postulante);
CREATE INDEX idx_pago_estado     ON pago(estado_pago);

COMMENT ON TABLE  pago                    IS 'Registro de pagos de inscripción procesados a través de la pasarela externa (CU07).';
COMMENT ON COLUMN pago.codigo_comprobante  IS 'Código único devuelto por la pasarela de pago. UNIQUE garantiza idempotencia.';

-- ============================================================
-- 11. MATERIA
-- ============================================================
CREATE TABLE materia (
    id_materia     SERIAL          PRIMARY KEY,
    nombre_materia VARCHAR(100)    NOT NULL,
    sigla          VARCHAR(10)     NOT NULL UNIQUE,
    horas_semana   INT             NOT NULL CHECK (horas_semana > 0),
    activo         BOOLEAN         NOT NULL DEFAULT TRUE
);

COMMENT ON TABLE materia IS 'Materias del plan de estudios del CUP (Matemáticas, Física, Química, etc.).';

-- ============================================================
-- 12. EVALUACION
-- ============================================================
CREATE TABLE evaluacion (
    id_evaluacion    SERIAL          PRIMARY KEY,
    id_postulante    INT             NOT NULL REFERENCES postulante(id_postulante)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    id_materia       INT             NOT NULL REFERENCES materia(id_materia)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    nota1            NUMERIC(5,2)    CHECK (nota1 BETWEEN 0 AND 100),
    nota2            NUMERIC(5,2)    CHECK (nota2 BETWEEN 0 AND 100),
    nota3            NUMERIC(5,2)    CHECK (nota3 BETWEEN 0 AND 100),
    promedio         NUMERIC(5,2)    GENERATED ALWAYS AS
                                     (ROUND((COALESCE(nota1,0) + COALESCE(nota2,0) + COALESCE(nota3,0)) / 3.0, 2))
                                     STORED,
    estado           VARCHAR(20)     NOT NULL DEFAULT 'PENDIENTE'
                                     CHECK (estado IN ('PENDIENTE','APROBADO','REPROBADO')),
    fecha_evaluacion TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_evaluacion UNIQUE (id_postulante, id_materia)
);

CREATE INDEX idx_evaluacion_postulante ON evaluacion(id_postulante);
CREATE INDEX idx_evaluacion_materia    ON evaluacion(id_materia);

COMMENT ON TABLE  evaluacion          IS 'Calificaciones del postulante por materia. El promedio es una columna GENERATED ALWAYS AS STORED (calculada automáticamente por PostgreSQL).';
COMMENT ON COLUMN evaluacion.promedio  IS 'Columna calculada: (nota1+nota2+nota3)/3. No requiere trigger ni CU15 adicional; PostgreSQL la actualiza al hacer INSERT/UPDATE.';
COMMENT ON COLUMN evaluacion.estado    IS 'Derivado del promedio: APROBADO si promedio >= 60, REPROBADO en caso contrario (actualizado por trigger o lógica CU16).';

-- ============================================================
-- 13. GRUPO
-- ============================================================
CREATE TABLE grupo (
    id_grupo        SERIAL          PRIMARY KEY,
    nombre_grupo    VARCHAR(50)     NOT NULL UNIQUE,
    capacidad_maxima INT            NOT NULL DEFAULT 80 CHECK (capacidad_maxima > 0),
    aula            VARCHAR(50),
    turno           VARCHAR(10)     NOT NULL
                                    CHECK (turno IN ('MAÑANA','TARDE','NOCHE')),
    activo          BOOLEAN         NOT NULL DEFAULT TRUE
);

COMMENT ON TABLE  grupo             IS 'Grupo de estudio del CUP. La capacidad máxima (80) es el divisor del algoritmo CEIL en CU19.';
COMMENT ON COLUMN grupo.nombre_grupo IS 'Identificador visual del grupo, p.ej. A-MAÑ-2026, B-TAR-2026.';

-- ============================================================
-- 14. GRUPO_POSTULANTE
-- ============================================================
CREATE TABLE grupo_postulante (
    id_grupo_postulante SERIAL      PRIMARY KEY,
    id_grupo            INT         NOT NULL REFERENCES grupo(id_grupo)
                                    ON UPDATE CASCADE ON DELETE RESTRICT,
    id_postulante       INT         NOT NULL REFERENCES postulante(id_postulante)
                                    ON UPDATE CASCADE ON DELETE RESTRICT,
    fecha_asignacion    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_grupo_postulante UNIQUE (id_grupo, id_postulante)
);

CREATE INDEX idx_gp_grupo      ON grupo_postulante(id_grupo);
CREATE INDEX idx_gp_postulante ON grupo_postulante(id_postulante);

COMMENT ON TABLE grupo_postulante IS 'Tabla de unión N:M entre GRUPO y POSTULANTE. Un postulante pertenece a un solo grupo (controlado en aplicación).';

-- ============================================================
-- 15. GRUPO_MATERIA
-- ============================================================
CREATE TABLE grupo_materia (
    id_grupo_materia SERIAL       PRIMARY KEY,
    id_grupo         INT          NOT NULL REFERENCES grupo(id_grupo)
                                  ON UPDATE CASCADE ON DELETE CASCADE,
    id_materia       INT          NOT NULL REFERENCES materia(id_materia)
                                  ON UPDATE CASCADE ON DELETE RESTRICT,
    horario          VARCHAR(100),
    CONSTRAINT uq_grupo_materia UNIQUE (id_grupo, id_materia)
);

COMMENT ON TABLE grupo_materia IS 'Materias asignadas a cada grupo con su horario de clase.';

-- ============================================================
-- 16. DOCENTE
-- ============================================================
CREATE TABLE docente (
    id_docente       SERIAL          PRIMARY KEY,
    id_usuario       INT             NOT NULL UNIQUE
                                     REFERENCES usuario(id_usuario)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    especialidad     VARCHAR(150),
    titulo_academico VARCHAR(50)     NOT NULL
                                     CHECK (titulo_academico IN ('LICENCIATURA','MAESTRIA','DOCTORADO','DIPLOMADO')),
    telefono         VARCHAR(20),
    activo           BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_docente_usuario ON docente(id_usuario);

COMMENT ON TABLE  docente                  IS 'Perfil académico del docente. La validación de título (maestría o superior) se realiza en CU21.';
COMMENT ON COLUMN docente.titulo_academico  IS 'Conjunto cerrado de títulos aceptados por el reglamento FICCT.';

-- ============================================================
-- 17. ASIGNACION_DOCENTE
-- ============================================================
CREATE TABLE asignacion_docente (
    id_asignacion    SERIAL          PRIMARY KEY,
    id_docente       INT             NOT NULL REFERENCES docente(id_docente)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    id_grupo         INT             NOT NULL REFERENCES grupo(id_grupo)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    id_materia       INT             NOT NULL REFERENCES materia(id_materia)
                                     ON UPDATE CASCADE ON DELETE RESTRICT,
    fecha_asignacion TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    activo           BOOLEAN         NOT NULL DEFAULT TRUE,
    CONSTRAINT uq_asignacion UNIQUE (id_grupo, id_materia)   -- un docente por grupo-materia
);

CREATE INDEX idx_asignacion_docente  ON asignacion_docente(id_docente);
CREATE INDEX idx_asignacion_grupo    ON asignacion_docente(id_grupo);

COMMENT ON TABLE asignacion_docente IS 'Carga horaria: un docente asignado a un grupo y una materia específica (CU22). La restricción UNIQUE garantiza que no haya duplicados por grupo-materia.';

-- ============================================================
-- FIN DEL DISEÑO FÍSICO
-- ============================================================
