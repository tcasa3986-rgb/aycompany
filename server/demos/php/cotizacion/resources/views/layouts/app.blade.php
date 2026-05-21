<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CotizaPro') }} — {{ $title ?? 'Sistema' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-w:    240px;
            --sidebar-bg:   #1e2d3d;
            --sidebar-dark: #172434;
            --accent:       #4ade80;
            --accent-h:     #22c55e;
            --accent-2:     #38bdf8;
            --topbar-h:     58px;
            --bg-main:      #f0f2f5;
            --bg-card:      #ffffff;
            --text-main:    #2d3748;
            --text-muted:   #718096;
            --text-sidebar: #8fa8c0;
            --border:       #e2e8f0;
            --danger:       #f56565;
            --warning:      #ed8936;
            --success:      #48bb78;
            --info:         #4299e1;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-main); display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        #sidebar {
            width: var(--sidebar-w); background: var(--sidebar-bg);
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 200;
            display: flex; flex-direction: column;
        }
        .sidebar-logo {
            padding: 22px 20px; display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .logo-box {
            width: 42px; height: 42px; border-radius: 8px; flex-shrink: 0;
            background: var(--sidebar-dark);
            border: 2px solid var(--accent);
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        .logo-box::before {
            content: ''; position: absolute; width: 20px; height: 20px;
            border: 3px solid var(--accent); border-radius: 3px;
        }
        .logo-box::after {
            content: ''; position: absolute; width: 10px; height: 10px;
            background: #f59e0b; border-radius: 2px; top: 6px; left: 6px;
        }
        .logo-text { line-height: 1.2; }
        .logo-text strong { display: block; font-size: 11px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: .08em; }
        .logo-text span   { font-size: 10px; color: var(--text-sidebar); }

        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-label {
            font-size: 9.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: rgba(143,168,192,.5);
            padding: 12px 20px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 20px; color: var(--text-sidebar);
            font-size: 13px; font-weight: 500; text-decoration: none;
            transition: all .15s; position: relative; cursor: pointer;
            border-left: 3px solid transparent;
        }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; opacity: .7; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,.05); }
        .nav-item.active {
            color: var(--accent); background: rgba(74,222,128,.08);
            border-left-color: var(--accent);
        }
        .nav-item.active svg { opacity: 1; }
        .nav-badge {
            margin-left: auto; background: var(--accent); color: #1e2d3d;
            font-size: 10px; font-weight: 700; border-radius: 10px;
            padding: 1px 7px; min-width: 20px; text-align: center;
        }

        .sidebar-bottom {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .user-row { display: flex; align-items: center; gap: 10px; padding: 8px; margin-bottom: 8px; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #1e2d3d;
        }
        .user-name  { font-size: 12.5px; font-weight: 600; color: #fff; }
        .user-email { font-size: 10.5px; color: var(--text-sidebar); }
        .btn-signout {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 9px; border-radius: 6px;
            background: rgba(245,101,101,.12); border: 1px solid rgba(245,101,101,.25);
            color: #fc8181; font-size: 12px; font-weight: 600;
            cursor: pointer; text-transform: uppercase; letter-spacing: .06em;
            transition: all .15s;
        }
        .btn-signout:hover { background: rgba(245,101,101,.22); }
        .btn-signout svg { width: 14px; height: 14px; }

        /* ── MAIN ── */
        #main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── TOPBAR ── */
        .topbar {
            height: var(--topbar-h); background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 16px;
            padding: 0 28px; position: sticky; top: 0; z-index: 100;
        }
        .topbar-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; }
        .topbar-breadcrumb svg { width: 14px; height: 14px; color: var(--text-muted); }
        .topbar-breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .topbar-breadcrumb span { color: var(--text-main); font-weight: 600; }
        .topbar-search {
            flex: 1; display: flex; align-items: center; gap: 8px;
            background: var(--bg-main); border: 1px solid var(--border);
            border-radius: 8px; padding: 6px 14px; max-width: 380px; margin-left: 20px;
        }
        .topbar-search svg { width: 15px; height: 15px; color: var(--text-muted); flex-shrink: 0; }
        .topbar-search input {
            border: none; background: transparent; outline: none;
            font-size: 13px; color: var(--text-main); width: 100%;
        }
        .topbar-search input::placeholder { color: var(--text-muted); }
        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .topbar-page-title { font-size: 15px; font-weight: 700; color: var(--text-main); }

        /* ── CONTENT ── */
        .page-content { padding: 24px 28px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .card-title { font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px; }
        .card-sub   { font-size: 11px; color: var(--text-muted); }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }

        /* ── STAT CARDS ── */
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            display: flex; align-items: center; gap: 14px;
        }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }
        .stat-value { font-size: 22px; font-weight: 800; color: var(--text-main); margin-top: 2px; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 7px;
            font-size: 12.5px; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none; transition: all .15s;
        }
        .btn svg { width: 14px; height: 14px; }
        .btn-primary  { background: var(--accent); color: #1e2d3d; }
        .btn-primary:hover { background: var(--accent-h); }
        .btn-secondary { background: #fff; color: var(--text-main); border: 1px solid var(--border); }
        .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
        .btn-danger   { background: rgba(245,101,101,.1); color: var(--danger); border: 1px solid rgba(245,101,101,.25); }
        .btn-danger:hover { background: rgba(245,101,101,.2); }
        .btn-success  { background: rgba(72,187,120,.12); color: var(--success); border: 1px solid rgba(72,187,120,.3); }
        .btn-success:hover { background: rgba(72,187,120,.22); }
        .btn-info     { background: rgba(66,153,225,.12); color: var(--info); border: 1px solid rgba(66,153,225,.3); }
        .btn-info:hover { background: rgba(66,153,225,.22); }
        .btn-sm { padding: 5px 10px; font-size: 11.5px; }
        .btn-sm svg { width: 12px; height: 12px; }

        /* ── FORMS ── */
        .form-group  { margin-bottom: 16px; }
        .form-label  { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
        .form-control {
            width: 100%; padding: 9px 12px;
            background: #fff; border: 1px solid var(--border);
            border-radius: 7px; color: var(--text-main); font-size: 13px;
            outline: none; transition: border .15s; font-family: inherit;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(74,222,128,.12); }
        .form-control::placeholder { color: #b0bec5; }
        textarea.form-control { resize: vertical; }
        .form-row { display: grid; gap: 14px; }
        .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .form-error { font-size: 11.5px; color: var(--danger); margin-top: 4px; }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            padding: 10px 14px; text-align: left;
            font-size: 10.5px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 2px solid var(--border);
            background: #f8fafc;
        }
        tbody tr { border-bottom: 1px solid #f0f4f8; transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fffe; }
        td { padding: 11px 14px; color: var(--text-main); vertical-align: middle; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-gray  { background: #f0f4f8; color: #718096; }
        .badge-blue  { background: #ebf8ff; color: #2b6cb0; }
        .badge-green { background: #f0fff4; color: #276749; }
        .badge-red   { background: #fff5f5; color: #c53030; }
        .badge-amber { background: #fffbeb; color: #92400e; }
        .badge-cyan  { background: #e0f7fa; color: #00838f; }

        /* ── ALERTS ── */
        .alert {
            padding: 11px 16px; border-radius: 8px; font-size: 13px;
            margin-bottom: 18px; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }
        .alert-error   { background: #fff5f5; border: 1px solid #feb2b2; color: #c53030; }

        /* ── SEARCH BAR ── */
        .search-bar { display: flex; gap: 10px; margin-bottom: 18px; align-items: center; flex-wrap: wrap; }
        .search-bar .form-control { max-width: 280px; }

        /* ── PAGINATION ── */
        .pagination { display: flex; gap: 4px; justify-content: flex-end; margin-top: 16px; }
        .pagination a, .pagination span {
            padding: 5px 10px; border-radius: 6px; font-size: 12px;
            border: 1px solid var(--border); color: var(--text-muted);
            text-decoration: none; transition: all .15s; background: #fff;
        }
        .pagination a:hover { border-color: var(--accent); color: var(--accent); }
        .pagination [aria-current="page"] span { background: var(--accent); border-color: var(--accent); color: #1e2d3d; font-weight: 700; }

        /* ── PROGRESS BAR ── */
        .progress { height: 6px; background: #e8edf2; border-radius: 10px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 10px; transition: width .4s; }

        /* ── RESPONSIVE ── */
        .sidebar-toggle { display:none; background:none; border:none; cursor:pointer; padding:6px; color:rgba(255,255,255,.6); }
        .sidebar-toggle svg { width:22px; height:22px; }
        #sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:199; }
        @media(max-width:900px){
            .sidebar-toggle { display:flex; align-items:center; }
            #sidebar { transform: translateX(-100%); transition: transform .25s; z-index:200; }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0 !important; }
            #sidebar-overlay.open { display:block; }
            .page-content { padding: 14px; }
            .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
        }

        /* ── TOAST ── */
        #toast-container { position:fixed; top:20px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
        .toast {
            display:flex; align-items:flex-start; gap:12px;
            background:#fff; border-radius:10px; padding:14px 18px;
            box-shadow:0 8px 32px rgba(0,0,0,.15); min-width:300px; max-width:420px;
            border-left:4px solid var(--success); animation:toastIn .3s ease;
            pointer-events:all;
        }
        .toast.error   { border-left-color:var(--danger); }
        .toast.warning { border-left-color:#f59e0b; }
        .toast-body { flex:1; font-size:13px; color:var(--text-main); font-weight:500; line-height:1.45; }
        .toast-close { background:none; border:none; cursor:pointer; font-size:18px; color:var(--text-muted); padding:0; line-height:1; flex-shrink:0; }
        .toast-close:hover { color:var(--text-main); }
        @keyframes toastIn  { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { to   { opacity:0; transform:translateX(20px); } }
    </style>
</head>
<body>
    {{-- ══ SIDEBAR ══ --}}
    <aside id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-box"></div>
            <div class="logo-text">
                <strong>CotizaPro</strong>
                <span>Sistema Cotización</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Principal</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="nav-label" style="margin-top:8px">Operaciones</div>
            <a href="{{ route('quotations.index') }}" class="nav-item {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Cotizaciones
                @php $pendientes = \App\Models\Quotation::where('status','Emitida')->count(); @endphp
                @if($pendientes > 0) <span class="nav-badge">{{ $pendientes }}</span> @endif
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Reportes
            </a>

            <div class="nav-label" style="margin-top:8px">Catálogos</div>
            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Clientes
            </a>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Productos
            </a>
            <a href="{{ route('companies.index') }}" class="nav-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Empresas
            </a>

            <div class="nav-label" style="margin-top:8px">Sistema</div>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Configuración
            </a>
            <a href="{{ route('system.backup') }}" class="nav-item {{ request()->routeIs('system.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Mantenimiento
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user-row">
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;flex:1;min-width:0;">
                    <div class="user-avatar" style="transition:ring .15s;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                    <div style="min-width:0">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-email" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->email }}</div>
                    </div>
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-signout">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- ══ MAIN ══ --}}
    <div id="main">
        <div class="topbar">
            <div class="topbar-breadcrumb">
                <button class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle menu">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;color:var(--text-muted)"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @isset($title)
                    <span style="color:var(--text-muted)">/</span>
                    <span>{{ $title }}</span>
                @endisset
            </div>
            <form class="topbar-search" method="GET" action="{{ route('quotations.index') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" placeholder="Buscar cotizaciones..." value="{{ request()->routeIs('quotations.*') ? request('search') : '' }}" style="background:none;border:none;outline:none;font-size:13px;color:var(--text-main);width:100%;">
            </form>
            <div class="topbar-actions">
                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </div>

        <div class="page-content">
            {{ $slot }}
        </div>
    </div>

    {{-- TOAST CONTAINER --}}
    <div id="toast-container"></div>

    <script>
    // ── Sidebar toggle ──────────────────────────────────────
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('open');
    }

    // ── Toast system ────────────────────────────────────────
    function showToast(message, type = 'success') {
        const icons = {
            success: '<svg style="width:18px;height:18px;color:#48bb78;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error:   '<svg style="width:18px;height:18px;color:#f56565;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg style="width:18px;height:18px;color:#f59e0b;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
        };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type !== 'success' ? type : ''}`;
        toast.innerHTML = `${icons[type] || icons.success}<div class="toast-body">${message}</div><button class="toast-close" onclick="removeToast(this.parentElement)">×</button>`;
        container.appendChild(toast);
        setTimeout(() => removeToast(toast), 5000);
    }
    function removeToast(el) {
        if (!el) return;
        el.style.animation = 'toastOut .3s ease forwards';
        setTimeout(() => el.remove(), 300);
    }

    // Auto-show toasts from session
    @if(session('success')) showToast(@json(session('success')), 'success'); @endif
    @if(session('error'))   showToast(@json(session('error')),   'error');   @endif
    @if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif
    </script>
</body>
</html>
