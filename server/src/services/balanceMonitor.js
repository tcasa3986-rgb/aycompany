/**
 * Monitor de saldo para Anthropic y Railway.
 * - Anthropic: rastrea tokens consumidos y calcula gasto acumulado
 * - Railway: consulta uso de recursos y estima costo mensual
 * Alerta por Telegram cuando el saldo cae por debajo de $1 USD
 */

const cron          = require('node-cron');
const https         = require('https');
const telegramService = require('./telegramService');

// Precios Anthropic claude-sonnet-4-6 (USD por millón de tokens)
const ANTHROPIC_PRICE = { input: 3.0, output: 15.0 };

// Precios Railway por unidad
const RAILWAY_PRICE = {
  CPU_USAGE:       0.000463,  // USD por vCPU-hora
  MEMORY_USAGE_GB: 0.000231,  // USD por GB-hora
  NETWORK_TX_GB:   0.10,      // USD por GB egress
  DISK_USAGE_GB:   0.000109,  // USD por GB-hora
};

// ─── helpers ──────────────────────────────────────────────────────────────────

function railwayFetch(query, variables = {}) {
  const body = JSON.stringify({ query, variables });
  return new Promise((resolve, reject) => {
    const req = https.request({
      hostname: 'backboard.railway.com',
      path: '/graphql/v2',
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${process.env.RAILWAY_API_TOKEN}`,
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(body),
      },
    }, res => {
      let d = '';
      res.on('data', c => d += c);
      res.on('end', () => {
        try { resolve(JSON.parse(d)); }
        catch { resolve(null); }
      });
    });
    req.on('error', reject);
    req.write(body);
    req.end();
  });
}

// ─── Anthropic: calcular gasto desde tokens registrados ────────────────────

function calcAnthropicCost(inputTokens, outputTokens) {
  return (inputTokens / 1_000_000) * ANTHROPIC_PRICE.input
       + (outputTokens / 1_000_000) * ANTHROPIC_PRICE.output;
}

async function checkAnthropicBalance() {
  const budget  = parseFloat(process.env.ANTHROPIC_BUDGET  || '10');   // Recarga que pusiste
  const spent   = parseFloat(process.env.ANTHROPIC_SPENT   || '0');    // Gasto acumulado
  const remaining = budget - spent;

  console.log(`[Balance] Anthropic → presupuesto $${budget} | gastado $${spent.toFixed(3)} | restante $${remaining.toFixed(3)}`);

  if (remaining <= 1.0) {
    const msg =
`💳 *Alerta de saldo — Anthropic*

📉 Saldo restante: *$${remaining.toFixed(2)} USD*

Presupuesto cargado: $${budget} USD
Gastado este ciclo: $${spent.toFixed(2)} USD

Recarga créditos para que el blog siga publicando automáticamente.`;

    await telegramService.enviarConBotones(msg, [[
      { text: '💳 Recargar Anthropic', url: 'https://console.anthropic.com/settings/billing' },
    ]]);
    console.log('[Balance] Alerta Anthropic enviada');
  }

  return { budget, spent, remaining };
}

// ─── Railway: calcular costo estimado del mes ──────────────────────────────

async function checkRailwayBalance() {
  if (!process.env.RAILWAY_API_TOKEN || !process.env.RAILWAY_PROJECT_ID) return null;

  const query = `query($projectId: String!) {
    estimatedUsage(measurements: [CPU_USAGE, MEMORY_USAGE_GB, NETWORK_TX_GB, DISK_USAGE_GB], projectId: $projectId) {
      measurement
      estimatedValue
    }
  }`;

  try {
    const res = await railwayFetch(query, { projectId: process.env.RAILWAY_PROJECT_ID });
    if (!res?.data?.estimatedUsage) return null;

    // Calcular costo estimado del mes en curso
    let estimatedCost = 0;
    const breakdown = {};
    for (const item of res.data.estimatedUsage) {
      const price = RAILWAY_PRICE[item.measurement] || 0;
      // Railway reporta en unidades acumuladas del mes → convertir a horas donde aplica
      const value = item.measurement.includes('GB') && item.measurement !== 'NETWORK_TX_GB'
        ? item.estimatedValue / 60  // GB-minutos → GB-horas
        : item.estimatedValue;
      const cost = value * price;
      estimatedCost += cost;
      breakdown[item.measurement] = cost.toFixed(4);
    }

    const plan      = parseFloat(process.env.RAILWAY_PLAN_LIMIT || '5');  // $5 Hobby, $20 Pro
    const remaining = plan - estimatedCost;

    console.log(`[Balance] Railway → plan $${plan} | costo estimado $${estimatedCost.toFixed(3)} | restante ~$${remaining.toFixed(3)}`);

    if (remaining <= 1.0) {
      const msg =
`🚆 *Alerta de saldo — Railway*

📉 Saldo restante estimado: *$${remaining.toFixed(2)} USD*

Plan actual: $${plan}/mes
Consumo estimado este mes: $${estimatedCost.toFixed(2)} USD

Revisa tu facturación o sube el plan para evitar suspensiones.`;

      await telegramService.enviarConBotones(msg, [[
        { text: '💳 Ver facturación Railway', url: 'https://railway.app/account/billing' },
      ]]);
      console.log('[Balance] Alerta Railway enviada');
    }

    return { estimatedCost, plan, remaining };
  } catch (e) {
    console.error('[Balance] Error Railway:', e.message);
    return null;
  }
}

// ─── Registro de uso Anthropic desde GitHub Actions ───────────────────────
// Llamado por el endpoint /api/seo/registrar-uso cuando se publica un artículo

async function registrarUsoAnthropic(inputTokens, outputTokens) {
  const cost   = calcAnthropicCost(inputTokens, outputTokens);
  const spent  = parseFloat(process.env.ANTHROPIC_SPENT || '0');
  const nuevo  = spent + cost;

  // Actualizar variable en Railway via GraphQL
  await actualizarVariableRailway('ANTHROPIC_SPENT', nuevo.toFixed(4));
  console.log(`[Balance] Uso registrado: +$${cost.toFixed(4)} | total $${nuevo.toFixed(4)}`);

  // Verificar si ya se debe alertar
  const budget    = parseFloat(process.env.ANTHROPIC_BUDGET || '10');
  const remaining = budget - nuevo;
  if (remaining <= 1.0) {
    await checkAnthropicBalance();
  }

  return { cost, total: nuevo };
}

async function actualizarVariableRailway(name, value) {
  if (!process.env.RAILWAY_API_TOKEN) return;
  const mutation = `mutation($input: VariableCollectionUpsertInput!) { variableCollectionUpsert(input: $input) }`;
  const variables = {
    input: {
      projectId:     process.env.RAILWAY_PROJECT_ID     || 'e4571697-e9a6-4e58-9cb6-cc2c34c8e622',
      environmentId: process.env.RAILWAY_ENVIRONMENT_ID || '9d539fd9-436c-40aa-93d2-4ca33a743c06',
      serviceId:     process.env.RAILWAY_SERVICE_ID     || '838f98bc-515d-43ef-b6b5-f08fa0608af4',
      replace: false,
      variables: { [name]: String(value) },
    },
  };
  await railwayFetch(mutation, variables);
}

// ─── Cron diario ──────────────────────────────────────────────────────────────

function iniciarBalanceMonitor() {
  // Revisar saldos todos los días a las 8am Colombia
  cron.schedule('0 13 * * *', async () => {
    console.log('[Balance] Revisando saldos...');
    await checkAnthropicBalance();
    await checkRailwayBalance();
  }, { timezone: 'America/Bogota' });

  console.log('💰 Balance Monitor activo (revisión diaria 8am Colombia)');
}

module.exports = { iniciarBalanceMonitor, registrarUsoAnthropic, checkAnthropicBalance, checkRailwayBalance };
