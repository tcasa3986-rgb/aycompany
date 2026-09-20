# mi-plataforma — AI Company CO
#
# Antes esta imagen partía de php:8.3-cli con Composer y las extensiones de
# Laravel, y compilaba 21 sistemas de demostración (11 composer install +
# 16 builds de Vite) más Chromium de Playwright. Todo eso existía para los
# demos y el Prospector, que se retiraron: la imagen ahora es Node a secas.
FROM node:20-slim

ENV NODE_ENV=production
WORKDIR /app

# Dependencias del servidor
COPY server/package*.json ./server/
RUN cd server && npm install --omit=dev --no-audit --no-fund

# Build del cliente (devDependencies necesarias para Vite)
COPY client/package*.json ./client/
RUN cd client && npm ci --no-audit --no-fund
COPY client ./client
RUN cd client && npm run build && rm -rf node_modules

COPY server ./server

EXPOSE 5000
CMD ["node", "server/src/app.js"]
