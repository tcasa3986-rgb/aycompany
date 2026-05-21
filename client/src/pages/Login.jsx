import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';
import api from '../api/axios';
import toast from 'react-hot-toast';

export default function Login() {
  const [form, setForm] = useState({ email: '', password: '' });
  const [loading, setLoading] = useState(false);
  const { login } = useAuthStore();
  const navigate = useNavigate();

  async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);
    try {
      const { data } = await api.post('/auth/login', form);
      login(data.token, data.user);
      navigate(data.user?.rol === 'vendedor' ? '/vendedor' : '/dashboard');
    } catch {
      toast.error('Credenciales incorrectas');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)' }}>
      <div style={{ background: '#fff', borderRadius: 16, padding: '40px 36px', width: 380, boxShadow: '0 20px 60px rgba(0,0,0,.3)' }}>
        <div style={{ textAlign: 'center', marginBottom: 32 }}>
          <img src="/logo-ai-company.png" alt="AI Company" style={{ width: 80, height: 80, objectFit: 'contain', marginBottom: 8 }} onError={e => { e.target.style.display = 'none'; }} />
          <h1 style={{ fontSize: '1.5rem', fontWeight: 800, color: '#1e1b4b', marginTop: 4, letterSpacing: '-0.5px' }}>AI Company</h1>
          <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 4 }}>Panel de administración</p>
        </div>
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: 14 }}>
            <label style={{ display: 'block', fontSize: '.85rem', fontWeight: 600, color: '#374151', marginBottom: 6 }}>Correo electrónico</label>
            <input type="email" placeholder="admin@tuplataforma.com" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} required />
          </div>
          <div style={{ marginBottom: 24 }}>
            <label style={{ display: 'block', fontSize: '.85rem', fontWeight: 600, color: '#374151', marginBottom: 6 }}>Contraseña</label>
            <input type="password" placeholder="••••••••" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })} required />
          </div>
          <button type="submit" disabled={loading} style={{ width: '100%', padding: '11px', background: '#4f46e5', color: '#fff', border: 'none', borderRadius: 8, fontSize: '1rem', fontWeight: 600, opacity: loading ? .7 : 1 }}>
            {loading ? 'Entrando...' : 'Entrar'}
          </button>
        </form>
      </div>
    </div>
  );
}
