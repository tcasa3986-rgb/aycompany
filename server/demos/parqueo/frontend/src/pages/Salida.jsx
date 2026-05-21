import { useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Search, Clock, DollarSign, CreditCard, QrCode, Banknote, CheckCircle } from 'lucide-react';

const METODOS = [
  { value: 'efectivo', label: 'Efectivo', Icon: Banknote },
  { value: 'tarjeta', label: 'Tarjeta', Icon: CreditCard },
  { value: 'QR', label: 'QR / Digital', Icon: QrCode },
];

import { useConfig } from '../contexts/ConfigContext';

export default function Salida() {
  const { config } = useConfig();
  const [busqueda, setBusqueda] = useState('');
  const [ticket, setTicket] = useState(null);
  const [cobro, setCobro] = useState(null);
  const [metPago, setMetPago] = useState('efectivo');
  const [montoRecibido, setMontoRecibido] = useState('');
  const [loading, setLoading] = useState(false);
  const [pagado, setPagado] = useState(null);

  const buscarTicket = async (e) => {
    e.preventDefault();
    setLoading(true);
    setTicket(null);
    setCobro(null);
    setPagado(null);
    try {
      const res = await api.get(`/tickets/buscar/${busqueda.toUpperCase()}`);
      setTicket(res.data);
    } catch (err) {
      toast.error(err.response?.data?.error || 'Ticket no encontrado');
    } finally {
      setLoading(false);
    }
  };

  const calcularSalida = async () => {
    if (!ticket) return;
    setLoading(true);
    try {
      const res = await api.put(`/tickets/${ticket.id}/salida`, { descuento: 0 });
      setCobro(res.data);
      setMontoRecibido(String(res.data.monto_cobrar.toFixed(2)));
    } catch (err) {
      toast.error(err.response?.data?.error || 'Error al calcular salida');
    } finally {
      setLoading(false);
    }
  };

  const registrarPago = async () => {
    if (!cobro) return;
    setLoading(true);
    try {
      const res = await api.post('/pagos', {
        ticket_id: cobro.ticket_id,
        monto: cobro.monto_cobrar,
        metodo_pago: metPago,
        monto_recibido: parseFloat(montoRecibido) || cobro.monto_cobrar
      });
      setPagado({ ...cobro, cambio: res.data.cambio, metodo: metPago });
      toast.success('✅ Pago registrado exitosamente');
      setTicket(null);
      setCobro(null);
      setBusqueda('');
    } catch (err) {
      toast.error(err.response?.data?.error || 'Error al registrar pago');
    } finally {
      setLoading(false);
    }
  };

  const minutosToText = (m) => {
    if (m < 60) return `${m} min`;
    const h = Math.floor(m / 60);
    const min = m % 60;
    return `${h}h ${min}min`;
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6 animate-fade-in">
      {/* Búsqueda */}
      <div className="card">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 bg-park-accent/10 rounded-xl flex items-center justify-center">
            <Search className="w-5 h-5 text-park-accent" />
          </div>
          <div>
            <h2 className="text-park-text font-semibold">Registrar Salida</h2>
            <p className="text-park-muted text-xs">Busca por placa o código de ticket</p>
          </div>
        </div>
        <form onSubmit={buscarTicket} className="flex gap-3">
          <input
            className="input flex-1 uppercase"
            placeholder="Placa: ABC-1234"
            value={busqueda}
            onChange={e => setBusqueda(e.target.value)}
            autoFocus
          />
          <button type="submit" className="btn-primary px-6" disabled={loading}>
            <Search className="w-4 h-4" />
            {loading ? '...' : 'Buscar'}
          </button>
        </form>
      </div>

      {/* Ticket encontrado */}
      {ticket && !cobro && (
        <div className="card border-blue-700/30 animate-slide-in">
          <h3 className="text-park-text font-semibold mb-4 flex items-center gap-2">
            <Clock className="w-4 h-4 text-blue-400" /> Ticket Activo
          </h3>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5 text-sm">
            {[
              ['Código', ticket.codigo],
              ['Placa', ticket.placa],
              ['Tipo', ticket.tipo_vehiculo],
              ['Espacio', ticket.espacio_numero],
              ['Zona', ticket.zona_nombre],
              ['Hora entrada', new Date(ticket.hora_entrada).toLocaleString('es-EC')],
            ].map(([k, v]) => (
              <div key={k} className="bg-park-sidebar rounded-lg p-3">
                <p className="text-park-muted text-xs">{k}</p>
                <p className="text-park-text font-semibold capitalize">{v}</p>
              </div>
            ))}
          </div>
          <button onClick={calcularSalida} disabled={loading} className="btn-primary w-full justify-center py-3">
            <DollarSign className="w-4 h-4" />
            {loading ? 'Calculando...' : 'Calcular Cobro y Registrar Salida'}
          </button>
        </div>
      )}

      {/* Cobro calculado */}
      {cobro && !pagado && (
        <div className="card border-park-accent/30 animate-slide-in">
          <h3 className="text-park-accent font-bold text-lg mb-4 flex items-center gap-2">
            <DollarSign className="w-5 h-5" /> Resumen de Cobro
          </h3>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5 text-sm">
            {[
              ['Tiempo', minutosToText(cobro.tiempo_minutos)],
              ['Tarifa/hora', `${config?.moneda || '$'}${cobro.tarifa_hora}`],
              ['Descuento', `${config?.moneda || '$'}${cobro.descuento}`],
            ].map(([k, v]) => (
              <div key={k} className="bg-park-sidebar rounded-lg p-3 text-center">
                <p className="text-park-muted text-xs">{k}</p>
                <p className="text-park-text font-semibold">{v}</p>
              </div>
            ))}
          </div>
          <div className="bg-park-accent/10 border border-park-accent/30 rounded-xl p-4 text-center mb-5">
            <p className="text-park-muted text-sm mb-1">TOTAL A COBRAR</p>
            <p className="text-park-accent text-4xl font-black">{config?.moneda || '$'}{cobro.monto_cobrar.toFixed(2)}</p>
          </div>

          <div className="space-y-4">
            <div>
              <label className="block text-park-muted text-sm font-medium mb-2">Método de Pago</label>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
                {METODOS.map(({ value, label, Icon }) => (
                  <button
                    key={value}
                    type="button"
                    onClick={() => setMetPago(value)}
                    className={`flex flex-col items-center gap-1.5 p-3 rounded-xl border transition-all ${
                      metPago === value ? 'border-park-accent bg-park-accent/10 text-park-accent' : 'border-park-border text-park-muted hover:border-park-accent/50'
                    }`}
                  >
                    <Icon className="w-5 h-5" />
                    <span className="text-xs font-medium">{label}</span>
                  </button>
                ))}
              </div>
            </div>
            {metPago === 'efectivo' && (
              <div>
                <label className="block text-park-muted text-sm font-medium mb-1.5">Monto Recibido</label>
                <input
                  type="number"
                  step="0.01"
                  className="input"
                  value={montoRecibido}
                  onChange={e => setMontoRecibido(e.target.value)}
                />
                {parseFloat(montoRecibido) >= cobro.monto_cobrar && (
                  <p className="text-park-libre text-sm mt-1.5">
                    Cambio: <strong>{config?.moneda || '$'}{(parseFloat(montoRecibido) - cobro.monto_cobrar).toFixed(2)}</strong>
                  </p>
                )}
              </div>
            )}
            <button onClick={registrarPago} disabled={loading} className="btn-primary w-full justify-center py-3">
              <CheckCircle className="w-4 h-4" />
              {loading ? 'Procesando...' : 'Registrar Pago y Dar Salida'}
            </button>
          </div>
        </div>
      )}

      {/* Pago exitoso */}
      {pagado && (
        <div className="card border-park-libre/30 animate-slide-in text-center py-8">
          <div className="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircle className="w-8 h-8 text-park-libre" />
          </div>
          <h3 className="text-park-libre text-xl font-bold mb-2">¡Pago Exitoso!</h3>
          <p className="text-park-text font-semibold text-2xl mb-1">{config?.moneda || '$'}{pagado.monto_cobrar?.toFixed(2)}</p>
          {pagado.cambio > 0 && (
            <p className="text-park-muted">Cambio entregado: <strong className="text-park-text">{config?.moneda || '$'}{pagado.cambio.toFixed(2)}</strong></p>
          )}
          <p className="text-park-muted text-sm mt-3 capitalize">Método: {pagado.metodo}</p>
          <button onClick={() => setPagado(null)} className="btn-secondary mt-6 mx-auto">
            Nueva Búsqueda
          </button>
        </div>
      )}
    </div>
  );
}
