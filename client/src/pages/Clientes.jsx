import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Search, Pencil, Trash2, X, ExternalLink, Copy } from 'lucide-react';

const VACIO = { nombre: '', email: '', telefono: '', empresa: '', direccion: '', notas: '' };

export default function Clientes() {
  const [clientes, setClientes] = useState([]);
  const [busqueda, setBusqueda] = useState('');
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState(VACIO);
  const [editId, setEditId] = useState(null);

  const cargar = () => api.get('/clientes').then(r => setClientes(r.data.data));
  useEffect(() => { cargar(); }, []);

  const filtrados = clientes.filter(c =>
    `${c.nombre} ${c.empresa} ${c.telefono} ${c.email}`.toLowerCase().includes(busqueda.toLowerCase())
  );

  function abrirNuevo() { setForm(VACIO); setEditId(null); setModal(true); }
  function abrirEditar(c) { setForm(c); setEditId(c.id); setModal(true); }

  async function guardar(e) {
    e.preventDefault();
    try {
      if (editId) {
        await api.put(`/clientes/${editId}`, form);
        toast.success('Cliente actualizado');
      } else {
        await api.post('/clientes', form);
        toast.success('Cliente creado');
      }
      setModal(false);
      cargar();
    } catch { toast.error('Error al guardar'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este cliente?')) return;
    await api.delete(`/clientes/${id}`);
    toast.success('Eliminado');
    cargar();
  }

  async function abrirPortal(c) {
    try {
      const r = await api.post(`/clientes/${c.id}/portal-token`, { enviarEmail: !!c.email });
      const url = r.data.portalUrl;
      await navigator.clipboard.writeText(url);
      toast.success(c.email ? `Link copiado y enviado a ${c.email}` : 'Link copiado al portapapeles');
    } catch {
      toast.error('Error al generar el portal');
    }
  }

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Clientes</h1>
        <button onClick={abrirNuevo} style={btnStyle('#4f46e5')}>
          <Plus size={16} /> Nuevo cliente
        </button>
      </div>

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
        <div style={{ padding: '16px 20px', borderBottom: '1px solid #f1f5f9', display: 'flex', alignItems: 'center', gap: 10 }}>
          <Search size={16} color="#94a3b8" />
          <input placeholder="Buscar por nombre, empresa, teléfono..." value={busqueda} onChange={e => setBusqueda(e.target.value)} style={{ border: 'none', outline: 'none', fontSize: '.9rem', flex: 1 }} />
        </div>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['Nombre', 'Empresa', 'Teléfono', 'Email', 'Portal', 'Acciones'].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {filtrados.map(c => (
              <tr key={c.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                <td style={td}><strong>{c.nombre}</strong></td>
                <td style={td}>{c.empresa || '—'}</td>
                <td style={td}>{c.telefono || '—'}</td>
                <td style={td}>{c.email || '—'}</td>
                <td style={td}>
                  <button onClick={() => abrirPortal(c)}
                    title={c.email ? 'Generar link y enviar por email' : 'Copiar link del portal'}
                    style={btnSmall('#7c3aed')}>
                    {c.email ? <ExternalLink size={13} /> : <Copy size={13} />}
                  </button>
                </td>
                <td style={td}>
                  <button onClick={() => abrirEditar(c)} style={btnSmall('#6366f1')}><Pencil size={13} /></button>
                  <button onClick={() => eliminar(c.id)} style={btnSmall('#ef4444')}><Trash2 size={13} /></button>
                </td>
              </tr>
            ))}
            {filtrados.length === 0 && <tr><td colSpan={5} style={{ padding: 24, textAlign: 'center', color: '#94a3b8' }}>No hay clientes</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{editId ? 'Editar' : 'Nuevo'} cliente</h2>
              <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={guardar}>
              <Grid2>
                <Field label="Nombre *"><input value={form.nombre} onChange={e => setForm({...form, nombre: e.target.value})} required /></Field>
                <Field label="Empresa"><input value={form.empresa} onChange={e => setForm({...form, empresa: e.target.value})} /></Field>
                <Field label="Teléfono"><input value={form.telefono} onChange={e => setForm({...form, telefono: e.target.value})} /></Field>
                <Field label="Email"><input type="email" value={form.email} onChange={e => setForm({...form, email: e.target.value})} /></Field>
              </Grid2>
              <Field label="Dirección"><input value={form.direccion} onChange={e => setForm({...form, direccion: e.target.value})} /></Field>
              <Field label="Notas"><textarea rows={3} value={form.notas} onChange={e => setForm({...form, notas: e.target.value})} style={{ resize: 'vertical' }} /></Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModal(false)} style={btnStyle('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btnStyle('#4f46e5')}>Guardar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem' };
const overlay = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 560, maxHeight: '90vh', overflowY: 'auto' };
const btnStyle = (bg) => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600 });
const btnSmall = (bg) => ({ padding: '5px 8px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, marginRight: 4 });
function Grid2({ children }) { return <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 12 }}>{children}</div>; }
function Field({ label, children }) { return <div style={{ marginBottom: 12 }}><label style={{ display: 'block', fontSize: '.82rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>{children}</div>; }
