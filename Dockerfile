FROM php:8.3-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV DEBIAN_FRONTEND=noninteractive

# ── Node.js 20 via nodesource ──────────────────────────────────────────────────
RUN apt-get update && apt-get install -y ca-certificates curl gnupg && \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# ── PHP extensions para Laravel (compiladas desde fuente) ─────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev libcurl4-openssl-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring zip bcmath intl gd curl && \
    rm -rf /var/lib/apt/lists/*

# ── Composer desde imagen oficial ─────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .

# ── Node.js backend dependencies ──────────────────────────────────────────────
RUN cd server && npm ci --omit=dev
RUN cd server/demos/viaje360/backend && npm ci --omit=dev
RUN cd server/demos/condominio/backend && npm ci --omit=dev
RUN cd server/demos/odontologia/server && npm ci --omit=dev
RUN cd server/demos/ventas/backend && npm ci --omit=dev
RUN cd server/demos/ferreteria/backend && npm ci --omit=dev
RUN cd server/demos/polleria/backend && npm ci --omit=dev
RUN cd server/demos/salon/backend && npm ci --omit=dev
RUN cd server/demos/parqueo/backend && npm ci --omit=dev
RUN cd server/demos/prestamos/backend && npm ci --omit=dev

# ── PHP/Composer dependencies (original demos) ────────────────────────────────
RUN cd server/demos/php/delivery  && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/celulares && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/colegio   && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/farmacia  && composer install --no-dev --optimize-autoloader --no-interaction

# ── PHP/Composer dependencies (new demos) ─────────────────────────────────────
RUN cd server/demos/php/panaderia   && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/restaurante && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/citas       && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/hospedaje   && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/inventario  && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/laboratorio && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd server/demos/php/cotizacion  && composer install --no-dev --optimize-autoloader --no-interaction

# ── Vite builds for PHP demos that need CSS compilation ───────────────────────
RUN cd server/demos/php/restaurante && npm ci && npm run build
RUN cd server/demos/php/hospedaje   && npm ci && npm run build
RUN cd server/demos/php/laboratorio && npm ci && npm run build

# ── Build React frontends ──────────────────────────────────────────────────────
RUN cd client && npm ci && npm run build
RUN cd server/demos/viaje360/frontend  && npm ci && VITE_API_URL=/demos/viaje360/api  npm run build
RUN cd server/demos/condominio/frontend && npm ci && VITE_API_URL=/demos/condominio/api npm run build
RUN cd server/demos/odontologia/client  && npm ci && VITE_API_URL=/demos/odontologia/api npm run build
RUN cd server/demos/ventas/frontend    && npm ci && VITE_API_URL=/demos/ventas/api    npm run build
RUN cd server/demos/ferreteria/frontend && npm ci && VITE_API_URL=/demos/ferreteria/api npm run build
RUN cd server/demos/polleria/frontend   && npm ci && VITE_API_URL=/demos/polleria/api  npm run build
RUN cd server/demos/salon/frontend      && npm ci && VITE_API_URL=/demos/salon/api     npm run build
RUN cd server/demos/parqueo/frontend    && npm ci && VITE_API_URL=/demos/parqueo/api   npm run build

EXPOSE 5000

CMD ["node", "server/src/app.js"]
