<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CP Players TV</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #090b10;
            color: #111111;
            font-family: Arial, Helvetica, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body.playback-mode {
            background: #000000;
        }

        body.playback-mode #app,
        body.playback-mode #detail-overlay,
        body.playback-mode #keyboard-overlay {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        #app {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }

        #sidebar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 190px;
            background: #d9d9d6;
            border-right: 1px solid rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .brand-wrap {
            padding: 28px 24px 18px;
            text-align: center;
        }

        .brand-card {
            width: 96px;
            height: 96px;
            margin: 0 auto;
            border-radius: 20px;
            background: #11213f;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.18);
            padding-top: 25px;
        }

        .brand-title {
            color: #ffffff;
            font-size: 22px;
            font-weight: bold;
            line-height: 24px;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #d7deed;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        #menu-scroll {
            position: absolute;
            left: 18px;
            right: 18px;
            top: 150px;
            bottom: 70px;
            overflow: hidden;
        }

        #section-focus {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 18px;
            padding: 10px 12px;
            border-radius: 16px;
            background: rgba(17, 33, 63, 0.10);
            color: #11213f;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-item {
            position: relative;
            height: 56px;
            line-height: 56px;
            margin-bottom: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.42);
            color: #202020;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            border: 2px solid transparent;
            transition: background 0.16s ease, color 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .menu-item:before {
            content: '';
            position: absolute;
            left: 14px;
            top: 50%;
            width: 8px;
            height: 8px;
            margin-top: -4px;
            border-radius: 50%;
            background: transparent;
            opacity: 0;
            transition: background 0.16s ease, opacity 0.16s ease;
        }

        .menu-item.current,
        .menu-item.active {
            background: rgba(17, 33, 63, 0.10);
            color: #11213f;
            border-color: rgba(17, 33, 63, 0.10);
            box-shadow: inset 0 0 0 1px rgba(17, 33, 63, 0.08);
        }

        .menu-item.current:before,
        .menu-item.active:before {
            background: #11213f;
            opacity: 0.36;
        }

        .menu-item.focused {
            background: #000000;
            color: #ffffff;
            border-color: #000000;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.22);
        }

        .menu-item.focused:before {
            background: #ffffff;
            opacity: 1;
        }

        #main {
            position: absolute;
            left: 190px;
            right: 0;
            top: 0;
            bottom: 0;
            background: #efede7;
            overflow: hidden;
        }

        #network-shell {
            position: absolute;
            left: 30px;
            right: 30px;
            top: 18px;
            height: 58px;
            overflow: visible;
        }

        .shell-label {
            display: block;
            height: 16px;
            line-height: 16px;
            margin-bottom: 8px;
            color: #777264;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .focus-zone {
            position: relative;
        }

        .focus-zone:before {
            content: '';
            position: absolute;
            left: -4px;
            right: -4px;
            top: -4px;
            bottom: -4px;
            border-radius: 18px;
            border: 1px solid transparent;
            background: rgba(17, 33, 63, 0);
            box-shadow: none;
            opacity: 0;
            pointer-events: none;
            transition: border-color 0.16s ease, background 0.16s ease, opacity 0.16s ease;
        }

        .focus-zone.focused-zone:before {
            opacity: 0;
            border-color: transparent;
            background: transparent;
        }

        .focus-zone.focused-zone .shell-label {
            color: #11213f;
        }

        #items-shell.focus-zone:before {
            display: none;
        }

        #hero-shell {
            position: absolute;
            left: 30px;
            right: 30px;
            top: 88px;
            bottom: 43%;
            border-radius: 26px;
            overflow: hidden;
            background: #0f1218;
            box-shadow: 0 22px 34px rgba(0, 0, 0, 0.22);
        }

        #hero-backdrop {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background: #1d2230 center center no-repeat;
            background-size: cover;
        }

        .hero-shade {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.20);
        }

        .hero-gradient {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, rgba(7, 10, 18, 0.78) 0%, rgba(7, 10, 18, 0.34) 44%, rgba(7, 10, 18, 0.12) 68%, rgba(7, 10, 18, 0.35) 100%),
                linear-gradient(180deg, rgba(7, 10, 18, 0.12) 0%, rgba(7, 10, 18, 0.10) 54%, rgba(7, 10, 18, 0.92) 100%);
        }

        .hero-copy {
            position: absolute;
            left: 38px;
            right: 38px;
            bottom: 34px;
            color: #ffffff;
        }

        #hero-badge {
            display: inline-block;
            margin-bottom: 16px;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        #hero-title {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.08;
            font-weight: bold;
            letter-spacing: -0.6px;
            text-transform: uppercase;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.45);
        }

        #hero-subtitle {
            max-width: 720px;
            margin-bottom: 16px;
            color: rgba(255, 255, 255, 0.90);
            font-size: 18px;
            line-height: 26px;
        }

        .meta-row {
            white-space: nowrap;
            overflow: hidden;
            height: 34px;
        }

        .meta-chip {
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.4px;
        }

        .hero-dots {
            position: absolute;
            right: 34px;
            bottom: 26px;
            white-space: nowrap;
        }

        .hero-dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin-left: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.45);
        }

        .hero-dot.active {
            width: 22px;
            border-radius: 8px;
            background: #ffffff;
        }

        #content-shell {
            position: absolute;
            left: 30px;
            right: 30px;
            top: 58%;
            bottom: 16px;
            padding: 14px 0 0;
            overflow: hidden;
        }

        #section-bar {
            height: 44px;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
        }

        #section-label {
            display: inline-block;
            margin-right: 18px;
            color: #1f9bb0;
            font-size: 21px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        #section-context {
            display: inline-block;
            max-width: 58%;
            color: #212121;
            font-size: 17px;
            font-weight: bold;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #section-page {
            float: right;
            margin-top: 4px;
            color: #555555;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        #search-shell,
        #genre-shell,
        #channel-shell {
            margin-bottom: 12px;
            overflow: visible;
        }

        #search-shell {
            display: none;
        }

        .search-input {
            height: 52px;
            line-height: 52px;
            border-radius: 18px;
            padding: 0 18px;
            background: #ffffff;
            border: 2px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.08);
            color: #171717;
            font-size: 17px;
            font-weight: bold;
            transition: border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .search-input.focused {
            border-color: #11213f;
            box-shadow: 0 16px 30px rgba(17, 33, 63, 0.16);
        }

        .search-icon {
            display: inline-block;
            width: 28px;
            text-align: center;
            margin-right: 10px;
            color: #000000;
        }

        .chip-row {
            height: 44px;
            white-space: nowrap;
            overflow: hidden;
            padding: 3px 0 5px;
        }

        .filter-chip {
            position: relative;
            display: inline-block;
            vertical-align: top;
            min-width: 96px;
            max-width: 240px;
            height: 40px;
            line-height: 38px;
            padding: 0 18px;
            margin-right: 12px;
            border-radius: 20px;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.82);
            color: #1d1d1d;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: background 0.16s ease, color 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .filter-chip.active {
            background: rgba(255, 255, 255, 0.82);
            color: #11213f;
            border-color: transparent;
            box-shadow: none;
        }

        .filter-chip.focused {
            z-index: 4;
            border-color: #000000;
            background: #000000;
            color: #ffffff;
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.22);
        }

        #items-shell {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 132px;
            overflow: hidden;
        }

        #content-shell.no-channel-tabs #items-shell {
            top: 82px;
        }

        #content-shell.no-genre-tabs.no-channel-tabs #items-shell {
            top: 48px;
        }

        #items-row {
            height: 100%;
            padding: 14px 0 16px 8px;
            white-space: nowrap;
            overflow: hidden;
        }

        .media-card {
            position: relative;
            display: inline-block;
            vertical-align: top;
            width: 184px;
            height: 176px;
            margin-right: 18px;
            border-radius: 18px;
            overflow: hidden;
            background: #2f3542;
            border: 2px solid transparent;
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.16);
            transition: box-shadow 0.16s ease, border-color 0.16s ease, top 0.16s ease;
        }

        .media-card.focused {
            top: -2px;
            border-color: #11213f;
            box-shadow: 0 0 0 3px rgba(17, 33, 63, 0.12), 0 20px 30px rgba(0, 0, 0, 0.22);
            z-index: 3;
        }

        .media-card-thumb {
            height: 104px;
            background: #1b2130 center center no-repeat;
            background-size: cover;
        }

        .media-card-empty {
            height: 104px;
            background: #252b39;
            color: rgba(255, 255, 255, 0.72);
            font-size: 30px;
            line-height: 104px;
            text-align: center;
        }

        .media-card-body {
            height: 72px;
            padding: 10px 12px 12px;
            background: #ffffff;
            color: #151515;
        }

        .media-card-title {
            height: 36px;
            overflow: hidden;
            font-size: 15px;
            line-height: 18px;
            font-weight: bold;
        }

        .media-card-subtitle {
            margin-top: 5px;
            color: #6a6a6a;
            font-size: 12px;
            line-height: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .media-card-badge {
            position: absolute;
            right: 10px;
            top: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #21c28b;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .media-card.more-card {
            background: #2b3040;
        }

        .media-card.more-card .media-card-empty {
            background: #2b3040;
            color: #ffffff;
            font-size: 44px;
        }

        .media-card.more-card .media-card-body {
            background: #2b3040;
            color: #ffffff;
            text-align: center;
        }

        .media-card.more-card .media-card-subtitle {
            color: rgba(255, 255, 255, 0.76);
        }

        #empty-state {
            display: none;
            padding: 46px 24px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.62);
            color: #272727;
            font-size: 18px;
            line-height: 28px;
            text-align: center;
        }

        body.layout-short #menu-scroll {
            top: 136px;
            bottom: 62px;
        }

        body.layout-short #network-shell,
        body.layout-short #hero-shell,
        body.layout-short #content-shell {
            left: 22px;
            right: 22px;
        }

        body.layout-short .menu-item {
            height: 50px;
            line-height: 50px;
            margin-bottom: 10px;
            font-size: 15px;
        }

        body.layout-short #section-bar {
            height: 40px;
            margin-bottom: 6px;
        }

        body.layout-short #section-label {
            font-size: 19px;
        }

        body.layout-short #section-context {
            max-width: 52%;
            font-size: 15px;
        }

        body.layout-short #section-page,
        body.layout-short #section-focus {
            font-size: 11px;
        }

        body.layout-short #section-focus {
            left: 18px;
            right: 18px;
            bottom: 14px;
            padding: 9px 11px;
            border-radius: 15px;
        }

        body.layout-short #search-shell,
        body.layout-short #genre-shell,
        body.layout-short #channel-shell {
            margin-bottom: 8px;
        }

        body.layout-short .shell-label {
            height: 15px;
            line-height: 15px;
            font-size: 12px;
        }

        body.layout-short .search-input {
            height: 48px;
            line-height: 48px;
            font-size: 15px;
            border-radius: 16px;
        }

        body.layout-short .chip-row {
            height: 40px;
            padding: 3px 0 5px;
        }

        body.layout-short .filter-chip {
            min-width: 88px;
            max-width: 220px;
            height: 36px;
            line-height: 34px;
            padding: 0 17px;
            margin-right: 10px;
            border-radius: 18px;
            font-size: 13px;
        }

        body.layout-short #items-row {
            padding: 10px 0 12px 6px;
        }

        body.layout-short #content-shell.no-channel-tabs #items-shell {
            top: 76px;
        }

        body.layout-short #content-shell.no-genre-tabs.no-channel-tabs #items-shell {
            top: 44px;
        }

        body.layout-short .media-card {
            width: 170px;
            height: 156px;
            margin-right: 14px;
            border-radius: 16px;
        }

        body.layout-short .media-card-thumb,
        body.layout-short .media-card-empty {
            height: 90px;
            line-height: 90px;
        }

        body.layout-short .media-card-body {
            height: 66px;
            padding: 8px 10px 10px;
        }

        body.layout-short .media-card-title {
            height: 32px;
            font-size: 14px;
            line-height: 16px;
        }

        body.layout-short .media-card-subtitle {
            font-size: 11px;
            line-height: 14px;
        }

        body.layout-short .hero-copy {
            left: 30px;
            right: 30px;
            bottom: 24px;
        }

        body.layout-short #hero-title {
            font-size: 34px;
        }

        body.layout-short #hero-subtitle {
            max-width: 620px;
            font-size: 15px;
            line-height: 21px;
        }

        body.layout-short .meta-chip {
            padding: 7px 11px;
            font-size: 11px;
        }

        body.layout-tight #sidebar {
            width: 174px;
        }

        body.layout-tight #main {
            left: 174px;
        }

        body.layout-tight #detail-overlay {
            left: 174px;
        }

        body.layout-tight .brand-wrap {
            padding: 18px 18px 12px;
        }

        body.layout-tight .brand-card {
            width: 82px;
            height: 82px;
            border-radius: 18px;
            padding-top: 20px;
        }

        body.layout-tight .brand-title {
            font-size: 18px;
            line-height: 20px;
        }

        body.layout-tight .brand-subtitle {
            font-size: 11px;
        }

        body.layout-tight #menu-scroll {
            left: 14px;
            right: 14px;
            top: 116px;
            bottom: 54px;
        }

        body.layout-tight #network-shell,
        body.layout-tight #hero-shell,
        body.layout-tight #content-shell {
            left: 16px;
            right: 16px;
        }

        body.layout-tight .menu-item {
            height: 44px;
            line-height: 44px;
            margin-bottom: 8px;
            border-radius: 14px;
            font-size: 14px;
        }

        body.layout-tight #section-bar {
            height: 36px;
            margin-bottom: 4px;
        }

        body.layout-tight #section-label {
            margin-right: 12px;
            font-size: 17px;
        }

        body.layout-tight #section-context {
            max-width: 48%;
            font-size: 14px;
        }

        body.layout-tight #section-page,
        body.layout-tight #section-focus {
            font-size: 10px;
        }

        body.layout-tight #section-focus {
            left: 14px;
            right: 14px;
            bottom: 10px;
            padding: 8px 10px;
            border-radius: 14px;
        }

        body.layout-tight .hero-copy {
            left: 22px;
            right: 22px;
            bottom: 18px;
        }

        body.layout-tight #hero-badge {
            margin-bottom: 10px;
            padding: 6px 12px;
            font-size: 11px;
        }

        body.layout-tight #hero-title {
            font-size: 28px;
        }

        body.layout-tight #hero-subtitle {
            max-width: 520px;
            margin-bottom: 12px;
            font-size: 13px;
            line-height: 18px;
        }

        body.layout-tight .meta-chip {
            padding: 6px 10px;
            font-size: 10px;
        }

        body.layout-tight .hero-dot {
            width: 8px;
            height: 8px;
            margin-left: 6px;
        }

        body.layout-tight .hero-dot.active {
            width: 18px;
        }

        body.layout-tight #search-shell,
        body.layout-tight #genre-shell,
        body.layout-tight #channel-shell {
            margin-bottom: 6px;
        }

        body.layout-tight .shell-label {
            height: 14px;
            line-height: 14px;
            font-size: 11px;
        }

        body.layout-tight .search-input {
            height: 44px;
            line-height: 44px;
            font-size: 14px;
            border-radius: 16px;
        }

        body.layout-tight .chip-row {
            height: 38px;
            padding: 2px 0 4px;
        }

        body.layout-tight .filter-chip {
            min-width: 82px;
            max-width: 200px;
            height: 34px;
            line-height: 32px;
            padding: 0 16px;
            margin-right: 8px;
            border-radius: 17px;
            font-size: 12px;
        }

        body.layout-tight #items-row {
            padding: 8px 0 10px 4px;
        }

        body.layout-tight #content-shell.no-channel-tabs #items-shell {
            top: 70px;
        }

        body.layout-tight #content-shell.no-genre-tabs.no-channel-tabs #items-shell {
            top: 40px;
        }

        body.layout-tight .media-card {
            width: 150px;
            height: 142px;
            margin-right: 12px;
            border-radius: 15px;
        }

        body.layout-tight .media-card-thumb,
        body.layout-tight .media-card-empty {
            height: 80px;
            line-height: 80px;
        }

        body.layout-tight .media-card-body {
            height: 62px;
            padding: 8px 9px 10px;
        }

        body.layout-tight .media-card-title {
            height: 30px;
            font-size: 13px;
            line-height: 15px;
        }

        body.layout-tight .media-card-subtitle {
            font-size: 10px;
            line-height: 13px;
        }

        body.layout-tight #playback-hud {
            left: 22px;
        }

        #detail-overlay {
            position: absolute;
            left: 190px;
            right: 0;
            top: 0;
            bottom: 0;
            display: none;
            background: rgba(7, 10, 18, 0.96);
            z-index: 40;
            overflow: hidden;
        }

        #detail-overlay.visible {
            display: block;
        }

        #detail-hero {
            position: absolute;
            left: 32px;
            right: 32px;
            top: 24px;
            height: 250px;
            border-radius: 24px;
            overflow: hidden;
            background: #131926;
        }

        #detail-backdrop {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background: #131926 center center no-repeat;
            background-size: cover;
        }

        .detail-mask {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, rgba(7, 10, 18, 0.88) 0%, rgba(7, 10, 18, 0.48) 46%, rgba(7, 10, 18, 0.70) 100%),
                linear-gradient(180deg, rgba(7, 10, 18, 0.18) 0%, rgba(7, 10, 18, 0.96) 100%);
        }

        .detail-copy {
            position: absolute;
            left: 34px;
            right: 34px;
            bottom: 26px;
            color: #ffffff;
        }

        #detail-kicker {
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.86);
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        #detail-title {
            margin: 0 0 10px;
            font-size: 38px;
            line-height: 1.08;
            font-weight: bold;
            text-transform: uppercase;
        }

        #detail-plot {
            max-width: 900px;
            color: rgba(255, 255, 255, 0.92);
            font-size: 17px;
            line-height: 25px;
            max-height: 72px;
            overflow: hidden;
        }

        #detail-meta {
            margin-top: 14px;
            white-space: nowrap;
            overflow: hidden;
        }

        #detail-columns {
            position: absolute;
            left: 32px;
            right: 32px;
            top: 296px;
            bottom: 26px;
        }

        .detail-column {
            position: absolute;
            top: 0;
            bottom: 0;
            border-radius: 22px;
            background: rgba(12, 18, 29, 0.88);
            border: 2px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        #detail-seasons-wrap {
            left: 0;
            width: 38%;
        }

        #detail-episodes-wrap {
            right: 0;
            width: 59.5%;
        }

        .detail-column.focused {
            border-color: rgba(76, 196, 255, 0.62);
            box-shadow: 0 0 0 2px rgba(76, 196, 255, 0.16);
        }

        .detail-column-head {
            height: 64px;
            line-height: 64px;
            padding: 0 20px;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .detail-list {
            position: absolute;
            left: 0;
            right: 0;
            top: 64px;
            bottom: 0;
            padding: 0 16px 16px;
            overflow: hidden;
        }

        .detail-item {
            position: relative;
            min-height: 82px;
            margin-bottom: 14px;
            padding: 16px 16px 16px 108px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid transparent;
            color: #ffffff;
            transition: transform 0.16s ease, border-color 0.16s ease, background 0.16s ease, box-shadow 0.16s ease;
        }

        .detail-item.active {
            background: rgba(255, 255, 255, 0.10);
        }

        .detail-item.focused {
            border-color: #4cc4ff;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.22);
        }

        .detail-item-image {
            position: absolute;
            left: 14px;
            top: 14px;
            width: 78px;
            height: 54px;
            border-radius: 12px;
            background: #1f2532 center center no-repeat;
            background-size: cover;
        }

        .detail-item-title {
            font-size: 18px;
            line-height: 22px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .detail-item-subtitle {
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.70);
            font-size: 13px;
            line-height: 18px;
        }

        #keyboard-overlay {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            display: none;
            background: rgba(7, 10, 18, 0.94);
            z-index: 50;
        }

        #keyboard-overlay.visible {
            display: block;
        }

        #keyboard-panel {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 960px;
            height: 640px;
            margin-left: -480px;
            margin-top: -320px;
            border-radius: 28px;
            background: #111725;
            box-shadow: 0 26px 42px rgba(0, 0, 0, 0.38);
            overflow: hidden;
        }

        #keyboard-head {
            padding: 28px 30px 12px;
            color: #ffffff;
        }

        #keyboard-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.78);
        }

        #keyboard-query {
            margin-top: 12px;
            min-height: 62px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 2px solid rgba(255, 255, 255, 0.10);
            padding: 16px 20px;
            font-size: 24px;
            line-height: 30px;
            font-weight: bold;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #keyboard-help {
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.62);
            font-size: 13px;
            line-height: 18px;
        }

        #keyboard-grid {
            position: absolute;
            left: 26px;
            right: 26px;
            top: 170px;
            bottom: 26px;
        }

        .keyboard-row {
            height: 74px;
            margin-bottom: 12px;
            white-space: nowrap;
            overflow: hidden;
        }

        .keyboard-key {
            display: inline-block;
            vertical-align: top;
            width: 82px;
            height: 74px;
            line-height: 70px;
            margin-right: 10px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 2px solid transparent;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .keyboard-key.wide {
            width: 176px;
            font-size: 18px;
            letter-spacing: 0.8px;
        }

        .keyboard-key.xwide {
            width: 268px;
            font-size: 18px;
            letter-spacing: 0.8px;
        }

        .keyboard-key.focused {
            background: #ffffff;
            border-color: #4cc4ff;
            color: #111725;
            transform: scale(1.04);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
        }

        #boot-overlay {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 60;
            background: rgba(6, 10, 16, 0.96);
            color: #ffffff;
            text-align: center;
            padding-top: 16%;
        }

        .boot-spinner {
            width: 66px;
            height: 66px;
            margin: 0 auto 18px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.12);
            border-top-color: #ffffff;
            animation: mag-spin 1s linear infinite;
        }

        @keyframes mag-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #boot-title {
            margin-bottom: 10px;
            font-size: 34px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        #boot-msg {
            max-width: 720px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.82);
            font-size: 17px;
            line-height: 25px;
        }

        #toast {
            position: absolute;
            left: 50%;
            bottom: 26px;
            z-index: 70;
            display: none;
            min-width: 320px;
            max-width: 760px;
            margin-left: -160px;
            padding: 14px 22px;
            border-radius: 16px;
            background: rgba(10, 12, 18, 0.92);
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.28);
        }

        #playback-hud {
            position: absolute;
            left: 28px;
            bottom: 24px;
            z-index: 80;
            display: none;
            width: 520px;
        }

        #playback-hud.visible {
            display: block;
        }

        .hud-card {
            padding: 18px 20px;
            border-radius: 20px;
            background: rgba(8, 12, 18, 0.84);
            color: #ffffff;
            box-shadow: 0 22px 32px rgba(0, 0, 0, 0.30);
        }

        .hud-label {
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.68);
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .hud-title {
            font-size: 24px;
            line-height: 28px;
            font-weight: bold;
        }

        .hud-meta {
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.80);
            font-size: 14px;
            line-height: 20px;
        }

        .hud-help {
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.62);
            font-size: 12px;
            line-height: 18px;
        }
    </style>
</head>
<body>
<div id="app">
    <div id="sidebar">
        <div class="brand-wrap">
            <div class="brand-card">
                <div class="brand-title">CP</div>
                <div class="brand-subtitle">PLAYERS</div>
            </div>
        </div>
        <div id="menu-scroll">
            <div id="menu-list"></div>
        </div>
        <div id="section-focus">Focus: Menu</div>
    </div>

    <div id="main">
        <div id="network-shell" class="focus-zone">
            <div class="shell-label" id="networks-label">Networks</div>
            <div class="chip-row" id="network-row"></div>
        </div>

        <div id="hero-shell">
            <div id="hero-backdrop"></div>
            <div class="hero-shade"></div>
            <div class="hero-gradient"></div>
            <div class="hero-copy">
                <div id="hero-badge"></div>
                <div id="hero-title"></div>
                <div id="hero-subtitle"></div>
                <div class="meta-row" id="hero-meta"></div>
            </div>
            <div class="hero-dots" id="hero-dots"></div>
        </div>

        <div id="content-shell">
            <div id="section-bar">
                <div id="section-label">Live TV</div>
                <div id="section-context"></div>
                <div id="section-page"></div>
            </div>

            <div id="search-shell" class="focus-zone">
                <div class="search-input" id="search-input">
                    <span class="search-icon">&#128269;</span>
                    <span id="search-text">Search Live TV, movies, web series and more</span>
                </div>
            </div>

            <div id="genre-shell" class="focus-zone">
                <div class="shell-label" id="genre-label">Genres</div>
                <div class="chip-row" id="genre-row"></div>
            </div>

            <div id="channel-shell" class="focus-zone">
                <div class="shell-label" id="channel-label">Channels</div>
                <div class="chip-row" id="channel-row"></div>
            </div>

            <div id="items-shell" class="focus-zone">
                <div id="items-row"></div>
                <div id="empty-state"></div>
            </div>
        </div>
    </div>
</div>

<div id="detail-overlay">
    <div id="detail-hero">
        <div id="detail-backdrop"></div>
        <div class="detail-mask"></div>
        <div class="detail-copy">
            <div id="detail-kicker">Episodes</div>
            <div id="detail-title">Loading</div>
            <div id="detail-plot"></div>
            <div id="detail-meta"></div>
        </div>
    </div>

    <div id="detail-columns">
        <div class="detail-column" id="detail-seasons-wrap">
            <div class="detail-column-head">Seasons</div>
            <div class="detail-list" id="detail-seasons"></div>
        </div>
        <div class="detail-column" id="detail-episodes-wrap">
            <div class="detail-column-head">Episodes</div>
            <div class="detail-list" id="detail-episodes"></div>
        </div>
    </div>
</div>

<div id="keyboard-overlay">
    <div id="keyboard-panel">
        <div id="keyboard-head">
            <div id="keyboard-title">Universal Search</div>
            <div id="keyboard-query"></div>
            <div id="keyboard-help">Use Left, Right, Up and Down to move. Press OK to type. Search updates as you enter text. Back closes the keyboard.</div>
        </div>
        <div id="keyboard-grid"></div>
    </div>
</div>

<div id="boot-overlay">
    <div class="boot-spinner"></div>
    <div id="boot-title">Starting CP Players</div>
    <div id="boot-msg">Detecting your MAG device and loading your TV portal.</div>
</div>

<div id="toast"></div>

<div id="playback-hud">
    <div class="hud-card">
        <div class="hud-label" id="hud-label">Now Playing</div>
        <div class="hud-title" id="hud-title">Loading</div>
        <div class="hud-meta" id="hud-meta"></div>
        <div class="hud-help" id="hud-help">Back stops playback. OK toggles this panel. Up and Down switch live channels while playing.</div>
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
