const PDFDocument = require('pdfkit');

function formatCOP(n) {
    return '$' + Number(n).toLocaleString('es-CO');
}

function formatFecha(f) {
    return new Date(f + 'T00:00:00').toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
}

/**
 * Genera un PDF de factura en memoria y devuelve un Buffer.
 */
function generarPDFFactura({ numero, fecha, concepto, monto, metodo_pago, clienteNombre, clienteEmail, clienteTelefono, empresa }) {
    return new Promise((resolve, reject) => {
        const doc = new PDFDocument({ margin: 50, size: 'A4' });
        const chunks = [];
        doc.on('data', c => chunks.push(c));
        doc.on('end',  () => resolve(Buffer.concat(chunks)));
        doc.on('error', reject);

        const nombreEmpresa = empresa || process.env.NOMBRE_EMPRESA || 'AI Company CO';
        const PURPLE = '#4f46e5';
        const GRAY   = '#64748b';
        const GREEN  = '#10b981';
        const W      = 495; // ancho útil (A4 595 - 2*50)

        // ── Encabezado ────────────────────────────────────────────
        doc.fillColor(PURPLE)
           .fontSize(22).font('Helvetica-Bold')
           .text(nombreEmpresa, 50, 50);

        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text('+57 321 267 4754 · aicompanyco.com', 50, 78);

        // Número de factura (derecha)
        doc.fillColor('#111').fontSize(20).font('Helvetica-Bold')
           .text(numero, 50, 50, { align: 'right' });

        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text(`Fecha: ${formatFecha(fecha)}`, 50, 78, { align: 'right' });

        // Línea separadora
        doc.moveTo(50, 105).lineTo(545, 105).strokeColor('#e2e8f0').lineWidth(1).stroke();

        // ── Datos del cliente ────────────────────────────────────
        doc.fillColor(GRAY).fontSize(8).font('Helvetica-Bold')
           .text('FACTURADO A', 50, 120);

        doc.fillColor('#111').fontSize(12).font('Helvetica-Bold')
           .text(clienteNombre || '—', 50, 133);

        let cy = 150;
        if (clienteEmail) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteEmail, 50, cy);
            cy += 13;
        }
        if (clienteTelefono) {
            doc.fillColor(GRAY).fontSize(9).font('Helvetica').text(clienteTelefono, 50, cy);
            cy += 13;
        }

        // ── Tabla de conceptos ───────────────────────────────────
        const ty = Math.max(cy + 20, 200);

        // Cabecera tabla
        doc.fillColor(PURPLE).rect(50, ty, W, 24).fill();
        doc.fillColor('#fff').fontSize(9).font('Helvetica-Bold')
           .text('CONCEPTO',    60,  ty + 7)
           .text('SUBTOTAL',   460,  ty + 7, { width: 80, align: 'right' });

        // Fila
        const ry = ty + 24;
        doc.fillColor('#f8fafc').rect(50, ry, W, 30).fill();
        doc.fillColor('#111').fontSize(10).font('Helvetica')
           .text(concepto, 60, ry + 8, { width: 380 });
        doc.fillColor(GREEN).font('Helvetica-Bold')
           .text(formatCOP(monto), 460, ry + 8, { width: 80, align: 'right' });

        // Línea inferior tabla
        doc.moveTo(50, ry + 30).lineTo(545, ry + 30).strokeColor('#e2e8f0').lineWidth(1).stroke();

        // ── Total ────────────────────────────────────────────────
        const totY = ry + 45;
        doc.fillColor(GRAY).fontSize(9).font('Helvetica')
           .text('Método de pago:', 50, totY)
           .text(metodo_pago || 'efectivo', 160, totY);

        doc.fillColor('#111').fontSize(13).font('Helvetica-Bold')
           .text('TOTAL:', 350, totY - 2);
        doc.fillColor(GREEN).fontSize(16).font('Helvetica-Bold')
           .text(formatCOP(monto), 410, totY - 4, { width: 135, align: 'right' });

        // ── Pie ──────────────────────────────────────────────────
        doc.moveTo(50, 760).lineTo(545, 760).strokeColor('#e2e8f0').lineWidth(1).stroke();
        doc.fillColor(GRAY).fontSize(8).font('Helvetica')
           .text(`${nombreEmpresa} · Generado automáticamente`, 50, 768, { align: 'center', width: W });

        doc.end();
    });
}

module.exports = { generarPDFFactura };
