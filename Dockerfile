# mi-plataforma — AI Company CO
#
# Antes esta imagen partía de php:8.3-cli con Composer y las extensiones de
# Laravel, y compilaba 21 sistemas de demostración (11 composer install +
# 16 builds de Vite) más Chromium de Playwright. Todo eso existía para los
# demos y el Prospector, que se retiraron: la imagen ahora es Node a secas.
FROM node:20-slim

WORKDIR /app

# NODE_ENV NO se fija aquí a proposito: con NODE_ENV=production npm omite las
# devDependencies, y Vite es una de ellas — el build del cliente fallaba con
# "vite: not found". Railway ya inyecta NODE_ENV=production en ejecucion.

# Dependencias del servidor (sin las de desarrollo, ahi si corresponde)
COPY server/package*.json ./server/
RUN cd server && npm install --omit=dev --no-audit --no-fund

# Cliente: se instalan CON devDependencies porque Vite compila en este paso
COPY client/package*.json ./client/
RUN cd client && npm ci --include=dev --no-audit --no-fund
COPY client ./client
RUN cd client && npm run build && rm -rf node_modules

COPY server ./server

EXPOSE 5000
CMD ["node", "server/src/app.js"]
