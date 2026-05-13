const PDFDocument = require('pdfkit');
const path = require('path');
const fs   = require('fs');

const LOGO_PATH = path.join(__dirname, '../assets/logo.png');

function cop(n) {
    return '$' + Number(n).toLocaleString('es-CO') + ' COP';
}
function fechaLarga(f) {
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
        const PW = 595;
        const PH = 842;

        const PURPLE      = '#5b21b6';
        const PURPLE_MID  = '#7c3aed';
        const PURPLE_LIGHT= '#ede9fe';
        const PURPLE_SOFT = '#f5f3ff';
        const GREEN       = '#059669';
        const DARK        = '#1e1b4b';
        const GRAY        = '#64748b';
        const GRAY_LIGHT  = '#f8fafc';
        const BORDER      = '#e2e8f0';

        // ── Barra superior morada (acento) ──────────────────────
        doc.rect(0, 0, PW, 7).fill(PURPLE_MID);

        // ── Encabezado blanco ────────────────────────────────────
        // Logo (transparente — se ve perfecto sobre blanco)
        const logoW = 100;
        const logoH = 100;
        if (fs.existsSync(LOGO_PATH)) {
            doc.image(LOGO_PATH, 36, 18, { width: logoW, height: logoH });
        }

        // Nombre empresa
        doc.fillColor(DARK)
           .fontSize(20).font('Helvetica-Bold')
           .text(nombreEmpresa, 148, 32);

        doc.fillColor(GRAY)
           .fontSize(9).font('Helvetica')
           .text('aicompanyco.com', 148, 57)
           .text('+57 321 267 4754  ·  Colombia', 148, 70);

        // FACTURA label + número (esquina derecha)
        doc.fillColor(PURPLE_MID)
           .fontSize(10).font('Helvetica-Bold')
           .text('FACTURA', 0, 32, { align: 'right', width: PW - 36 });

        doc.fillColor(DARK)
           .fontSize(26).font('Helvetica-Bold')
           .text(numero, 0, 46, { align: 'right', width: PW - 36 });

        doc.fillColor(GRAY)
           .fontSize(9).font('Helvetica')
           .text(`Emitida el ${fechaLarga(fechaStr)}`, 0, 79, { align: 'right', width: PW - 36 });

        // Línea divisoria header
        const divY = 128;
        doc.moveTo(36, divY).lineTo(PW - 36, divY).strokeColor(BORDER).lineWidth(1).stroke();

        // ── Bloque cliente + info pago ───────────────────────────
        const secY = divY + 22;

        // Columna izquierda — datos del cliente
        doc.fillColor(PURPLE_MID)
           .fontSize(7.5).font('Helvetica-Bold')
           .text('FACTURADO A', 36, secY);

        doc.fillColor(DARK)
           .fontSize(13).font('Helvetica-Bold')
           .text(clienteNombre || '—', 36, secY + 13);

        let cy = secY + 32;
        if (clienteEmail) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteEmail, 36, cy);
            cy += 14;
        }
        if (clienteTelefono) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteTelefono, 36, cy);
            cy += 14;
        }

        // Columna derecha — método de pago
        doc.fillColor(PURPLE_MID)
           .fontSize(7.5).font('Helvetica-Bold')
           .text('MÉTODO DE PAGO', 360, secY);
        doc.fillColor(DARK)
           .fontSize(11).font('Helvetica-Bold')
           .text(metodo_pago || 'Efectivo', 360, secY + 13);

        doc.fillColor(PURPLE_MID)
           .fontSize(7.5).font('Helvetica-Bold')
           .text('FECHA DE EMISIÓN', 360, secY + 38);
        doc.fillColor(DARK)
           .fontSize(10).font('Helvetica')
           .text(fechaLarga(fechaStr), 360, secY + 51);

        // ── Tabla ────────────────────────────────────────────────
        const tableY = Math.max(cy + 22, secY + 100);

        // Cabecera tabla
        doc.rect(36, tableY, PW - 72, 28).fill(DARK);
        doc.fillColor('#ffffff').fontSize(9).font('Helvetica-Bold')
           .text('DESCRIPCIÓN', 50, tableY + 9)
           .text('CANTIDAD', 340, tableY + 9, { width: 70, align: 'center' })
           .text('IMPORTE', PW - 130, tableY + 9, { width: 88, align: 'right' });

        // Fila
        const rowY = tableY + 28;
        doc.rect(36, rowY, PW - 72, 44).fill(PURPLE_SOFT);
        doc.rect(36, rowY, PW - 72, 44).strokeColor(BORDER).lineWidth(0.5).stroke();

        doc.fillColor(DARK).fontSize(10).font('Helvetica')
           .text(concepto, 50, rowY + 14, { width: 280 });
        doc.fillColor(GRAY).fontSize(10).font('Helvetica')
           .text('1', 340, rowY + 14, { width: 70, align: 'center' });
        doc.fillColor(GREEN).fontSize(11).font('Helvetica-Bold')
           .text(cop(monto), PW - 130, rowY + 13, { width: 88, align: 'right' });

        // ── Subtotal / Total ──────────────────────────────────────
        const sumY = rowY + 56;

        // Línea subtotal
        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text('Subtotal', PW - 220, sumY, { width: 90, align: 'right' });
        doc.fillColor(DARK).fontSize(9).font('Helvetica')
           .text(cop(monto), PW - 125, sumY, { width: 88, align: 'right' });

        doc.moveTo(PW - 220, sumY + 16).lineTo(PW - 36, sumY + 16).strokeColor(BORDER).lineWidth(0.5).stroke();

        // Caja TOTAL
        const totBoxY = sumY + 22;
        doc.rect(PW - 220, totBoxY, 184, 48).fill(PURPLE).strokeColor(PURPLE).stroke();
        doc.fillColor('#ffffff').fontSize(9).font('Helvetica')
           .text('TOTAL', PW - 212, totBoxY + 9, { width: 168, align: 'center' });
        doc.fillColor('#ffffff').fontSize(18).font('Helvetica-Bold')
           .text(cop(monto), PW - 212, totBoxY + 23, { width: 168, align: 'center' });

        // ── Nota / agradecimiento ─────────────────────────────────
        const notaY = totBoxY + 72;
        doc.rect(36, notaY, PW - 72, 40).fill(GRAY_LIGHT).strokeColor(BORDER).lineWidth(0.5).stroke();
        doc.fillColor(PURPLE_MID).fontSize(8).font('Helvetica-Bold')
           .text('NOTA:', 50, notaY + 10);
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text('Gracias por su pago. Este documento es una constancia de pago generada electrónicamente.', 80, notaY + 10, { width: PW - 130 });
        doc.fillColor(GRAY).fontSize(8)
           .text('No requiere firma ni sello para su validez.', 80, notaY + 22, { width: PW - 130 });

        // ── Pie de página ─────────────────────────────────────────
        const pieY = PH - 60;
        doc.rect(0, pieY, PW, 60).fill(PURPLE_SOFT);
        doc.moveTo(0, pieY).lineTo(PW, pieY).strokeColor(BORDER).lineWidth(1).stroke();

        // Barra inferior de color
        doc.rect(0, PH - 7, PW, 7).fill(PURPLE_MID);

        doc.fillColor(DARK).fontSize(9).font('Helvetica-Bold')
           .text(nombreEmpresa, 36, pieY + 14);
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text('aicompanyco.com  ·  +57 321 267 4754', 36, pieY + 28);

        doc.fillColor(PURPLE_MID).fontSize(9).font('Helvetica-Bold')
           .text(numero, 0, pieY + 14, { align: 'right', width: PW - 36 });
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text('Documento electrónico', 0, pieY + 28, { align: 'right', width: PW - 36 });

        doc.end();
    });
}

module.exports = { generarPDFFactura };
