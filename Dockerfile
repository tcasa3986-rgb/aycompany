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

# ── PHP extensions para Laravel + SQLite ──────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev libcurl4-openssl-dev libsqlite3-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip bcmath intl gd curl && \
    rm -rf /var/lib/apt/lists/*

# ── Composer desde imagen oficial ─────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .

# ── Servidor principal ─────────────────────────────────────────────────────────
RUN cd server && npm install --omit=dev --no-audit
RUN cd server && npx playwright install chromium --with-deps || echo "⚠️ playwright chromium skip"

# ── PHP/Composer: instalar dependencias de todos los demos Laravel ─────────────
RUN cd server/demos/php/delivery    && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ delivery composer skip"
RUN cd server/demos/php/celulares   && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ celulares composer skip"
RUN cd server/demos/php/colegio     && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ colegio composer skip"
RUN cd server/demos/php/farmacia    && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ farmacia composer skip"
RUN cd server/demos/php/panaderia   && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ panaderia composer skip"
RUN cd server/demos/php/restaurante && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ restaurante composer skip"
RUN cd server/demos/php/citas       && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ citas composer skip"
RUN cd server/demos/php/hospedaje   && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ hospedaje composer skip"
RUN cd server/demos/php/inventario  && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ inventario composer skip"
RUN cd server/demos/php/laboratorio && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ laboratorio composer skip"
RUN cd server/demos/php/cotizacion  && composer install --no-dev --optimize-autoloader --no-interaction || echo "⚠️ cotizacion composer skip"

# ── Vite builds de demos PHP/Laravel que usan @vite() ─────────────────────────
RUN cd server/demos/php/delivery    && npm install && npm run build || echo "⚠️ delivery vite skip"
RUN cd server/demos/php/panaderia   && npm ci && npm run build || echo "⚠️ panaderia vite skip"
RUN cd server/demos/php/restaurante && npm ci && npm run build || echo "⚠️ restaurante vite skip"
RUN cd server/demos/php/citas       && npm ci && npm run build || echo "⚠️ citas vite skip"
RUN cd server/demos/php/hospedaje   && npm install && npm run build || echo "⚠️ hospedaje vite skip"
RUN cd server/demos/php/inventario  && npm ci && npm run build || echo "⚠️ inventario vite skip"
RUN cd server/demos/php/laboratorio && npm install && npm run build || echo "⚠️ laboratorio vite skip"
RUN cd server/demos/php/cotizacion  && npm ci && npm run build || echo "⚠️ cotizacion vite skip"

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
