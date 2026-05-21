const bcrypt = require('bcryptjs');
const pool = require('./src/config/db');

async function fixPasswords() {
  const hash = await bcrypt.hash('Admin123!', 10);
  console.log('Hash correcto:', hash);
  
  await pool.query('UPDATE usuarios SET password_hash = ?', [hash]);
  console.log('Contraseñas actualizadas para todos los usuarios');
  
  // Verificar
  const [rows] = await pool.query('SELECT email, password_hash FROM usuarios WHERE email = ?', ['admin@laspalmas.com']);
  const ok = await bcrypt.compare('Admin123!', rows[0].password_hash);
  console.log('Verificación login admin:', ok ? '✅ CORRECTO' : '❌ FALLO');
  
  process.exit(0);
}

fixPasswords().catch(err => { console.error(err); process.exit(1); });
