import { create } from 'zustand';
import api from '../services/api';

const useConfigStore = create((set) => ({
  config: null,
  loading: true,
  fetchConfig: async () => {
    try {
      const res = await api.get('/configuracion_general');
      set({ config: res.data, loading: false });
    } catch {
      set({ loading: false });
    }
  }

}));

export default useConfigStore;
