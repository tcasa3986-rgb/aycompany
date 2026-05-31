from django.shortcuts import render
from django.contrib.auth.decorators import login_required
from django.utils import timezone
from django.db.models import Sum


@login_required
def dashboard(request):
    from apps.agenda.models import Cita
    from apps.clientes.models import Mascota, Cliente
    from apps.facturacion.models import Factura
    from apps.inventario.models import Producto
    import datetime

    hoy = timezone.now().date()
    mes_inicio = hoy.replace(day=1)

    # KPIs
    citas_hoy = Cita.objects.filter(fecha=hoy).count()
    total_mascotas = Mascota.objects.count()
    mascotas_mes = Mascota.objects.filter(fecha_registro__date__gte=mes_inicio).count()
    ingresos_mes = Factura.objects.filter(
        estado='PAGADA', fecha__date__gte=mes_inicio
    ).aggregate(total=Sum('total'))['total'] or 0
    ticket_promedio = (ingresos_mes / Factura.objects.filter(
        estado='PAGADA', fecha__date__gte=mes_inicio
    ).count()) if Factura.objects.filter(
        estado='PAGADA', fecha__date__gte=mes_inicio
    ).count() > 0 else 0
    productos_activos = Producto.objects.filter(is_active=True).count()
    stock_bajo = sum(1 for p in Producto.objects.filter(is_active=True) if p.necesita_reposicion)

    # Próximas citas del día
    proximas_citas = Cita.objects.filter(
        fecha=hoy
    ).exclude(estado='COMPLETADA').exclude(estado='CANCELADA').select_related(
        'mascota__cliente', 'veterinario'
    ).order_by('hora')[:5]

    # Gráfico: citas por día últimos 7 días
    labels = []
    data_citas = []
    data_ingresos = []
    for i in range(6, -1, -1):
        dia = hoy - datetime.timedelta(days=i)
        labels.append(dia.strftime('%a'))
        data_citas.append(Cita.objects.filter(fecha=dia).count())
        ing_dia = Factura.objects.filter(
            estado='PAGADA', fecha__date=dia
        ).aggregate(t=Sum('total'))['t'] or 0
        data_ingresos.append(float(ing_dia))

    context = {
        'citas_hoy': citas_hoy,
        'total_mascotas': total_mascotas,
        'mascotas_mes': mascotas_mes,
        'ingresos_mes': ingresos_mes,
        'ticket_promedio': round(ticket_promedio, 2),
        'productos_activos': productos_activos,
        'stock_bajo': stock_bajo,
        'proximas_citas': proximas_citas,
        'labels': labels,
        'data_citas': data_citas,
        'data_ingresos': data_ingresos,
    }
    return render(request, 'dashboard/index.html', context)


@login_required
def mantenimiento_index(request):
    if not request.user.is_superuser:
        from django.contrib import messages
        from django.shortcuts import redirect
        messages.error(request, 'Acceso denegado. Solo administradores pueden ver esta sección.')
        return redirect('dashboard')
        
    from django.db import connection
    from django.conf import settings
    import os
    import glob
    
    # Contar registros
    tablas_info = []
    total_registros = 0
    with connection.cursor() as cursor:
        cursor.execute("SHOW TABLES")
        tablas = cursor.fetchall()
        for tabla in tablas:
            nombre_tabla = tabla[0]
            if not nombre_tabla.startswith('auth_') and not nombre_tabla.startswith('django_'):
                cursor.execute(f"SELECT COUNT(*) FROM {nombre_tabla}")
                conteo = cursor.fetchone()[0]
                tablas_info.append({'nombre': nombre_tabla, 'cantidad': conteo})
                total_registros += conteo
                
    # Tamaño BD aprox
    tamano_bd_mb = 0
    try:
        with connection.cursor() as cursor:
            cursor.execute(f"SELECT sum(data_length + index_length) / 1024 / 1024 FROM information_schema.TABLES WHERE table_schema = '{settings.DATABASES['default']['NAME']}'")
            tamano_bd_mb = round(cursor.fetchone()[0] or 0, 2)
    except:
        pass
        
    # Verificar backups en la carpeta temporal / media si es que guardamos ahí
    backups_dir = os.path.join(settings.BASE_DIR, 'backups')
    os.makedirs(backups_dir, exist_ok=True)
    archivos_backup = len(glob.glob(os.path.join(backups_dir, '*.sql')))
    
    context = {
        'tablas_info': tablas_info,
        'total_registros': total_registros,
        'tamano_bd_mb': tamano_bd_mb,
        'archivos_backup': archivos_backup,
    }
    return render(request, 'core/mantenimiento.html', context)


@login_required
def mantenimiento_backup(request):
    if not request.user.is_superuser:
        return redirect('dashboard')
        
    import os
    import subprocess
    from django.conf import settings
    from django.http import FileResponse
    from django.utils import timezone
    
    db_settings = settings.DATABASES['default']
    db_name = db_settings['NAME']
    db_user = db_settings['USER']
    db_password = db_settings['PASSWORD']
    db_host = db_settings['HOST']
    
    fecha_str = timezone.now().strftime('%Y%m%d_%H%M%S')
    filename = f"backup_vet_{fecha_str}.sql"
    filepath = os.path.join(settings.BASE_DIR, 'backups', filename)
    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    
    cmd = f"mysqldump -h {db_host} -u {db_user} "
    if db_password:
        cmd += f"-p{db_password} "
    cmd += f"{db_name} > \"{filepath}\""
    
    try:
        subprocess.run(cmd, shell=True, check=True)
        response = FileResponse(open(filepath, 'rb'), as_attachment=True, filename=filename)
        return response
    except Exception as e:
        from django.contrib import messages
        from django.shortcuts import redirect
        messages.error(request, f'Error al generar backup: {str(e)}')
        return redirect('mantenimiento_index')


@login_required
def mantenimiento_restore(request):
    from django.contrib import messages
    from django.shortcuts import redirect
    
    if not request.user.is_superuser:
        return redirect('dashboard')
        
    if request.method == 'POST' and request.FILES.get('backup_file'):
        import os
        import subprocess
        from django.conf import settings
        
        archivo = request.FILES['backup_file']
        if not archivo.name.endswith('.sql'):
            messages.error(request, 'El archivo debe ser formato .sql')
            return redirect('mantenimiento_index')
            
        temp_path = os.path.join(settings.BASE_DIR, 'backups', 'temp_restore.sql')
        os.makedirs(os.path.dirname(temp_path), exist_ok=True)
        
        with open(temp_path, 'wb+') as destination:
            for chunk in archivo.chunks():
                destination.write(chunk)
                
        db_settings = settings.DATABASES['default']
        db_name = db_settings['NAME']
        db_user = db_settings['USER']
        db_password = db_settings['PASSWORD']
        db_host = db_settings['HOST']
        
        cmd = f"mysql -h {db_host} -u {db_user} "
        if db_password:
            cmd += f"-p{db_password} "
        cmd += f"{db_name} < \"{temp_path}\""
        
        try:
            subprocess.run(cmd, shell=True, check=True)
            messages.success(request, 'Base de datos restaurada correctamente.')
        except Exception as e:
            messages.error(request, f'Error al restaurar: {str(e)}')
        finally:
            if os.path.exists(temp_path):
                os.remove(temp_path)
                
    return redirect('mantenimiento_index')


@login_required
def mantenimiento_reset(request):
    from django.contrib import messages
    from django.shortcuts import redirect
    
    if not request.user.is_superuser:
        return redirect('dashboard')
        
    if request.method == 'POST' and request.POST.get('confirm_reset') == 'RESET_SISTEMA_VET':
        from django.db import connection
        
        # Tablas a limpiar (transaccionales)
        tablas_limpiar = [
            'facturacion_detallefactura', 'facturacion_factura', 'facturacion_caja',
            'grooming_serviciogrooming',
            'inventario_movimiento', 
            'medico_detallereceta', 'medico_receta', 'medico_vacuna', 'medico_historiaclinica',
            'agenda_cita',
            'clientes_mascota', 'clientes_cliente'
        ]
        
        try:
            with connection.cursor() as cursor:
                cursor.execute('SET FOREIGN_KEY_CHECKS = 0;')
                for tabla in tablas_limpiar:
                    cursor.execute(f'TRUNCATE TABLE {tabla};')
                cursor.execute('SET FOREIGN_KEY_CHECKS = 1;')
            messages.success(request, 'Sistema reseteado correctamente. Datos de negocio eliminados. (Usuarios conservados)')
        except Exception as e:
            messages.error(request, f'Error al resetear: {str(e)}')
            
    else:
        messages.error(request, 'Confirmación incorrecta para resetear el sistema.')
        
    return redirect('mantenimiento_index')

