const PDFDocument = require('pdfkit');
const path = require('path');
const fs   = require('fs');

const LOGO_PATH = path.join(__dirname, '../assets/logo.png');

function cop(n) {
    return '$' + Number(n).toLocaleString('es-CO') + ' COP';
}
function fecha(f) {
    return new Date(f + 'T00:00:00').toLocaleDateString('es-CO', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
}

function generarPDFFactura({ numero, fecha: fechaStr, concepto, monto, metodo_pago,
    clienteNombre, clienteEmail, clienteTelefono, empresa }) {

    return new Promise((resolve, reject) => {
        const doc = new PDFDocument({ margin: 0, size: 'A4' });
        const chunks = [];
        doc.on('data', c => chunks.push(c));
        doc.on('end',  () => resolve(Buffer.concat(chunks)));
        doc.on('error', reject);

        const nombreEmpresa = empresa || process.env.NOMBRE_EMPRESA || 'AI Company CO';
        const PW = 595; // ancho A4
        const HEADER_H = 130;
        const PURPLE_DARK  = '#1a0d3d';
        const PURPLE_MID   = '#4f46e5';
        const PURPLE_LIGHT = '#7c3aed';
        const GREEN  = '#10b981';
        const GRAY   = '#64748b';
        const BORDER = '#e2e8f0';

        // ══════════════════════════════════════════════════════
        // BANDA SUPERIOR — fondo morado oscuro
        // ══════════════════════════════════════════════════════
        doc.rect(0, 0, PW, HEADER_H).fill(PURPLE_DARK);

        // Franja de acento morado claro (línea inferior del header)
        doc.rect(0, HEADER_H - 4, PW, 4).fill(PURPLE_LIGHT);

        // Logo (izquierda, centrado verticalmente en la banda)
        const logoSize = 82;
        if (fs.existsSync(LOGO_PATH)) {
            doc.image(LOGO_PATH, 24, (HEADER_H - logoSize) / 2, { width: logoSize, height: logoSize });
        }

        // Nombre empresa y tagline (junto al logo)
        doc.fillColor('#ffffff')
           .fontSize(19).font('Helvetica-Bold')
           .text(nombreEmpresa, 118, 32);
        doc.fillColor('#a78bfa')
           .fontSize(9).font('Helvetica')
           .text('aicompanyco.com  ·  +57 321 267 4754', 118, 56);

        // FACTURA + número (derecha)
        doc.fillColor('#a78bfa')
           .fontSize(10).font('Helvetica-Bold')
           .text('FACTURA', 0, 30, { align: 'right', width: PW - 30 });
        doc.fillColor('#ffffff')
           .fontSize(22).font('Helvetica-Bold')
           .text(numero, 0, 46, { align: 'right', width: PW - 30 });
        doc.fillColor('#a78bfa')
           .fontSize(9).font('Helvetica')
           .text(`Fecha: ${fecha(fechaStr)}`, 0, 74, { align: 'right', width: PW - 30 });

        // ══════════════════════════════════════════════════════
        // CUERPO — fondo blanco
        // ══════════════════════════════════════════════════════
        const bodyY = HEADER_H + 28;

        // ── Datos del cliente ──
        doc.fillColor(GRAY)
           .fontSize(8).font('Helvetica-Bold')
           .text('FACTURADO A', 40, bodyY);

        doc.fillColor('#111')
           .fontSize(14).font('Helvetica-Bold')
           .text(clienteNombre || '—', 40, bodyY + 14);

        let cy = bodyY + 34;
        if (clienteEmail) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteEmail, 40, cy);
            cy += 14;
        }
        if (clienteTelefono) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteTelefono, 40, cy);
            cy += 14;
        }

        // Número de factura también en el cuerpo (pequeño, a la derecha)
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text('N° de factura', PW - 200, bodyY, { width: 170, align: 'right' });
        doc.fillColor(PURPLE_MID).fontSize(13).font('Helvetica-Bold')
           .text(numero, PW - 200, bodyY + 13, { width: 170, align: 'right' });

        // ── Separador ──
        const tableY = Math.max(cy + 18, bodyY + 80);
        doc.moveTo(40, tableY).lineTo(PW - 40, tableY).strokeColor(BORDER).lineWidth(1).stroke();

        // ── Cabecera tabla ──
        const thY = tableY + 8;
        doc.rect(40, thY, PW - 80, 26).fill(PURPLE_MID);
        doc.fillColor('#fff').fontSize(9).font('Helvetica-Bold')
           .text('DESCRIPCIÓN / CONCEPTO', 52, thY + 8)
           .text('IMPORTE', PW - 130, thY + 8, { width: 90, align: 'right' });

        // ── Fila de concepto ──
        const rowY = thY + 26;
        doc.rect(40, rowY, PW - 80, 40).fill('#f8f7ff');
        doc.fillColor('#1e1b4b').fontSize(10).font('Helvetica')
           .text(concepto, 52, rowY + 12, { width: PW - 170 });
        doc.fillColor(GREEN).fontSize(12).font('Helvetica-Bold')
           .text(cop(monto), PW - 130, rowY + 11, { width: 90, align: 'right' });

        // Borde tabla
        doc.rect(40, thY, PW - 80, 66).strokeColor(BORDER).lineWidth(1).stroke();

        // ── Total ──
        const totY = rowY + 56;

        doc.rect(PW - 220, totY, 180, 44).fill('#f8f7ff').strokeColor(PURPLE_LIGHT).lineWidth(1).stroke();
        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text('TOTAL A PAGAR', PW - 212, totY + 7, { width: 164, align: 'center' });
        doc.fillColor(PURPLE_DARK).fontSize(17).font('Helvetica-Bold')
           .text(cop(monto), PW - 212, totY + 20, { width: 164, align: 'center' });

        // Método de pago
        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text(`Método de pago: ${metodo_pago || 'efectivo'}`, 40, totY + 16);

        // ── Separador pie ──
        const pieY = totY + 70;
        doc.moveTo(40, pieY).lineTo(PW - 40, pieY).strokeColor(BORDER).lineWidth(1).stroke();

        // ── Pie de página ──
        doc.rect(0, pieY + 1, PW, 90).fill('#faf9ff');

        doc.fillColor(PURPLE_DARK).fontSize(10).font('Helvetica-Bold')
           .text(nombreEmpresa, 40, pieY + 16);
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text('aicompanyco.com  ·  +57 321 267 4754  ·  Colombia', 40, pieY + 31);

        doc.fillColor('#c4b5fd').fontSize(8).font('Helvetica')
           .text('Este documento es una constancia de pago generada electrónicamente.', 40, pieY + 50);
        doc.text('No requiere firma ni sello para su validez.', 40, pieY + 62);

        // Número de factura repetido en el pie (marca de agua pequeña)
        doc.fillColor('#e2e8f0').fontSize(9).font('Helvetica-Bold')
           .text(numero, 0, pieY + 48, { align: 'right', width: PW - 40 });

        doc.end();
    });
}

module.exports = { generarPDFFactura };
