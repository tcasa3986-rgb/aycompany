FROM node:20-slim

ENV DEBIAN_FRONTEND=noninteractive

# ── Dependencias del sistema para Playwright ───────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl gnupg \
    libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 \
    libxkbcommon0 libxcomposite1 libxdamage1 libxfixes3 libxrandr2 \
    libgbm1 libasound2 libpangocairo-1.0-0 libcairo2 libatspi2.0-0 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

# ── Servidor principal ─────────────────────────────────────────────────────────
RUN cd server && npm install --omit=dev --no-audit --prefer-offline || npm install --omit=dev --no-audit
RUN cd server && npx playwright install chromium --with-deps || echo "⚠️ playwright chromium skip"

# ── Build del cliente principal ────────────────────────────────────────────────
RUN cd client && npm ci && npm run build

# ── Frontends React de demos Node.js (cada uno independiente) ─────────────────
RUN cd server/demos/viaje360/frontend   && npm ci && VITE_API_URL=/demos/viaje360/api   npm run build || echo "⚠️ viaje360 frontend skip"
RUN cd server/demos/condominio/frontend && npm ci && VITE_API_URL=/demos/condominio/api npm run build || echo "⚠️ condominio frontend skip"
RUN cd server/demos/odontologia/client  && npm ci && VITE_API_URL=/demos/odontologia/api npm run build || echo "⚠️ odontologia frontend skip"
RUN cd server/demos/ventas/frontend     && npm ci && VITE_API_URL=/demos/ventas/api     npm run build || echo "⚠️ ventas frontend skip"
RUN cd server/demos/ferreteria/frontend && npm ci && VITE_API_URL=/demos/ferreteria/api npm run build || echo "⚠️ ferreteria frontend skip"
RUN cd server/demos/polleria/frontend   && npm ci && VITE_API_URL=/demos/polleria/api   npm run build || echo "⚠️ polleria frontend skip"
RUN cd server/demos/salon/frontend      && npm ci && VITE_API_URL=/demos/salon/api      npm run build || echo "⚠️ salon frontend skip"
RUN cd server/demos/parqueo/frontend    && npm ci && VITE_API_URL=/demos/parqueo/api    npm run build || echo "⚠️ parqueo frontend skip"

EXPOSE 5000

CMD ["node", "server/src/app.js"]
