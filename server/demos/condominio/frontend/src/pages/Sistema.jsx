import React, { useState, useEffect, useRef } from 'react';
import api from '../services/api';
import './Sistema.css';

export default function Sistema() {
  const [loadingInfo, setLoadingInfo] = useState(true);
  const [sysInfo, setSysInfo] = useState(null);
  const [isBackingUp, setIsBackingUp] = useState(false);
  
  // Restore state
  const [selectedFile, setSelectedFile] = useState(null);
  const [isRestoring, setIsRestoring] = useState(false);
  const [restoreResults, setRestoreResults] = useState(null);
  const fileInputRef = useRef(null);

  // Reset state
  const [showResetModal, setShowResetModal] = useState(false);
  const [confirmText, setConfirmText] = useState('');
  const [isResetting, setIsResetting] = useState(false);
  const [resetResults, setResetResults] = useState(null);

  useEffect(() => {
    fetchInfo();
  }, []);

  const fetchInfo = async () => {
    try {
      setLoadingInfo(true);
      const res = await api.get('/sistema/info');
      setSysInfo(res.data.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoadingInfo(false);
    }
  };

  const handleBackup = async () => {
    setIsBackingUp(true);
    try {
      const response = await api.get('/sistema/backup', {
        responseType: 'blob' // Important
      });
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      
      const contentDisposition = response.headers['content-disposition'];
      let fileName = `backup_condominio_${new Date().toISOString().slice(0, 10)}.sql`;
      if (contentDisposition) {
        const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
        if (fileNameMatch && fileNameMatch.length === 2) {
          fileName = fileNameMatch[1];
        }
      }
      link.setAttribute('download', fileName);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
    } catch (error) {
      alert('Error al generar la copia de seguridad');
      console.error(error);
    } finally {
      setIsBackingUp(false);
    }
  };

  const handleFileChange = (e) => {
    if (e.target.files && e.target.files.length > 0) {
      const file = e.target.files[0];
      if (!file.name.endsWith('.sql')) {
        alert('Solo se permiten archivos .sql');
        return;
      }
      setSelectedFile(file);
    }
  };

  const handleRestore = async () => {
    if (!selectedFile) return;
    
    if (!window.confirm('¿Está seguro de restaurar este archivo? Esta acción reescribirá los datos del sistema.')) {
      return;
    }

    setIsRestoring(true);
    setRestoreResults(null);

    const formData = new FormData();
    formData.append('archivo', selectedFile);

    try {
      const res = await api.post('/sistema/restaurar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      setRestoreResults({
        ok: true,
        msg: res.data.message,
        data: res.data.data
      });
      setSelectedFile(null);
      if (fileInputRef.current) fileInputRef.current.value = '';
      fetchInfo(); // Refresh stats
    } catch (err) {
      setRestoreResults({
        ok: false,
        msg: err.response?.data?.message || err.message,
      });
    } finally {
      setIsRestoring(false);
    }
  };

  const handleReset = async () => {
    if (confirmText !== 'CONFIRMAR') return;
    
    setIsResetting(true);
    setResetResults(null);

    try {
      const res = await api.post('/sistema/reset', { confirmacion: confirmText });
      setResetResults({
        ok: true,
        msg: res.data.message,
        data: res.data.data
      });
      setConfirmText('');
      fetchInfo();
    } catch (err) {
      setResetResults({
        ok: false,
        msg: err.response?.data?.message || err.message
      });
    } finally {
      setIsResetting(false);
    }
  };

  return (
    <div className="fade-in">
      <div className="page-header" style={{ marginBottom: 24 }}>
        <div>
          <div className="page-title">⚙️ Mantenimiento del Sistema</div>
          <div className="page-subtitle">Copias de seguridad, restauración y flujos de recuperación</div>
        </div>
      </div>

      <div className="sistema-container">
        
        {/* ROW 1: Backup e Info */}
        <div className="sistema-grid">
          
          <div className="sistema-panel">
            <div className="panel-icon">💾</div>
            <div className="panel-title">Copia de Seguridad</div>
            <div className="panel-desc">Genera un respaldo completo de la base de datos operativa, incluyendo catálogos, configuraciones y todos los registros transaccionales.</div>
            
            <div className="panel-stats">
              <div className="stat-item">
                <span className="stat-label">Registros Totales</span>
                <span className="stat-value">{loadingInfo ? '...' : (sysInfo?.total_registros?.toLocaleString() || '0')}</span>
              </div>
              <div className="stat-item">
                <span className="stat-label">Tamaño Aprox.</span>
                <span className="stat-value">{loadingInfo ? '...' : `${sysInfo?.tamano_total_kb || '0'} KB`}</span>
              </div>
            </div>

            <button 
              className="btn-system primary" 
              onClick={handleBackup} 
              disabled={isBackingUp || loadingInfo}
            >
              {isBackingUp ? <><div className="sys-loader"></div> Generando .sql...</> : '⬇️ Descargar Backup'}
            </button>
          </div>

          <div className="sistema-panel">
            <div className="panel-icon">🔄</div>
            <div className="panel-title">Restaurar Sistema</div>
            <div className="panel-desc">Sube un archivo .sql previamente generado para restaurar la base de datos a un punto anterior.</div>
            
            <input 
              type="file" 
              accept=".sql" 
              style={{ display: 'none' }} 
              ref={fileInputRef}
              onChange={handleFileChange}
            />
            
            <div 
              className={`dropzone ${selectedFile ? 'active' : ''}`}
              onClick={() => fileInputRef.current?.click()}
            >
              <div className="dropzone-icon">📁</div>
              <div className="dropzone-text">
                {selectedFile ? (
                  <span style={{color: '#fff', fontWeight: 'bold'}}>{selectedFile.name} ({(selectedFile.size / 1024).toFixed(1)} KB)</span>
                ) : (
                  "Haz click para seleccionar un archivo .sql"
                )}
              </div>
            </div>

            {restoreResults && (
              <div className="results-box" style={{ marginBottom: 16 }}>
                <div className={restoreResults.ok ? 'status-ok' : 'status-err'} style={{ fontWeight: 'bold', marginBottom: 8 }}>
                  {restoreResults.msg}
                </div>
                {restoreResults.ok && restoreResults.data && (
                  <div style={{ fontSize: 12, color: 'rgba(255,255,255,0.7)' }}>
                    Sentencias ejecutadas: {restoreResults.data.ejecutadas} <br/>
                    Errores: {restoreResults.data.errores_count}
                  </div>
                )}
              </div>
            )}

            <button 
              className={`btn-system ${selectedFile ? 'primary' : ''}`} 
              disabled={!selectedFile || isRestoring}
              onClick={handleRestore}
              style={!selectedFile ? { background: 'rgba(255,255,255,0.1)' } : {}}
            >
              {isRestoring ? <><div className="sys-loader"></div> Procesando SQL...</> : '🚀 Iniciar Restauración'}
            </button>
          </div>

        </div>

        {/* ROW 2: Danger Zone */}
        <div className="sistema-panel danger mt-16">
          <div className="panel-icon" style={{ background: 'rgba(239, 68, 68, 0.1)', color: '#ef4444' }}>⚠️</div>
          <div className="panel-title" style={{ color: '#ef4444' }}>Zona de Peligro: Reseteo de Sistema</div>
          <div className="panel-desc">
            Esta acción vaciará por completo los datos operativos del condominio (cuotas, incidentes, mensajes, recibos, transacciones, etc.) preparando el sistema para una empresa nueva (Wipe Data). 
            Se conservarán únicamente la configuración general, usuarios y catálogos base. <strong>Esta acción es irreversible si no cuentas con un backup.</strong>
          </div>
          <div style={{ display: 'flex', justifyContent: 'flex-start' }}>
            <button className="btn-system danger" style={{ width: 'auto', padding: '12px 32px' }} onClick={() => setShowResetModal(true)}>
              🗑️ Vaciar Datos Operativos
            </button>
          </div>
        </div>

      </div>

      {/* Modal de Reseteo */}
      {showResetModal && (
        <div className="modal-overlay" style={{ zIndex: 9999 }}>
          <div className="modal" style={{ maxWidth: 500, padding: 0, overflow: 'hidden' }}>
            <div className="modal-danger-header">
              ⚠️ Confirmación de Reseteo
            </div>
            <div className="modal-danger-body">
              <div className="warning-box">
                Estás a punto de borrar todos los datos transaccionales del condominio. 
                Los catálogos (usuarios, cuentas, configuración) permanecerán. 
                Asegúrate de haber generado una Copia de Seguridad antes de continuar.
              </div>

              {resetResults && (
                <div className="results-box" style={{ marginBottom: 20 }}>
                  <div className={resetResults.ok ? 'status-ok' : 'status-err'} style={{ fontWeight: 'bold', marginBottom: 8 }}>
                    {resetResults.msg}
                  </div>
                  {resetResults.ok && resetResults.data?.resultados && (
                    <ul className="results-list">
                      {resetResults.data.resultados.map((r, i) => (
                        <li key={i}>
                          <span>{r.tabla}</span>
                          <span className={r.estado === 'error' ? 'status-err' : 'status-ok'}>
                            {r.estado === 'limpiada' ? `-${r.filas_borradas} filas` : r.estado}
                          </span>
                        </li>
                      ))}
                    </ul>
                  )}
                </div>
              )}

              <p style={{ color: '#fff', marginBottom: 12, fontWeight: 600 }}>
                Para proceder, escribe la palabra <span style={{ color: '#ef4444', userSelect: 'all' }}>CONFIRMAR</span> en el campo inferior:
              </p>
              
              <input 
                type="text" 
                className="confirm-input" 
                placeholder="CONFIRMAR"
                value={confirmText}
                onChange={e => setConfirmText(e.target.value)}
                disabled={isResetting || (resetResults && resetResults.ok)}
              />

              <div style={{ display: 'flex', gap: 12, marginTop: 24, justifyContent: 'flex-end' }}>
                <button 
                  className="btn btn-secondary" 
                  onClick={() => {
                    setShowResetModal(false);
                    setResetResults(null);
                    setConfirmText('');
                  }}
                  disabled={isResetting}
                >
                  {resetResults && resetResults.ok ? 'Cerrar' : 'Cancelar'}
                </button>
                {(!resetResults || !resetResults.ok) && (
                  <button 
                    className="btn btn-danger" 
                    onClick={handleReset}
                    disabled={confirmText !== 'CONFIRMAR' || isResetting}
                  >
                    {isResetting ? 'Vaciando Datos...' : '⚠ Estoy seguro, vaciar sistema'}
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      )}

    </div>
  );
}
