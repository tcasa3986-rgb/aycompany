const sequelize = require('./src/config/database');
const { Paquete, Cliente, Usuario } = require('./src/models');

async function testConnection() {
  try {
    await sequelize.authenticate();
    console.log('✅ Connection has been established successfully.');

    const paquetesCount = await Paquete.count();
    console.log('📦 Total Paquetes:', paquetesCount);

    const clientesCount = await Cliente.count();
    console.log('👥 Total Clientes:', clientesCount);

    const usuariosCount = await Usuario.count();
    console.log('👤 Total Usuarios:', usuariosCount);

    if (paquetesCount > 0) {
      const paquetes = await Paquete.findAll({ limit: 5 });
      console.log('Samples Paquetes:', paquetes.map(p => p.nombre));
    }

  } catch (error) {
    console.error('❌ Unable to connect to the database:', error);
  } finally {
    await sequelize.close();
  }
}

testConnection();
