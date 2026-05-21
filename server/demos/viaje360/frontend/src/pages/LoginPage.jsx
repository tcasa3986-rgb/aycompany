import React, { useState } from 'react';
import { Navigate } from 'react-router-dom';
import { Plane, Eye, EyeOff, ArrowRight } from 'lucide-react';
import useAuthStore from '../store/authStore';

export default function LoginPage() {
  const { login, cargando, error, usuario } = useAuthStore();
  const [email, setEmail]       = useState('admin@viaje360.com');
  const [password, setPassword] = useState('Admin@360');
  const [showPass, setShowPass] = useState(false);

  if (usuario) return <Navigate to="/" replace />;

  const handleSubmit = async (e) => {
    e.preventDefault();
    await login(email, password);
  };

  return (
    <div className="login-page">
      {/* Orbs de fondo */}
      <div className="login-bg-orb login-bg-orb-1" />
      <div className="login-bg-orb login-bg-orb-2" />

      <div className="login-card animate-fade-in-up">
        {/* Logo */}
        <div className="login-logo">
          <Plane size={24} color="white" />
        </div>

        <h1 className="login-title">Viaje 360 CRM</h1>
        <p className="login-subtitle">Plataforma de gestión para agencias de viajes</p>

        {error && (
          <div className="alert alert-danger mb-4">
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label className="form-label required">Correo Electrónico</label>
            <input
              type="email"
              className="form-control"
              value={email}
              onChange={e => setEmail(e.target.value)}
              placeholder="admin@viaje360.com"
              required
              autoComplete="email"
            />
          </div>

          <div className="form-group">
            <label className="form-label required">Contraseña</label>
            <div style={{ position: 'relative' }}>
              <input
                type={showPass ? 'text' : 'password'}
                className="form-control"
                value={password}
                onChange={e => setPassword(e.target.value)}
                placeholder="••••••••"
                required
                autoComplete="current-password"
                style={{ paddingRight: '42px' }}
              />
              <button
                type="button"
                onClick={() => setShowPass(s => !s)}
                style={{
                  position: 'absolute', right: '12px', top: '50%',
                  transform: 'translateY(-50%)',
                  background: 'none', border: 'none',
                  color: 'var(--text-muted)', cursor: 'pointer'
                }}
              >
                {showPass ? <EyeOff size={16} /> : <Eye size={16} />}
              </button>
            </div>
          </div>

          <button
            type="submit"
            className="btn btn-primary w-full"
            disabled={cargando}
            style={{ justifyContent: 'center', height: '44px', marginTop: '8px' }}
          >
            {cargando ? (
              <span className="spinner" style={{ width: 20, height: 20, borderWidth: 2 }} />
            ) : (
              <>Iniciar Sesión <ArrowRight size={16} /></>
            )}
          </button>
        </form>

        <div className="divider" />
        <p className="text-center text-xs text-muted">
          © 2024 Viaje 360 CRM · Todos los derechos reservados
        </p>
      </div>
    </div>
  );
}
