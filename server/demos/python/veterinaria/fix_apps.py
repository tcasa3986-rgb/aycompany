import os

apps = ['core', 'clientes', 'agenda', 'medico', 'inventario', 'facturacion', 'grooming', 'reportes']

for app in apps:
    path = f'apps/{app}/apps.py'
    try:
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
        content = content.replace(f"name = '{app}'", f"name = 'apps.{app}'")
        with open(path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Fixed {path}")
    except Exception as e:
        print(f"Error {path}: {e}")
