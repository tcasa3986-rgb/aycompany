import React, { useState, useEffect, useRef } from 'react';
import {
  Shield, Download, Upload, RefreshCw, Database, Info,
  CheckCircle, XCircle, AlertTriangle, Clock, HardDrive,
  Table2, Activity, FileText, X, Loader2, ChevronDown,
  ChevronUp, Trash2, RotateCcw
} from 'lucide-react';
import toast from 'react-hot-toast';

const API = '';

function getToken() {
  return localStorage.getItem('viaje360_token') || null;
}

async function apiFetch(path, opts = {}) {
  const token = getToken();
  const res = await fetch(`${API}${path}`, {
    ...opts,
    headers: {
      ...(!opts.isFormData ? { 'Content-Type': 'application/json' } : {}),
      Authorization: `Bearer ${token}`,
      ...(opts.headers || {}),
    },
  });
  if (!res.ok) {
    const data = await res.json().catch(() => ({ msg: 'Error desconocido' }));
    throw new Error(data.msg || `HTTP ${res.status}`);
  }
  return res;
}

/* ═══════════════════════════════════════════════════════════════
   COMPONENTE PRINCIPAL
═══════════════════════════════════════════════════════════════ */
export default function MantenimientoPage() {
  const [sysInfo, setSysInfo]           = useState(null);
  const [infoLoading, setInfoLoading]   = useState(true);
  const [historial, setHistorial]       = useState([]);
  const [tablasExpanded, setTablasExpanded] = useState(false);

  // Estados de cada operación
  const [backupLoading, setBackupLoading]       = useState(false);
  const [restaurarLoading, setRestaurarLoading] = useState(false);
  const [resetLoading, setResetLoading]         = useState(false);

  // Modal de confirmación reset
  const [showResetModal, setShowResetModal] = useState(false);
  const [resetConfirmText, setResetConfirmText] = useState('');
  const [resetResult, setResetResult] = useState(null);

  // Modal resultado restaurar
  const [showRestaurarResult, setShowRestaurarResult] = useState(false);
  const [restaurarResult, setRestaurarResult] = useState(null);

  const fileInputRef = useRef(null);

  const cargarInfo = async () => {
    setInfoLoading(true);
    try {
      const res = await apiFetch('/api/mantenimiento/info');
      const json = await res.json();
      setSysInfo(json.data);
    } catch (err) {
      toast.error('Error cargando información del sistema');
    } finally {
      setInfoLoading(false);
    }
  };

  useEffect(() => { cargarInfo(); }, []);

  const addHistorial = (tipo, mensaje, exito) => {
    setHistorial(prev => [{
      id: Date.now(),
      tipo,
      mensaje,
      exito,
      fecha: new Date().toLocaleTimeString('es-CO'),
    }, ...prev].slice(0, 20));
  };

  /* ── BACKUP ────────────────────────────────────────────────── */
  const handleBackup = async () => {
    setBackupLoading(true);
    const token = getToken();

    if (!token) {
      toast.error('No se encontró el token de autenticación');
      setBackupLoading(false);
      return;
    }

    // Usamos navegación directa al endpoint nativo del navegador,
    // garantizando que Chrome reciba la respuesta HTTP real y sus metadatos (Content-Disposition).
    window.location.href = `${API}/api/mantenimiento/backup?token=${token}`;

    toast.success('Descarga de Backup iniciada automáticamente');
    addHistorial('Backup', 'Orden de descarga iniciada al navegador', true);
    setBackupLoading(false);
    cargarInfo();
  };

  /* ── RESTAURAR ─────────────────────────────────────────────── */
  const handleRestaurarFile = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    e.target.value = '';

    if (!file.name.endsWith('.sql')) {
      toast.error('Solo se aceptan archivos .sql');
      return;
    }

    setRestaurarLoading(true);
    const tid = toast.loading(`Restaurando ${file.name}...`);

    try {
      const formData = new FormData();
      formData.append('archivo', file);

      const res = await apiFetch('/api/mantenimiento/restaurar', {
        method: 'POST',
        body: formData,
        isFormData: true,
      });
      const json = await res.json();

      toast.success(json.msg, { id: tid });
      setRestaurarResult(json.data);
      setShowRestaurarResult(true);
      addHistorial('Restauración', json.msg, true);
      cargarInfo();
    } catch (err) {
      toast.error(`Error: ${err.message}`, { id: tid });
      addHistorial('Restauración', `Error: ${err.message}`, false);
    } finally {
      setRestaurarLoading(false);
    }
  };

  /* ── RESET ─────────────────────────────────────────────────── */
  const handleResetConfirm = async () => {
    if (resetConfirmText !== 'CONFIRMAR') return;
    setResetLoading(true);
    const tid = toast.loading('Ejecutando reset del sistema...');

    try {
      const res = await apiFetch('/api/mantenimiento/reset', {
        method: 'POST',
        body: JSON.stringify({ confirmacion: 'CONFIRMAR' }),
      });
      const json = await res.json();

      toast.success(json.msg, { id: tid });
      setResetResult(json.data);
      addHistorial('Reset', json.msg, true);
      setShowResetModal(false);
      setResetConfirmText('');
      cargarInfo();
    } catch (err) {
      toast.error(`Error: ${err.message}`, { id: tid });
      addHistorial('Reset', `Error: ${err.message}`, false);
    } finally {
      setResetLoading(false);
    }
  };

  /* ═══════════════════════════════════════════════════════════
     RENDER
  ═══════════════════════════════════════════════════════════ */
  return (
    <div className="mantenimiento-page animate-fade-in">
      {/* ── Header ─────────────────────────────────────────── */}
      <div className="mant-header">
        <div className="mant-header-left">
          <div className="mant-header-icon">
            <Shield size={24} />
          </div>
          <div>
            <h1 className="mant-title">Mantenimiento del Sistema</h1>
            <p className="mant-subtitle">Gestión de copias de seguridad, restauración y configuración inicial</p>
          </div>
        </div>
        <button className="mant-refresh-btn" onClick={cargarInfo} title="Actualizar información">
          <RefreshCw size={16} className={infoLoading ? 'spin' : ''} />
          <span>Actualizar</span>
        </button>
      </div>

      {/* ── Panel de info del sistema ───────────────────────── */}
      <div className="mant-info-panel">
        <div className="mant-info-grid">
          <InfoCard
            icon={<Database size={20} />}
            label="Base de Datos"
            value={sysInfo?.base_datos || '—'}
            loading={infoLoading}
            color="blue"
          />
          <InfoCard
            icon={<Activity size={20} />}
            label="Versión MySQL"
            value={sysInfo?.version || '—'}
            loading={infoLoading}
            color="purple"
          />
          <InfoCard
            icon={<Table2 size={20} />}
            label="Tablas"
            value={sysInfo ? `${sysInfo.total_tablas}` : '—'}
            loading={infoLoading}
            color="cyan"
          />
          <InfoCard
            icon={<FileText size={20} />}
            label="Registros totales"
            value={sysInfo ? sysInfo.total_registros.toLocaleString() : '—'}
            loading={infoLoading}
            color="green"
          />
          <InfoCard
            icon={<HardDrive size={20} />}
            label="Tamaño BD"
            value={sysInfo ? `${(sysInfo.tamano_total_kb / 1024).toFixed(2)} MB` : '—'}
            loading={infoLoading}
            color="amber"
          />
          <InfoCard
            icon={<Clock size={20} />}
            label="Actualizado"
            value={sysInfo ? new Date(sysInfo.generado_en).toLocaleTimeString('es-CO') : '—'}
            loading={infoLoading}
            color="rose"
          />
        </div>

        {/* Tabla de detalle de tablas */}
        {sysInfo?.tablas && (
          <div className="mant-tables-detail">
            <button
              className="mant-tables-toggle"
              onClick={() => setTablasExpanded(p => !p)}
            >
              <span>Ver detalle de tablas ({sysInfo.tablas.length})</span>
              {tablasExpanded ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
            </button>
            {tablasExpanded && (
              <div className="mant-tables-grid">
                {sysInfo.tablas.map(t => (
                  <div key={t.nombre} className="mant-table-row">
                    <span className="mant-table-name">{t.nombre}</span>
                    <span className="mant-table-rows">{parseInt(t.filas || 0).toLocaleString()} filas</span>
                    <span className="mant-table-size">{t.tamano_kb} KB</span>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}
      </div>

      {/* ── Tarjetas de Acción ──────────────────────────────── */}
      <div className="mant-actions-grid">
        {/* BACKUP */}
        <ActionCard
          color="blue"
          icon={<Download size={30} />}
          badge="Backup"
          title="Copia de Seguridad"
          description="Genera y descarga un archivo .sql completo con toda la base de datos. El backup incluye estructura de tablas y todos los datos."
          features={['Formato SQL estándar', 'Sin dependencias externas', 'Incluye estructura + datos', 'Compatible con MySQL / MariaDB']}
          buttonLabel={backupLoading ? 'Generando...' : 'Descargar Backup'}
          buttonIcon={backupLoading ? <Loader2 size={18} className="spin" /> : <Download size={18} />}
          onClick={handleBackup}
          disabled={backupLoading}
        />

        {/* RESTAURAR */}
        <ActionCard
          color="amber"
          icon={<Upload size={30} />}
          badge="Restaurar"
          title="Restaurar Copia"
          description="Sube un archivo .sql generado previamente y restaura la base de datos. El sistema ejecutará cada sentencia de forma inteligente."
          features={['Acepta backups del sistema', 'Manejo automático de errores', 'Reporte de ejecución', 'Hasta 100 MB de archivo']}
          buttonLabel={restaurarLoading ? 'Restaurando...' : 'Seleccionar Archivo .sql'}
          buttonIcon={restaurarLoading ? <Loader2 size={18} className="spin" /> : <Upload size={18} />}
          onClick={() => !restaurarLoading && fileInputRef.current?.click()}
          disabled={restaurarLoading}
          warning="Esta acción sobrescribirá los datos actuales"
        />

        {/* RESET */}
        <ActionCard
          color="rose"
          icon={<RotateCcw size={30} />}
          badge="Reset"
          title="Nueva Empresa"
          description="Borra todos los datos operativos (clientes, reservas, pagos, etc.) para configurar el sistema para una empresa nueva. Los catálogos y usuarios se conservan."
          features={['Elimina datos operativos', 'Conserva catálogos', 'Mantiene usuarios', 'Requiere confirmación']}
          buttonLabel={resetLoading ? 'Ejecutando...' : 'Iniciar Reset'}
          buttonIcon={resetLoading ? <Loader2 size={18} className="spin" /> : <Trash2 size={18} />}
          onClick={() => setShowResetModal(true)}
          disabled={resetLoading}
          danger="Acción irreversible – borrar datos permanente"
        />
      </div>

      {/* ── Historial de Operaciones ────────────────────────── */}
      {historial.length > 0 && (
        <div className="mant-historial">
          <h3 className="mant-historial-title">
            <Clock size={16} />
            Historial de Operaciones
          </h3>
          <div className="mant-historial-list">
            {historial.map(h => (
              <div key={h.id} className={`mant-historial-item ${h.exito ? 'success' : 'error'}`}>
                <div className="mant-hist-icon">
                  {h.exito
                    ? <CheckCircle size={16} />
                    : <XCircle size={16} />
                  }
                </div>
                <div className="mant-hist-content">
                  <span className="mant-hist-tipo">{h.tipo}</span>
                  <span className="mant-hist-msg">{h.mensaje}</span>
                </div>
                <span className="mant-hist-hora">{h.fecha}</span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* ── Input file oculto ────────────────────────────────── */}
      <input
        ref={fileInputRef}
        type="file"
        accept=".sql"
        style={{ display: 'none' }}
        onChange={handleRestaurarFile}
      />

      {/* ── Modal Confirmar Reset ────────────────────────────── */}
      {showResetModal && (
        <div className="mant-modal-overlay" onClick={() => { setShowResetModal(false); setResetConfirmText(''); }}>
          <div className="mant-modal mant-modal-danger" onClick={e => e.stopPropagation()}>
            <div className="mant-modal-header">
              <div className="mant-modal-icon danger">
                <AlertTriangle size={28} />
              </div>
              <h2>Confirmar Reset del Sistema</h2>
              <button className="mant-modal-close" onClick={() => { setShowResetModal(false); setResetConfirmText(''); }}>
                <X size={20} />
              </button>
            </div>

            <div className="mant-modal-body">
              <div className="mant-modal-warning">
                <AlertTriangle size={16} />
                <span>Esta acción es <strong>irreversible</strong>. Se eliminarán todos los datos operativos.</span>
              </div>

              <div className="mant-tables-delete">
                <p className="mant-tables-delete-label">Tablas que serán vaciadas:</p>
                <div className="mant-tables-delete-list">
                  {['clientes', 'reservas', 'pagos', 'oportunidades', 'tareas', 'campanas', 'interacciones', 'documentos', 'notas'].map(t => (
                    <span key={t} className="mant-table-badge">{t}</span>
                  ))}
                </div>
              </div>

              <div className="mant-tables-keep">
                <p className="mant-tables-keep-label">Se conservarán intactos:</p>
                <div className="mant-tables-delete-list">
                  {['usuarios', 'roles', 'catálogos', 'configuración'].map(t => (
                    <span key={t} className="mant-table-badge safe">{t}</span>
                  ))}
                </div>
              </div>

              <div className="mant-modal-confirm-input">
                <label>Para confirmar, escribe <strong>CONFIRMAR</strong> en el campo de abajo:</label>
                <input
                  type="text"
                  placeholder="CONFIRMAR"
                  value={resetConfirmText}
                  onChange={e => setResetConfirmText(e.target.value.toUpperCase())}
                  className={resetConfirmText === 'CONFIRMAR' ? 'valid' : ''}
                  autoFocus
                />
              </div>
            </div>

            <div className="mant-modal-footer">
              <button
                className="btn-secondary-mant"
                onClick={() => { setShowResetModal(false); setResetConfirmText(''); }}
              >
                Cancelar
              </button>
              <button
                className={`btn-danger-mant ${resetConfirmText !== 'CONFIRMAR' ? 'disabled' : ''}`}
                onClick={handleResetConfirm}
                disabled={resetConfirmText !== 'CONFIRMAR' || resetLoading}
              >
                {resetLoading ? <Loader2 size={16} className="spin" /> : <Trash2 size={16} />}
                Ejecutar Reset
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── Modal Resultado Restauración ──────────────────────── */}
      {showRestaurarResult && restaurarResult && (
        <div className="mant-modal-overlay" onClick={() => setShowRestaurarResult(false)}>
          <div className="mant-modal" onClick={e => e.stopPropagation()}>
            <div className="mant-modal-header">
              <div className="mant-modal-icon success">
                <CheckCircle size={28} />
              </div>
              <h2>Restauración Completada</h2>
              <button className="mant-modal-close" onClick={() => setShowRestaurarResult(false)}>
                <X size={20} />
              </button>
            </div>

            <div className="mant-modal-body">
              <div className="mant-restaurar-stats">
                <div className="mant-rst-stat blue">
                  <span className="mant-rst-val">{restaurarResult.total_sentencias}</span>
                  <span className="mant-rst-lbl">Total sentencias</span>
                </div>
                <div className="mant-rst-stat green">
                  <span className="mant-rst-val">{restaurarResult.ejecutadas}</span>
                  <span className="mant-rst-lbl">Ejecutadas</span>
                </div>
                <div className="mant-rst-stat rose">
                  <span className="mant-rst-val">{restaurarResult.errores_count}</span>
                  <span className="mant-rst-lbl">Errores</span>
                </div>
              </div>

              {restaurarResult.errores?.length > 0 && (
                <div className="mant-errores-list">
                  <p className="mant-errores-title">Errores encontrados (primeros {restaurarResult.errores.length}):</p>
                  {restaurarResult.errores.map((e, i) => (
                    <div key={i} className="mant-error-item">
                      <code>{e.sentencia}</code>
                      <span>{e.error}</span>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <div className="mant-modal-footer">
              <button className="btn-primary-mant" onClick={() => setShowRestaurarResult(false)}>
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── Modal Resultado Reset ──────────────────────────────── */}
      {resetResult && (
        <div className="mant-modal-overlay" onClick={() => setResetResult(null)}>
          <div className="mant-modal" onClick={e => e.stopPropagation()}>
            <div className="mant-modal-header">
              <div className="mant-modal-icon success">
                <CheckCircle size={28} />
              </div>
              <h2>Reset Completado</h2>
              <button className="mant-modal-close" onClick={() => setResetResult(null)}>
                <X size={20} />
              </button>
            </div>

            <div className="mant-modal-body">
              <div className="mant-reset-summary">
                <div className="mant-reset-total">
                  <Trash2 size={24} />
                  <span>{resetResult.total_registros_eliminados.toLocaleString()} registros eliminados</span>
                </div>
              </div>
              <div className="mant-reset-table-list">
                {resetResult.resultados.map(r => (
                  <div key={r.tabla} className={`mant-reset-row ${r.estado === 'error' ? 'error' : ''}`}>
                    <span className="mant-reset-tabla">{r.tabla}</span>
                    <span className={`mant-reset-estado ${r.estado}`}>{r.estado}</span>
                    <span className="mant-reset-filas">{(r.filas_borradas || 0).toLocaleString()} filas</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="mant-modal-footer">
              <button className="btn-primary-mant" onClick={() => setResetResult(null)}>
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

/* ═══════════════════════════════════════════════════════════
   COMPONENTES AUXILIARES
═══════════════════════════════════════════════════════════ */
function InfoCard({ icon, label, value, loading, color }) {
  return (
    <div className={`mant-info-card mant-info-card--${color}`}>
      <div className={`mant-info-icon mant-info-icon--${color}`}>{icon}</div>
      <div className="mant-info-data">
        <span className="mant-info-label">{label}</span>
        {loading
          ? <span className="mant-info-skeleton" />
          : <span className="mant-info-value">{value}</span>
        }
      </div>
    </div>
  );
}

function ActionCard({ color, icon, badge, title, description, features, buttonLabel, buttonIcon, onClick, disabled, warning, danger }) {
  return (
    <div className={`mant-action-card mant-action-card--${color}`}>
      <div className="mant-action-card-glow" />
      <div className="mant-action-header">
        <div className={`mant-action-icon mant-action-icon--${color}`}>{icon}</div>
        <span className={`mant-action-badge mant-action-badge--${color}`}>{badge}</span>
      </div>
      <div className="mant-action-body">
        <h3 className="mant-action-title">{title}</h3>
        <p className="mant-action-desc">{description}</p>
        <ul className="mant-action-features">
          {features.map((f, i) => (
            <li key={i}>
              <CheckCircle size={13} />
              <span>{f}</span>
            </li>
          ))}
        </ul>
      </div>
      {(warning || danger) && (
        <div className={`mant-action-alert ${danger ? 'danger' : 'warning'}`}>
          <AlertTriangle size={13} />
          <span>{danger || warning}</span>
        </div>
      )}
      <button
        className={`mant-action-btn mant-action-btn--${color} ${disabled ? 'loading' : ''}`}
        onClick={onClick}
        disabled={disabled}
      >
        {buttonIcon}
        <span>{buttonLabel}</span>
      </button>
    </div>
  );
}
