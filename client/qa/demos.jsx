import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import Cazador from '../src/pages/Cazador';
import api from '../src/api/axios';
if (!['localhost','127.0.0.1'].includes(location.hostname)) throw new Error('Sólo para revisión local');
api.defaults.baseURL='http://127.0.0.1:5197/api';
createRoot(document.getElementById('root')).render(<><Cazador/><Toaster/></>);
