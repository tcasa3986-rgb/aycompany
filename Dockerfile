FROM node:20-bookworm-slim

# Install PHP 8.2 and required extensions (Debian Bookworm has PHP 8.2 natively)
RUN apt-get update && apt-get install -y --no-install-recommends \
    php8.2-cli \
    php8.2-mysql \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-gd \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Get Composer from official image (avoids SSL download issues at build time)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .

# ── Node.js backend dependencies ───────────────────────────────────────────────
RUN cd server && npm ci --omit=dev
RUN cd server/demos/viaje360/backend && npm ci --omit=dev
RUN cd server/demos/condominio/backend && npm ci --omit=dev
RUN cd server/demos/odontologia/server && npm ci --omit=dev
RUN cd server/demos/ventas/backend && npm ci --omit=dev

# ── PHP/Composer dependencies ───────────────────────────────────────────────────
RUN cd server/demos/php/delivery  && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/celulares && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/colegio   && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/farmacia  && composer install --no-dev --optimize-autoloader --no-interaction

# ── Build React frontends ───────────────────────────────────────────────────────
RUN cd client && npm ci && npm run build
RUN cd server/demos/viaje360/frontend  && npm ci && VITE_API_URL=/demos/viaje360/api  npm run build
RUN cd server/demos/condominio/frontend && npm ci && VITE_API_URL=/demos/condominio/api npm run build
RUN cd server/demos/odontologia/client  && npm ci && VITE_API_URL=/demos/odontologia/api npm run build
RUN cd server/demos/ventas/frontend    && npm ci && VITE_API_URL=/demos/ventas/api    npm run build

EXPOSE 5000

CMD ["node", "server/src/app.js"]
