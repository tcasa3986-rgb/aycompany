import { createContext, useState, useEffect } from 'react';
import api from '../services/api';

export const ConfigContext = createContext();

export const ConfigProvider = ({ children }) => {
    const [config, setConfig] = useState(null);
    const [loadingConfig, setLoadingConfig] = useState(true);

    const fetchConfig = async () => {
        try {
            // Using a public endpoint or the protected one depending on auth state
            // Let's use the public one we created so it loads instantly even before login if needed
            const response = await api.get('/configuracion/public');
            const data = response.data;

            // Format logo url if exists
            if (data.logo_url) {
                const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:5000/api';
                const serverUrl = baseUrl.replace('/api', '');
                data.logo_url = `${serverUrl}${data.logo_url}`;
            }

            setConfig(data);
        } catch (error) {
            console.error("Error fetching global config:", error);
            // Fallback default config
            setConfig({ nombre_empresa: 'Belleza Admin', simbolo_moneda: '$', logo_url: null });
        } finally {
            setLoadingConfig(false);
        }
    };

    useEffect(() => {
        fetchConfig();
    }, []);

    const refreshConfig = () => {
        fetchConfig();
    };

    return (
        <ConfigContext.Provider value={{ config, loadingConfig, refreshConfig }}>
            {children}
        </ConfigContext.Provider>
    );
};
