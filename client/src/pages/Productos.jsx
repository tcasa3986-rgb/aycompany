import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Pencil, Trash2, X } from 'lucide-react';

const VACIO = { nombre: '', descripcion: '', precio_mensual: '', activo: true };

export default function Productos() {
  const [productos, setProductos] = useState([]);
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState(VACIO);
  const [editId, setEditId] = useState(null);

  const cargar = () => api.get('/productos').then(r => setProductos(r.data.data));
  useEffect(() => { cargar(); }, []);

  function abrirNuevo() { setForm(VACIO); setEditId(null); setModal(true); }
  function abrirEditar(p) { setForm(p); setEditId(p.id); setModal(true); }

  async function guardar(e) {
    e.preventDefault();
    try {
      editId ? await api.put(`/productos/${editId}`, form) : await api.post('/productos', form);
      toast.success(editId ? 'Producto actualizado' : 'Producto creado');
      setModal(false);
      cargar();
    } catch { toast.error('Error al guardar'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este producto?')) return;
    await api.delete(`/productos/${id}`);
    toast.success('Eliminado');
    cargar();
  }

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Productos / Sistemas</h1>
        <button onClick={abrirNuevo} style={btn('#4f46e5')}><Plus size={16} /> Nuevo producto</button>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: 16 }}>
        {productos.map(p => (
          <div key={p.id} style={{ background: '#fff', borderRadius: 12, padding: 20, boxShadow: '0 1px 4px rgba(0,0,0,.07)', borderTop: `3px solid ${p.activo ? '#6366f1' : '#e2e8f0'}` }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 8 }}>
              <h3 style={{ fontSize: '1rem', fontWeight: 700, color: '#1e293b' }}>{p.nombre}</h3>
              <span style={{ background: p.activo ? '#dcfce7' : '#f1f5f9', color: p.activo ? '#16a34a' : '#94a3b8', padding: '2px 9px', borderRadius: 20, fontSize: '.72rem', fontWeight: 600 }}>
                {p.activo ? 'Activo' : 'Inactivo'}
              </span>
            </div>
            <p style={{ fontSize: '.85rem', color: '#64748b', marginBottom: 12, minHeight: 36 }}>{p.descripcion || 'Sin descripción'}</p>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <span style={{ fontSize: '1.2rem', fontWeight: 700, color: '#4f46e5' }}>${Number(p.precio_mensual).toLocaleString('es')}<span style={{ fontSize: '.75rem', color: '#94a3b8', fontWeight: 400 }}>/mes</span></span>
              <div>
                <button onClick={() => abrirEditar(p)} style={btnSm('#6366f1')}><Pencil size={13} /></button>
                <button onClick={() => eliminar(p.id)} style={btnSm('#ef4444')}><Trash2 size={13} /></button>
              </div>
            </div>
          </div>
        ))}
        {productos.length === 0 && <p style={{ color: '#94a3b8' }}>No hay productos registrados</p>}
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{editId ? 'Editar' : 'Nuevo'} producto</h2>
              <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={guardar}>
              <Field label="Nombre del sistema *"><input value={form.nombre} onChange={e => setForm({...form, nombre: e.target.value})} required /></Field>
              <Field label="Descripción"><textarea rows={3} value={form.descripcion} onChange={e => setForm({...form, descripcion: e.target.value})} style={{ resize: 'vertical' }} /></Field>
              <Field label="Precio mensual ($)"><input type="number" min="0" step="0.01" value={form.precio_mensual} onChange={e => setForm({...form, precio_mensual: e.target.value})} required /></Field>
              <Field label="Estado">
                <select value={form.activo ? '1' : '0'} onChange={e => setForm({...form, activo: e.target.value === '1'})}>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#4f46e5')}>Guardar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const btn   = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600 });
const btnSm = bg => ({ padding: '5px 8px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, marginLeft: 4 });
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 480 };
function Field({ label, children }) { return <div style={{ marginBottom: 14 }}><label style={{ display: 'block', fontSize: '.82rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>{children}</div>; }
