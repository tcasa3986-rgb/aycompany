import { Outlet, NavLink, useNavigate, useLocation } from 'react-router-dom';
import { useState } from 'react';
import { useAuthStore } from '../store/authStore';
import CapturaRapida from './CapturaRapida';
import {
  Sun, Wallet, Key, ScrollText, Server, Users, Handshake, FolderOpen,
  Target, Landmark, Package, UserCog, BarChart2, Settings, Headphones,
  Calendar, TrendingUp, Lightbulb, MessageCircle, UserPlus, GitBranch,
  FileText, AlertCircle, CreditCard, Brain, LogOut, Plus, MoreHorizontal
} from 'lucide-react';

// El menú se agrupa por lo que Cristian hace, no por módulos del sistema.
// Antes eran 24 enlaces planos y no se encontraba nada.
const GRUPOS = [
  { titulo: 'Hoy', items: [
    { to: '/hoy', icon: Sun, label: 'Inicio', movil: true },
  ]},
  { titulo: 'Plata', items: [
    { to: '/finanzas',  icon: Wallet,     label: 'Finanzas', movil: true },
    { to: '/licencias', icon: Key,        label: 'Licencias' },
    { to: '/contratos', icon: ScrollText, label: 'Contratos' },
    { to: '/costos',    icon: Server,     label: 'Costos' },
    { to: '/pagos',     icon: CreditCard, label: 'Pagos' },
    { to: '/facturas',  icon: FileText,   label: 'Facturas' },
    { to: '/cartera',   icon: AlertCircle,label: 'Cartera' },
  ]},
  { titulo: 'Clientes', items: [
    { to: '/clientes',  icon: Users,      label: 'Clientes', movil: true },
    { to: '/leads',     icon: Handshake,  label: 'Tratos' },
    { to: '/pipeline',  icon: GitBranch,  label: 'Pipeline' },
    { to: '/proyectos', icon: FolderOpen, label: 'Proyectos' },
    { to: '/tickets',   icon: Headphones, label: 'Tickets' },
  ]},
  { titulo: 'Personal', items: [
    { to: '/finanzas?tab=metas',  icon: Target,   label: 'Metas', movil: true },
    { to: '/finanzas?tab=deudas', icon: Landmark, label: 'Deudas' },
  ]},
  { titulo: 'Negocio', items: [
    { to: '/marketing',     icon: TrendingUp,     label: 'Marketing' },
    { to: '/calendario',    icon: Calendar,       label: 'Calendario' },
    { to: '/contenido',     icon: Lightbulb,      label: 'Contenido' },
    { to: '/social',        icon: MessageCircle,  label: 'Bandeja social' },
    { to: '/productos',     icon: Package,        label: 'Productos' },
    { to: '/reportes',      icon: BarChart2,      label: 'Reportes' },
    { to: '/analitica',     icon: Brain,          label: 'Analítica' },
    { to: '/usuarios',      icon: UserCog,        label: 'Usuarios' },
    { to: '/configuracion', icon: Settings,       label: 'Configuración' },
  ]},
];

// En el celular solo caben 5. El quinto abre el resto.
const MOVIL = GRUPOS.flatMap(g => g.items).filter(i => i.movil).slice(0, 4);

export default function Layout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { user, logout } = useAuthStore();
  const [captura, setCaptura] = useState(false);
  const [masAbierto, setMasAbierto] = useState(false);

  const salir = () => { logout(); navigate('/'); };
  const activo = to => location.pathname === to.split('?')[0];

  return (
    <div style={S.app}>
      {/* ── Barra lateral (escritorio) ─────────────────────────────── */}
      <aside style={S.aside} className="solo-escritorio">
        <div style={S.marca}>
          <span style={S.punto} />
          <span style={S.marcaTexto}>AI Company CO</span>
        </div>

        <nav style={S.nav}>
          {GRUPOS.map(g => (
            <div key={g.titulo} style={{ marginBottom: 18 }}>
              <div style={S.grupoTitulo}>{g.titulo}</div>
              {g.items.map(({ to, icon: Icon, label }) => (
                <NavLink key={to} to={to} style={{ ...S.enlace, ...(activo(to) ? S.enlaceActivo : {}) }}>
                  <Icon size={17} strokeWidth={activo(to) ? 2.4 : 1.9} />
                  <span>{label}</span>
                </NavLink>
              ))}
            </div>
          ))}
        </nav>

        <div style={S.perfil}>
          <div style={S.avatar}>{(user?.nombre || 'C')[0].toUpperCase()}</div>
          <div style={{ minWidth: 0, flex: 1 }}>
            <div style={S.perfilNombre}>{user?.nombre || 'Cristian Gutiérrez'}</div>
            <div style={S.perfilEmpresa}>AI Company CO</div>
          </div>
          <button onClick={salir} title="Salir" style={S.salir}><LogOut size={16} /></button>
        </div>
      </aside>

      {/* ── Contenido ──────────────────────────────────────────────── */}
      <main style={S.main}>
        <Outlet />
        <div style={{ height: 84 }} className="solo-movil" />
      </main>

      {/* ── Navegación inferior (celular) ──────────────────────────── */}
      <nav style={S.barraMovil} className="solo-movil">
        {MOVIL.slice(0, 2).map(({ to, icon: Icon, label }) => (
          <NavLink key={to} to={to} style={{ ...S.itemMovil, ...(activo(to) ? S.itemMovilActivo : {}) }}>
            <Icon size={21} strokeWidth={activo(to) ? 2.4 : 1.9} /><span>{label}</span>
          </NavLink>
        ))}

        {/* El botón de captura vive en el centro: es lo que más se usa */}
        <button onClick={() => setCaptura(true)} style={S.botonCaptura} aria-label="Registrar movimiento">
          <Plus size={26} strokeWidth={2.6} />
        </button>

        {MOVIL.slice(2, 4).map(({ to, icon: Icon, label }) => (
          <NavLink key={to} to={to} style={{ ...S.itemMovil, ...(activo(to) ? S.itemMovilActivo : {}) }}>
            <Icon size={21} strokeWidth={activo(to) ? 2.4 : 1.9} /><span>{label}</span>
          </NavLink>
        ))}
        <button onClick={() => setMasAbierto(true)} style={{ ...S.itemMovil, border: 'none', background: 'none' }}>
          <MoreHorizontal size={21} /><span>Más</span>
        </button>
      </nav>

      {/* ── Hoja "Más" (celular) ───────────────────────────────────── */}
      {masAbierto && (
        <div style={S.hojaFondo} onClick={() => setMasAbierto(false)}>
          <div style={S.hoja} onClick={e => e.stopPropagation()}>
            <div style={S.hojaAsa} />
            {GRUPOS.map(g => (
              <div key={g.titulo} style={{ marginBottom: 14 }}>
                <div style={S.grupoTitulo}>{g.titulo}</div>
                <div style={S.hojaRejilla}>
                  {g.items.map(({ to, icon: Icon, label }) => (
                    <NavLink key={to} to={to} onClick={() => setMasAbierto(false)} style={S.hojaItem}>
                      <Icon size={19} /><span>{label}</span>
                    </NavLink>
                  ))}
                </div>
              </div>
            ))}
            <button onClick={salir} style={S.hojaSalir}><LogOut size={17} /> Cerrar sesión</button>
          </div>
        </div>
      )}

      {captura && <CapturaRapida onCerrar={() => setCaptura(false)} />}

      <style>{`
        @media (max-width: 900px) { .solo-escritorio { display: none !important; } }
        @media (min-width: 901px)  { .solo-movil      { display: none !important; } }
        a { text-decoration: none; }
      `}</style>
    </div>
  );
}

const S = {
  app: { display: 'flex', minHeight: '100vh', background: '#F8FAFC' },
  aside: { width: 240, flexShrink: 0, background: '#fff', borderRight: '1px solid #E2E8F0',
           display: 'flex', flexDirection: 'column', position: 'sticky', top: 0, height: '100vh' },
  marca: { display: 'flex', alignItems: 'center', gap: 9, padding: '22px 20px 16px' },
  punto: { width: 9, height: 9, borderRadius: '50%', background: '#2563EB', flexShrink: 0 },
  marcaTexto: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontWeight: 700, fontSize: '.98rem', color: '#0F172A' },
  nav: { flex: 1, overflowY: 'auto', padding: '4px 12px 12px' },
  grupoTitulo: { fontSize: '.67rem', fontWeight: 700, color: '#94A3B8', textTransform: 'uppercase',
                 letterSpacing: '.07em', padding: '0 8px 7px' },
  enlace: { display: 'flex', alignItems: 'center', gap: 10, padding: '8px 10px', borderRadius: 8,
            color: '#475569', fontSize: '.875rem', fontWeight: 500, marginBottom: 1 },
  enlaceActivo: { background: '#EFF6FF', color: '#2563EB', fontWeight: 650 },
  perfil: { display: 'flex', alignItems: 'center', gap: 10, padding: '14px 16px', borderTop: '1px solid #F1F5F9' },
  avatar: { width: 34, height: 34, borderRadius: '50%', background: '#7C3AED', color: '#fff',
            display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 700, fontSize: '.9rem', flexShrink: 0 },
  perfilNombre: { fontSize: '.83rem', fontWeight: 650, color: '#0F172A', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' },
  perfilEmpresa: { fontSize: '.7rem', color: '#94A3B8' },
  salir: { background: 'none', border: 'none', color: '#94A3B8', cursor: 'pointer', padding: 4 },
  main: { flex: 1, minWidth: 0 },

  barraMovil: { position: 'fixed', left: 0, right: 0, bottom: 0, height: 64, background: '#fff',
                borderTop: '1px solid #E2E8F0', display: 'flex', alignItems: 'center',
                justifyContent: 'space-around', zIndex: 40, paddingBottom: 'env(safe-area-inset-bottom)' },
  itemMovil: { display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 3, flex: 1,
               color: '#94A3B8', fontSize: '.64rem', fontWeight: 600, padding: '6px 0', cursor: 'pointer' },
  itemMovilActivo: { color: '#2563EB' },
  botonCaptura: { width: 54, height: 54, borderRadius: '50%', background: '#2563EB', color: '#fff',
                  border: 'none', display: 'flex', alignItems: 'center', justifyContent: 'center',
                  boxShadow: '0 6px 18px rgba(37,99,235,.42)', marginTop: -22, flexShrink: 0, cursor: 'pointer' },

  hojaFondo: { position: 'fixed', inset: 0, background: 'rgba(15,23,42,.5)', zIndex: 60, display: 'flex', alignItems: 'flex-end' },
  hoja: { background: '#fff', width: '100%', borderRadius: '18px 18px 0 0', padding: '10px 18px 26px',
          maxHeight: '82vh', overflowY: 'auto' },
  hojaAsa: { width: 38, height: 4, borderRadius: 2, background: '#CBD5E1', margin: '0 auto 16px' },
  hojaRejilla: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 6 },
  hojaItem: { display: 'flex', alignItems: 'center', gap: 9, padding: '11px 12px', borderRadius: 10,
              background: '#F8FAFC', color: '#334155', fontSize: '.85rem', fontWeight: 550 },
  hojaSalir: { display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 8, width: '100%',
               padding: '13px', borderRadius: 10, background: '#FEF2F2', color: '#DC2626',
               border: 'none', fontSize: '.88rem', fontWeight: 650, marginTop: 8, cursor: 'pointer' },
};
