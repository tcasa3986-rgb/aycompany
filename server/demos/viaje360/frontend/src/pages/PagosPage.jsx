import React, { useEffect, useState } from 'react';
import { CreditCard, Search, FileText, CheckCircle, Clock, XCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import api from '../services/api';
import useConfigStore from '../store/configStore';

export default function PagosPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [pagos, setPagos] = useState([]);

  const [loading, setLoading] = useState(true);
  const [buscar, setBuscar] = useState('');

  const fetchPagos = async () => {
    setLoading(true);
    try {
      const res = await api.get('/pagos');
      setPagos(res.data);
    } catch (error) {
      console.error(error);
      toast.error('Error al cargar la bandeja de pagos');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPagos();
  }, []);

  const handleDescargarRecibo = async (pagoId) => {
    try {
      const { data } = await api.get(`/pagos/${pagoId}/recibo`, { responseType: 'blob' });
      const url = window.URL.createObjectURL(new Blob([data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `Recibo-${pagoId}-Viaje360.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      toast.success('Recibo descargado correctamente');
    } catch (error) {
      console.error('Error al descargar recibo:', error);
      toast.error('Hubo un error al generar el recibo PDF');
    }
  };

  const getEstadoBadge = (estado) => {
    switch(estado) {
      case 'Verificado': return <span className="badge badge-green"><CheckCircle size={12}/> Verificado</span>;
      case 'Pendiente':  return <span className="badge badge-amber"><Clock size={12}/> Pendiente</span>;
      case 'Rechazado':  return <span className="badge badge-red"><XCircle size={12}/> Rechazado</span>;
      default: return <span className="badge badge-gray">{estado}</span>;
    }
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Historial de Pagos</h1>
          <p className="page-subtitle">Monitoreo global de ingresos y finanzas</p>
        </div>
      </div>

      <div className="table-wrapper">
        <div className="table-header">
          <div className="table-search">
            <Search size={14} style={{ color:'var(--text-muted)' }} />
            <input 
              placeholder="Buscar por código de reserva o referencia..." 
              value={buscar} 
              onChange={e => setBuscar(e.target.value)} 
            />
          </div>
        </div>

        {loading ? (
          <div style={{ textAlign:'center', padding:40 }}><div className="spinner" style={{ margin:'0 auto' }}/></div>
        ) : (
          <div style={{ overflowX: 'auto' }}>
            <table>
              <thead>
                <tr>
                  <th>Cod. Reserva</th>
                  <th>Cliente</th>
                  <th>Fecha Pago</th>
                  <th>Método</th>
                  <th>Referencia</th>
                  <th>Monto ({m})</th>

                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                {pagos
                  .filter(p => !buscar || 
                    p.Reserva?.codigo_reserva?.toLowerCase().includes(buscar.toLowerCase()) || 
                    p.referencia?.toLowerCase().includes(buscar.toLowerCase())
                  )
                  .map(pago => (
                  <tr key={pago.id}>
                    <td>
                      <span className="font-bold">{pago.Reserva?.codigo_reserva || 'N/A'}</span>
                    </td>
                    <td>
                      <div>{pago.Reserva?.cliente?.nombre} {pago.Reserva?.cliente?.apellido}</div>
                      <div className="text-xs text-muted">{pago.Reserva?.cliente?.email}</div>
                    </td>
                    <td>{new Date(pago.fecha_pago).toLocaleDateString('es-PE')}</td>
                    <td>
                      <div style={{ display:'flex', alignItems:'center', gap: 6 }}>
                        <CreditCard size={14} className="text-muted" />
                        {pago.metodo?.nombre || 'General'}
                      </div>
                    </td>
                    <td className="text-muted">{pago.referencia || '-'}</td>
                    <td className="font-bold" style={{ color: 'var(--color-success)' }}>
                      {m}{parseFloat(pago.monto).toLocaleString('es')}
                    </td>

                    <td>{getEstadoBadge(pago.estado)}</td>
                    <td>
                      <div className="td-actions">
                        <button 
                          className="btn btn-secondary btn-sm" 
                          onClick={() => handleDescargarRecibo(pago.id)}
                          title="Descargar Recibo Factura en PDF"
                        >
                          <FileText size={14} /> Recibo PDF
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
                {pagos.length === 0 && (
                  <tr>
                    <td colSpan="8" style={{ textAlign:'center', padding:'30px 20px', color:'var(--text-muted)' }}>
                      No hay pagos registrados
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
