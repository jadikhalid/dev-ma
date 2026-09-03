@php
    $sidebarSide = $sidebarSide ?? 'left';
    $sidebarWidth = $sidebarWidth ?? '32%';
    $mainWidth = $mainWidth ?? '68%';
    $sidebarBg = $sidebarBg ?? '#1e3a5f';
    $sidebarExtra = $sidebarExtra ?? '';
@endphp
        .cv-document { width: 100%; }
        table.cv-columns {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td.sidebar {
            width: {{ $sidebarWidth }};
            vertical-align: top;
            background-color: {{ $sidebarBg }};
            {{ $sidebarExtra }}
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        td.main {
            width: {{ $mainWidth }};
            vertical-align: top;
            background-color: #ffffff;
            padding: 18px 16px 18px 14px;
        }
        td.main .section-title { margin-bottom: 8px; }
        td.main .entry-title { margin-bottom: 4px; }
        .sidebar-inner,
        .main-inner {
            width: 100%;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
