<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CP Players</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #f0f4f8;
            --sidebar-full: 220px;
            --sidebar-mini: 68px;
            --accent:       #e50914;
            --text:         #1a1d23;
            --muted:        #6b7280;
            --card-bg:      #ffffff;
            --card-border:  rgba(0,0,0,0.1);
            --focus-color:  #f5a623;
            --focus-ring:   0 0 0 3px #fff, 0 0 0 6px #f5a623;
            --focus-scale:  scale(1.06);
            --radius:       14px;
            --lang-row-h:   220px;
            --ch-bottom-h:  252px;
            --sb-speed:     .28s;
        }

        html, body { width: 100%; height: 100%; background: var(--bg); color: var(--text); font-family: 'Segoe UI', Tahoma, Arial, sans-serif; overflow: hidden; font-size: 16px; }

        /* ── PLAYBACK MODE ── */
        body.playback-mode { background: #000; }
        body.playback-mode #sidebar,
        body.playback-mode #main { opacity: 0; visibility: hidden; pointer-events: none; }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: var(--sidebar-mini);          /* collapsed by default */
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.08);
            display: flex; flex-direction: column;
            z-index: 200;
            overflow: hidden;
            transition: width var(--sb-speed) cubic-bezier(.4,0,.2,1),
                        box-shadow var(--sb-speed) ease;
        }
        #sidebar.expanded {
            width: var(--sidebar-full);
            box-shadow: 4px 0 24px rgba(0,0,0,0.12);
        }

        /* BRAND */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 0 22px 0;
            min-height: 68px;
            white-space: nowrap;
            overflow: hidden;
        }
        .brand-icon {
            flex-shrink: 0;
            width: 68px;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
        }
        /* "CP" — always visible, same size in both states */
        .brand-cp {
            font-size: 20px; font-weight: 900; letter-spacing: .5px;
            color: var(--text);
            line-height: 1;
            white-space: nowrap;
        }

        /* "Players" — fades in when expanded */
        .brand-text {
            font-size: 20px; font-weight: 800; letter-spacing: .5px;
            color: var(--text);
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity var(--sb-speed) ease, transform var(--sb-speed) ease;
            white-space: nowrap;
        }
        .brand-text span { color: var(--accent); }
        #sidebar.expanded .brand-text {
            opacity: 1;
            transform: translateX(0);
        }

        /* NAV ITEMS */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0;
            height: 52px;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition: background var(--sb-speed) ease,
                        border-color var(--sb-speed) ease,
                        color var(--sb-speed) ease;
            overflow: hidden;
            white-space: nowrap;
            color: var(--muted);
            outline: none;
        }
        .nav-item.active {
            color: var(--accent);
            border-left-color: var(--accent);
            background: rgba(229,9,20,0.08);
        }
        .nav-icon {
            flex-shrink: 0;
            width: 65px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            transition: color var(--sb-speed) ease;
        }
        .nav-label {
            font-size: 14px; font-weight: 600;
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity var(--sb-speed) ease, transform var(--sb-speed) ease;
            white-space: nowrap;
        }
        #sidebar.expanded .nav-label {
            opacity: 1;
            transform: translateX(0);
        }

        /* Keyboard focus highlight — works collapsed + expanded */
        .nav-item.focused {
            background: rgba(245,166,35,0.15);
            border-left-color: var(--focus-color);
            color: var(--focus-color);
            box-shadow: inset 0 0 0 2px rgba(245,166,35,0.5);
        }
        .nav-item.focused .nav-icon {
            color: var(--focus-color);
        }

        /* Hover on sidebar items when expanded */
        #sidebar.expanded .nav-item:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text);
        }
        #sidebar.expanded .nav-item.active:hover {
            background: rgba(229,9,20,0.12);
        }

        /* ── MAIN CONTENT ── */
        #main {
            margin-left: var(--sidebar-mini);   /* matches collapsed sidebar */
            height: 100vh;
            overflow: hidden;
            transition: margin-left var(--sb-speed) cubic-bezier(.4,0,.2,1);
        }
        body.sidebar-expanded #main {
            margin-left: var(--sidebar-full);
        }

        /* ── SCREEN 1: HOME ── full-height flex */
        #screen-home {
            display: flex !important;
            flex-direction: column;
            height: 100vh;
        }
        #screen-home.hidden { display: none !important; }

        /* Screen 2 — same flex-column structure as Screen 1 */
        #screen-channels {
            display: none;
            flex-direction: column;
            height: 100vh;
        }
        #screen-channels.visible { display: flex; }

        /* SLIDER — grows to fill remaining space */
        #slider-wrap {
            position: relative;
            width: 100%;
            flex: 1 1 0;
            min-height: 0;
            overflow: hidden;
            background: #000;
        }
        .slide {
            position: absolute; inset: 0;
            opacity: 0;
            transition: opacity .5s ease;
        }
        .slide.active { opacity: 1; }
        .slide-img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }
        .slide-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.2) 60%, transparent 100%),
                        linear-gradient(0deg, rgba(0,0,0,0.65) 0%, transparent 50%);
        }
        .slide-info {
            position: absolute;
            bottom: 48px; left: 40px;
            max-width: 520px;
        }
        .slide-title {
            font-size: 32px; font-weight: 800;
            line-height: 1.2; margin-bottom: 10px;
            text-shadow: 0 2px 8px rgba(0,0,0,.6);
        }
        .slide-dots {
            position: absolute;
            bottom: 18px; left: 50%;
            transform: translateX(-50%);
            display: flex; gap: 8px;
        }
        .slide-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transition: background .2s, width .2s;
        }
        .slide-dot.active {
            background: var(--accent);
            width: 22px;
            border-radius: 4px;
        }

        /* SLIDER PLACEHOLDER */
        .slider-placeholder {
            width: 100%; height: 100%;
            background: linear-gradient(135deg, #dde3ed, #c8d0de);
            display: flex; align-items: center; justify-content: center;
            color: #8a96a8; font-size: 15px;
        }

        /* LANG BOTTOM AREA — fixed height */
        #lang-bottom {
            flex: 0 0 var(--lang-row-h);
            height: var(--lang-row-h);
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05);
        }

        /* SECTION TITLE */
        .section-title {
            padding: 10px 32px 8px;
            font-size: 16px; font-weight: 700;
            letter-spacing: .3px;
            flex-shrink: 0;
        }
        .section-title span { color: var(--muted); font-size: 13px; font-weight: 400; margin-left: 8px; }

        /* LANGUAGE ROW */
        #lang-row-outer {
            flex: 1 1 0;
            min-height: 0;
            overflow: hidden;
        }
        #lang-row {
            display: flex;
            gap: 16px;
            padding: 8px 40px;
            overflow-x: auto;
            height: 100%;
            align-items: center;
            scroll-behavior: smooth;
        }
        #lang-row::-webkit-scrollbar { height: 4px; }
        #lang-row::-webkit-scrollbar-track { background: transparent; }
        #lang-row::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        .lang-card {
            flex: 0 0 auto;
            width: 148px;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            border-radius: var(--radius);
            padding: 14px 12px 12px;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, transform .18s, box-shadow .15s;
            outline: none;
            transform: scale(1);
        }
        .lang-card.focused,
        .lang-card:focus {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring), 0 6px 24px rgba(245,166,35,0.35);
            transform: var(--focus-scale);
            z-index: 5;
        }
        .lang-card {
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .lang-card img {
            width: 56px; height: 56px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 8px;
            background: rgba(0,0,0,0.05);
        }
        .lang-card .lang-flag-placeholder {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin: 0 auto 10px;
        }
        .lang-card .lang-name {
            font-size: 14px; font-weight: 600;
            color: var(--text);
        }

        /* LOADING STATE */
        .skeleton-row {
            display: flex; gap: 16px;
            padding: 0 32px 36px;
        }
        .skeleton {
            border-radius: var(--radius);
            background: linear-gradient(90deg, rgba(0,0,0,0.06) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.06) 100%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton-lang { width: 148px; height: 140px; flex: 0 0 auto; }

        /* SCREEN 2 BANNER — grows to fill remaining space, mirrors #slider-wrap */
        #ch-banner {
            position: relative;
            width: 100%;
            flex: 1 1 0;
            min-height: 0;
            background: #1a1a28;   /* dark fallback when no slider */
            overflow: hidden;
        }

        /* Slider fills the entire banner */
        #ch-slider-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
            background: #000;
        }

        /* Overlay: back btn + lang header sit ON TOP of the banner image */
        #ch-banner-overlay {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 18px 28px 22px;
            background: linear-gradient(180deg,
                rgba(0,0,0,0.60) 0%,
                rgba(0,0,0,0.05) 45%,
                rgba(0,0,0,0.55) 100%);
            pointer-events: none;   /* let clicks pass through to slider */
        }
        #ch-banner-overlay > * { pointer-events: auto; }    /* re-enable for children */

        /* SCREEN 2 BOTTOM — fixed height, mirrors #lang-bottom */
        #ch-bottom {
            flex: 0 0 var(--ch-bottom-h);
            height: var(--ch-bottom-h);
            background: #ffffff;
            display: flex;
            flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* GENRE CHIPS */
        #genre-bar-outer {
            overflow: hidden;
            flex-shrink: 0;
        }
        #genre-bar {
            display: flex;
            gap: 10px;
            padding: 14px 28px 10px;
            overflow-x: auto;
            flex-wrap: nowrap;
        }
        #genre-bar::-webkit-scrollbar { display: none; }

        .genre-chip {
            flex: 0 0 auto;
            padding: 9px 20px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.12);
            color: var(--muted);
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            outline: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            transition: background .12s, color .12s, border-color .12s, box-shadow .12s, transform .18s;
            transform: scale(1);
        }
        /* selected genre — solid red fill */
        .genre-chip.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(229,9,20,0.3);
        }
        /* keyboard cursor */
        .genre-chip.focused {
            border-color: var(--focus-color);
            color: var(--text);
            background: #fff;
            box-shadow: var(--focus-ring);
            transform: var(--focus-scale);
            z-index: 5;
        }
        /* cursor is ON the selected/active chip */
        .genre-chip.active.focused {
            background: var(--accent);
            border-color: var(--focus-color);
            color: #fff;
            box-shadow: var(--focus-ring);
            transform: var(--focus-scale);
        }

        /* CHANNEL ROW — single horizontal scroll row */
        #channel-grid-outer {
            flex: 1 1 0;
            min-height: 0;
            overflow: hidden;
        }
        #channel-grid {
            display: flex;
            flex-direction: row;
            gap: 14px;
            padding: 8px 40px;
            overflow-x: auto;
            height: 100%;
            align-items: center;
            scroll-behavior: smooth;
        }
        #channel-grid::-webkit-scrollbar { height: 4px; }
        #channel-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        .ch-card {
            flex: 0 0 160px;
            width: 160px;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            border-radius: var(--radius);
            padding: 18px 12px 14px;
            text-align: center;
            cursor: pointer;
            outline: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: border-color .15s, transform .18s, box-shadow .15s;
            position: relative;
            transform: scale(1);
        }
        .ch-card.focused,
        .ch-card:focus {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring), 0 6px 24px rgba(245,166,35,0.35);
            transform: var(--focus-scale);
            z-index: 5;
        }
        .ch-logo-wrap {
            width: 80px; height: 60px;
            margin: 0 auto 10px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            background: rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .ch-logo-wrap img {
            max-width: 100%; max-height: 100%;
            object-fit: contain;
        }
        .ch-logo-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); font-size: 11px;
        }
        .ch-name {
            font-size: 13px; font-weight: 600;
            color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .ch-num {
            font-size: 11px; color: var(--muted);
            margin-top: 4px;
        }
        .live-badge {
            position: absolute;
            top: 8px; right: 8px;
            background: var(--accent);
            color: #fff;
            font-size: 10px; font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            letter-spacing: .5px;
        }

        /* BACK BUTTON — sits on banner overlay, white style */
        #back-btn {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.45);
            color: #ffffff;
            font-size: 13px; font-weight: 600;
            cursor: pointer;
            outline: none;
            backdrop-filter: blur(6px);
            transition: background .12s, box-shadow .12s, border-color .12s;
        }
        #back-btn.focused, #back-btn:focus {
            background: rgba(245,166,35,0.9);
            border-color: #fff;
            color: #1a1d23;
            box-shadow: var(--focus-ring);
            transform: scale(1.04);
        }
        #back-btn svg { width: 16px; height: 16px; fill: currentColor; }

        /* LANG HEADER — bottom of banner overlay */
        .screen2-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .screen2-lang-logo {
            width: 44px; height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.6);
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        .screen2-lang-name {
            font-size: 26px; font-weight: 800;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        /* BOOT OVERLAY */
        #boot-overlay {
            position: fixed; inset: 0;
            background: rgba(240,244,248,0.97);
            z-index: 9999;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 16px;
            transition: opacity .25s;
        }
        #boot-overlay.hidden { opacity: 0; pointer-events: none; }
        .boot-spinner {
            width: 44px; height: 44px;
            border: 3px solid rgba(0,0,0,0.1);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        #boot-title { font-size: 22px; font-weight: 700; color: var(--text); }
        #boot-msg { font-size: 15px; color: var(--muted); text-align: center; max-width: 520px; }
        #boot-msg code { color: #0066cc; }

        /* TOAST */
        #toast {
            position: fixed;
            bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: rgba(30,35,50,0.92);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 14px;
            color: #ffffff;
            z-index: 9998;
            transition: transform .25s ease;
            pointer-events: none;
        }
        #toast.visible { transform: translateX(-50%) translateY(0); }

        /* PLAYBACK HUD */
        #playback-hud {
            position: fixed;
            bottom: 32px; left: calc(var(--sidebar-w) + 24px); right: 24px;
            z-index: 8888;
            opacity: 0; visibility: hidden;
            transition: opacity .2s, visibility .2s;
            pointer-events: none;
        }
        #playback-hud.visible { opacity: 1; visibility: visible; }
        .phud-card {
            display: inline-block;
            max-width: 520px;
            background: rgba(8,12,20,0.88);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 16px 20px;
            backdrop-filter: blur(10px);
        }
        .phud-label { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #7fc8f0; margin-bottom: 6px; }
        .phud-title { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .phud-meta  { font-size: 13px; color: var(--muted); margin-bottom: 8px; }
        .phud-help  { font-size: 12px; color: #6a80a0; }

        /* EMPTY STATE */
        .empty-state {
            padding: 60px 32px;
            text-align: center;
            color: var(--muted);
            font-size: 15px;
        }
        .empty-state h3 { font-size: 20px; color: var(--text); margin-bottom: 8px; }

        /* SCROLLBAR global */
        #main::-webkit-scrollbar { width: 5px; }
        #main::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 3px; }

        /* ── SCREEN OTT: same flex-column as home ── */
        #screen-ott {
            display: none;
            flex-direction: column;
            height: 100vh;
        }
        #screen-ott.visible { display: flex; }
        #ott-slider-wrap {
            flex: 1 1 0;
            min-height: 0;
            position: relative;
            overflow: hidden;
            background: #000;
        }
        #ott-bottom {
            flex: 0 0 var(--lang-row-h);
            height: var(--lang-row-h);
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05);
        }
        #ott-row-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #ott-row {
            display: flex; gap: 16px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #ott-row::-webkit-scrollbar { height: 4px; }
        #ott-row::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }
        .ott-net-card {
            flex: 0 0 148px; width: 148px;
            background: var(--card-bg); border: 2px solid var(--card-border);
            border-radius: var(--radius); padding: 14px 12px 12px;
            text-align: center; cursor: pointer; outline: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: border-color .15s, transform .18s, box-shadow .15s;
            transform: scale(1);
        }
        .ott-net-card.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring), 0 6px 24px rgba(245,166,35,0.35);
            transform: var(--focus-scale);
            z-index: 5;
        }
        .ott-net-card img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; margin-bottom: 8px; }
        .ott-net-card .net-ph {
            width: 56px; height: 56px; border-radius: 10px;
            background: linear-gradient(135deg,#e2e8f0,#cbd5e1);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: var(--muted); margin: 0 auto 10px;
        }
        .ott-net-card .net-name { font-size: 14px; font-weight: 600; color: var(--text); }

        /* ── SCREEN NETWORK: same flex-column as channels ── */
        #screen-network {
            display: none;
            flex-direction: column;
            height: 100vh;
        }
        #screen-network.visible { display: flex; }
        #net-banner { position: relative; width: 100%; flex: 1 1 0; min-height: 0; background: #1a1a28; overflow: hidden; }
        #net-slider-wrap { position: absolute; inset: 0; overflow: hidden; background: #000; }
        #net-banner-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 18px 28px 22px;
            background: linear-gradient(180deg, rgba(0,0,0,.60) 0%, rgba(0,0,0,.05) 45%, rgba(0,0,0,.55) 100%);
            pointer-events: none;
        }
        #net-banner-overlay > * { pointer-events: auto; }
        #net-bottom {
            flex: 0 0 var(--ch-bottom-h); height: var(--ch-bottom-h);
            background: #ffffff; display: flex; flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05); overflow: hidden;
        }
        #net-filter-outer { overflow: hidden; flex-shrink: 0; }
        #net-filter-bar {
            display: flex; gap: 10px; padding: 14px 28px 10px; overflow-x: auto; flex-wrap: nowrap;
        }
        #net-filter-bar::-webkit-scrollbar { display: none; }
        #net-content-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #net-content-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #net-content-grid::-webkit-scrollbar { height: 4px; }
        #net-content-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }
        .content-card {
            flex: 0 0 120px; width: 120px;
            background: var(--card-bg); border: 2px solid var(--card-border);
            border-radius: var(--radius); cursor: pointer; outline: none; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: border-color .15s, transform .18s, box-shadow .15s;
            position: relative;
            transform: scale(1);
        }
        .content-card.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring), 0 6px 24px rgba(245,166,35,0.35);
            transform: var(--focus-scale);
            z-index: 5;
        }
        .content-card-img { width: 100%; height: 88px; object-fit: cover; display: block; background: rgba(0,0,0,0.05); }
        .content-card-ph {
            width: 100%; height: 88px; background: linear-gradient(135deg,#dde3ed,#c8d0de);
            display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 24px;
        }
        .content-card-body { padding: 6px 8px 8px; }
        .content-card-title { font-size: 12px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .content-card-type { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .content-type-badge {
            position: absolute; top: 4px; right: 4px;
            background: rgba(0,0,0,0.55); color: #fff; font-size: 9px; font-weight: 700;
            padding: 2px 5px; border-radius: 3px; letter-spacing: .3px;
        }
        /* reuse back-btn look for net-back-btn */
        #net-back-btn {
            align-self: flex-start; display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 18px; border-radius: 999px;
            background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.45);
            color: #ffffff; font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none; backdrop-filter: blur(6px);
            transition: background .12s, box-shadow .12s, border-color .12s;
        }
        #net-back-btn.focused, #net-back-btn:focus {
            background: rgba(245,166,35,0.9);
            border-color: #fff;
            color: #1a1d23;
            box-shadow: var(--focus-ring);
            transform: scale(1.04);
        }
        #net-back-btn svg { width: 16px; height: 16px; fill: currentColor; }
        .screen3-header { display: flex; align-items: center; gap: 14px; }
        .screen3-net-logo { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 2px solid rgba(255,255,255,0.6); }
        .screen3-net-name { font-size: 26px; font-weight: 800; color: #ffffff; text-shadow: 0 2px 8px rgba(0,0,0,0.6); }

        /* ── SCREEN SERIES: two-panel ── */
        #screen-series {
            display: none;
            flex-direction: column;
            height: 100vh;
        }
        #screen-series.visible { display: flex; }
        #series-top {
            flex-shrink: 0; height: 56px; background: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            display: flex; align-items: center; gap: 16px; padding: 0 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        #series-title { font-size: 16px; font-weight: 700; color: var(--text); flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        #series-body { flex: 1 1 0; min-height: 0; display: flex; flex-direction: row; }
        #series-seasons-outer {
            width: 200px; flex-shrink: 0; background: #f8f9fb;
            border-right: 1px solid rgba(0,0,0,0.08); overflow-y: auto; padding: 12px 0;
        }
        #series-seasons-outer::-webkit-scrollbar { width: 4px; }
        #series-seasons-outer::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }
        .season-item {
            padding: 12px 18px; font-size: 14px; font-weight: 600; color: var(--muted);
            cursor: pointer; border-left: 3px solid transparent;
            transition: color .12s, background .12s, border-color .12s;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .season-item.active { color: var(--accent); border-left-color: var(--accent); background: rgba(229,9,20,0.07); }
        .season-item.focused {
            color: var(--focus-color);
            background: rgba(245,166,35,0.1);
            border-left-color: var(--focus-color);
            box-shadow: inset 0 0 0 2px rgba(245,166,35,0.4);
            outline: none;
        }
        .season-item.active.focused {
            color: var(--focus-color);
            border-left-color: var(--focus-color);
            background: rgba(245,166,35,0.15);
            box-shadow: inset 0 0 0 2px rgba(245,166,35,0.5);
        }
        #series-episodes-outer {
            flex: 1 1 0; min-height: 0; overflow-y: auto; padding: 12px 20px;
            background: var(--bg); display: flex; flex-direction: column; gap: 8px;
        }
        #series-episodes-outer::-webkit-scrollbar { width: 4px; }
        #series-episodes-outer::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }
        .episode-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px;
            background: var(--card-bg); border: 2px solid var(--card-border);
            border-radius: 10px; cursor: pointer; outline: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: border-color .12s, transform .18s, box-shadow .12s;
            transform: scale(1);
        }
        .episode-item.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring), 0 4px 16px rgba(245,166,35,0.3);
            transform: scale(1.02);
            z-index: 3;
        }
        .episode-thumb {
            width: 72px; height: 44px; border-radius: 6px; object-fit: cover; flex-shrink: 0; background: rgba(0,0,0,0.05);
        }
        .episode-thumb-ph {
            width: 72px; height: 44px; border-radius: 6px; flex-shrink: 0;
            background: linear-gradient(135deg,#dde3ed,#c8d0de);
            display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 18px;
        }
        .episode-info { flex: 1 1 0; min-width: 0; }
        .episode-title { font-size: 14px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .episode-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
        /* series back btn */
        #series-back-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.12);
            color: var(--text); font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none;
            transition: background .12s, box-shadow .12s;
            flex-shrink: 0;
        }
        #series-back-btn.focused, #series-back-btn:focus {
            background: rgba(245,166,35,0.9);
            border-color: var(--focus-color);
            color: #1a1d23;
            box-shadow: var(--focus-ring);
            transform: scale(1.04);
        }
        #series-back-btn svg { width: 16px; height: 16px; fill: currentColor; }

        /* ── SCREEN MOVIES ── */
        #screen-movies {
            display: none; flex-direction: column; height: 100vh;
        }
        #screen-movies.visible { display: flex; }

        /* Banner: slider + network chips on top */
        #mov-banner {
            position: relative; width: 100%; flex: 1 1 0; min-height: 0;
            background: #111; overflow: hidden;
        }
        #mov-slider-wrap { position: absolute; inset: 0; overflow: hidden; background: #000; }
        #mov-banner-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 14px 24px 18px;
            background: linear-gradient(180deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.04) 45%, rgba(0,0,0,.55) 100%);
            pointer-events: none;
        }
        #mov-banner-overlay > * { pointer-events: auto; }
        /* Network chips row inside banner */
        #mov-net-bar {
            display: flex; gap: 10px; overflow-x: auto; flex-wrap: nowrap; padding: 8px 0 4px;
        }
        #mov-net-bar::-webkit-scrollbar { display: none; }
        .mov-net-chip {
            flex: 0 0 auto; display: flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,0,0,0.55); border: 1.5px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none; backdrop-filter: blur(4px);
            transition: background .12s, border-color .12s, transform .18s, box-shadow .15s;
            transform: scale(1);
        }
        .mov-net-chip img { width: 22px; height: 22px; border-radius: 4px; object-fit: cover; }
        .mov-net-chip.active {
            background: rgba(229,9,20,0.85); border-color: var(--accent); color: #fff;
        }
        .mov-net-chip.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring);
            transform: scale(1.06); z-index: 5;
        }
        .mov-net-chip.active.focused {
            background: rgba(229,9,20,0.85); border-color: var(--focus-color);
            box-shadow: var(--focus-ring); transform: scale(1.06);
        }
        /* Bottom content area */
        #mov-bottom {
            flex: 0 0 var(--ch-bottom-h); height: var(--ch-bottom-h);
            background: #fff; display: flex; flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05); overflow: hidden;
        }
        #mov-genre-outer { overflow: hidden; flex-shrink: 0; }
        #mov-genre-bar {
            display: flex; gap: 10px; padding: 14px 28px 10px; overflow-x: auto; flex-wrap: nowrap;
        }
        #mov-genre-bar::-webkit-scrollbar { display: none; }
        #mov-content-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #mov-content-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #mov-content-grid::-webkit-scrollbar { height: 4px; }
        #mov-content-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        /* ── SCREEN WEB SERIES — same layout as movies ── */
        #screen-wseries {
            display: none; flex-direction: column; height: 100vh;
        }
        #screen-wseries.visible { display: flex; }
        #ws-banner { position: relative; width: 100%; flex: 1 1 0; min-height: 0; background: #111; overflow: hidden; }
        #ws-slider-wrap { position: absolute; inset: 0; overflow: hidden; background: #000; }
        #ws-banner-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 14px 24px 18px;
            background: linear-gradient(180deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.04) 45%, rgba(0,0,0,.55) 100%);
            pointer-events: none;
        }
        #ws-banner-overlay > * { pointer-events: auto; }
        #ws-net-bar { display: flex; gap: 10px; overflow-x: auto; flex-wrap: nowrap; padding: 8px 0 4px; }
        #ws-net-bar::-webkit-scrollbar { display: none; }
        .ws-net-chip {
            flex: 0 0 auto; display: flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,0,0,0.55); border: 1.5px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none; backdrop-filter: blur(4px);
            transition: background .12s, border-color .12s, transform .18s, box-shadow .15s;
            transform: scale(1);
        }
        .ws-net-chip img { width: 22px; height: 22px; border-radius: 4px; object-fit: cover; }
        .ws-net-chip.active {
            background: rgba(229,9,20,0.85); border-color: var(--accent); color: #fff;
        }
        .ws-net-chip.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring);
            transform: scale(1.06); z-index: 5;
        }
        .ws-net-chip.active.focused {
            background: rgba(229,9,20,0.85); border-color: var(--focus-color);
            box-shadow: var(--focus-ring); transform: scale(1.06);
        }
        #ws-bottom {
            flex: 0 0 var(--ch-bottom-h); height: var(--ch-bottom-h);
            background: #fff; display: flex; flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05); overflow: hidden;
        }
        #ws-genre-outer { overflow: hidden; flex-shrink: 0; }
        #ws-genre-bar { display: flex; gap: 10px; padding: 14px 28px 10px; overflow-x: auto; flex-wrap: nowrap; }
        #ws-genre-bar::-webkit-scrollbar { display: none; }
        #ws-content-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #ws-content-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #ws-content-grid::-webkit-scrollbar { height: 4px; }
        #ws-content-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        /* ── SCREEN TV SHOWS — same layout as movies/webseries ── */
        #screen-tvshows {
            display: none; flex-direction: column; height: 100vh;
        }
        #screen-tvshows.visible { display: flex; }
        #tv-banner { position: relative; width: 100%; flex: 1 1 0; min-height: 0; background: #111; overflow: hidden; }
        #tv-slider-wrap { position: absolute; inset: 0; overflow: hidden; background: #000; }
        #tv-banner-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 14px 24px 18px;
            background: linear-gradient(180deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.04) 45%, rgba(0,0,0,.55) 100%);
            pointer-events: none;
        }
        #tv-banner-overlay > * { pointer-events: auto; }
        #tv-net-bar { display: flex; gap: 10px; overflow-x: auto; flex-wrap: nowrap; padding: 8px 0 4px; }
        #tv-net-bar::-webkit-scrollbar { display: none; }
        .tv-net-chip {
            flex: 0 0 auto; display: flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,0,0,0.55); border: 1.5px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none; backdrop-filter: blur(4px);
            transition: background .12s, border-color .12s, transform .18s, box-shadow .15s;
            transform: scale(1);
        }
        .tv-net-chip img { width: 22px; height: 22px; border-radius: 4px; object-fit: cover; }
        .tv-net-chip.active {
            background: rgba(229,9,20,0.85); border-color: var(--accent); color: #fff;
        }
        .tv-net-chip.focused {
            border-color: var(--focus-color);
            box-shadow: var(--focus-ring);
            transform: scale(1.06); z-index: 5;
        }
        .tv-net-chip.active.focused {
            background: rgba(229,9,20,0.85); border-color: var(--focus-color);
            box-shadow: var(--focus-ring); transform: scale(1.06);
        }
        #tv-bottom {
            flex: 0 0 var(--ch-bottom-h); height: var(--ch-bottom-h);
            background: #fff; display: flex; flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05); overflow: hidden;
        }
        #tv-genre-outer { overflow: hidden; flex-shrink: 0; }
        #tv-genre-bar { display: flex; gap: 10px; padding: 14px 28px 10px; overflow-x: auto; flex-wrap: nowrap; }
        #tv-genre-bar::-webkit-scrollbar { display: none; }
        #tv-content-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #tv-content-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #tv-content-grid::-webkit-scrollbar { height: 4px; }
        #tv-content-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        /* ── SCREEN KIDS — same layout as TV Shows ── */
        #screen-kids {
            display: none; flex-direction: column; height: 100vh;
        }
        #screen-kids.visible { display: flex; }
        #kids-banner { position: relative; width: 100%; flex: 1 1 0; min-height: 0; background: #111; overflow: hidden; }
        #kids-slider-wrap { position: absolute; inset: 0; overflow: hidden; background: #000; }
        #kids-banner-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 14px 24px 18px;
            background: linear-gradient(180deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.04) 45%, rgba(0,0,0,.55) 100%);
            pointer-events: none;
        }
        #kids-banner-overlay > * { pointer-events: auto; }
        #kids-net-bar { display: flex; gap: 10px; overflow-x: auto; flex-wrap: nowrap; padding: 8px 0 4px; }
        #kids-net-bar::-webkit-scrollbar { display: none; }
        .kids-net-chip {
            flex: 0 0 auto; display: flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,0,0,0.55); border: 1.5px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600;
            cursor: pointer; outline: none; backdrop-filter: blur(4px);
            transition: background .12s, border-color .12s, transform .18s, box-shadow .15s;
            transform: scale(1);
        }
        .kids-net-chip img { width: 22px; height: 22px; border-radius: 4px; object-fit: cover; }
        .kids-net-chip.active {
            background: rgba(229,9,20,0.85); border-color: var(--accent); color: #fff;
        }
        .kids-net-chip.focused {
            border-color: var(--focus-color); box-shadow: var(--focus-ring);
            transform: scale(1.06); z-index: 5;
        }
        .kids-net-chip.active.focused {
            background: rgba(229,9,20,0.85); border-color: var(--focus-color);
            box-shadow: var(--focus-ring); transform: scale(1.06);
        }
        #kids-bottom {
            flex: 0 0 var(--ch-bottom-h); height: var(--ch-bottom-h);
            background: #fff; display: flex; flex-direction: column;
            border-top: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.05); overflow: hidden;
        }
        #kids-genre-outer { overflow: hidden; flex-shrink: 0; }
        #kids-genre-bar { display: flex; gap: 10px; padding: 14px 28px 10px; overflow-x: auto; flex-wrap: nowrap; }
        #kids-genre-bar::-webkit-scrollbar { display: none; }
        #kids-content-outer { flex: 1 1 0; min-height: 0; overflow: hidden; }
        #kids-content-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 8px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #kids-content-grid::-webkit-scrollbar { height: 4px; }
        #kids-content-grid::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.14); border-radius: 2px; }

        /* ── SCREEN SEARCH ── */
        #screen-search {
            display: none; flex-direction: column; height: 100vh;
            background: #0d1117;
        }
        #screen-search.visible { display: flex; }
        #srch-query-bar {
            flex: 0 0 auto; padding: 22px 40px 14px;
            background: #161b22; border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        #srch-query-display {
            display: flex; align-items: center; gap: 4px;
            background: rgba(255,255,255,0.06); border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 10px; padding: 10px 18px; min-height: 48px;
        }
        #srch-query-text {
            font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 2px;
            flex: 1; word-break: break-all;
        }
        .srch-cursor {
            display: inline-block; width: 2px; height: 26px;
            background: var(--focus-color); margin-left: 2px;
            animation: blink .8s steps(1) infinite;
        }
        @keyframes blink { 0%,50%{opacity:1} 51%,100%{opacity:0} }
        #srch-keyboard {
            flex: 0 0 auto; padding: 14px 30px 10px;
            display: flex; flex-direction: column; gap: 8px;
        }
        .srch-kb-row {
            display: flex; gap: 7px; justify-content: center;
        }
        .srch-key {
            flex: 0 0 auto; min-width: 46px; height: 46px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.08); border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 8px; color: #e8eaf0; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background .1s, border-color .15s, transform .15s, box-shadow .15s;
        }
        .srch-key.wide { min-width: 90px; font-size: 12px; letter-spacing: .5px; }
        .srch-key.del  { min-width: 60px; color: #f5a623; }
        .srch-key.focused {
            background: rgba(245,166,35,0.18); border-color: var(--focus-color);
            box-shadow: var(--focus-ring); transform: scale(1.08); z-index: 5; color: #fff;
        }
        #srch-results-outer {
            flex: 1 1 0; min-height: 0; overflow: hidden;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        #srch-results-grid {
            display: flex; flex-direction: row; gap: 14px; padding: 12px 40px;
            overflow-x: auto; height: 100%; align-items: center; scroll-behavior: smooth;
        }
        #srch-results-grid::-webkit-scrollbar { height: 4px; }
        #srch-results-grid::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.14); border-radius: 2px; }
        #srch-empty {
            color: rgba(255,255,255,0.35); font-size: 14px; padding: 20px 40px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div id="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <span class="brand-cp">CP</span>
        </div>
        <div class="brand-text"><span>Players</span></div>
    </div>
    <div class="nav-item" id="nav-search" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </div>
        <div class="nav-label">Search</div>
    </div>
    <div class="nav-item active" id="nav-livetv" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z"/></svg>
        </div>
        <div class="nav-label">Live TV</div>
    </div>
    <div class="nav-item" id="nav-ott" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>
        </div>
        <div class="nav-label">OTT Apps</div>
    </div>
    <div class="nav-item" id="nav-movies" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
        </div>
        <div class="nav-label">Movies</div>
    </div>
    <div class="nav-item" id="nav-wseries" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/></svg>
        </div>
        <div class="nav-label">Web Series</div>
    </div>
    <div class="nav-item" id="nav-tvshows" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.99-.9 1.99-2L23 5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z"/></svg>
        </div>
        <div class="nav-label">TV Shows</div>
    </div>
    <div class="nav-item" id="nav-kids" tabindex="0">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11.5 2C6.81 2 3 5.81 3 10.5S6.81 19 11.5 19h.5v3c4.86-2.34 8-7 8-11.5C20 5.81 16.19 2 11.5 2zm1 14.5h-2v-2h2v2zm0-4h-2c0-3.25 3-3 3-5 0-1.1-.9-2-2-2s-2 .9-2 2h-2c0-2.21 1.79-4 4-4s4 1.79 4 4c0 2.5-3 2.75-3 5z"/></svg>
        </div>
        <div class="nav-label">Kids</div>
    </div>
</div>

<!-- MAIN -->
<div id="main">

    <!-- SCREEN 1: HOME -->
    <div id="screen-home">
        <div id="slider-wrap">
            <div class="slider-placeholder" id="slider-placeholder">Loading featured content...</div>
        </div>

        <div id="lang-bottom">
            <div class="section-title" id="lang-section-title">Live Channels <span>Select a language to browse</span></div>
            <div id="lang-row-outer">
                <div id="lang-row">
                    <div class="skeleton-row" id="lang-skeleton" style="padding:0;display:flex;gap:16px;align-items:center;">
                        <div class="skeleton skeleton-lang"></div>
                        <div class="skeleton skeleton-lang"></div>
                        <div class="skeleton skeleton-lang"></div>
                        <div class="skeleton skeleton-lang"></div>
                        <div class="skeleton skeleton-lang"></div>
                    </div>
                </div>
            </div>
        </div><!-- /lang-bottom -->
    </div>

    <!-- SCREEN OTT: NETWORKS LIST -->
    <div id="screen-ott">
        <div id="ott-slider-wrap">
            <div class="slider-placeholder">OTT Apps</div>
        </div>
        <div id="ott-bottom">
            <div class="section-title" id="ott-section-title">OTT Apps <span>Select a network to browse</span></div>
            <div id="ott-row-outer">
                <div id="ott-row"></div>
            </div>
        </div>
    </div>

    <!-- SCREEN NETWORK: NETWORK DETAIL -->
    <div id="screen-network">
        <div id="net-banner">
            <div id="net-slider-wrap"></div>
            <div id="net-banner-overlay">
                <button id="net-back-btn" tabindex="0">
                    <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    Back
                </button>
                <div class="screen3-header" id="screen3-header"></div>
            </div>
        </div>
        <div id="net-bottom">
            <div id="net-filter-outer"><div id="net-filter-bar"></div></div>
            <div id="net-content-outer"><div id="net-content-grid"></div></div>
        </div>
    </div>

    <!-- SCREEN MOVIES -->
    <div id="screen-movies">
        <div id="mov-banner">
            <div id="mov-slider-wrap"></div>
            <div id="mov-banner-overlay">
                <div id="mov-net-bar"></div>
                <div style="color:#fff;font-size:22px;font-weight:800;text-shadow:0 2px 8px rgba(0,0,0,.6);" id="mov-banner-title">Movies</div>
            </div>
        </div>
        <div id="mov-bottom">
            <div id="mov-genre-outer"><div id="mov-genre-bar"></div></div>
            <div id="mov-content-outer"><div id="mov-content-grid"></div></div>
        </div>
    </div>

    <!-- SCREEN WEB SERIES -->
    <div id="screen-wseries">
        <div id="ws-banner">
            <div id="ws-slider-wrap"></div>
            <div id="ws-banner-overlay">
                <div id="ws-net-bar"></div>
                <div style="color:#fff;font-size:22px;font-weight:800;text-shadow:0 2px 8px rgba(0,0,0,.6);" id="ws-banner-title">Web Series</div>
            </div>
        </div>
        <div id="ws-bottom">
            <div id="ws-genre-outer"><div id="ws-genre-bar"></div></div>
            <div id="ws-content-outer"><div id="ws-content-grid"></div></div>
        </div>
    </div>

    <!-- SCREEN TV SHOWS -->
    <div id="screen-tvshows">
        <div id="tv-banner">
            <div id="tv-slider-wrap"></div>
            <div id="tv-banner-overlay">
                <div id="tv-net-bar"></div>
                <div style="color:#fff;font-size:22px;font-weight:800;text-shadow:0 2px 8px rgba(0,0,0,.6);" id="tv-banner-title">TV Shows</div>
            </div>
        </div>
        <div id="tv-bottom">
            <div id="tv-genre-outer"><div id="tv-genre-bar"></div></div>
            <div id="tv-content-outer"><div id="tv-content-grid"></div></div>
        </div>
    </div>

    <!-- SCREEN KIDS -->
    <div id="screen-kids">
        <div id="kids-banner">
            <div id="kids-slider-wrap"></div>
            <div id="kids-banner-overlay">
                <div id="kids-net-bar"></div>
                <div style="color:#fff;font-size:22px;font-weight:800;text-shadow:0 2px 8px rgba(0,0,0,.6);" id="kids-banner-title">Kids</div>
            </div>
        </div>
        <div id="kids-bottom">
            <div id="kids-genre-outer"><div id="kids-genre-bar"></div></div>
            <div id="kids-content-outer"><div id="kids-content-grid"></div></div>
        </div>
    </div>

    <!-- SCREEN SEARCH -->
    <div id="screen-search">
        <div id="srch-query-bar">
            <div id="srch-query-display">
                <span id="srch-query-text"></span>
                <span class="srch-cursor"></span>
            </div>
        </div>
        <div id="srch-keyboard"></div>
        <div id="srch-results-outer">
            <div id="srch-results-grid"></div>
        </div>
    </div>

    <!-- SCREEN SERIES: SEASONS + EPISODES -->
    <div id="screen-series">
        <div id="series-top">
            <button id="series-back-btn" tabindex="0">
                <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Back
            </button>
            <div id="series-title"></div>
        </div>
        <div id="series-body">
            <div id="series-seasons-outer"><div id="series-seasons"></div></div>
            <div id="series-episodes-outer"></div>
        </div>
    </div>

    <!-- SCREEN 2: CHANNELS -->
    <div id="screen-channels">

        <!-- Banner: slider image + back btn + lang title as overlay -->
        <div id="ch-banner">
            <div id="ch-slider-wrap"></div>
            <div id="ch-banner-overlay">
                <button id="back-btn" tabindex="0">
                    <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    Back
                </button>
                <div class="screen2-header" id="screen2-header"></div>
            </div>
        </div>

        <div id="ch-bottom">
            <div id="genre-bar-outer"><div id="genre-bar"></div></div>
            <div id="channel-grid-outer"><div id="channel-grid"></div></div>
        </div>

    </div>

</div>

<!-- BOOT OVERLAY -->
<div id="boot-overlay">
    <div class="boot-spinner"></div>
    <div id="boot-title">Starting CP Players</div>
    <div id="boot-msg">Detecting your MAG device&hellip;</div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- PLAYBACK HUD -->
<div id="playback-hud">
    <div class="phud-card">
        <div class="phud-label" id="phud-label">Now Playing</div>
        <div class="phud-title" id="phud-title">Loading&hellip;</div>
        <div class="phud-meta"  id="phud-meta"></div>
        <div class="phud-help"  id="phud-help">Back = stop &bull; OK = toggle info</div>
    </div>
</div>

<script>
window.MAG_PORTAL_CONFIG = {!! json_encode([
    'assetBaseUrl' => $assetBaseUrl,
    'loadUrl'      => $loadUrl,
    'apiBase'      => $magApiBase,
    'apiBaseUrl'   => $apiBaseUrl,
    'portalName'   => 'CP Players',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
</script>
<script src="{{ $assetBaseUrl }}/version.js"></script>
<script src="{{ $assetBaseUrl }}/xpcom.common.js"></script>
</body>
</html>
