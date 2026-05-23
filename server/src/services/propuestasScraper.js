const { chromium } = require('playwright');

async function buscarEnMaps(browser, nombre, ciudad) {
    const query = `${nombre} ${ciudad}`;
    const page  = await browser.newPage();
    try {
        await page.goto(
            `https://www.google.com/maps/search/${encodeURIComponent(query)}`,
            { waitUntil: 'domcontentloaded', timeout: 20000 }
        );
        await page.waitForTimeout(3000);

        const placeUrl = await page.evaluate(() => {
            const link = document.querySelector('a[href*="/maps/place/"]');
            return link?.href || null;
        });
        if (!placeUrl) return null;

        await page.goto(placeUrl, { waitUntil: 'domcontentloaded', timeout: 20000 });
        await page.waitForTimeout(4000);

        return await page.evaluate(() => {
            const webBtn = [...document.querySelectorAll('a[href]')].find(a => {
                const label = (a.getAttribute('aria-label') || '').toLowerCase();
                const item  = (a.getAttribute('data-item-id') || '').toLowerCase();
                const href  = a.href || '';
                return (label.includes('sitio') || label.includes('web') || item === 'authority') &&
                       href.startsWith('http') && !href.includes('google');
            });
            const tel    = document.querySelector('a[href^="tel:"]')?.href?.replace('tel:', '') || null;
            const rating = document.querySelector('.F7nice span[aria-hidden="true"]')?.textContent?.trim() || null;
            const addr   = document.querySelector('button[data-item-id="address"]')?.textContent?.trim() || null;
            return {
                nombreMaps: document.querySelector('h1')?.textContent?.trim() || '',
                sitioWeb:   webBtn?.href || null,
                telefono:   tel,
                rating:     rating ? parseFloat(rating) : null,
                direccion:  addr
            };
        });
    } catch (err) {
        console.error('[Propuestas Maps]', err.message);
        return null;
    } finally {
        await page.close();
    }
}

async function rasparSitio(browser, url) {
    const page = await browser.newPage();
    try {
        await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 25000 });
        await page.waitForTimeout(2000);

        const datos = await page.evaluate(() => {
            const texto = document.body.innerText || '';
            const html  = document.body.innerHTML || '';

            const extraerTels = () => {
                const links = [...document.querySelectorAll('a[href^="tel:"]')]
                    .map(a => a.href.replace('tel:', '').trim());
                const rx = /(\+?57\s?)?(\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4})/g;
                const del = [...texto.matchAll(rx)].map(m => m[0].trim());
                return [...new Set([...links, ...del])].slice(0, 3);
            };
            const extraerEmails = () => {
                const links = [...document.querySelectorAll('a[href^="mailto:"]')]
                    .map(a => a.href.replace('mailto:', '').split('?')[0]);
                const rx = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
                const del = [...texto.matchAll(rx)].map(m => m[0]);
                return [...new Set([...links, ...del])].slice(0, 3);
            };

            return {
                titulo:       document.title || '',
                descripcion:  document.querySelector('meta[name="description"]')?.content || '',
                telefonos:    extraerTels(),
                emails:       extraerEmails(),
                tieneWA:      /whatsapp/i.test(texto) || html.includes('wa.me'),
                tieneFB:      html.includes('facebook.com/'),
                tieneIG:      html.includes('instagram.com/'),
                tieneYT:      html.includes('youtube.com/'),
                tienePedido:  /pedir|pedido|reserv|reservar|orden/i.test(texto),
                tieneEcomm:   /carrito|comprar|tienda|shop/i.test(texto) || !!document.querySelector('[class*="cart"],[class*="shop"]'),
                tieneChatbot: /chatbot|asistente virtual|chat en vivo/i.test(texto),
                textoResumen: texto.slice(0, 1500).replace(/\s+/g, ' ')
            };
        });

        const screenshot = await page.screenshot({
            type: 'jpeg', quality: 65,
            clip: { x: 0, y: 0, width: 1280, height: 640 }
        });
        datos.screenshot = screenshot.toString('base64');
        datos.url = url;
        return datos;
    } catch (err) {
        console.error('[Propuestas Sitio]', err.message);
        return null;
    } finally {
        await page.close();
    }
}

async function investigarNegocio({ nombre, ciudad, tipo, urlDirecta }) {
    console.log(`🔍 [Propuestas] Investigando: ${nombre} — ${ciudad}...`);

    let browser;
    try {
        browser = await chromium.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
        });

        const maps = await buscarEnMaps(browser, nombre, ciudad);
        if (maps) console.log(`   Maps: ${maps.nombreMaps || 'encontrado'} | Tel: ${maps.telefono || 'no'} | Web: ${maps.sitioWeb || 'sin sitio'}`);

        const urlFinal = urlDirecta || maps?.sitioWeb || null;

        let datosWeb = null;
        if (urlFinal) {
            console.log(`🌐 Visitando: ${urlFinal}`);
            datosWeb = await rasparSitio(browser, urlFinal);
        }

        return {
            nombre:    maps?.nombreMaps || nombre,
            ciudad,
            tipo,
            sitioUrl:  urlFinal,
            telefono:  maps?.telefono || datosWeb?.telefonos?.[0] || null,
            rating:    maps?.rating   || null,
            direccion: maps?.direccion || null,
            tieneMaps: !!maps,
            web:       datosWeb
        };
    } finally {
        if (browser) await browser.close();
    }
}

module.exports = { investigarNegocio };
