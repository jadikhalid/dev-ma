@php
    $sidebarSide = $sidebarSide ?? 'left';
    $sidebarWidth = $sidebarWidth ?? '32%';
    $mainWidth = $mainWidth ?? '68%';
    $sidebarBg = $sidebarBg ?? '#1e3a5f';
    $sidebarExtra = $sidebarExtra ?? '';
@endphp
        .cv-document { position: relative; width: 100%; }
        .cv-sidebar-band {
            position: absolute;
            top: 0;
            bottom: 0;
            @if ($sidebarSide === 'right')
            left: {{ $mainWidth }};
            @else
            left: 0;
            @endif
            width: {{ $sidebarWidth }};
            background: {{ $sidebarBg }};
            z-index: 0;
            {{ $sidebarExtra }}
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table.cv-columns {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            position: relative;
            z-index: 1;
        }
        td.sidebar {
            width: {{ $sidebarWidth }};
            vertical-align: top;
            background: transparent;
        }
        td.main {
            width: {{ $mainWidth }};
            vertical-align: top;
            background: #fff;
        }
        .sidebar-inner,
        .main-inner {
            width: 100%;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
