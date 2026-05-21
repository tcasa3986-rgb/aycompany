import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { CheckCircle, XCircle, Clock, Download, RefreshCw, User, Phone, Mail, Building2, Save } from 'lucide-react';

const API = '/api/portal';

export default function PortalCliente() {
  const { token } = useParams();
  const [data,      setData]      = useState(null);
  const [facturas,  setFacturas]  = useState([]);
  const [error,     setError]     = useState('');
  const [tab,       setTab]       = useState('licencias');
  const [form,      setForm]      = useState({});
  const [guardando, setGuardando] = useState(false);
  const [msgGuard,  setMsgGuard]  = useState('');

  useEffect(() => {
    fetch(`${API}/${token}`)
      .then(r => r.json())
      .then(d => {
        if (!d.ok) return setError(d.msg || 'Portal no encontrado');
        setData(d);
        setForm({
          nombre:   d.cliente.nombre || '',
          email:    d.cliente.email  || '',
          telefono: d.cliente.telefono || '',
          empresa:  d.cliente.empresa  || ''
        });
      })
      .catch(() => setError('No se pudo cargar el portal'));

    fetch(`${API}/${token}/facturas`)
      .then(r => r.json())
      .then(d => { if (d.ok) setFacturas(d.data); });
  }, [token]);

  function descargarPDF(facturaId, numero) {
    fetch(`${API}/${token}/facturas/${facturaId}/pdf`)
      .then(r => r.blob())
      .then(blob => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `${numero}.pdf`;
        a.click();
        URL.revokeObjectURL(a.href);
      });
  }

  async function guardarDatos(e) {
    e.preventDefault();
    setGuardando(true);
    setMsgGuard('');
    try {
      const r = await fetch(`${API}/${token}/datos`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form)
      });
      const d = await r.json();
      setMsgGuard(d.ok ? '✅ Datos actualizados' : d.msg);
    } catch {
      setMsgGuard('Error al guardar');
    } finally {
      setGuardando(false);
    }
  }

  if (error) return (
    <div style={styles.fullPage}>
      <div style={styles.errorBox}>
        <XCircle size={48} color="#ef4444" style={{ marginBottom: 12 }} />
        <h2 style={{ color: '#ef4444', marginBottom: 8 }}>Portal no encontrado</h2>
        <p style={{ color: '#64748b', fontSize: '.9rem' }}>{error}</p>
      </div>
    </div>
  );

  if (!data) return (
    <div style={styles.fullPage}>
      <div style={{ color: '#7c3aed', fontSize: '.95rem' }}>Cargando portal...</div>
    </div>
  );

  const { cliente, licencias } = data;
  const licPrincipal = licencias[0];

  return (
    <div style={styles.page}>
      {/* ── Header ── */}
      <div style={styles.header}>
        <div style={styles.headerInner}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 14 }}>
            <div style={styles.avatar}>{cliente.nombre.charAt(0).toUpperCase()}</div>
            <div>
              <div style={{ fontSize: '1.2rem', fontWeight: 700, color: '#fff' }}>{cliente.nombre}</div>
              {cliente.empresa && <div style={{ color: '#c4b5fd', fontSize: '.88rem' }}>{cliente.empresa}</div>}
            </div>
          </div>
          <div style={{ textAlign: 'right' }}>
            <div style={{ color: '#c4b5fd', fontSize: '.8rem' }}>AI Company CO</div>
            <div style={{ color: '#fff', fontSize: '.85rem', fontWeight: 600 }}>Portal del Cliente</div>
          </div>
        </div>
      </div>

      <div style={styles.body}>
        {/* ── Tabs ── */}
        <div style={styles.tabs}>
          {[['licencias','Mis licencias'],['facturas','Mis facturas'],['datos','Mis datos']].map(([k, label]) => (
            <button key={k} onClick={() => setTab(k)}
              style={{ ...styles.tab, ...(tab === k ? styles.tabActive : {}) }}>
              {label}
            </button>
          ))}
        </div>

        {/* ── Tab: Licencias ── */}
        {tab === 'licencias' && (
          <div>
            {licencias.length === 0 && (
              <div style={styles.empty}>No hay licencias asociadas a su cuenta.</div>
            )}
            {licencias.map(lic => (
              <div key={lic.id} style={styles.licCard}>
                {/* Estado badge */}
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 10 }}>
                  <div>
                    <div style={{ fontSize: '1.1rem', fontWeight: 700, color: '#1e1b4b', marginBottom: 4 }}>
                      {lic.producto}
                    </div>
                    <div style={{ fontSize: '.85rem', color: '#64748b' }}>
                      Vence: <strong>{new Date(lic.fecha_vencimiento + 'T00:00:00').toLocaleDateString('es-CO', { year:'numeric', month:'long', day:'numeric' })}</strong>
                    </div>
                  </div>
                  <div style={{ ...styles.badge, ...(lic.valida ? styles.badgeOk : styles.badgeError) }}>
                    {lic.valida
                      ? <><CheckCircle size={14} /> Activa</>
                      : <><XCircle size={14} /> Vencida</>
                    }
                  </div>
                </div>

                {/* Días restantes */}
                <div style={styles.diasBar}>
                  {lic.valida ? (
                    lic.dias_restantes <= 7
                      ? <div style={{ ...styles.diasPill, background: '#fef3c7', color: '#d97706' }}>
                          <Clock size={13} /> Vence en {lic.dias_restantes} día{lic.dias_restantes !== 1 ? 's' : ''}
                        </div>
                      : <div style={{ ...styles.diasPill, background: '#d1fae5', color: '#059669' }}>
                          <CheckCircle size={13} /> {lic.dias_restantes} días restantes
                        </div>
                  ) : (
                    <div style={{ ...styles.diasPill, background: '#fee2e2', color: '#dc2626' }}>
                      <XCircle size={13} /> Sistema bloqueado
                    </div>
                  )}
                  {lic.suscripcion_activa && (
                    <div style={{ ...styles.diasPill, background: '#ede9fe', color: '#7c3aed' }}>
                      <RefreshCw size={13} /> Renovación automática activa
                    </div>
                  )}
                </div>

                {/* Botón pagar */}
                {(!lic.valida || lic.dias_restantes <= 10) && (
                  <a href={lic.pago_url} target="_blank" rel="noopener noreferrer" style={styles.btnPagar}>
                    💳 {lic.valida ? 'Renovar anticipadamente' : 'Pagar y reactivar ahora'}
                  </a>
                )}
              </div>
            ))}

            {/* Soporte */}
            <div style={styles.soporteBox}>
              <p style={{ margin: 0, fontSize: '.88rem', color: '#64748b' }}>
                ¿Tiene dudas? Contáctenos por WhatsApp
              </p>
              <a href="https://wa.me/573212674754" target="_blank" rel="noopener noreferrer" style={styles.btnWa}>
                💬 Abrir WhatsApp
              </a>
            </div>
          </div>
        )}

        {/* ── Tab: Facturas ── */}
        {tab === 'facturas' && (
          <div>
            {facturas.length === 0 && (
              <div style={styles.empty}>No hay facturas disponibles aún.</div>
            )}
            {facturas.map(f => (
              <div key={f.id} style={styles.facturaRow}>
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={{ fontFamily: 'monospace', fontWeight: 700, color: '#7c3aed', fontSize: '.95rem' }}>{f.numero}</div>
                  <div style={{ fontSize: '.82rem', color: '#64748b', marginTop: 2 }}>{f.concepto}</div>
                  <div style={{ fontSize: '.8rem', color: '#94a3b8', marginTop: 2 }}>
                    {new Date(f.fecha + 'T00:00:00').toLocaleDateString('es-CO')} · {f.metodo_pago}
                  </div>
                </div>
                <div style={{ textAlign: 'right', flexShrink: 0 }}>
                  <div style={{ fontWeight: 700, color: '#059669', fontSize: '.95rem' }}>
                    ${Number(f.monto).toLocaleString('es-CO')}
                  </div>
                  <button onClick={() => descargarPDF(f.id, f.numero)} style={styles.btnPDF}>
                    <Download size={13} /> PDF
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}

        {/* ── Tab: Datos ── */}
        {tab === 'datos' && (
          <div style={styles.datosCard}>
            <h3 style={{ fontSize: '1rem', fontWeight: 700, color: '#1e1b4b', marginBottom: 20 }}>
              Actualizar mis datos
            </h3>
            <form onSubmit={guardarDatos}>
              <div style={styles.formGrid}>
                <FieldPortal icon={<User size={14} />} label="Nombre completo">
                  <input value={form.nombre} onChange={e => setForm({...form, nombre: e.target.value})} style={styles.input} required />
                </FieldPortal>
                <FieldPortal icon={<Building2 size={14} />} label="Empresa">
                  <input value={form.empresa} onChange={e => setForm({...form, empresa: e.target.value})} style={styles.input} />
                </FieldPortal>
                <FieldPortal icon={<Mail size={14} />} label="Correo electrónico">
                  <input type="email" value={form.email} onChange={e => setForm({...form, email: e.target.value})} style={styles.input} />
                </FieldPortal>
                <FieldPortal icon={<Phone size={14} />} label="Teléfono">
                  <input value={form.telefono} onChange={e => setForm({...form, telefono: e.target.value})} style={styles.input} />
                </FieldPortal>
              </div>
              {msgGuard && (
                <div style={{ color: msgGuard.startsWith('✅') ? '#059669' : '#dc2626', fontSize: '.88rem', marginBottom: 12 }}>
                  {msgGuard}
                </div>
              )}
              <button type="submit" disabled={guardando} style={styles.btnGuardar}>
                <Save size={15} /> {guardando ? 'Guardando...' : 'Guardar cambios'}
              </button>
            </form>
          </div>
        )}
      </div>

      {/* ── Footer ── */}
      <div style={styles.footer}>
        <span>AI Company CO · +57 321 267 4754 · aicompanyco.com</span>
      </div>
    </div>
  );
}

function FieldPortal({ icon, label, children }) {
  return (
    <div style={{ marginBottom: 16 }}>
      <label style={{ display: 'flex', alignItems: 'center', gap: 5, fontSize: '.8rem', fontWeight: 600, color: '#374151', marginBottom: 6 }}>
        {icon} {label}
      </label>
      {children}
    </div>
  );
}

const styles = {
  fullPage:   { minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#f5f3ff' },
  errorBox:   { background: '#fff', borderRadius: 16, padding: 40, textAlign: 'center', boxShadow: '0 2px 12px rgba(0,0,0,.08)', maxWidth: 360 },
  page:       { minHeight: '100vh', background: '#f5f3ff', display: 'flex', flexDirection: 'column' },
  header:     { background: 'linear-gradient(135deg, #1e1b4b 0%, #5b21b6 100%)', padding: '24px 0 28px' },
  headerInner:{ maxWidth: 680, margin: '0 auto', padding: '0 20px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 12 },
  avatar:     { width: 50, height: 50, borderRadius: '50%', background: '#7c3aed', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '1.4rem', fontWeight: 700, color: '#fff', border: '2px solid rgba(255,255,255,.3)' },
  body:       { maxWidth: 680, margin: '0 auto', padding: '28px 20px', flex: 1, width: '100%', boxSizing: 'border-box' },
  tabs:       { display: 'flex', gap: 6, marginBottom: 20, background: '#fff', borderRadius: 10, padding: 5, boxShadow: '0 1px 4px rgba(0,0,0,.06)' },
  tab:        { flex: 1, padding: '9px 12px', border: 'none', borderRadius: 7, background: 'transparent', fontSize: '.88rem', fontWeight: 500, color: '#64748b', cursor: 'pointer' },
  tabActive:  { background: '#7c3aed', color: '#fff', fontWeight: 700 },
  licCard:    { background: '#fff', borderRadius: 14, padding: 22, marginBottom: 14, boxShadow: '0 1px 6px rgba(0,0,0,.07)' },
  badge:      { display: 'inline-flex', alignItems: 'center', gap: 5, padding: '5px 12px', borderRadius: 20, fontSize: '.82rem', fontWeight: 700 },
  badgeOk:    { background: '#d1fae5', color: '#065f46' },
  badgeError: { background: '#fee2e2', color: '#991b1b' },
  diasBar:    { display: 'flex', gap: 8, flexWrap: 'wrap', margin: '14px 0' },
  diasPill:   { display: 'inline-flex', alignItems: 'center', gap: 5, padding: '4px 10px', borderRadius: 20, fontSize: '.8rem', fontWeight: 600 },
  btnPagar:   { display: 'block', textAlign: 'center', padding: '13px', background: 'linear-gradient(135deg, #5b21b6, #7c3aed)', color: '#fff', borderRadius: 10, textDecoration: 'none', fontWeight: 700, fontSize: '.95rem', marginTop: 8 },
  soporteBox: { background: '#fff', borderRadius: 12, padding: '16px 20px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12, flexWrap: 'wrap', boxShadow: '0 1px 4px rgba(0,0,0,.06)' },
  btnWa:      { padding: '9px 18px', background: '#25d366', color: '#fff', borderRadius: 8, textDecoration: 'none', fontWeight: 700, fontSize: '.88rem', whiteSpace: 'nowrap' },
  facturaRow: { background: '#fff', borderRadius: 12, padding: '16px 18px', marginBottom: 10, display: 'flex', alignItems: 'center', gap: 16, boxShadow: '0 1px 4px rgba(0,0,0,.06)' },
  btnPDF:     { display: 'inline-flex', alignItems: 'center', gap: 4, marginTop: 6, padding: '5px 12px', background: '#ede9fe', color: '#7c3aed', border: 'none', borderRadius: 7, fontSize: '.8rem', fontWeight: 700, cursor: 'pointer' },
  datosCard:  { background: '#fff', borderRadius: 14, padding: 28, boxShadow: '0 1px 6px rgba(0,0,0,.07)' },
  formGrid:   { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 16px' },
  input:      { width: '100%', padding: '9px 12px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.9rem', outline: 'none', boxSizing: 'border-box', background: '#fafafa' },
  btnGuardar: { display: 'inline-flex', alignItems: 'center', gap: 6, padding: '10px 22px', background: '#5b21b6', color: '#fff', border: 'none', borderRadius: 9, fontSize: '.9rem', fontWeight: 700, cursor: 'pointer' },
  empty:      { textAlign: 'center', padding: 40, color: '#94a3b8', fontSize: '.9rem' },
  footer:     { textAlign: 'center', padding: '16px 20px', color: '#94a3b8', fontSize: '.78rem' }
};
