import { useState, useEffect } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Pencil, Trash2, X, Save } from 'lucide-react';

const TIPOS = ['auto', 'moto', 'discapacitado', 'VIP'];
const MODALIDADES = ['hora', 'fraccion', 'dia', 'mensual'];
const EMPTY = { tipo_vehiculo: 'auto', modalidad: 'hora', precio: '', tiempo_gracia: 10, descripcion: '' };

import { useConfig } from '../contexts/ConfigContext';

export default function Tarifas() {
  const { config } = useConfig();
  const [tarifas, setTarifas] = useState([]);
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState(EMPTY);
  const [editId, setEditId] = useState(null);
  const [loading, setLoading] = useState(false);

  const fetch = async () => {
    const res = await api.get('/tarifas');
    setTarifas(res.data);
  };
  useEffect(() => { fetch(); }, []);

  const openAdd = () => { setForm(EMPTY); setEditId(null); setModal(true); };
  const openEdit = (t) => { setForm({ ...t }); setEditId(t.id); setModal(true); };

  const save = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      if (editId) {
        await api.put(`/tarifas/${editId}`, { ...form, activo: 1 });
        toast.success('Tarifa actualizada');
      } else {
        await api.post('/tarifas', form);
        toast.success('Tarifa creada');
      }
      setModal(false);
      fetch();
    } catch (err) {
      toast.error(err.response?.data?.error || 'Error');
    } finally {
      setLoading(false);
    }
  };

  const remove = async (id) => {
    if (!confirm('¿Desactivar esta tarifa?')) return;
    await api.delete(`/tarifas/${id}`);
    toast.success('Tarifa desactivada');
    fetch();
  };

  const TIPO_BADGE = { auto: 'bg-blue-900/40 text-blue-400', moto: 'bg-purple-900/40 text-purple-400', VIP: 'bg-amber-900/40 text-amber-400', discapacitado: 'bg-green-900/40 text-green-400' };

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="flex items-center justify-between">
        <h2 className="text-park-text font-semibold text-lg">Gestión de Tarifas</h2>
        <button onClick={openAdd} className="btn-primary">
          <Plus className="w-4 h-4" /> Nueva Tarifa
        </button>
      </div>

      <div className="card overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-park-border">
              {['Tipo Vehículo', 'Modalidad', 'Precio', 'T. Gracia', 'Descripción', 'Estado', ''].map(h => (
                <th key={h} className="table-header text-left pb-3 px-2">{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {tarifas.map(t => (
              <tr key={t.id} className="hover:bg-park-border/10 transition-colors">
                <td className="table-cell px-2">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${TIPO_BADGE[t.tipo_vehiculo]}`}>
                    {t.tipo_vehiculo}
                  </span>
                </td>
                <td className="table-cell px-2 capitalize">{t.modalidad}</td>
                <td className="table-cell px-2 font-bold text-park-accent">{config?.moneda || '$'}{parseFloat(t.precio).toFixed(2)}</td>
                <td className="table-cell px-2">{t.tiempo_gracia} min</td>
                <td className="table-cell px-2 text-park-muted text-xs max-w-xs truncate">{t.descripcion}</td>
                <td className="table-cell px-2">
                  {t.activo ? <span className="badge-libre">Activa</span> : <span className="badge-mant">Inactiva</span>}
                </td>
                <td className="table-cell px-2">
                  <div className="flex gap-2">
                    <button onClick={() => openEdit(t)} className="text-park-muted hover:text-park-accent transition-colors p-1">
                      <Pencil className="w-4 h-4" />
                    </button>
                    <button onClick={() => remove(t.id)} className="text-park-muted hover:text-park-ocupado transition-colors p-1">
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {!tarifas.length && <p className="text-center text-park-muted py-8">No hay tarifas registradas</p>}
      </div>

      {/* Modal */}
      {modal && (
        <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
          <div className="card max-w-md w-full animate-slide-in">
            <div className="flex items-center justify-between mb-5">
              <h3 className="text-park-text font-semibold">{editId ? 'Editar' : 'Nueva'} Tarifa</h3>
              <button onClick={() => setModal(false)} className="text-park-muted hover:text-park-text"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={save} className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-park-muted text-sm mb-1">Tipo Vehículo</label>
                  <select className="select" value={form.tipo_vehiculo} onChange={e => setForm({ ...form, tipo_vehiculo: e.target.value })}>
                    {TIPOS.map(t => <option key={t}>{t}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-park-muted text-sm mb-1">Modalidad</label>
                  <select className="select" value={form.modalidad} onChange={e => setForm({ ...form, modalidad: e.target.value })}>
                    {MODALIDADES.map(m => <option key={m}>{m}</option>)}
                  </select>
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-park-muted text-sm mb-1">Precio ({config?.moneda || '$'})</label>
                  <input type="number" step="0.01" className="input" value={form.precio} onChange={e => setForm({ ...form, precio: e.target.value })} required />
                </div>
                <div>
                  <label className="block text-park-muted text-sm mb-1">Tiempo Gracia (min)</label>
                  <input type="number" className="input" value={form.tiempo_gracia} onChange={e => setForm({ ...form, tiempo_gracia: e.target.value })} />
                </div>
              </div>
              <div>
                <label className="block text-park-muted text-sm mb-1">Descripción</label>
                <input className="input" value={form.descripcion} onChange={e => setForm({ ...form, descripcion: e.target.value })} />
              </div>
              <div className="flex gap-3 pt-2">
                <button type="button" onClick={() => setModal(false)} className="btn-secondary flex-1 justify-center">Cancelar</button>
                <button type="submit" disabled={loading} className="btn-primary flex-1 justify-center">
                  <Save className="w-4 h-4" /> {loading ? '...' : 'Guardar'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
