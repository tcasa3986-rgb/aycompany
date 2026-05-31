from django.shortcuts import render, get_object_or_404, redirect
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.http import JsonResponse
from django.utils import timezone
from django.db.models import Sum, Q
from decimal import Decimal
from apps.facturacion.models import Factura, DetalleFactura, Caja
from apps.clientes.models import Cliente
from apps.inventario.models import Producto


def get_numero_factura():
    """Genera el número de factura auto-incremental."""
    last = Factura.objects.order_by('-id').first()
    if last:
        try:
            num = int(last.numero.split('-')[-1]) + 1
        except Exception:
            num = last.id + 1
    else:
        num = 1
    return f"F-{num:06d}"


@login_required
def factura_lista(request):
    fecha_desde = request.GET.get('fecha_desde', '')
    fecha_hasta = request.GET.get('fecha_hasta', '')
    q = request.GET.get('q', '')

    facturas = Factura.objects.select_related('cliente').order_by('-fecha')

    if fecha_desde:
        facturas = facturas.filter(fecha__date__gte=fecha_desde)
    if fecha_hasta:
        facturas = facturas.filter(fecha__date__lte=fecha_hasta)
    if q:
        facturas = facturas.filter(
            Q(numero__icontains=q) | Q(cliente__nombres__icontains=q) | Q(cliente__apellidos__icontains=q)
        )

    total_ingresos = facturas.filter(estado='PAGADA').aggregate(t=Sum('total'))['t'] or 0

    return render(request, 'facturacion/lista.html', {
        'facturas': facturas,
        'total_ingresos': total_ingresos,
        'fecha_desde': fecha_desde,
        'fecha_hasta': fecha_hasta,
        'q': q,
    })


@login_required
def factura_crear(request):
    clientes = Cliente.objects.all().order_by('nombres')
    productos = Producto.objects.filter(is_active=True).order_by('nombre')

    if request.method == 'POST':
        cliente_id = request.POST.get('cliente')
        metodo_pago = request.POST.get('metodo_pago', 'EFECTIVO')
        descripciones = request.POST.getlist('descripcion[]')
        cantidades = request.POST.getlist('cantidad[]')
        precios = request.POST.getlist('precio[]')
        producto_ids = request.POST.getlist('producto_id[]')

        if not descripciones or not any(descripciones):
            messages.error(request, 'Debe agregar al menos un ítem a la factura.')
            return render(request, 'facturacion/nueva_factura.html', {'clientes': clientes, 'productos': productos})

        cliente = get_object_or_404(Cliente, pk=cliente_id) if cliente_id else None
        subtotal = Decimal('0.00')

        factura = Factura.objects.create(
            cliente=cliente,
            numero=get_numero_factura(),
            subtotal=0,
            igv=0,
            total=0,
            metodo_pago=metodo_pago,
            cajero=request.user.get_full_name() or request.user.username,
        )

        for i, desc in enumerate(descripciones):
            if not desc.strip():
                continue
            cant = int(cantidades[i]) if i < len(cantidades) else 1
            precio = Decimal(precios[i]) if i < len(precios) else Decimal('0')
            prod_id = producto_ids[i] if i < len(producto_ids) else None
            producto_obj = None
            if prod_id:
                try:
                    producto_obj = Producto.objects.get(pk=int(prod_id))
                except (Producto.DoesNotExist, ValueError):
                    pass

            DetalleFactura.objects.create(
                factura=factura,
                producto=producto_obj,
                descripcion=desc,
                cantidad=cant,
                precio_unitario=precio,
            )
            subtotal += Decimal(cant) * precio

        igv = round(subtotal * Decimal('0.18'), 2)
        factura.subtotal = subtotal
        factura.igv = igv
        factura.total = subtotal + igv
        factura.save()

        # Actualizar caja del día
        caja, _ = Caja.objects.get_or_create(fecha=timezone.localdate(), defaults={'monto_inicial': 0})
        if not caja.cerrada:
            caja.total_ingresos = (caja.total_ingresos or 0) + factura.total
            caja.monto_final = (caja.monto_inicial or 0) + (caja.total_ingresos or 0) - (caja.total_egresos or 0)
            caja.save()

        messages.success(request, f'Factura {factura.numero} generada por S/ {factura.total}.')
        return redirect('factura_detalle', pk=factura.pk)

    return render(request, 'facturacion/nueva_factura.html', {'clientes': clientes, 'productos': productos})


@login_required
def factura_detalle(request, pk):
    factura = get_object_or_404(Factura.objects.select_related('cliente').prefetch_related('detalles__producto'), pk=pk)
    return render(request, 'facturacion/detalle_factura.html', {'factura': factura})


@login_required
def factura_anular(request, pk):
    factura = get_object_or_404(Factura, pk=pk)
    if request.method == 'POST':
        factura.estado = 'ANULADA'
        factura.save()
        messages.warning(request, f'Factura {factura.numero} anulada.')
        return redirect('factura_lista')
    return render(request, 'clientes/confirmar_eliminar.html', {'objeto': factura, 'tipo': 'factura (anular)'})


@login_required
def caja_hoy(request):
    hoy = timezone.localdate()
    caja, created = Caja.objects.get_or_create(fecha=hoy, defaults={'monto_inicial': 0})
    facturas_hoy = Factura.objects.filter(fecha__date=hoy, estado='PAGADA')
    total_hoy = facturas_hoy.aggregate(t=Sum('total'))['t'] or 0
    return render(request, 'facturacion/caja.html', {
        'caja': caja, 'facturas_hoy': facturas_hoy, 'total_hoy': total_hoy, 'hoy': hoy
    })


@login_required
def caja_cerrar(request):
    if request.method == 'POST':
        hoy = timezone.localdate()
        caja = get_object_or_404(Caja, fecha=hoy)
        if not caja.cerrada:
            caja.cerrada = True
            caja.fecha_cierre = timezone.now()
            caja.usuario_cierre = request.user.get_full_name() or request.user.username
            caja.save()
            messages.success(request, 'Caja cerrada correctamente.')
        return redirect('caja_hoy')


@login_required
def api_producto_precio(request, pk):
    """Devuelve precio y nombre del producto para llenado AJAX en factura."""
    producto = get_object_or_404(Producto, pk=pk)
    return JsonResponse({
        'nombre': producto.nombre,
        'precio': float(producto.precio_venta),
        'stock': producto.stock_actual,
    })
