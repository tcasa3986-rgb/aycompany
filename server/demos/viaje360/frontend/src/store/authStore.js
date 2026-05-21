import { create } from 'zustand';
import api from '../services/api';

const useAuthStore = create((set) => ({
  usuario: JSON.parse(localStorage.getItem('viaje360_usuario') || 'null'),
  token: localStorage.getItem('viaje360_token') || null,
  cargando: false,
  error: null,

  login: async (email, password) => {
    set({ cargando: true, error: null });
    try {
      const res = await api.post('/auth/login', { email, password });
      localStorage.setItem('viaje360_token', res.token);
      localStorage.setItem('viaje360_usuario', JSON.stringify(res.usuario));
      set({ usuario: res.usuario, token: res.token, cargando: false });
      return true;
    } catch (err) {
      set({ error: err.msg || 'Error al iniciar sesión', cargando: false });
      return false;
    }
  },

  logout: async () => {
    try { await api.post('/auth/logout'); } catch (_) {}
    localStorage.removeItem('viaje360_token');
    localStorage.removeItem('viaje360_usuario');
    set({ usuario: null, token: null });
  },

  clearError: () => set({ error: null }),
}));

export default useAuthStore;
