# 📚 PLAN DE TRABAJO — ERP EDUCATIVO
### Sistema de Gestión Integral para Institutos, Academias y Universidades

---

## 🏗️ STACK TECNOLÓGICO

| Capa | Tecnología |
|------|-----------|
| Frontend | Blazor Server / Blazor WebAssembly |
| Backend | ASP.NET Core 10 (C#) |
| ORM | Entity Framework Core 10 |
| Base de Datos | SQL Server 2025 |
| SDK | .NET 10 |
| Autenticación | ASP.NET Core Identity + JWT |
| UI Components | MudBlazor / Radzen |
| Reportes | FastReport / RDLC / QuestPDF |
| Gráficos | ApexCharts para Blazor |
| Exportación | ClosedXML (Excel), QuestPDF (PDF) |
| Almacenamiento | Azure Blob Storage / Local FileSystem |

---

## 🗂️ ESTRUCTURA DEL PROYECTO

```
ERP-Educativo/
├── src/
│   ├── ERP.Web/                  → Proyecto Blazor (UI)
│   ├── ERP.Application/          → Casos de uso / Servicios
│   ├── ERP.Domain/               → Entidades y reglas de negocio
│   ├── ERP.Infrastructure/       → EF Core, Repositorios, DB
│   ├── ERP.API/                  → ASP.NET Core Web API
│   └── ERP.Shared/               → DTOs, Enums, Constantes
├── tests/
│   ├── ERP.UnitTests/
│   └── ERP.IntegrationTests/
├── docs/
└── sql/
    ├── migrations/
    └── seeds/
```

---

## 📦 MÓDULOS DEL SISTEMA

---

### MÓDULO 1 — CONFIGURACIÓN GENERAL DEL SISTEMA

**Objetivo:** Parametrizar la institución educativa antes de operar.

#### Funcionalidades:
- [ ] Registro de datos de la institución (nombre, logo, RUC/NIT, dirección, contacto)
- [ ] Configuración de años/ciclos académicos
- [ ] Configuración de períodos académicos (bimestre, trimestre, semestre, anual)
- [ ] Gestión de niveles educativos (primaria, secundaria, técnico, universitario)
- [ ] Configuración de modalidades (presencial, semipresencial, virtual)
- [ ] Gestión de turnos (mañana, tarde, noche)
- [ ] Configuración de moneda y parámetros financieros
- [ ] Configuración de correo SMTP para notificaciones
- [ ] Gestión de roles y permisos del sistema
- [ ] Auditoría de acciones (log del sistema)
- [ ] Configuración de políticas de contraseña
- [ ] Backup y restauración de base de datos
- [ ] Personalización de tema/colores del sistema

---

### MÓDULO 2 — GESTIÓN DE USUARIOS Y SEGURIDAD

**Objetivo:** Controlar accesos al sistema.

#### Funcionalidades:
- [ ] Registro de usuarios del sistema
- [ ] Asignación de roles: Administrador, Docente, Alumno, Padre de Familia, Contador, Recepcionista
- [ ] Permisos granulares por módulo y acción (CRUD)
- [ ] Login con usuario y contraseña
- [ ] Autenticación de dos factores (2FA)
- [ ] Recuperación de contraseña por correo
- [ ] Cierre de sesión automático por inactividad
- [ ] Historial de accesos (IP, fecha, dispositivo)
- [ ] Bloqueo de cuenta por intentos fallidos
- [ ] Perfil de usuario (foto, datos, cambio de contraseña)

---

### MÓDULO 3 — GESTIÓN ACADÉMICA

**Objetivo:** Administrar la estructura curricular y oferta educativa.

#### Funcionalidades:
- [ ] Registro de facultades / departamentos / escuelas
- [ ] Gestión de carreras / programas educativos
- [ ] Gestión de planes de estudio (malla curricular)
- [ ] Gestión de cursos / asignaturas / materias
- [ ] Asignación de horas académicas por curso
- [ ] Gestión de prerrequisitos entre cursos
- [ ] Configuración de créditos académicos
- [ ] Gestión de secciones / grupos / aulas
- [ ] Asignación de docentes a cursos/secciones
- [ ] Horario de clases (generación automática / manual)
- [ ] Gestión de calendario académico (feriados, eventos)
- [ ] Registro de syllabus por curso
- [ ] Control de carga lectiva del docente
- [ ] Gestión de ambientes / aulas / laboratorios

---

### MÓDULO 4 — GESTIÓN DE ADMISIÓN Y MATRÍCULA

**Objetivo:** Gestionar el proceso de ingreso de nuevos estudiantes.

#### Funcionalidades:
- [ ] Registro de prospectos / leads
- [ ] Proceso de admisión (postulación en línea)
- [ ] Carga de documentos para admisión
- [ ] Examen de admisión (configuración y registro de resultados)
- [ ] Cuadro de méritos y clasificación de postulantes
- [ ] Proceso de matrícula (nueva / regular / traslado)
- [ ] Matrícula en línea con pago integrado
- [ ] Generación de ficha de matrícula (PDF)
- [ ] Gestión de vacantes por sección
- [ ] Control de prerrequisitos para matrícula
- [ ] Matrícula condicional (deuda pendiente)
- [ ] Historial de matrículas por estudiante
- [ ] Reportes de matrícula por período / carrera / sección

---

### MÓDULO 5 — GESTIÓN DE ESTUDIANTES

**Objetivo:** Administrar el expediente completo del alumno.

#### Funcionalidades:
- [ ] Registro completo del estudiante (datos personales, foto, DNI/CI)
- [ ] Datos de apoderado / tutor legal
- [ ] Código de estudiante (autogenerado)
- [ ] Historial académico completo
- [ ] Traslado interno entre programas
- [ ] Reserva de matrícula
- [ ] Gestión de retiro / abandono temporal
- [ ] Reingreso de ex-alumnos
- [ ] Estado del estudiante (activo, retirado, egresado, suspendido)
- [ ] Carga de documentos del expediente (DNI, partida, fotos, certificados)
- [ ] Ficha de salud / información médica
- [ ] Carnet de estudiante (generación PDF/QR)
- [ ] Búsqueda avanzada de estudiantes
- [ ] Exportar lista de estudiantes (Excel/PDF)

---

### MÓDULO 6 — GESTIÓN DE DOCENTES

**Objetivo:** Administrar el personal docente.

#### Funcionalidades:
- [ ] Registro de docentes (datos personales, profesionales, foto)
- [ ] Gestión de contratos docentes (tiempo completo, parcial, hora)
- [ ] Registro de especialidades / grados académicos
- [ ] Hoja de vida del docente
- [ ] Carga de documentos (títulos, certificados)
- [ ] Asignación de cursos por período
- [ ] Control de carga horaria
- [ ] Evaluación del desempeño docente
- [ ] Registro de capacitaciones y formación continua
- [ ] Historial de cursos dictados
- [ ] Generación de contrato docente (PDF)
- [ ] Estado del docente (activo, inactivo, licencia)

---

### MÓDULO 7 — GESTIÓN DE ASISTENCIA

**Objetivo:** Controlar la asistencia de alumnos y docentes.

#### Funcionalidades:
- [ ] Registro de asistencia de alumnos por sesión/clase
- [ ] Registro de asistencia de docentes
- [ ] Asistencia masiva (lista de clase)
- [ ] Integración con lector biométrico / QR / RFID
- [ ] Justificación de inasistencias
- [ ] Control de porcentaje mínimo de asistencia (%)
- [ ] Alertas automáticas de baja asistencia (email/SMS)
- [ ] Reportes de asistencia por alumno / sección / período
- [ ] Reporte de tardanzas
- [ ] Panel visual de asistencia (calendario)
- [ ] Exportar reportes de asistencia (Excel/PDF)

---

### MÓDULO 8 — GESTIÓN DE CALIFICACIONES Y EVALUACIONES

**Objetivo:** Gestionar el sistema de notas y evaluaciones.

#### Funcionalidades:
- [ ] Configuración del sistema de calificación (0-20, A-F, 0-100)
- [ ] Configuración de fórmulas de promedio ponderado
- [ ] Registro de tipos de evaluación (examen, práctica, trabajo, laboratorio)
- [ ] Creación de cronograma de evaluaciones
- [ ] Ingreso de notas por docente (por sesión, por unidad)
- [ ] Cálculo automático de promedios
- [ ] Control de notas mínimas aprobatorias
- [ ] Gestión de recuperaciones / subsanaciones / aplazados
- [ ] Recalificación con historial de cambios
- [ ] Libretas de notas (por período y anual)
- [ ] Actas de calificación (generación PDF firmado)
- [ ] Ranking académico de estudiantes
- [ ] Alertas de alumnos en riesgo académico
- [ ] Historial de notas por alumno
- [ ] Reporte de rendimiento por curso / sección / carrera

---

### MÓDULO 9 — GESTIÓN FINANCIERA Y TESORERÍA

**Objetivo:** Administrar los ingresos y conceptos de pago de la institución.

#### Funcionalidades:
- [ ] Configuración de conceptos de pago (matrícula, mensualidad, examen, etc.)
- [ ] Configuración de tarifarios por carrera / nivel / modalidad
- [ ] Generación de deuda automática por matrícula
- [ ] Registro de pagos (caja, transferencia, QR, tarjeta)
- [ ] Generación de recibos/comprobantes de pago (PDF)
- [ ] Control de cuentas por cobrar (deuda vencida)
- [ ] Descuentos y becas (porcentaje / monto fijo)
- [ ] Fraccionamiento de deuda (cuotas)
- [ ] Gestión de mora (recargo automático por días)
- [ ] Estado de cuenta del estudiante
- [ ] Caja diaria y cierre de caja
- [ ] Reporte de ingresos por período
- [ ] Integración con pasarelas de pago (Niubiz, Izipay, PayPal)
- [ ] Emisión de boletas/facturas electrónicas (integración con SUNAT/SAT)
- [ ] Control de egresos / gastos operativos
- [ ] Flujo de caja
- [ ] Exportación contable

---

### MÓDULO 10 — GESTIÓN DE BIBLIOTECA

**Objetivo:** Administrar el fondo bibliográfico y préstamos.

#### Funcionalidades:
- [ ] Catálogo de libros y recursos bibliográficos
- [ ] Clasificación por categorías / áreas / materia
- [ ] Gestión de ejemplares (cantidad disponible)
- [ ] Código QR / código de barras por ejemplar
- [ ] Registro de préstamos y devoluciones
- [ ] Reserva de libros en línea
- [ ] Control de libros vencidos y multas
- [ ] Búsqueda de catálogo (por título, autor, ISBN)
- [ ] Gestión de usuarios habilitados para préstamo
- [ ] Historial de préstamos por estudiante
- [ ] Reportes de libros más solicitados
- [ ] Alta de nuevas adquisiciones

---

### MÓDULO 11 — GESTIÓN DE HORARIOS

**Objetivo:** Planificar y publicar horarios académicos.

#### Funcionalidades:
- [ ] Configuración de franjas horarias
- [ ] Creación de horarios por sección / carrera / docente
- [ ] Detección de conflictos de horario (docente / aula / sección)
- [ ] Generación automática de horarios (algoritmo básico)
- [ ] Vista de horario por alumno (Mi Horario)
- [ ] Vista de horario por docente
- [ ] Publicación de horarios a plataforma virtual
- [ ] Impresión de horarios (PDF)
- [ ] Cambio y reasignación de horarios
- [ ] Control de ambientes / capacidad por aula

---

### MÓDULO 12 — COMUNICACIONES Y NOTIFICACIONES

**Objetivo:** Mantener comunicación entre la institución, docentes y familias.

#### Funcionalidades:
- [ ] Bandeja de mensajes internos (entre usuarios del sistema)
- [ ] Envío de comunicados masivos (email / notificación push)
- [ ] Gestión de circulares y documentos institucionales
- [ ] Notificaciones automáticas de notas, asistencia, deuda
- [ ] Chat básico docente-alumno / institución-apoderado
- [ ] Tablón de anuncios
- [ ] Agenda institucional (calendario de eventos)
- [ ] Envío de SMS (integración con proveedor SMS)
- [ ] Historial de comunicaciones por usuario

---

### MÓDULO 13 — PORTAL DEL ESTUDIANTE

**Objetivo:** Dar acceso en línea al alumno para consultar su información.

#### Funcionalidades:
- [ ] Dashboard personalizado del alumno
- [ ] Consulta de notas en tiempo real
- [ ] Consulta de asistencia
- [ ] Descarga de constancias y certificados
- [ ] Estado de cuenta / pagos pendientes
- [ ] Pago en línea de pensiones
- [ ] Horario de clases
- [ ] Descarga de materiales del curso (si el docente los sube)
- [ ] Solicitud de documentos (trámites)
- [ ] Mensajería con docentes / tutores
- [ ] Notificaciones del sistema

---

### MÓDULO 14 — PORTAL DEL DOCENTE

**Objetivo:** Herramientas digitales para el trabajo docente.

#### Funcionalidades:
- [ ] Dashboard con resumen de cursos asignados
- [ ] Ingreso de notas en línea
- [ ] Registro de asistencia digital
- [ ] Subida de material educativo (archivos, enlaces)
- [ ] Creación de tareas / actividades
- [ ] Visualización de lista de alumnos por curso
- [ ] Mensajería con alumnos y administración
- [ ] Consulta de horario personal
- [ ] Descarga de actas de notas

---

### MÓDULO 15 — PORTAL DEL APODERADO / PADRE DE FAMILIA

**Objetivo:** Involucrar a los padres en el seguimiento académico.

#### Funcionalidades:
- [ ] Dashboard del apoderado
- [ ] Consulta de notas de sus hijos
- [ ] Consulta de asistencia de sus hijos
- [ ] Estado de cuenta y pagos en línea
- [ ] Recepción de comunicados institucionales
- [ ] Solicitud de citas con docentes / tutores
- [ ] Visualización de horarios de los hijos

---

### MÓDULO 16 — TRÁMITES Y DOCUMENTOS

**Objetivo:** Gestionar solicitudes documentarias de los alumnos.

#### Funcionalidades:
- [ ] Catálogo de documentos disponibles (constancia, certificado, historial, etc.)
- [ ] Solicitud en línea de documentos
- [ ] Aprobación / rechazo de solicitudes
- [ ] Generación automática de documentos en PDF (plantillas)
- [ ] Firma digital de documentos institucionales
- [ ] Seguimiento del estado de solicitud (en proceso, listo, entregado)
- [ ] Historial de documentos emitidos por alumno
- [ ] Control de pago por trámite

---

### MÓDULO 17 — GESTIÓN DE PERSONAL ADMINISTRATIVO

**Objetivo:** Administrar el personal no docente.

#### Funcionalidades:
- [ ] Registro de personal administrativo
- [ ] Gestión de contratos y salarios
- [ ] Control de asistencia del personal
- [ ] Registro de vacaciones y permisos
- [ ] Evaluación del personal
- [ ] Liquidación básica de haberes
- [ ] Generación de boletas de pago (PDF)
- [ ] Historial laboral

---

### MÓDULO 18 — INVENTARIO Y ACTIVOS

**Objetivo:** Controlar los bienes y recursos de la institución.

#### Funcionalidades:
- [ ] Registro de activos fijos (mobiliario, equipos)
- [ ] Codificación de activos (código de inventario)
- [ ] Asignación de activos a personas / ambientes
- [ ] Control de mantenimiento preventivo y correctivo
- [ ] Registro de bajas de activos
- [ ] Gestión de almacén (útiles, materiales)
- [ ] Control de stock mínimo y máximo
- [ ] Solicitud de materiales por área
- [ ] Reportes de inventario

---

### MÓDULO 19 — REPORTES Y ESTADÍSTICAS (BI)

**Objetivo:** Proveer inteligencia de negocio para la toma de decisiones.

#### Funcionalidades:
- [ ] Dashboard ejecutivo con KPIs generales
- [ ] Reporte de matrícula por período / carrera / turno
- [ ] Reporte de rendimiento académico
- [ ] Reporte de asistencia general
- [ ] Reporte financiero (ingresos, deudas, morosidad)
- [ ] Estadísticas de egresados y titulados
- [ ] Reporte de retención y deserción estudiantil
- [ ] Gráficos de evolución por período
- [ ] Top 10 mejores alumnos
- [ ] Exportación de todos los reportes a Excel/PDF
- [ ] Programación de reportes automáticos (por email)

---

### MÓDULO 20 — GRADUACIÓN Y TITULACIÓN

**Objetivo:** Gestionar el proceso de egreso y titulación.

#### Funcionalidades:
- [ ] Verificación de requisitos de egreso (créditos, cursos aprobados, deuda)
- [ ] Proceso de grado / graduación
- [ ] Gestión de tesis / proyectos de titulación
- [ ] Registro de jurados y fechas de sustentación
- [ ] Actas de grado (generación PDF)
- [ ] Registro de títulos emitidos
- [ ] Diploma (plantilla y generación PDF)
- [ ] Libro de graduados
- [ ] Seguimiento de ex-alumnos (alumni)

---

## 🗓️ PLAN DE FASES DE DESARROLLO

### FASE 1 — FUNDAMENTOS (Semanas 1–3)
- Configuración del proyecto Blazor + API + EF Core
- Diseño de base de datos completa (SQL Server 2025)
- Módulo de Configuración General
- Módulo de Usuarios y Seguridad (Identity)
- Layout principal y sistema de navegación
- Componentes UI base (MudBlazor)

### FASE 2 — NÚCLEO ACADÉMICO (Semanas 4–7)
- Módulo Académico (carreras, cursos, malla)
- Módulo Admisión y Matrícula
- Módulo Estudiantes
- Módulo Docentes
- Módulo Horarios

### FASE 3 — EVALUACIÓN Y ASISTENCIA (Semanas 8–10)
- Módulo Calificaciones y Evaluaciones
- Módulo Asistencia
- Portales: Alumno, Docente, Apoderado

### FASE 4 — FINANZAS Y ADMINISTRACIÓN (Semanas 11–13)
- Módulo Tesorería y Pagos
- Módulo Trámites y Documentos
- Módulo Personal Administrativo
- Módulo Inventario

### FASE 5 — COMUNICACIONES Y BI (Semanas 14–15)
- Módulo Biblioteca
- Módulo Comunicaciones
- Módulo Reportes y Dashboard BI
- Módulo Graduación y Titulación

### FASE 6 — PRUEBAS Y DESPLIEGUE (Semanas 16–18)
- Pruebas unitarias e integración
- Corrección de bugs
- Datos de prueba (seeds)
- Documentación técnica y de usuario
- Despliegue en IIS / Azure / Servidor Windows

---

## 🗄️ DISEÑO DE BASE DE DATOS — TABLAS PRINCIPALES

```
CONFIGURACION
├── Instituciones
├── PeriodosAcademicos
├── CiclosAcademicos
├── NivelesEducativos
├── Turnos
└── Parametros

SEGURIDAD
├── Usuarios
├── Roles
├── Permisos
├── RolPermisos
└── AuditoriaAccesos

ACADEMICO
├── Facultades
├── Carreras
├── PlanesEstudio
├── Cursos
├── Secciones
├── Horarios
├── Aulas
└── Prerrequisitos

PERSONAS
├── Estudiantes
├── Docentes
├── PersonalAdministrativo
├── Apoderados
└── Documentos

MATRICULA
├── Postulantes
├── Matriculas
├── DetalleMatricula
└── Traslados

ACADEMICO_OPERATIVO
├── Asistencia
├── Evaluaciones
├── TiposEvaluacion
├── Notas
├── Actas
└── Promedios

FINANCIERO
├── ConceptosPago
├── Tarifarios
├── Deudas
├── Pagos
├── Recibos
├── Descuentos
└── CajaMovimientos

BIBLIOTECA
├── Libros
├── Ejemplares
├── Prestamos
└── Multas

TRAMITES
├── TiposDocumento
├── SolicitudesDocumento
└── DocumentosEmitidos

INVENTARIO
├── Activos
├── CategoriaActivos
├── MovimientosActivos
└── Mantenimientos

COMUNICACIONES
├── Mensajes
├── Comunicados
├── Notificaciones
└── Anuncios

GRADUACION
├── Tesis
├── Jurados
├── Grados
└── Titulos
```

---

## 🔑 CARACTERÍSTICAS TÉCNICAS CLAVE

| Característica | Implementación |
|---------------|----------------|
| Arquitectura | Clean Architecture (Domain, Application, Infrastructure, Web) |
| Patrón | CQRS con MediatR |
| Repositorios | Repository Pattern + Unit of Work |
| Autenticación | ASP.NET Core Identity + JWT Bearer |
| Validaciones | FluentValidation |
| Mapeo | AutoMapper |
| Logging | Serilog (consola + archivo + DB) |
| Caché | IMemoryCache / Redis (opcional) |
| Notificaciones en tiempo real | SignalR |
| Generación PDF | QuestPDF |
| Generación Excel | ClosedXML |
| Pruebas | xUnit + Moq |
| CI/CD | GitHub Actions |

---

## 📋 PRIORIDADES (MoSCoW)

### Must Have (Obligatorio)
- Configuración del sistema
- Usuarios y seguridad
- Gestión académica
- Matrícula
- Estudiantes y docentes
- Calificaciones y asistencia
- Tesorería básica

### Should Have (Importante)
- Horarios
- Trámites y documentos
- Portales (alumno, docente, apoderado)
- Reportes y BI
- Biblioteca

### Could Have (Deseable)
- Comunicaciones y mensajería
- Graduación y titulación
- Inventario
- Integración con pasarelas de pago

### Won't Have (Fuera de alcance inicial)
- App móvil nativa
- IA para predicción de deserción
- LMS completo integrado

---

## ✅ TOTAL DE MÓDULOS: 20
## ✅ TOTAL DE FUNCIONALIDADES: ~200+
## ✅ DURACIÓN ESTIMADA: 18 semanas (equipo de 2-3 desarrolladores)

---

*Documento generado: Abril 2026 | Versión 1.0*
