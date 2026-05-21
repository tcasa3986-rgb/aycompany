import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Settings, Save } from 'lucide-react';

const GRUPOS = {
  empresa:    'Información de la empresa',
  apariencia: 'Apariencia',
  finanzas:   'Finanzas y pagos',
  contacto:   'Contacto y soporte',
  sistema:    'Sistema'
};

export default function Configuracion() {
  const [configs, setConfigs] = useState([]);
  const [form,    setForm]    = useState({});
  const [saving,  setSaving]  = useState(false);

  useEffect(() => {
    api.get('/configuracion').then(r => {
      setConfigs(r.data.data);
      const f = {};
      r.data.data.forEach(c => { f[c.clave] = c.valor || ''; });
      setForm(f);
    });
  }, []);

  async function guardar(e) {
    e.preventDefault();
    setSaving(true);
    try {
      await api.put('/configuracion', form);
      toast.success('Configuración guardada');
    } catch { toast.error('Error al guardar'); }
    setSaving(false);
  }

  const grupos = {};
  configs.forEach(c => {
    if (!grupos[c.grupo]) grupos[c.grupo] = [];
    grupos[c.grupo].push(c);
  });

  return (
    <div style={{ padding:32, maxWidth:800 }}>
      <div style={{ display:'flex', alignItems:'center', gap:10, marginBottom:28 }}>
        <Settings size={22} color="#6366f1"/>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, color:'#1e293b' }}>Configuración del sistema</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', marginTop:2 }}>Personaliza tu plataforma sin tocar código</p>
        </div>
      </div>

      <form onSubmit={guardar}>
        {Object.entries(grupos).map(([grupo, items]) => (
          <div key={grupo} style={{ background:'#fff', borderRadius:12, padding:24, marginBottom:16, boxShadow:'0 1px 4px rgba(0,0,0,.07)' }}>
            <h2 style={{ fontSize:'.95rem', fontWeight:700, color:'#1e1b4b', marginBottom:18, paddingBottom:10, borderBottom:'1px solid #f1f5f9' }}>
              {GRUPOS[grupo] || grupo}
            </h2>
            <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:'0 20px' }}>
              {items.map(c => (
                <div key={c.clave} style={{ marginBottom:16 }}>
                  <label style={{ display:'block', fontSize:'.8rem', fontWeight:600, color:'#374151', marginBottom:5 }}>
                    {c.etiqueta}
                  </label>
                  {c.tipo === 'booleano' ? (
                    <select value={form[c.clave] || 'false'} onChange={e => setForm(f => ({...f, [c.clave]: e.target.value}))}
                      style={inputStyle}>
                      <option value="true">Activado</option>
                      <option value="false">Desactivado</option>
                    </select>
                  ) : c.tipo === 'color' ? (
                    <div style={{ display:'flex', gap:8, alignItems:'center' }}>
                      <input type="color" value={form[c.clave] || '#6366f1'}
                        onChange={e => setForm(f => ({...f, [c.clave]: e.target.value}))}
                        style={{ width:40, height:36, padding:2, border:'1px solid #e2e8f0', borderRadius:6, cursor:'pointer' }}/>
                      <input type="text" value={form[c.clave] || ''} onChange={e => setForm(f => ({...f, [c.clave]: e.target.value}))}
                        style={{ ...inputStyle, flex:1 }} placeholder="#6366f1"/>
                    </div>
                  ) : (
                    <input type={c.tipo === 'numero' ? 'number' : 'text'}
                      value={form[c.clave] || ''} onChange={e => setForm(f => ({...f, [c.clave]: e.target.value}))}
                      style={inputStyle} placeholder={c.etiqueta}/>
                  )}
                </div>
              ))}
            </div>
          </div>
        ))}

        <div style={{ display:'flex', justifyContent:'flex-end' }}>
          <button type="submit" disabled={saving}
            style={{ display:'inline-flex', alignItems:'center', gap:6, padding:'11px 24px', background:'#6366f1', color:'#fff', border:'none', borderRadius:9, fontSize:'.95rem', fontWeight:700, cursor:'pointer' }}>
            <Save size={16}/> {saving ? 'Guardando...' : 'Guardar configuración'}
          </button>
        </div>
      </form>
    </div>
  );
}

const inputStyle = { width:'100%', padding:'8px 12px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.9rem', outline:'none', boxSizing:'border-box', background:'#fafafa' };
