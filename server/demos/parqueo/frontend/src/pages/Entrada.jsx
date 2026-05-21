import { useState, useEffect } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Car, Printer, CheckCircle } from 'lucide-react';

const TIPOS = ['auto', 'moto', 'discapacitado', 'VIP'];

export default function Entrada() {
  const [form, setForm] = useState({ placa: '', tipo_vehiculo: 'auto', color: '', marca: '', observaciones: '' });
  const [loading, setLoading] = useState(false);
  const [ticket, setTicket] = useState(null);
  const [espacioDisp, setEspacioDisp] = useState(null);

  useEffect(() => {
    if (form.tipo_vehiculo) {
      api.get(`/espacios/disponibles?tipo=${form.tipo_vehiculo}`)
        .then(r => setEspacioDisp(r.data))
        .catch(() => setEspacioDisp(null));
    }
  }, [form.tipo_vehiculo]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.placa.trim()) return toast.error('La placa es requerida');
    setLoading(true);
    try {
      const res = await api.post('/tickets/entrada', form);
      setTicket(res.data);
      toast.success(`✅ Entrada registrada — Espacio ${res.data.espacio_numero}`);
      setForm({ placa: '', tipo_vehiculo: 'auto', color: '', marca: '', observaciones: '' });
    } catch (err) {
      toast.error(err.response?.data?.error || 'Error al registrar entrada');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6 animate-fade-in">
      {/* Disponibilidad */}
      {espacioDisp ? (
        <div className="bg-emerald-900/20 border border-emerald-700/50 rounded-xl p-4 flex items-center gap-3">
          <div className="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center">
            <CheckCircle className="w-5 h-5 text-park-libre" />
          </div>
          <div>
            <p className="text-park-libre font-semibold text-sm">Espacio disponible</p>
            <p className="text-park-muted text-xs">Se asignará el espacio <strong className="text-park-text">{espacioDisp.numero}</strong></p>
          </div>
        </div>
      ) : (
        <div className="bg-red-900/20 border border-red-700/50 rounded-xl p-4">
          <p className="text-park-ocupado font-semibold text-sm">⚠️ Sin espacios disponibles para {form.tipo_vehiculo}</p>
        </div>
      )}

      {/* Form */}
      <div className="card">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-10 h-10 bg-park-accent/10 rounded-xl flex items-center justify-center">
            <Car className="w-5 h-5 text-park-accent" />
          </div>
          <div>
            <h2 className="text-park-text font-semibold">Registrar Entrada</h2>
            <p className="text-park-muted text-xs">Ingreso de vehículo al parqueo</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-park-muted text-sm font-medium mb-1.5">Placa *</label>
              <input
                className="input uppercase"
                placeholder="ABC-1234"
                value={form.placa}
                onChange={e => setForm({ ...form, placa: e.target.value.toUpperCase() })}
                required
                autoFocus
              />
            </div>
            <div>
              <label className="block text-park-muted text-sm font-medium mb-1.5">Tipo de Vehículo *</label>
              <select
                className="select"
                value={form.tipo_vehiculo}
                onChange={e => setForm({ ...form, tipo_vehiculo: e.target.value })}
              >
                {TIPOS.map(t => <option key={t} value={t} className="capitalize">{t}</option>)}
              </select>
            </div>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-park-muted text-sm font-medium mb-1.5">Color</label>
              <input className="input" placeholder="Azul, Rojo..." value={form.color} onChange={e => setForm({ ...form, color: e.target.value })} />
            </div>
            <div>
              <label className="block text-park-muted text-sm font-medium mb-1.5">Marca</label>
              <input className="input" placeholder="Toyota, Honda..." value={form.marca} onChange={e => setForm({ ...form, marca: e.target.value })} />
            </div>
          </div>
          <div>
            <label className="block text-park-muted text-sm font-medium mb-1.5">Observaciones</label>
            <textarea className="input resize-none" rows={2} placeholder="Notas adicionales..." value={form.observaciones} onChange={e => setForm({ ...form, observaciones: e.target.value })} />
          </div>
          <button type="submit" disabled={loading || !espacioDisp} className="btn-primary w-full justify-center py-3">
            {loading ? <span className="animate-pulse">Registrando...</span> : <><Car className="w-4 h-4" /> Registrar Entrada</>}
          </button>
        </form>
      </div>

      {/* Ticket Preview */}
      {ticket && (
        <div className="card border-park-libre/30 animate-slide-in">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-park-libre font-bold text-lg flex items-center gap-2">
              <CheckCircle className="w-5 h-5" /> Ticket Generado
            </h3>
            <button onClick={() => window.print()} className="btn-secondary text-sm py-1.5">
              <Printer className="w-4 h-4" /> Imprimir
            </button>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            {[
              ['Código', ticket.codigo],
              ['Placa', ticket.placa],
              ['Tipo', ticket.tipo_vehiculo],
              ['Espacio', ticket.espacio_numero],
              ['Hora entrada', new Date(ticket.hora_entrada).toLocaleString('es-EC')],
            ].map(([k, v]) => (
              <div key={k} className="bg-park-sidebar rounded-lg p-3">
                <p className="text-park-muted text-xs">{k}</p>
                <p className="text-park-text font-semibold capitalize">{v}</p>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
