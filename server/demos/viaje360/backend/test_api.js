const axios = require('axios');

async function testApi() {
  try {
    console.log('--- Intentando Login ---');
    const loginRes = await axios.post('http://localhost:3001/api/auth/login', {
      email: 'admin@viaje360.com',
      password: 'Viaje360@' // Password del seed
    });

    const token = loginRes.data.token;
    console.log('✅ Login exitoso. Token obtenido.');

    console.log('\n--- Fetching Paquetes ---');
    const paquetesRes = await axios.get('http://localhost:3001/api/paquetes', {
      headers: { Authorization: `Bearer ${token}` }
    });

    console.log('✅ Paquetes recibidos:', paquetesRes.data.data.length);
    console.log('Muestras:', paquetesRes.data.data.slice(0, 2).map(p => p.nombre));

    console.log('\n--- Fetching Clientes ---');
    const clientesRes = await axios.get('http://localhost:3001/api/clientes', {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('✅ Clientes recibidos:', clientesRes.data.data.length);

  } catch (error) {
    if (error.response) {
      console.error('❌ Error API:', error.response.status, error.response.data);
    } else {
      console.error('❌ Error Conexión:', error.message);
    }
  }
}

testApi();
