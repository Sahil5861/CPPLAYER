(function (win, doc) {
    'use strict';

    var cfg   = win.MAG_PORTAL_CONFIG || {};
    var api   = cfg.apiBase    || '/mag/api';
    var capi  = cfg.apiBaseUrl || '/c/api';    // new REST API base
    var loadUrl = cfg.loadUrl  || '/mag/server/load.php';

    // ── STATE ──────────────────────────────────────────────────────────────
    var st = {
        mac:       '',
        token:     '',
        screen:    'home',          // 'home' | 'channels' | 'ott' | 'network' | 'series' | 'movies' | 'wseries' | 'tvshows' | 'kids' | 'search'
        // home
        sliders:   [],
        slideIdx:  0,
        slideTimer: null,
        languages: [],
        langFocus: 0,
        // channels screen
        activeLang:    null,        // {id, title, logo}
        langSliders:   [],
        chSlideIdx:    0,
        chSlideTimer:  null,
        genres:        [],
        genreFocus:    0,
        activeGenre:   '',
        channels:      [],
        chFocus:       0,
        chFocusZone:   'genres',    // 'back' | 'genres' | 'grid'
        // sidebar
        sidebarOpen:  false,
        // playback
        isPlaying:    false,
        playLabel:    '',
        playMeta:     '',
        playType:     '',
        playerMode:   '',
        hudTimer:     null,
        // OTT screen
        ottNetworks:  [],
        ottFocus:     0,
        ottSliders:   [],
        ottSlideIdx:  0,
        ottSlideTimer: null,
        // Network screen
        activeNetwork:  null,
        netRows:        [],         // [{id, title, accent, items:[]}]
        netSlides:      [],
        netSlideIdx:    0,
        netSlideTimer:  null,
        netFilterFocus: 0,
        netItemFocus:   0,
        netFocusZone:   'back',    // 'back' | 'filters' | 'grid'
        netRowIdx:      0,         // which row is active (maps to filter idx)
        // Web Series screen
        wsNetworks:     [],
        wsActiveNetIdx: 0,
        wsNetFocus:     0,
        wsGenres:       [],
        wsGenreFocus:   0,
        wsActiveGenre:  '',
        wsItems:        [],
        wsItemFocus:    0,
        wsFocusZone:    'networks', // 'networks' | 'genres' | 'grid'
        wsSliders:      [],
        wsSlideIdx:     0,
        wsSlideTimer:   null,
        // TV Shows screen
        tvNetworks:     [],
        tvActiveNetIdx: 0,
        tvNetFocus:     0,
        tvGenres:       [],
        tvGenreFocus:   0,
        tvActiveGenre:  '',
        tvItems:        [],
        tvItemFocus:    0,
        tvFocusZone:    'networks', // 'networks' | 'genres' | 'grid'
        tvSliders:      [],
        tvSlideIdx:     0,
        tvSlideTimer:   null,
        // Movies screen
        movNetworks:    [],
        movActiveNetIdx: 0,
        movNetFocus:    0,
        movGenres:      [],
        movGenreFocus:  0,
        movActiveGenre: '',
        movItems:       [],
        movItemFocus:   0,
        movFocusZone:   'networks', // 'networks' | 'genres' | 'grid'
        movSliders:     [],
        movSlideIdx:    0,
        movSlideTimer:  null,
        // Series screen
        serContent:     null,       // full content object with seasons
        serSeasonFocus: 0,
        serEpFocus:     0,
        serFocusZone:   'seasons',  // 'back' | 'seasons' | 'episodes'
        serBackScreen:  'network',  // which screen to go back to
        // Kids screen
        kidsNetworks:     [],
        kidsActiveNetIdx: 0,
        kidsNetFocus:     0,
        kidsGenres:       [],
        kidsGenreFocus:   0,
        kidsActiveGenre:  '',
        kidsItems:        [],
        kidsItemFocus:    0,
        kidsFocusZone:    'networks', // 'networks' | 'genres' | 'grid'
        kidsSliders:      [],
        kidsSlideIdx:     0,
        kidsSlideTimer:   null,
        // Search screen
        srchQuery:        '',
        srchResults:      [],
        srchItemFocus:    0,
        srchFocusZone:    'keyboard', // 'keyboard' | 'results'
        srchKbRow:        0,
        srchKbCol:        0,
        srchPage:         1,
        srchHasMore:      false,
        srchLoading:      false,
        srchTimer:        null,
    };

    // ── DOM CACHE ──────────────────────────────────────────────────────────
    var d = {};
    function $id(id) { return doc.getElementById(id); }

    function cacheDom() {
        d.boot          = $id('boot-overlay');
        d.bootTitle     = $id('boot-title');
        d.bootMsg       = $id('boot-msg');
        d.toast         = $id('toast');
        d.toastTimer    = null;
        // screen home
        d.screenHome    = $id('screen-home');
        d.sliderWrap    = $id('slider-wrap');
        d.sliderPH      = $id('slider-placeholder');
        d.langRow       = $id('lang-row');
        d.langSkeleton  = $id('lang-skeleton');
        // screen channels
        d.screenCh      = $id('screen-channels');
        d.chSliderWrap  = $id('ch-slider-wrap');
        d.backBtn       = $id('back-btn');
        if (d.backBtn) d.backBtn.onclick = function () { goHome(); };
        d.screen2Hdr    = $id('screen2-header');
        d.genreBar      = $id('genre-bar');
        d.chGrid        = $id('channel-grid');
        // screen ott
        d.screenOtt     = $id('screen-ott');
        d.ottSliderWrap = $id('ott-slider-wrap');
        d.ottRow        = $id('ott-row');
        // screen network
        d.screenNet     = $id('screen-network');
        d.netSliderWrap = $id('net-slider-wrap');
        d.netBackBtn    = $id('net-back-btn');
        if (d.netBackBtn) d.netBackBtn.onclick = function () { goOtt(); };
        d.screen3Hdr    = $id('screen3-header');
        d.netFilterBar  = $id('net-filter-bar');
        d.netContentGrid= $id('net-content-grid');
        // screen webseries
        d.screenWseries  = $id('screen-wseries');
        d.wsSliderWrap   = $id('ws-slider-wrap');
        d.wsNetBar       = $id('ws-net-bar');
        d.wsBannerTitle  = $id('ws-banner-title');
        d.wsGenreBar     = $id('ws-genre-bar');
        d.wsContentGrid  = $id('ws-content-grid');
        // screen movies
        d.screenMovies  = $id('screen-movies');
        d.movSliderWrap = $id('mov-slider-wrap');
        d.movNetBar     = $id('mov-net-bar');
        d.movBannerTitle= $id('mov-banner-title');
        d.movGenreBar   = $id('mov-genre-bar');
        d.movContentGrid= $id('mov-content-grid');
        // screen series
        d.screenSeries  = $id('screen-series');
        d.seriesTitle   = $id('series-title');
        d.seriesSeasons = $id('series-seasons');
        d.seriesEpisodes= $id('series-episodes-outer');
        d.seriesBackBtn = $id('series-back-btn');
        if (d.seriesBackBtn) d.seriesBackBtn.onclick = function () { goBackFromSeries(); };
        // sidebar / nav
        d.sidebar       = $id('sidebar');
        d.navLivetv     = $id('nav-livetv');
        d.navOtt        = $id('nav-ott');
        d.navMovies     = $id('nav-movies');
        d.navWseries    = $id('nav-wseries');
        d.navTvshows    = $id('nav-tvshows');
        // screen tvshows
        d.screenTvshows  = $id('screen-tvshows');
        d.tvSliderWrap   = $id('tv-slider-wrap');
        d.tvNetBar       = $id('tv-net-bar');
        d.tvBannerTitle  = $id('tv-banner-title');
        d.tvGenreBar     = $id('tv-genre-bar');
        d.tvContentGrid  = $id('tv-content-grid');
        // screen kids
        d.navKids        = $id('nav-kids');
        d.screenKids     = $id('screen-kids');
        d.kidsSliderWrap = $id('kids-slider-wrap');
        d.kidsNetBar     = $id('kids-net-bar');
        d.kidsBannerTitle= $id('kids-banner-title');
        d.kidsGenreBar   = $id('kids-genre-bar');
        d.kidsContentGrid= $id('kids-content-grid');
        // screen search
        d.navSearch      = $id('nav-search');
        d.screenSearch   = $id('screen-search');
        d.srchQueryText  = $id('srch-query-text');
        d.srchKeyboard   = $id('srch-keyboard');
        d.srchResultsGrid= $id('srch-results-grid');
        d.srchEmpty      = $id('srch-empty');
        if (d.navOtt)     d.navOtt.onclick     = function () { collapseSidebar(); openOtt(); };
        if (d.navMovies)  d.navMovies.onclick  = function () { collapseSidebar(); openMovies(); };
        if (d.navWseries) d.navWseries.onclick = function () { collapseSidebar(); openWseries(); };
        if (d.navTvshows) d.navTvshows.onclick = function () { collapseSidebar(); openTvshows(); };
        if (d.navKids)    d.navKids.onclick    = function () { collapseSidebar(); openKids(); };
        if (d.navSearch)  d.navSearch.onclick  = function () { collapseSidebar(); openSearch(); };
        d.phud          = $id('playback-hud');
        d.phudLabel     = $id('phud-label');
        d.phudTitle     = $id('phud-title');
        d.phudMeta      = $id('phud-meta');
        d.phudHelp      = $id('phud-help');
    }

    // ── BOOT ───────────────────────────────────────────────────────────────
    function boot() {
        cacheDom();
        bindKeys();
        setBoot('Starting CP Players', 'Detecting your MAG device\u2026');
        detectMac();
    }

    function detectMac() {
        var mac = readMacQuery() || readMacDevice();
        if (!mac) {
            setBoot(
                'MAC address required',
                'Open this page as <code>?mac=YOUR_DEVICE_MAC</code> in a browser or verify your MAG box is properly configured.'
            );
            return;
        }
        st.mac = norm(mac);
        setBoot('Authenticating', 'Connecting ' + esc(st.mac) + '\u2026');
        doHandshake();
    }

    function readMacQuery() {
        var m = String(win.location.search || '').match(/[?&]mac=([^&]+)/i);
        return m ? decodeURIComponent(m[1]) : '';
    }

    function readMacDevice() {
        var providers = [win.stb, win.gSTB, win.STB];
        var methods   = ['GetMacAddress','getMacAddress','GetMACAddress','getMacAddr'];
        for (var i = 0; i < providers.length; i++) {
            if (!providers[i]) continue;
            for (var j = 0; j < methods.length; j++) {
                if (typeof providers[i][methods[j]] === 'function') {
                    try { return providers[i][methods[j]](); } catch(e) {}
                }
            }
        }
        return '';
    }

    // ── HANDSHAKE ──────────────────────────────────────────────────────────
    function doHandshake() {
        magReq({ type: 'stb', action: 'handshake' }, function (js) {
            st.token = js.token || '';
            if (!st.token) { showError('Handshake did not return a portal token.'); return; }
            setBoot('Loading portal', 'Fetching home screen\u2026');
            loadHome();
        });
    }

    // ── HOME SCREEN ────────────────────────────────────────────────────────
    function loadHome() {
        var done = { slider: false, langs: false };
        function checkDone() {
            if (done.slider && done.langs) {
                hideBoot();
                renderHome();
            }
        }

        apiGet('/slider', function (res) {
            st.sliders = (res && res.data) ? toArr(res.data) : [];
            done.slider = true;
            checkDone();
        }, function () { done.slider = true; checkDone(); });

        apiGet('/languages', function (res) {
            st.languages = (res && res.languages) ? toArr(res.languages) : [];
            done.langs = true;
            checkDone();
        }, function () { done.langs = true; checkDone(); });
    }

    function renderHome() {
        showScreen('home');
        renderHomeSlider();
        renderLangRow();
    }

    function renderHomeSlider() {
        if (!st.sliders.length) {
            d.sliderWrap.innerHTML = '<div class="slider-placeholder">No featured content available.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.sliders.length; i++) {
            var s = st.sliders[i];
            var active = i === 0 ? ' active' : '';
            html += '<div class="slide' + active + '">' +
                    '<img class="slide-img" src="' + escAttr(s.banner) + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title) + '</div></div>' +
                    '</div>';
        }
        // dots
        html += '<div class="slide-dots" id="home-dots">' + renderDots(st.sliders.length, 0) + '</div>';
        d.sliderWrap.innerHTML = html;
        startSlideTimer();
    }

    function renderDots(count, active) {
        var h = '';
        for (var i = 0; i < count; i++) {
            h += '<div class="slide-dot' + (i === active ? ' active' : '') + '"></div>';
        }
        return h;
    }

    function startSlideTimer() {
        stopSlideTimer();
        if (st.sliders.length < 2) return;
        st.slideTimer = setInterval(function () {
            st.slideIdx = (st.slideIdx + 1) % st.sliders.length;
            updateSlide(d.sliderWrap, st.slideIdx, $id('home-dots'), st.sliders.length);
        }, 4500);
    }
    function stopSlideTimer() {
        if (st.slideTimer) { clearInterval(st.slideTimer); st.slideTimer = null; }
    }

    function updateSlide(wrap, idx, dotsEl, total) {
        var slides = wrap.querySelectorAll('.slide');
        for (var i = 0; i < slides.length; i++) {
            slides[i].className = 'slide' + (i === idx ? ' active' : '');
        }
        if (dotsEl) dotsEl.innerHTML = renderDots(total, idx);
    }

    function renderLangRow() {
        if (d.langSkeleton) d.langSkeleton.parentNode.removeChild(d.langSkeleton);
        d.langRow.innerHTML = '';
        if (!st.languages.length) {
            d.langRow.innerHTML = '<div style="padding:20px;color:#8a9bb8;">No languages available.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.languages.length; i++) {
            var l = st.languages[i];
            var initials = (l.title || '?').substring(0, 2).toUpperCase();
            var fbSvg = encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 56 56"><circle cx="28" cy="28" r="28" fill="#1e2d45"/><text x="28" y="33" font-size="18" font-weight="bold" text-anchor="middle" fill="#8a9bb8" font-family="Arial,sans-serif">' + initials + '</text></svg>');
            var fallback = 'data:image/svg+xml;charset=utf-8,' + fbSvg;
            var logoHtml = l.logo
                ? '<img src="' + escAttr(l.logo) + '" alt="" onerror="this.onerror=null;this.src=\'' + fallback + '\'">'
                : '<img src="' + fallback + '" alt="">';
            html += '<div class="lang-card" data-idx="' + i + '" tabindex="0">' +
                    logoHtml +
                    '<div class="lang-name">' + esc(l.title) + '</div>' +
                    '</div>';
        }
        d.langRow.innerHTML = html;
        // attach click
        var cards = d.langRow.querySelectorAll('.lang-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, idx) {
                card.onclick = function () { openLanguage(idx); };
            })(cards[k], k);
        }
        focusLangCard(0);
    }

    function focusLangCard(idx) {
        st.langFocus = idx;
        var cards = d.langRow.querySelectorAll('.lang-card');
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'lang-card' + (i === idx ? ' focused' : '');
        }
        if (idx === 0) {
            d.langRow.scrollLeft = 0;
        } else if (idx >= cards.length - 1) {
            d.langRow.scrollLeft = d.langRow.scrollWidth;
        } else if (cards[idx]) {
            scrollIntoView(cards[idx], d.langRow);
        }
    }

    // ── CHANNELS SCREEN ────────────────────────────────────────────────────
    function openLanguage(idx) {
        var lang = st.languages[idx];
        if (!lang) return;
        st.activeLang  = lang;
        st.activeGenre = '';
        st.genreFocus  = 0;
        st.chFocus     = 0;
        st.chFocusZone = 'genres';

        setBoot('Loading channels', 'Loading ' + lang.title + ' channels\u2026');
        stopSlideTimer();

        var done = { genres: false, channels: false };
        function checkDone() {
            if (done.genres && done.channels) {
                hideBoot();
                renderChannelsScreen();
            }
        }

        apiGet('/genres?language_id=' + lang.id, function (res) {
            st.genres = (res && res.data) ? toArr(res.data) : [];
            done.genres = true;
            checkDone();
        }, function () { st.genres = []; done.genres = true; checkDone(); });

        apiPost('/channels', { language_id: lang.id, genre: '' }, function (res) {
            st.channels     = (res && res.channels) ? toArr(res.channels) : [];
            st.langSliders  = (res && res.sliders)  ? toArr(res.sliders)  : [];
            done.channels   = true;
            checkDone();
        }, function () { st.channels = []; st.langSliders = []; done.channels = true; checkDone(); });
    }

    function renderChannelsScreen() {
        showScreen('channels');

        // Screen 2 header
        var lang = st.activeLang;
        var logoEl = lang.logo
            ? '<img class="screen2-lang-logo" src="' + escAttr(lang.logo) + '" alt="">'
            : '';
        d.screen2Hdr.innerHTML = logoEl +
            '<div class="screen2-lang-name">' + esc(lang.title) + '</div>';

        // Language sliders on top
        renderChSlider();

        // Genres
        renderGenreBar();

        // Channels
        renderChannelGrid();
    }

    function renderChSlider() {
        d.chSliderWrap.innerHTML = '';
        d.chSliderWrap.className = '';
        stopChSlideTimer();

        if (!st.langSliders.length) {
            return;
        }

        st.chSlideIdx = 0;
        var html = '';
        for (var i = 0; i < st.langSliders.length; i++) {
            var s = st.langSliders[i];
            var active = i === 0 ? ' active' : '';
            html += '<div class="slide' + active + '">' +
                    '<img class="slide-img" src="' + escAttr(s.banner) + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title) + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="ch-dots">' + renderDots(st.langSliders.length, 0) + '</div>';
        d.chSliderWrap.innerHTML = html;
        d.chSliderWrap.className = 'loaded';
        startChSlideTimer();
    }

    function startChSlideTimer() {
        stopChSlideTimer();
        if (st.langSliders.length < 2) return;
        st.chSlideTimer = setInterval(function () {
            st.chSlideIdx = (st.chSlideIdx + 1) % st.langSliders.length;
            updateSlide(d.chSliderWrap, st.chSlideIdx, $id('ch-dots'), st.langSliders.length);
        }, 4500);
    }
    function stopChSlideTimer() {
        if (st.chSlideTimer) { clearInterval(st.chSlideTimer); st.chSlideTimer = null; }
    }

    function renderGenreBar() {
        d.genreBar.innerHTML = '';
        if (!st.genres.length) return;
        var html = '';
        for (var i = 0; i < st.genres.length; i++) {
            var g = st.genres[i];
            var active = (g.title === 'All' && st.activeGenre === '') || g.title === st.activeGenre ? ' active' : '';
            html += '<div class="genre-chip' + active + '" data-genre="' + escAttr(g.title === 'All' ? '' : g.title) + '" data-idx="' + i + '">' +
                    esc(g.title) + '</div>';
        }
        d.genreBar.innerHTML = html;
        var chips = d.genreBar.querySelectorAll('.genre-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip) {
                chip.onclick = function () { selectGenre(chip.getAttribute('data-genre')); };
            })(chips[k]);
        }
        if (st.chFocusZone === 'genres') {
            focusGenreChip(st.genreFocus);
        }
    }

    function focusGenreChip(idx) {
        st.genreFocus = idx;
        var chips = d.genreBar.querySelectorAll('.genre-chip');
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(' focused','') + (i === idx ? ' focused' : '');
        }
        if (chips[idx]) scrollIntoView(chips[idx], d.genreBar);
    }

    function selectGenre(genre) {
        if (st.activeGenre === genre) return;
        st.activeGenre = genre;
        st.chFocus = 0;
        st.chFocusZone = 'grid';

        setBoot('Loading', 'Filtering channels\u2026');
        apiPost('/channels', { language_id: st.activeLang.id, genre: genre }, function (res) {
            st.channels = (res && res.channels) ? toArr(res.channels) : [];
            hideBoot();
            renderGenreBar();
            renderChannelGrid();
        }, function () { hideBoot(); showToast('Failed to filter channels.'); });
    }

    function renderChannelGrid() {
        d.chGrid.innerHTML = '';
        if (!st.channels.length) {
            d.chGrid.innerHTML = '<div class="empty-state"><h3>No Channels</h3>No channels found for this selection.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.channels.length; i++) {
            var ch = st.channels[i];
            var logoHtml = ch.logo
                ? '<img src="' + escAttr(ch.logo) + '" alt="" onerror="this.parentNode.innerHTML=\'<div class=ch-logo-placeholder>&#128250;</div>\'">'
                : '<div class="ch-logo-placeholder">&#128250;</div>';
            html += '<div class="ch-card" data-idx="' + i + '" tabindex="0">' +
                    '<span class="live-badge">LIVE</span>' +
                    '<div class="ch-logo-wrap">' + logoHtml + '</div>' +
                    '<div class="ch-name">' + esc(ch.name) + '</div>' +
                    '<div class="ch-num">Ch ' + esc(String(ch.number || '')) + '</div>' +
                    '</div>';
        }
        d.chGrid.innerHTML = html;
        var cards = d.chGrid.querySelectorAll('.ch-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, idx) {
                card.onclick = function () { openChannel(idx); };
            })(cards[k], k);
        }
        if (st.chFocusZone === 'grid') {
            focusChCard(st.chFocus >= 0 ? st.chFocus : 0);
        }
    }

    function focusChCard(idx) {
        st.chFocus = Math.max(0, Math.min(idx, st.channels.length - 1));
        var cards = d.chGrid.querySelectorAll('.ch-card');
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'ch-card' + (i === st.chFocus ? ' focused' : '');
        }
        if (st.chFocus === 0) {
            d.chGrid.scrollLeft = 0;
        } else if (st.chFocus >= cards.length - 1) {
            d.chGrid.scrollLeft = d.chGrid.scrollWidth;
        } else if (cards[st.chFocus]) {
            scrollIntoView(cards[st.chFocus], d.chGrid);
        }
    }

    function openChannel(idx) {
        var ch = st.channels[idx];
        if (!ch) return;
        st.chFocus = idx;
        createLink('itv', ch.cmd, ch.name);
    }

    // ── PLAYBACK ───────────────────────────────────────────────────────────
    function createLink(type, cmd, label) {
        st.playType  = type;
        st.playLabel = label || 'Stream';
        st.playMeta  = 'Live TV';
        setBoot('Opening stream', 'Creating secure playback URL\u2026');

        magReq({ type: type, action: 'create_link', cmd: cmd }, function (js) {
            var url = normCmd(js && js.cmd ? js.cmd : '');
            if (!url) { hideBoot(); showToast('Could not get playback URL.'); return; }
            hideBoot();
            playUrl(url);
        });
    }

    function playUrl(url) {
        stopPlaybackEngine();
        if (tryModernPlay(url)) return;
        if (tryLegacyPlay(url)) return;
        win.location.href = url;
    }

    function tryModernPlay(url) {
        var mgr = win.stbPlayerManager;
        var p   = mgr && mgr.list && mgr.list.length ? mgr.list[0] : null;
        if (!p || typeof p.play !== 'function') return false;
        try { setVideoTop(); } catch(e) {}
        try { if (typeof p.stop === 'function') p.stop(); } catch(e) {}
        try { p.aspectConversion = 5; } catch(e) {}
        try { p.videoWindowMode = 0; } catch(e) {}
        try { p.fullscreen = false; } catch(e) {}
        try { if (typeof p.setViewport === 'function') p.setViewport(getScreenViewport()); } catch(e) {}
        try { p.fullscreen = true; } catch(e) {}
        try {
            p.onPlayStart = function () { setVideoTop(); enterPlayback(); };
            p.onPlayError = function () { exitPlayback(); showToast('Stream could not start.'); };
            p.play({ solution: 'auto', uri: url });
            st.playerMode = 'modern';
            return true;
        } catch(e) { return false; }
    }

    function tryLegacyPlay(url) {
        var providers = [win.stb, win.gSTB, win.STB];
        var plays     = ['Play','play','Start','start','PlayUrl','playUrl','Open','open'];
        var stops     = ['Stop','stop'];
        var cmds      = [url, 'auto ' + url, 'ffrt ' + url, 'ffmpeg ' + url];
        for (var i = 0; i < providers.length; i++) {
            var p = providers[i];
            if (!p) continue;
            try { setVideoTop(); } catch(e) {}
            for (var j = 0; j < stops.length; j++) {
                if (typeof p[stops[j]] === 'function') try { p[stops[j]](); } catch(e) {}
            }
            if (typeof p.SetVideoScreen === 'function') {
                try { setLegacyVideoScreen(p); } catch(e) {}
            }
            for (var j = 0; j < plays.length; j++) {
                if (typeof p[plays[j]] === 'function') {
                    for (var k = 0; k < cmds.length; k++) {
                        try { p[plays[j]](cmds[k]); st.playerMode = 'legacy'; enterPlayback(); return true; } catch(e) {}
                    }
                }
            }
        }
        return false;
    }

    function getScreenViewport() {
        var w = 1280, h = 720;
        if (win.screen && win.screen.width)  { w = win.screen.width;  h = win.screen.height; }
        else if (win.innerWidth)             { w = win.innerWidth;    h = win.innerHeight; }
        return { x: 0, y: 0, width: w, height: h };
    }

    function setLegacyVideoScreen(p) {
        var vp = getScreenViewport();
        if (p && typeof p.SetVideoScreen === 'function') {
            p.SetVideoScreen(0, 0, vp.width - 1, vp.height - 1);
        }
    }

    function enterPlayback() {
        st.isPlaying = true;
        doc.body.classList.add('playback-mode');
        showHud(true);
    }

    function exitPlayback() {
        st.isPlaying = false;
        doc.body.classList.remove('playback-mode');
        hideHud();
        st.playerMode = '';
    }

    function stopPlayback() {
        stopPlaybackEngine();
        exitPlayback();
    }

    function stopPlaybackEngine() {
        var mgr = win.stbPlayerManager;
        var p   = mgr && mgr.list && mgr.list.length ? mgr.list[0] : null;
        if (p) {
            try { p.onPlayStart = null; p.onPlayError = null; } catch(e) {}
            try { if (typeof p.stop === 'function') p.stop(); } catch(e) {}
            try { p.videoWindowMode = 2; } catch(e) {}
            try { p.fullscreen = false; } catch(e) {}
        }
        var providers = [win.stb, win.gSTB, win.STB];
        for (var i = 0; i < providers.length; i++) {
            if (!providers[i]) continue;
            try { if (typeof providers[i].Stop === 'function') providers[i].Stop(); } catch(e) {}
            try { if (typeof providers[i].stop === 'function') providers[i].stop(); } catch(e) {}
        }
        try { if (win.gSTB && typeof win.gSTB.DeinitPlayer === 'function') win.gSTB.DeinitPlayer(); } catch(e) {}
        try { if (win.gSTB && typeof win.gSTB.SetVideoState === 'function') win.gSTB.SetVideoState(0); } catch(e) {}
        setGraphicTop();
    }

    // ── HUD ────────────────────────────────────────────────────────────────
    function showHud(persist) {
        d.phudLabel.innerHTML = esc(st.playType === 'vod' ? 'Now Playing Movie' : 'Now Playing Live TV');
        d.phudTitle.innerHTML = esc(st.playLabel);
        d.phudMeta.innerHTML  = esc(st.playMeta || '');
        d.phudHelp.innerHTML  = st.playType === 'itv'
            ? 'Up/Down = prev/next channel &bull; Back = stop &bull; OK = toggle info'
            : 'Back = stop &bull; OK = toggle info';
        d.phud.className = 'visible';
        clearHudTimer();
        if (!persist) {
            st.hudTimer = setTimeout(function () { if (st.isPlaying) hideHud(); }, 4000);
        }
    }

    function hideHud() {
        clearHudTimer();
        d.phud.className = '';
    }

    function toggleHud() {
        if (d.phud.className.indexOf('visible') !== -1) hideHud();
        else showHud(true);
    }

    function clearHudTimer() {
        if (st.hudTimer) { clearTimeout(st.hudTimer); st.hudTimer = null; }
    }

    // ── SIDEBAR ────────────────────────────────────────────────────────────
    function expandSidebar() {
        if (st.sidebarOpen) return;
        st.sidebarOpen = true;
        if (d.sidebar) d.sidebar.classList.add('expanded');
        doc.body.classList.add('sidebar-expanded');
        // highlight active nav item
        var activeNav = (st.screen === 'movies')  ? d.navMovies
                      : (st.screen === 'wseries') ? d.navWseries
                      : (st.screen === 'tvshows') ? d.navTvshows
                      : (st.screen === 'kids')    ? d.navKids
                      : (st.screen === 'search')  ? d.navSearch
                      : (st.screen === 'ott' || st.screen === 'network' || st.screen === 'series') ? d.navOtt
                      : d.navLivetv;
        if (activeNav) activeNav.classList.add('focused');
    }

    function collapseSidebar() {
        if (!st.sidebarOpen) return;
        st.sidebarOpen = false;
        if (d.sidebar) d.sidebar.classList.remove('expanded');
        doc.body.classList.remove('sidebar-expanded');
        if (d.navSearch)  d.navSearch.classList.remove('focused');
        if (d.navLivetv)  d.navLivetv.classList.remove('focused');
        if (d.navOtt)     d.navOtt.classList.remove('focused');
        if (d.navMovies)  d.navMovies.classList.remove('focused');
        if (d.navWseries) d.navWseries.classList.remove('focused');
        if (d.navTvshows) d.navTvshows.classList.remove('focused');
        if (d.navKids)    d.navKids.classList.remove('focused');
    }

    // ── KEYBOARD ───────────────────────────────────────────────────────────
    function bindKeys() {
        doc.onkeydown = function (e) {
            var c = e && (e.which || e.keyCode);

            if (isBack(c)) {
                e.preventDefault();
                if (st.isPlaying) { stopPlayback(); return; }
                if (st.sidebarOpen) { collapseSidebar(); return; }
                if (st.screen === 'channels') { goHome(); return; }
                if (st.screen === 'ott') { goHome(); return; }
                if (st.screen === 'network') { goOtt(); return; }
                if (st.screen === 'series') { goBackFromSeries(); return; }
                if (st.screen === 'movies') { goHome(); return; }
                if (st.screen === 'wseries') { goHome(); return; }
                if (st.screen === 'tvshows') { goHome(); return; }
                if (st.screen === 'kids') { goHome(); return; }
                if (st.screen === 'search') { goHome(); return; }
            }

            if (isOk(c)) {
                e.preventDefault();
                if (st.isPlaying) { toggleHud(); return; }
                if (st.sidebarOpen) {
                    // handle nav item selection
                    var focusedNav = d.sidebar ? d.sidebar.querySelector('.nav-item.focused') : null;
                    if (focusedNav && focusedNav.id === 'nav-ott')     { collapseSidebar(); openOtt(); return; }
                    if (focusedNav && focusedNav.id === 'nav-livetv')  { collapseSidebar(); goHome(); return; }
                    if (focusedNav && focusedNav.id === 'nav-movies')  { collapseSidebar(); openMovies(); return; }
                    if (focusedNav && focusedNav.id === 'nav-wseries') { collapseSidebar(); openWseries(); return; }
                    if (focusedNav && focusedNav.id === 'nav-tvshows') { collapseSidebar(); openTvshows(); return; }
                    if (focusedNav && focusedNav.id === 'nav-kids')    { collapseSidebar(); openKids(); return; }
                    if (focusedNav && focusedNav.id === 'nav-search')  { collapseSidebar(); openSearch(); return; }
                    collapseSidebar();
                    return;
                }
                if (st.screen === 'home') {
                    openLanguage(st.langFocus);
                } else if (st.screen === 'channels') {
                    if (st.chFocusZone === 'grid') {
                        openChannel(st.chFocus);
                    } else if (st.chFocusZone === 'genres') {
                        var chips = d.genreBar.querySelectorAll('.genre-chip');
                        if (chips[st.genreFocus]) {
                            selectGenre(chips[st.genreFocus].getAttribute('data-genre'));
                        }
                    } else if (st.chFocusZone === 'back') {
                        goHome();
                    }
                } else if (st.screen === 'ott') {
                    openNetwork(st.ottFocus);
                } else if (st.screen === 'network') {
                    if (st.netFocusZone === 'grid') {
                        openContent(st.netItemFocus);
                    } else if (st.netFocusZone === 'filters') {
                        selectNetRow(st.netFilterFocus);
                    } else if (st.netFocusZone === 'back') {
                        goOtt();
                    }
                } else if (st.screen === 'series') {
                    if (st.serFocusZone === 'episodes') {
                        playEpisode(st.serSeasonFocus, st.serEpFocus);
                    } else if (st.serFocusZone === 'seasons') {
                        selectSeason(st.serSeasonFocus);
                        st.serFocusZone = 'episodes';
                        focusEpisode(0);
                    } else if (st.serFocusZone === 'back') {
                        goBackFromSeries();
                    }
                } else if (st.screen === 'movies') {
                    if (st.movFocusZone === 'networks') {
                        selectMovNetwork(st.movNetFocus);
                    } else if (st.movFocusZone === 'genres') {
                        // chip 0 = All (genre=''), chip 1..n = genres[idx-1]
                        var selGenre = st.movGenreFocus === 0 ? '' : (st.movGenres[st.movGenreFocus - 1] || '');
                        selectMovGenre(selGenre);
                    } else if (st.movFocusZone === 'grid') {
                        playMovItem(st.movItemFocus);
                    }
                } else if (st.screen === 'wseries') {
                    if (st.wsFocusZone === 'networks') {
                        selectWsNetwork(st.wsNetFocus);
                    } else if (st.wsFocusZone === 'genres') {
                        var selWsGenre = st.wsGenreFocus === 0 ? '' : (st.wsGenres[st.wsGenreFocus - 1] || '');
                        selectWsGenre(selWsGenre);
                    } else if (st.wsFocusZone === 'grid') {
                        openWsCard(st.wsItemFocus);
                    }
                } else if (st.screen === 'tvshows') {
                    if (st.tvFocusZone === 'networks') {
                        selectTvNetwork(st.tvNetFocus);
                    } else if (st.tvFocusZone === 'genres') {
                        var selTvGenre = st.tvGenreFocus === 0 ? '' : (st.tvGenres[st.tvGenreFocus - 1] || '');
                        selectTvGenre(selTvGenre);
                    } else if (st.tvFocusZone === 'grid') {
                        openTvCard(st.tvItemFocus);
                    }
                } else if (st.screen === 'kids') {
                    if (st.kidsFocusZone === 'networks') {
                        selectKidsNetwork(st.kidsNetFocus);
                    } else if (st.kidsFocusZone === 'genres') {
                        var selKidsGenre = st.kidsGenreFocus === 0 ? '' : (st.kidsGenres[st.kidsGenreFocus - 1] || '');
                        selectKidsGenre(selKidsGenre);
                    } else if (st.kidsFocusZone === 'grid') {
                        openKidsCard(st.kidsItemFocus);
                    }
                } else if (st.screen === 'search') {
                    if (st.srchFocusZone === 'keyboard') {
                        var kbRows = getSrchKbRows();
                        var row = kbRows[st.srchKbRow];
                        if (row) pressSrchKey(row[st.srchKbCol] || '');
                    } else if (st.srchFocusZone === 'results') {
                        openSrchResult(st.srchItemFocus);
                    }
                }
                return;
            }

            if (isUp(c)) {
                e.preventDefault();
                if (st.isPlaying && st.playType === 'itv') { changeChannel(-1); return; }
                if (st.sidebarOpen) {
                    // cycle nav up: Kids → TVShows → WSeries → Movies → OTT → LiveTV → Search
                    var fn = d.sidebar ? d.sidebar.querySelector('.nav-item.focused') : null;
                    if (fn && fn.id === 'nav-kids')    { fn.classList.remove('focused'); if (d.navTvshows) d.navTvshows.classList.add('focused'); return; }
                    if (fn && fn.id === 'nav-tvshows') { fn.classList.remove('focused'); if (d.navWseries) d.navWseries.classList.add('focused'); return; }
                    if (fn && fn.id === 'nav-wseries') { fn.classList.remove('focused'); if (d.navMovies) d.navMovies.classList.add('focused'); return; }
                    if (fn && fn.id === 'nav-movies')  { fn.classList.remove('focused'); if (d.navOtt) d.navOtt.classList.add('focused'); return; }
                    if (fn && fn.id === 'nav-ott')     { fn.classList.remove('focused'); if (d.navLivetv) d.navLivetv.classList.add('focused'); return; }
                    if (fn && fn.id === 'nav-livetv')  { fn.classList.remove('focused'); if (d.navSearch) d.navSearch.classList.add('focused'); }
                    return;
                }
                if (st.screen === 'channels') {
                    if (st.chFocusZone === 'grid') {
                        st.chFocusZone = 'genres';
                        focusChCard(-1);
                        focusGenreChip(st.genreFocus);
                    } else if (st.chFocusZone === 'genres') {
                        st.chFocusZone = 'back';
                        focusGenreChip(-1);
                        d.backBtn && d.backBtn.classList.add('focused');
                    }
                } else if (st.screen === 'network') {
                    if (st.netFocusZone === 'grid') {
                        st.netFocusZone = 'filters';
                        clearFocusInEl(d.netContentGrid, '.content-card', 'content-card');
                        focusNetFilter(st.netFilterFocus);
                    } else if (st.netFocusZone === 'filters') {
                        st.netFocusZone = 'back';
                        clearFocusInEl(d.netFilterBar, '.genre-chip', 'genre-chip');
                        d.netBackBtn && d.netBackBtn.classList.add('focused');
                    }
                } else if (st.screen === 'series') {
                    if (st.serFocusZone === 'seasons') {
                        var prevS = st.serSeasonFocus;
                        var newS  = Math.max(0, prevS - 1);
                        if (newS !== prevS) selectSeasonFocused(newS);
                    } else if (st.serFocusZone === 'episodes') {
                        focusEpisode(st.serEpFocus - 1);
                    }
                } else if (st.screen === 'movies') {
                    if (st.movFocusZone === 'grid') {
                        st.movFocusZone = 'genres';
                        clearFocusInEl(d.movContentGrid, '.content-card', 'content-card');
                        focusMovGenre(st.movGenreFocus);
                    } else if (st.movFocusZone === 'genres') {
                        st.movFocusZone = 'networks';
                        clearFocusInEl(d.movGenreBar, '.genre-chip', 'genre-chip');
                        focusMovNet(st.movNetFocus);
                    }
                } else if (st.screen === 'wseries') {
                    if (st.wsFocusZone === 'grid') {
                        st.wsFocusZone = 'genres';
                        clearFocusInEl(d.wsContentGrid, '.content-card', 'content-card');
                        focusWsGenre(st.wsGenreFocus);
                    } else if (st.wsFocusZone === 'genres') {
                        st.wsFocusZone = 'networks';
                        clearFocusInEl(d.wsGenreBar, '.genre-chip', 'genre-chip');
                        focusWsNet(st.wsNetFocus);
                    }
                } else if (st.screen === 'tvshows') {
                    if (st.tvFocusZone === 'grid') {
                        st.tvFocusZone = 'genres';
                        clearFocusInEl(d.tvContentGrid, '.content-card', 'content-card');
                        focusTvGenre(st.tvGenreFocus);
                    } else if (st.tvFocusZone === 'genres') {
                        st.tvFocusZone = 'networks';
                        clearFocusInEl(d.tvGenreBar, '.genre-chip', 'genre-chip');
                        focusTvNet(st.tvNetFocus);
                    }
                } else if (st.screen === 'kids') {
                    if (st.kidsFocusZone === 'grid') {
                        st.kidsFocusZone = 'genres';
                        clearFocusInEl(d.kidsContentGrid, '.content-card', 'content-card');
                        focusKidsGenre(st.kidsGenreFocus);
                    } else if (st.kidsFocusZone === 'genres') {
                        st.kidsFocusZone = 'networks';
                        clearFocusInEl(d.kidsGenreBar, '.genre-chip', 'genre-chip');
                        focusKidsNet(st.kidsNetFocus);
                    }
                } else if (st.screen === 'search') {
                    if (st.srchFocusZone === 'results') {
                        // move back to keyboard from results
                        st.srchFocusZone = 'keyboard';
                        clearFocusInEl(d.srchResultsGrid, '.content-card', 'content-card');
                        focusSrchKey(st.srchKbRow, st.srchKbCol);
                    } else if (st.srchFocusZone === 'keyboard') {
                        if (st.srchKbRow > 0) {
                            var kbRows0 = getSrchKbRows();
                            var newRow0 = st.srchKbRow - 1;
                            var newCol0 = Math.min(st.srchKbCol, (kbRows0[newRow0] || []).length - 1);
                            focusSrchKey(newRow0, newCol0);
                        }
                    }
                }
                return;
            }

            if (isDown(c)) {
                e.preventDefault();
                if (st.isPlaying && st.playType === 'itv') { changeChannel(1); return; }
                if (st.sidebarOpen) {
                    // cycle nav down: Search → LiveTV → OTT → Movies → WSeries → TVShows → Kids
                    var fn2 = d.sidebar ? d.sidebar.querySelector('.nav-item.focused') : null;
                    if (fn2 && fn2.id === 'nav-search')  { fn2.classList.remove('focused'); if (d.navLivetv) d.navLivetv.classList.add('focused'); return; }
                    if (fn2 && fn2.id === 'nav-livetv')  { fn2.classList.remove('focused'); if (d.navOtt) d.navOtt.classList.add('focused'); return; }
                    if (fn2 && fn2.id === 'nav-ott')     { fn2.classList.remove('focused'); if (d.navMovies) d.navMovies.classList.add('focused'); return; }
                    if (fn2 && fn2.id === 'nav-movies')  { fn2.classList.remove('focused'); if (d.navWseries) d.navWseries.classList.add('focused'); return; }
                    if (fn2 && fn2.id === 'nav-wseries') { fn2.classList.remove('focused'); if (d.navTvshows) d.navTvshows.classList.add('focused'); return; }
                    if (fn2 && fn2.id === 'nav-tvshows') { fn2.classList.remove('focused'); if (d.navKids) d.navKids.classList.add('focused'); }
                    return;
                }
                if (st.screen === 'channels') {
                    if (st.chFocusZone === 'back') {
                        st.chFocusZone = 'genres';
                        d.backBtn && d.backBtn.classList.remove('focused');
                        focusGenreChip(st.genreFocus);
                    } else if (st.chFocusZone === 'genres') {
                        st.chFocusZone = 'grid';
                        focusChCard(st.chFocus >= 0 ? st.chFocus : 0);
                    }
                } else if (st.screen === 'network') {
                    if (st.netFocusZone === 'back') {
                        st.netFocusZone = 'filters';
                        d.netBackBtn && d.netBackBtn.classList.remove('focused');
                        focusNetFilter(st.netFilterFocus);
                    } else if (st.netFocusZone === 'filters') {
                        st.netFocusZone = 'grid';
                        clearFocusInEl(d.netFilterBar, '.genre-chip', 'genre-chip');
                        focusNetItem(st.netItemFocus >= 0 ? st.netItemFocus : 0);
                    }
                } else if (st.screen === 'series') {
                    if (st.serFocusZone === 'seasons') {
                        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
                        var prevS2  = st.serSeasonFocus;
                        var newS2   = Math.min(prevS2 + 1, seasons.length - 1);
                        if (newS2 !== prevS2) selectSeasonFocused(newS2);
                    } else if (st.serFocusZone === 'episodes') {
                        focusEpisode(st.serEpFocus + 1);
                    }
                } else if (st.screen === 'movies') {
                    if (st.movFocusZone === 'networks') {
                        st.movFocusZone = 'genres';
                        clearFocusInEl(d.movNetBar, '.mov-net-chip', 'mov-net-chip');
                        focusMovGenre(st.movGenreFocus);
                    } else if (st.movFocusZone === 'genres') {
                        st.movFocusZone = 'grid';
                        clearFocusInEl(d.movGenreBar, '.genre-chip', 'genre-chip');
                        focusMovItem(st.movItemFocus >= 0 ? st.movItemFocus : 0);
                    }
                } else if (st.screen === 'wseries') {
                    if (st.wsFocusZone === 'networks') {
                        st.wsFocusZone = 'genres';
                        clearFocusInEl(d.wsNetBar, '.ws-net-chip', 'ws-net-chip');
                        focusWsGenre(st.wsGenreFocus);
                    } else if (st.wsFocusZone === 'genres') {
                        st.wsFocusZone = 'grid';
                        clearFocusInEl(d.wsGenreBar, '.genre-chip', 'genre-chip');
                        focusWsItem(st.wsItemFocus >= 0 ? st.wsItemFocus : 0);
                    }
                } else if (st.screen === 'tvshows') {
                    if (st.tvFocusZone === 'networks') {
                        st.tvFocusZone = 'genres';
                        clearFocusInEl(d.tvNetBar, '.tv-net-chip', 'tv-net-chip');
                        focusTvGenre(st.tvGenreFocus);
                    } else if (st.tvFocusZone === 'genres') {
                        st.tvFocusZone = 'grid';
                        clearFocusInEl(d.tvGenreBar, '.genre-chip', 'genre-chip');
                        focusTvItem(st.tvItemFocus >= 0 ? st.tvItemFocus : 0);
                    }
                } else if (st.screen === 'kids') {
                    if (st.kidsFocusZone === 'networks') {
                        st.kidsFocusZone = 'genres';
                        clearFocusInEl(d.kidsNetBar, '.kids-net-chip', 'kids-net-chip');
                        focusKidsGenre(st.kidsGenreFocus);
                    } else if (st.kidsFocusZone === 'genres') {
                        st.kidsFocusZone = 'grid';
                        clearFocusInEl(d.kidsGenreBar, '.genre-chip', 'genre-chip');
                        focusKidsItem(st.kidsItemFocus >= 0 ? st.kidsItemFocus : 0);
                    }
                } else if (st.screen === 'search') {
                    if (st.srchFocusZone === 'keyboard') {
                        var kbRows1 = getSrchKbRows();
                        if (st.srchKbRow < kbRows1.length - 1) {
                            var newRow1 = st.srchKbRow + 1;
                            var newCol1 = Math.min(st.srchKbCol, (kbRows1[newRow1] || []).length - 1);
                            focusSrchKey(newRow1, newCol1);
                        } else if (st.srchResults.length > 0) {
                            // move from keyboard to results
                            st.srchFocusZone = 'results';
                            clearFocusInEl(d.srchKeyboard, '.srch-key', 'srch-key');
                            focusSrchItem(st.srchItemFocus >= 0 ? st.srchItemFocus : 0);
                        }
                    } else if (st.srchFocusZone === 'results') {
                        focusSrchItem(st.srchItemFocus + 1);
                    }
                }
                return;
            }

            if (isLeft(c)) {
                e.preventDefault();
                if (st.isPlaying) { showHud(true); return; }
                if (st.sidebarOpen) { return; }
                if (st.screen === 'home') {
                    if (st.langFocus === 0) { expandSidebar(); return; }
                    focusLangCard(st.langFocus - 1);
                } else if (st.screen === 'channels') {
                    if (st.chFocusZone === 'genres') {
                        if (st.genreFocus === 0) { expandSidebar(); return; }
                        focusGenreChip(st.genreFocus - 1);
                    } else if (st.chFocusZone === 'grid') {
                        if (st.chFocus === 0) { expandSidebar(); return; }
                        focusChCard(st.chFocus - 1);
                    } else if (st.chFocusZone === 'back') {
                        expandSidebar(); return;
                    }
                } else if (st.screen === 'ott') {
                    if (st.ottFocus === 0) { expandSidebar(); return; }
                    focusOttCard(st.ottFocus - 1);
                } else if (st.screen === 'network') {
                    if (st.netFocusZone === 'filters') {
                        if (st.netFilterFocus === 0) { expandSidebar(); return; }
                        focusNetFilter(st.netFilterFocus - 1);
                    } else if (st.netFocusZone === 'grid') {
                        if (st.netItemFocus === 0) { expandSidebar(); return; }
                        focusNetItem(st.netItemFocus - 1);
                    } else if (st.netFocusZone === 'back') {
                        expandSidebar(); return;
                    }
                } else if (st.screen === 'series') {
                    if (st.serFocusZone === 'episodes') {
                        st.serFocusZone = 'seasons';
                        clearFocusInEl(d.seriesEpisodes, '.episode-item', 'episode-item');
                        focusSeason(st.serSeasonFocus);
                    } else if (st.serFocusZone === 'seasons' || st.serFocusZone === 'back') {
                        expandSidebar(); return;
                    }
                } else if (st.screen === 'movies') {
                    if (st.movFocusZone === 'networks') {
                        if (st.movNetFocus === 0) { expandSidebar(); return; }
                        selectMovNetwork(st.movNetFocus - 1);
                    } else if (st.movFocusZone === 'genres') {
                        if (st.movGenreFocus === 0) { expandSidebar(); return; }
                        focusMovGenre(st.movGenreFocus - 1);
                    } else if (st.movFocusZone === 'grid') {
                        if (st.movItemFocus === 0) { expandSidebar(); return; }
                        focusMovItem(st.movItemFocus - 1);
                    }
                } else if (st.screen === 'wseries') {
                    if (st.wsFocusZone === 'networks') {
                        if (st.wsNetFocus === 0) { expandSidebar(); return; }
                        selectWsNetwork(st.wsNetFocus - 1);
                    } else if (st.wsFocusZone === 'genres') {
                        if (st.wsGenreFocus === 0) { expandSidebar(); return; }
                        focusWsGenre(st.wsGenreFocus - 1);
                    } else if (st.wsFocusZone === 'grid') {
                        if (st.wsItemFocus === 0) { expandSidebar(); return; }
                        focusWsItem(st.wsItemFocus - 1);
                    }
                } else if (st.screen === 'tvshows') {
                    if (st.tvFocusZone === 'networks') {
                        if (st.tvNetFocus === 0) { expandSidebar(); return; }
                        selectTvNetwork(st.tvNetFocus - 1);
                    } else if (st.tvFocusZone === 'genres') {
                        if (st.tvGenreFocus === 0) { expandSidebar(); return; }
                        focusTvGenre(st.tvGenreFocus - 1);
                    } else if (st.tvFocusZone === 'grid') {
                        if (st.tvItemFocus === 0) { expandSidebar(); return; }
                        focusTvItem(st.tvItemFocus - 1);
                    }
                } else if (st.screen === 'kids') {
                    if (st.kidsFocusZone === 'networks') {
                        if (st.kidsNetFocus === 0) { expandSidebar(); return; }
                        selectKidsNetwork(st.kidsNetFocus - 1);
                    } else if (st.kidsFocusZone === 'genres') {
                        if (st.kidsGenreFocus === 0) { expandSidebar(); return; }
                        focusKidsGenre(st.kidsGenreFocus - 1);
                    } else if (st.kidsFocusZone === 'grid') {
                        if (st.kidsItemFocus === 0) { expandSidebar(); return; }
                        focusKidsItem(st.kidsItemFocus - 1);
                    }
                } else if (st.screen === 'search') {
                    if (st.srchFocusZone === 'keyboard') {
                        if (st.srchKbCol === 0) { expandSidebar(); return; }
                        focusSrchKey(st.srchKbRow, st.srchKbCol - 1);
                    } else if (st.srchFocusZone === 'results') {
                        if (st.srchItemFocus === 0) {
                            st.srchFocusZone = 'keyboard';
                            clearFocusInEl(d.srchResultsGrid, '.content-card', 'content-card');
                            focusSrchKey(st.srchKbRow, st.srchKbCol);
                            return;
                        }
                        focusSrchItem(st.srchItemFocus - 1);
                    }
                }
                return;
            }

            if (isRight(c)) {
                e.preventDefault();
                if (st.isPlaying) { showHud(true); return; }
                if (st.sidebarOpen) { collapseSidebar(); return; }
                if (st.screen === 'home') {
                    focusLangCard(Math.min(st.langFocus + 1, st.languages.length - 1));
                } else if (st.screen === 'channels') {
                    if (st.chFocusZone === 'genres') {
                        var chips = d.genreBar.querySelectorAll('.genre-chip');
                        focusGenreChip(Math.min(st.genreFocus + 1, chips.length - 1));
                    } else if (st.chFocusZone === 'grid') {
                        focusChCard(Math.min(st.chFocus + 1, st.channels.length - 1));
                    }
                } else if (st.screen === 'ott') {
                    focusOttCard(Math.min(st.ottFocus + 1, st.ottNetworks.length - 1));
                } else if (st.screen === 'network') {
                    if (st.netFocusZone === 'filters') {
                        var nchips = d.netFilterBar ? d.netFilterBar.querySelectorAll('.genre-chip') : [];
                        focusNetFilter(Math.min(st.netFilterFocus + 1, nchips.length - 1));
                    } else if (st.netFocusZone === 'grid') {
                        var row = st.netRows[st.netRowIdx];
                        var total = row ? toArr(row.items).length : 0;
                        focusNetItem(Math.min(st.netItemFocus + 1, total - 1));
                    }
                } else if (st.screen === 'series') {
                    if (st.serFocusZone === 'seasons') {
                        st.serFocusZone = 'episodes';
                        focusSeason(-1);
                        focusEpisode(st.serEpFocus >= 0 ? st.serEpFocus : 0);
                    }
                } else if (st.screen === 'movies') {
                    if (st.movFocusZone === 'networks') {
                        var mchips = d.movNetBar ? d.movNetBar.querySelectorAll('.mov-net-chip') : [];
                        var mNext = Math.min(st.movNetFocus + 1, mchips.length - 1);
                        if (mNext !== st.movNetFocus) selectMovNetwork(mNext);
                    } else if (st.movFocusZone === 'genres') {
                        focusMovGenre(Math.min(st.movGenreFocus + 1, st.movGenres.length)); // +1 for All chip
                    } else if (st.movFocusZone === 'grid') {
                        focusMovItem(Math.min(st.movItemFocus + 1, st.movItems.length - 1));
                    }
                } else if (st.screen === 'wseries') {
                    if (st.wsFocusZone === 'networks') {
                        var wchips = d.wsNetBar ? d.wsNetBar.querySelectorAll('.ws-net-chip') : [];
                        var wNext = Math.min(st.wsNetFocus + 1, wchips.length - 1);
                        if (wNext !== st.wsNetFocus) selectWsNetwork(wNext);
                    } else if (st.wsFocusZone === 'genres') {
                        focusWsGenre(Math.min(st.wsGenreFocus + 1, st.wsGenres.length)); // +1 for All chip
                    } else if (st.wsFocusZone === 'grid') {
                        focusWsItem(Math.min(st.wsItemFocus + 1, st.wsItems.length - 1));
                    }
                } else if (st.screen === 'tvshows') {
                    if (st.tvFocusZone === 'networks') {
                        var tvchips = d.tvNetBar ? d.tvNetBar.querySelectorAll('.tv-net-chip') : [];
                        var tvNext = Math.min(st.tvNetFocus + 1, tvchips.length - 1);
                        if (tvNext !== st.tvNetFocus) selectTvNetwork(tvNext);
                    } else if (st.tvFocusZone === 'genres') {
                        focusTvGenre(Math.min(st.tvGenreFocus + 1, st.tvGenres.length)); // +1 for All chip
                    } else if (st.tvFocusZone === 'grid') {
                        focusTvItem(Math.min(st.tvItemFocus + 1, st.tvItems.length - 1));
                    }
                } else if (st.screen === 'kids') {
                    if (st.kidsFocusZone === 'networks') {
                        var kidchips = d.kidsNetBar ? d.kidsNetBar.querySelectorAll('.kids-net-chip') : [];
                        var kidNext = Math.min(st.kidsNetFocus + 1, kidchips.length - 1);
                        if (kidNext !== st.kidsNetFocus) selectKidsNetwork(kidNext);
                    } else if (st.kidsFocusZone === 'genres') {
                        focusKidsGenre(Math.min(st.kidsGenreFocus + 1, st.kidsGenres.length)); // +1 for All chip
                    } else if (st.kidsFocusZone === 'grid') {
                        focusKidsItem(Math.min(st.kidsItemFocus + 1, st.kidsItems.length - 1));
                    }
                } else if (st.screen === 'search') {
                    if (st.srchFocusZone === 'keyboard') {
                        var kbRows2 = getSrchKbRows();
                        var row2 = kbRows2[st.srchKbRow] || [];
                        if (st.srchKbCol < row2.length - 1) {
                            focusSrchKey(st.srchKbRow, st.srchKbCol + 1);
                        }
                    } else if (st.srchFocusZone === 'results') {
                        focusSrchItem(Math.min(st.srchItemFocus + 1, st.srchResults.length - 1));
                    }
                }
                return;
            }

            if (isPageUp(c)) {
                e.preventDefault();
                if (st.isPlaying && st.playType === 'itv') { changeChannel(-1); return; }
                if (st.screen === 'channels' && st.chFocusZone === 'grid') {
                    focusChCard(Math.max(0, st.chFocus - getGridCols() * 3));
                }
                return;
            }

            if (isPageDown(c)) {
                e.preventDefault();
                if (st.isPlaying && st.playType === 'itv') { changeChannel(1); return; }
                if (st.screen === 'channels' && st.chFocusZone === 'grid') {
                    focusChCard(Math.min(st.channels.length - 1, st.chFocus + getGridCols() * 3));
                }
                return;
            }
        };
    }

    function changeChannel(dir) {
        var next = st.chFocus + dir;
        if (next < 0) next = st.channels.length - 1;
        if (next >= st.channels.length) next = 0;
        openChannel(next);
    }

    function getGridCols() {
        var grid = d.chGrid;
        if (!grid || !st.channels.length) return 1;
        var style = win.getComputedStyle(grid);
        var cols  = style.gridTemplateColumns.split(' ').length;
        return cols > 0 ? cols : 4;
    }

    // ── SCREEN SWITCHING ───────────────────────────────────────────────────
    function showScreen(name) {
        st.screen = name;
        // hide all
        d.screenHome.classList.add('hidden');
        d.screenCh.classList.remove('visible');
        if (d.screenOtt)    d.screenOtt.classList.remove('visible');
        if (d.screenNet)    d.screenNet.classList.remove('visible');
        if (d.screenSeries) d.screenSeries.classList.remove('visible');
        if (d.screenMovies)  d.screenMovies.classList.remove('visible');
        if (d.screenWseries) d.screenWseries.classList.remove('visible');
        if (d.screenTvshows) d.screenTvshows.classList.remove('visible');
        if (d.screenKids)    d.screenKids.classList.remove('visible');
        if (d.screenSearch)  d.screenSearch.classList.remove('visible');
        // show requested
        if (name === 'home') {
            d.screenHome.classList.remove('hidden');
            // update sidebar active states
            if (d.navLivetv) d.navLivetv.classList.add('active');
            if (d.navOtt)    d.navOtt.classList.remove('active');
        } else if (name === 'channels') {
            d.screenCh.classList.add('visible');
            d.screenCh.scrollTop = 0;
            if (d.navLivetv) d.navLivetv.classList.add('active');
            if (d.navOtt)    d.navOtt.classList.remove('active');
        } else if (name === 'ott') {
            if (d.screenOtt) d.screenOtt.classList.add('visible');
            if (d.navOtt)    d.navOtt.classList.add('active');
            if (d.navLivetv) d.navLivetv.classList.remove('active');
        } else if (name === 'network') {
            if (d.screenNet) d.screenNet.classList.add('visible');
            if (d.navOtt)    d.navOtt.classList.add('active');
            if (d.navLivetv) d.navLivetv.classList.remove('active');
        } else if (name === 'series') {
            if (d.screenSeries) d.screenSeries.classList.add('visible');
            if (d.navLivetv)  d.navLivetv.classList.remove('active');
            if (d.navMovies)  d.navMovies.classList.remove('active');
            if (st.serBackScreen === 'wseries') {
                if (d.navWseries)  d.navWseries.classList.add('active');
                if (d.navOtt)      d.navOtt.classList.remove('active');
                if (d.navTvshows)  d.navTvshows.classList.remove('active');
                if (d.navKids)     d.navKids.classList.remove('active');
                if (d.navSearch)   d.navSearch.classList.remove('active');
            } else if (st.serBackScreen === 'tvshows') {
                if (d.navTvshows)  d.navTvshows.classList.add('active');
                if (d.navOtt)      d.navOtt.classList.remove('active');
                if (d.navWseries)  d.navWseries.classList.remove('active');
                if (d.navKids)     d.navKids.classList.remove('active');
                if (d.navSearch)   d.navSearch.classList.remove('active');
            } else if (st.serBackScreen === 'kids') {
                if (d.navKids)     d.navKids.classList.add('active');
                if (d.navOtt)      d.navOtt.classList.remove('active');
                if (d.navWseries)  d.navWseries.classList.remove('active');
                if (d.navTvshows)  d.navTvshows.classList.remove('active');
                if (d.navSearch)   d.navSearch.classList.remove('active');
            } else if (st.serBackScreen === 'search') {
                if (d.navSearch)   d.navSearch.classList.add('active');
                if (d.navOtt)      d.navOtt.classList.remove('active');
                if (d.navWseries)  d.navWseries.classList.remove('active');
                if (d.navTvshows)  d.navTvshows.classList.remove('active');
                if (d.navKids)     d.navKids.classList.remove('active');
            } else {
                if (d.navOtt)      d.navOtt.classList.add('active');
                if (d.navWseries)  d.navWseries.classList.remove('active');
                if (d.navTvshows)  d.navTvshows.classList.remove('active');
                if (d.navKids)     d.navKids.classList.remove('active');
                if (d.navSearch)   d.navSearch.classList.remove('active');
            }
        } else if (name === 'movies') {
            if (d.screenMovies) d.screenMovies.classList.add('visible');
            if (d.navMovies)  d.navMovies.classList.add('active');
            if (d.navLivetv)  d.navLivetv.classList.remove('active');
            if (d.navOtt)     d.navOtt.classList.remove('active');
            if (d.navWseries) d.navWseries.classList.remove('active');
        } else if (name === 'wseries') {
            if (d.screenWseries) d.screenWseries.classList.add('visible');
            if (d.navWseries)  d.navWseries.classList.add('active');
            if (d.navLivetv)   d.navLivetv.classList.remove('active');
            if (d.navOtt)      d.navOtt.classList.remove('active');
            if (d.navMovies)   d.navMovies.classList.remove('active');
            if (d.navTvshows)  d.navTvshows.classList.remove('active');
        } else if (name === 'tvshows') {
            if (d.screenTvshows) d.screenTvshows.classList.add('visible');
            if (d.navTvshows)  d.navTvshows.classList.add('active');
            if (d.navLivetv)   d.navLivetv.classList.remove('active');
            if (d.navOtt)      d.navOtt.classList.remove('active');
            if (d.navMovies)   d.navMovies.classList.remove('active');
            if (d.navWseries)  d.navWseries.classList.remove('active');
            if (d.navKids)     d.navKids.classList.remove('active');
            if (d.navSearch)   d.navSearch.classList.remove('active');
        } else if (name === 'kids') {
            if (d.screenKids)  d.screenKids.classList.add('visible');
            if (d.navKids)     d.navKids.classList.add('active');
            if (d.navLivetv)   d.navLivetv.classList.remove('active');
            if (d.navOtt)      d.navOtt.classList.remove('active');
            if (d.navMovies)   d.navMovies.classList.remove('active');
            if (d.navWseries)  d.navWseries.classList.remove('active');
            if (d.navTvshows)  d.navTvshows.classList.remove('active');
            if (d.navSearch)   d.navSearch.classList.remove('active');
        } else if (name === 'search') {
            if (d.screenSearch) d.screenSearch.classList.add('visible');
            if (d.navSearch)   d.navSearch.classList.add('active');
            if (d.navLivetv)   d.navLivetv.classList.remove('active');
            if (d.navOtt)      d.navOtt.classList.remove('active');
            if (d.navMovies)   d.navMovies.classList.remove('active');
            if (d.navWseries)  d.navWseries.classList.remove('active');
            if (d.navTvshows)  d.navTvshows.classList.remove('active');
            if (d.navKids)     d.navKids.classList.remove('active');
        }
    }

    function goHome() {
        collapseSidebar();
        stopChSlideTimer();
        stopOttSlideTimer();
        stopNetSlideTimer();
        stopMovSlideTimer();
        stopWsSlideTimer();
        stopTvSlideTimer();
        stopKidsSlideTimer();
        st.activeLang   = null;
        st.langSliders  = [];
        st.genres       = [];
        st.channels     = [];
        st.activeGenre  = '';
        d.chSliderWrap.innerHTML = '';
        d.chSliderWrap.className = '';
        showScreen('home');
        startSlideTimer();
        focusLangCard(st.langFocus);
    }

    function goOtt() {
        collapseSidebar();
        stopNetSlideTimer();
        stopChSlideTimer();
        st.activeNetwork = null;
        st.netRows = [];
        if (d.netSliderWrap) { d.netSliderWrap.innerHTML = ''; d.netSliderWrap.className = ''; }
        showScreen('ott');
        startOttSlideTimer();
        focusOttCard(st.ottFocus);
    }

    function goBackFromSeries() {
        collapseSidebar();
        showScreen(st.serBackScreen);
        if (st.serBackScreen === 'network') {
            focusNetItem(st.netItemFocus);
        } else if (st.serBackScreen === 'ott') {
            focusOttCard(st.ottFocus);
        } else if (st.serBackScreen === 'wseries') {
            focusWsItem(st.wsItemFocus);
        } else if (st.serBackScreen === 'tvshows') {
            focusTvItem(st.tvItemFocus);
        } else if (st.serBackScreen === 'kids') {
            focusKidsItem(st.kidsItemFocus);
        } else if (st.serBackScreen === 'search') {
            st.srchFocusZone = 'results';
            focusSrchItem(st.srchItemFocus);
        }
    }

    // ── OTT SCREEN ────────────────────────────────────────────────────────
    function openOtt() {
        if (st.ottNetworks.length) {
            showScreen('ott');
            startOttSlideTimer();
            focusOttCard(st.ottFocus);
            return;
        }
        setBoot('OTT Apps', 'Loading networks\u2026');
        apiGet('/ott-networks', function (res) {
            var nets = toArr(res && res.networks ? res.networks : []);
            // normalize: {id, name, logo} → {id, title, image}
            st.ottNetworks = nets.map(function(n) {
                return { id: n.id, title: n.name || '', image: n.logo || '' };
            });
            st.ottSliders = toArr(st.sliders);
            hideBoot();
            showScreen('ott');
            renderOttSlider();
            renderOttRow();
        }, function () {
            hideBoot();
            showToast('Failed to load OTT content.');
        });
    }

    function renderOttSlider() {
        if (!d.ottSliderWrap) return;
        if (!st.ottSliders.length) {
            d.ottSliderWrap.innerHTML = '<div class="slider-placeholder">OTT Apps</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.ottSliders.length; i++) {
            var s = st.ottSliders[i];
            html += '<div class="slide' + (i === 0 ? ' active' : '') + '">' +
                    '<img class="slide-img" src="' + escAttr(s.banner) + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title) + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="ott-dots">' + renderDots(st.ottSliders.length, 0) + '</div>';
        d.ottSliderWrap.innerHTML = html;
        startOttSlideTimer();
    }

    function startOttSlideTimer() {
        stopOttSlideTimer();
        if (!st.ottSliders || st.ottSliders.length < 2) return;
        st.ottSlideTimer = setInterval(function () {
            st.ottSlideIdx = (st.ottSlideIdx + 1) % st.ottSliders.length;
            if (d.ottSliderWrap) updateSlide(d.ottSliderWrap, st.ottSlideIdx, $id('ott-dots'), st.ottSliders.length);
        }, 4500);
    }
    function stopOttSlideTimer() {
        if (st.ottSlideTimer) { clearInterval(st.ottSlideTimer); st.ottSlideTimer = null; }
    }

    function renderOttRow() {
        if (!d.ottRow) return;
        d.ottRow.innerHTML = '';
        if (!st.ottNetworks.length) {
            d.ottRow.innerHTML = '<div style="padding:20px;color:var(--muted);">No networks available.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.ottNetworks.length; i++) {
            var n = st.ottNetworks[i];
            var imgHtml = n.image
                ? '<img src="' + escAttr(n.image) + '" alt="" onerror="this.parentNode.innerHTML=\'<div class=net-ph>' + esc((n.title||'?').substring(0,2).toUpperCase()) + '</div>\'">'
                : '<div class="net-ph">' + esc((n.title||'?').substring(0,2).toUpperCase()) + '</div>';
            html += '<div class="ott-net-card" data-idx="' + i + '">' +
                    imgHtml +
                    '<div class="net-name">' + esc(n.title) + '</div>' +
                    '</div>';
        }
        d.ottRow.innerHTML = html;
        var cards = d.ottRow.querySelectorAll('.ott-net-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, idx) { card.onclick = function () { openNetwork(idx); }; })(cards[k], k);
        }
        focusOttCard(0);
    }

    function focusOttCard(idx) {
        st.ottFocus = Math.max(0, Math.min(idx, st.ottNetworks.length - 1));
        var cards = d.ottRow ? d.ottRow.querySelectorAll('.ott-net-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'ott-net-card' + (i === st.ottFocus ? ' focused' : '');
        }
        if (st.ottFocus === 0) {
            if (d.ottRow) d.ottRow.scrollLeft = 0;
        } else if (st.ottFocus >= cards.length - 1) {
            if (d.ottRow) d.ottRow.scrollLeft = d.ottRow.scrollWidth;
        } else if (cards[st.ottFocus]) {
            scrollIntoView(cards[st.ottFocus], d.ottRow);
        }
    }

    // ── NETWORK SCREEN ───────────────────────────────────────────────────
    function openNetwork(idx) {
        var net = st.ottNetworks[idx];
        if (!net) return;
        st.ottFocus = idx;
        setBoot('Loading', 'Opening ' + esc(net.title) + '\u2026');
        stopOttSlideTimer();

        capiGet('/network/' + net.id + '?section=contents', function (res) {
            st.activeNetwork  = res.network || net;
            st.netRows        = toArr(res.rows);
            var slides = toArr(res.slides || []);
            // fallback: use hero backdrop as a single slide
            if (!slides.length && res.hero && res.hero.backdrop) {
                slides = [{ title: res.hero.title || (st.activeNetwork.title || ''), image: res.hero.backdrop }];
            }
            st.netSlides      = slides;
            st.netRowIdx      = 0;
            st.netFilterFocus = 0;
            st.netItemFocus   = 0;
            st.netFocusZone   = 'filters';
            hideBoot();
            renderNetworkScreen();
        }, function () {
            hideBoot(); showToast('Failed to load network.');
        });
    }

    function renderNetworkScreen() {
        showScreen('network');

        // Network header on overlay
        var net = st.activeNetwork || {};
        var logoHtml = net.image
            ? '<img class="screen3-net-logo" src="' + escAttr(net.image) + '" alt="">'
            : '';
        if (d.screen3Hdr) d.screen3Hdr.innerHTML = logoHtml +
            '<div class="screen3-net-name">' + esc(net.title || '') + '</div>';

        // Slider
        renderNetSlider();

        // Filter bar (one chip per row)
        renderNetFilterBar();

        // Content grid (first row by default)
        renderNetContentGrid();
    }

    function renderNetSlider() {
        if (!d.netSliderWrap) return;
        d.netSliderWrap.innerHTML = '';
        stopNetSlideTimer();
        if (!st.netSlides.length) return;
        st.netSlideIdx = 0;
        var html = '';
        for (var i = 0; i < st.netSlides.length; i++) {
            var s = st.netSlides[i];
            html += '<div class="slide' + (i === 0 ? ' active' : '') + '">' +
                    '<img class="slide-img" src="' + escAttr(s.image || s.banner || '') + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title || '') + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="net-dots">' + renderDots(st.netSlides.length, 0) + '</div>';
        d.netSliderWrap.innerHTML = html;
        startNetSlideTimer();
    }

    function startNetSlideTimer() {
        stopNetSlideTimer();
        if (!st.netSlides || st.netSlides.length < 2) return;
        st.netSlideTimer = setInterval(function () {
            st.netSlideIdx = (st.netSlideIdx + 1) % st.netSlides.length;
            if (d.netSliderWrap) updateSlide(d.netSliderWrap, st.netSlideIdx, $id('net-dots'), st.netSlides.length);
        }, 4500);
    }
    function stopNetSlideTimer() {
        if (st.netSlideTimer) { clearInterval(st.netSlideTimer); st.netSlideTimer = null; }
    }

    function renderNetFilterBar() {
        if (!d.netFilterBar) return;
        d.netFilterBar.innerHTML = '';
        if (!st.netRows.length) return;
        var html = '';
        for (var i = 0; i < st.netRows.length; i++) {
            var active = i === st.netRowIdx ? ' active' : '';
            html += '<div class="genre-chip' + active + '" data-row="' + i + '">' + esc(st.netRows[i].title) + '</div>';
        }
        d.netFilterBar.innerHTML = html;
        var chips = d.netFilterBar.querySelectorAll('.genre-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, rowIdx) {
                chip.onclick = function () { selectNetRow(rowIdx); };
            })(chips[k], k);
        }
        if (st.netFocusZone === 'filters') {
            focusNetFilter(st.netFilterFocus);
        }
    }

    function focusNetFilter(idx) {
        st.netFilterFocus = Math.max(0, Math.min(idx, st.netRows.length - 1));
        var chips = d.netFilterBar ? d.netFilterBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(' focused','') + (i === st.netFilterFocus ? ' focused' : '');
        }
        if (chips[st.netFilterFocus]) scrollIntoView(chips[st.netFilterFocus], d.netFilterBar);
    }

    function selectNetRow(rowIdx) {
        st.netRowIdx = rowIdx;
        st.netFilterFocus = rowIdx;
        st.netItemFocus = 0;
        st.netFocusZone = 'grid';
        renderNetFilterBar();
        renderNetContentGrid();
    }

    function renderNetContentGrid() {
        if (!d.netContentGrid) return;
        d.netContentGrid.innerHTML = '';
        var row = st.netRows[st.netRowIdx];
        if (!row || !row.items || !row.items.length) {
            d.netContentGrid.innerHTML = '<div class="empty-state"><h3>No Content</h3>No content found in this category.</div>';
            return;
        }
        var items = toArr(row.items);
        var typeIcons = { movie: '&#127916;', webseries: '&#127916;', tvshow: '&#128250;' };
        var typeLabels = { movie: 'Movie', webseries: 'Series', tvshow: 'TV Show' };
        var html = '';
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#127916;</div>\'">'
                : '<div class="content-card-ph">&#127916;</div>';
            var typeLbl = typeLabels[item.type] || item.type || '';
            var badge = typeLbl ? '<span class="content-type-badge">' + esc(typeLbl) + '</span>' : '';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    badge + imgHtml +
                    '<div class="content-card-body">' +
                    '<div class="content-card-title">' + esc(item.title) + '</div>' +
                    '<div class="content-card-type">' + esc(typeLbl) + '</div>' +
                    '</div></div>';
        }
        d.netContentGrid.innerHTML = html;
        var cards = d.netContentGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, cIdx) { card.onclick = function () { openContent(cIdx); }; })(cards[k], k);
        }
        if (st.netFocusZone === 'grid') {
            focusNetItem(st.netItemFocus >= 0 ? st.netItemFocus : 0);
        }
    }

    function focusNetItem(idx) {
        var row = st.netRows[st.netRowIdx];
        var total = row ? (row.items ? row.items.length : 0) : 0;
        st.netItemFocus = Math.max(0, Math.min(idx, total - 1));
        var cards = d.netContentGrid ? d.netContentGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.netItemFocus ? ' focused' : '');
        }
        if (st.netItemFocus === 0) {
            if (d.netContentGrid) d.netContentGrid.scrollLeft = 0;
        } else if (st.netItemFocus >= cards.length - 1) {
            if (d.netContentGrid) d.netContentGrid.scrollLeft = d.netContentGrid.scrollWidth;
        } else if (cards[st.netItemFocus]) {
            scrollIntoView(cards[st.netItemFocus], d.netContentGrid);
        }
    }

    function openContent(idx) {
        var row = st.netRows[st.netRowIdx];
        if (!row) return;
        var item = row.items[idx];
        if (!item) return;
        st.netItemFocus = idx;

        if (item.action === 'play' || item.type === 'movie') {
            var playType = item.play_type || 'movie';
            setBoot('Opening', 'Loading ' + esc(item.title) + '\u2026');
            capiGet('/play/' + playType + '/' + item.id, function (res) {
                hideBoot();
                if (res && res.status && res.playback && res.playback.url) {
                    st.playLabel = res.playback.title || item.title;
                    st.playMeta  = 'Movie';
                    st.playType  = 'vod';
                    playUrl(res.playback.url);
                } else {
                    showToast('Playback URL not available.');
                }
            }, function () { hideBoot(); showToast('Failed to load movie.'); });
        } else {
            // detail (series / tvshow)
            var detailType = item.detail_type || (item.type === 'tvshow' ? 'tvshow' : 'webseries');
            setBoot('Loading', 'Loading ' + esc(item.title) + '\u2026');
            capiGet('/content/' + detailType + '/' + item.id, function (res) {
                hideBoot();
                if (res && res.status && res.content) {
                    st.serContent     = res.content;
                    st.serSeasonFocus = 0;
                    st.serEpFocus     = 0;
                    st.serFocusZone   = 'seasons';
                    st.serBackScreen  = 'network';
                    showScreen('series');
                    renderSeriesScreen();
                } else {
                    showToast('Content detail not available.');
                }
            }, function () { hideBoot(); showToast('Failed to load detail.'); });
        }
    }

    // ── SERIES SCREEN ─────────────────────────────────────────────────────
    function renderSeriesScreen() {
        if (!st.serContent) return;
        if (d.seriesTitle) d.seriesTitle.textContent = st.serContent.title || '';
        renderSeriesSeasons();
        renderSeriesEpisodes(0);
    }

    function renderSeriesSeasons() {
        if (!d.seriesSeasons) return;
        var seasons = toArr(st.serContent.seasons);
        if (!seasons.length) {
            d.seriesSeasons.innerHTML = '<div style="padding:16px;color:var(--muted);font-size:13px;">No seasons</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < seasons.length; i++) {
            html += '<div class="season-item' + (i === 0 ? ' active' : '') + '" data-idx="' + i + '">' +
                    esc(seasons[i].title || ('Season ' + (i+1))) + '</div>';
        }
        d.seriesSeasons.innerHTML = html;
        var items = d.seriesSeasons.querySelectorAll('.season-item');
        for (var k = 0; k < items.length; k++) {
            (function (item, sIdx) { item.onclick = function () { selectSeason(sIdx); }; })(items[k], k);
        }
        if (st.serFocusZone === 'seasons') {
            focusSeason(st.serSeasonFocus >= 0 ? st.serSeasonFocus : 0);
        }
    }

    function focusSeason(idx) {
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        st.serSeasonFocus = Math.max(0, Math.min(idx, seasons.length - 1));
        var items = d.seriesSeasons ? d.seriesSeasons.querySelectorAll('.season-item') : [];
        for (var i = 0; i < items.length; i++) {
            var active = items[i].className.indexOf('active') !== -1 ? ' active' : '';
            items[i].className = 'season-item' + active + (i === st.serSeasonFocus ? ' focused' : '');
        }
        if (items[st.serSeasonFocus]) {
            items[st.serSeasonFocus].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectSeason(idx) {
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        // update active styling
        var items = d.seriesSeasons ? d.seriesSeasons.querySelectorAll('.season-item') : [];
        for (var i = 0; i < items.length; i++) {
            items[i].className = 'season-item' + (i === idx ? ' active' : '');
        }
        st.serSeasonFocus = idx;
        st.serEpFocus = 0;
        st.serFocusZone = 'seasons';
        renderSeriesEpisodes(idx);
        focusSeason(idx);
    }

    function renderSeriesEpisodes(seasonIdx) {
        if (!d.seriesEpisodes) return;
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        var season = seasons[seasonIdx];
        var episodes = season ? toArr(season.episodes) : [];
        if (!episodes.length) {
            d.seriesEpisodes.innerHTML = '<div class="empty-state"><h3>No Episodes</h3>No episodes in this season.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < episodes.length; i++) {
            var ep = episodes[i];
            var thumbHtml = ep.image
                ? '<img class="episode-thumb" src="' + escAttr(ep.image) + '" alt="" onerror="this.outerHTML=\'<div class=episode-thumb-ph>&#9654;</div>\'">'
                : '<div class="episode-thumb-ph">&#9654;</div>';
            html += '<div class="episode-item" data-idx="' + i + '">' +
                    thumbHtml +
                    '<div class="episode-info">' +
                    '<div class="episode-title">' + esc(ep.title) + '</div>' +
                    '<div class="episode-meta">' + esc(ep.subtitle || '') + '</div>' +
                    '</div></div>';
        }
        d.seriesEpisodes.innerHTML = html;
        var items = d.seriesEpisodes.querySelectorAll('.episode-item');
        for (var k = 0; k < items.length; k++) {
            (function (item, eIdx) { item.onclick = function () { playEpisode(seasonIdx, eIdx); }; })(items[k], k);
        }
        if (st.serFocusZone === 'episodes') {
            focusEpisode(st.serEpFocus >= 0 ? st.serEpFocus : 0);
        }
    }

    function focusEpisode(idx) {
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        var season = seasons[st.serSeasonFocus];
        var total = season ? toArr(season.episodes).length : 0;
        st.serEpFocus = Math.max(0, Math.min(idx, total - 1));
        var items = d.seriesEpisodes ? d.seriesEpisodes.querySelectorAll('.episode-item') : [];
        for (var i = 0; i < items.length; i++) {
            items[i].className = 'episode-item' + (i === st.serEpFocus ? ' focused' : '');
        }
        if (items[st.serEpFocus]) items[st.serEpFocus].scrollIntoView({ block: 'nearest' });
    }

    function playEpisode(seasonIdx, epIdx) {
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        var season = seasons[seasonIdx];
        var ep = season ? toArr(season.episodes)[epIdx] : null;
        if (!ep) return;
        var playType = ep.play_type || 'webseries-episode';
        setBoot('Opening', 'Loading ' + esc(ep.title) + '\u2026');
        capiGet('/play/' + playType + '/' + ep.id, function (res) {
            hideBoot();
            if (res && res.status && res.playback && res.playback.url) {
                st.playLabel = res.playback.title || ep.title;
                st.playMeta  = (st.serContent ? st.serContent.title : '') + ' — ' + (season.title || '');
                st.playType  = 'vod';
                playUrl(res.playback.url);
            } else {
                showToast('Playback URL not available.');
            }
        }, function () { hideBoot(); showToast('Failed to load episode.'); });
    }

    // ── CAPI HELPER (new REST endpoints at /c/api/) ─────────────────────
    function capiGet(path, onOk, onErr) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status < 200 || xhr.status >= 300) { if (onErr) onErr(xhr.status); return; }
            try { onOk(JSON.parse(xhr.responseText || '{}')); }
            catch(e) { if (onErr) onErr(e); }
        };
        var sep = path.indexOf('?') !== -1 ? '&' : '?';
        xhr.open('GET', capi + path + sep + 'mac=' + encodeURIComponent(st.mac) + '&token=' + encodeURIComponent(st.token), true);
        xhr.send(null);
    }

    // ── BOOT / TOAST ───────────────────────────────────────────────────────
    function setBoot(title, msg) {
        d.bootTitle.innerHTML = esc(title);
        d.bootMsg.innerHTML   = msg; // allow <code> tags
        d.boot.className      = '';
    }

    function hideBoot() {
        d.boot.className = 'hidden';
        setTimeout(function () { d.boot.style.display = 'none'; }, 260);
    }

    function showError(msg) {
        d.boot.style.display = 'flex';
        d.boot.className = '';
        d.bootTitle.innerHTML = 'Something went wrong';
        d.bootMsg.innerHTML   = esc(msg);
    }

    function showToast(msg) {
        d.toast.innerHTML = esc(msg);
        d.toast.className = 'visible';
        if (d.toastTimer) clearTimeout(d.toastTimer);
        d.toastTimer = setTimeout(function () { d.toast.className = ''; }, 3000);
    }

    // ── NETWORK ────────────────────────────────────────────────────────────
    function magReq(params, onOk) {
        params = params || {};
        params.mac  = st.mac;
        params.JsHttpRequest = '1-xml';
        if (st.token && !params.token) params.token = st.token;

        var qs = [];
        for (var k in params) {
            if (params.hasOwnProperty(k) && params[k] !== null && params[k] !== undefined && params[k] !== '') {
                qs.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status < 200 || xhr.status >= 300) { showError('Portal HTTP ' + xhr.status); return; }
            var payload;
            try { payload = JSON.parse(xhr.responseText || '{}'); } catch(e) { showError('Invalid portal response'); return; }
            var js = payload && payload.js ? payload.js : null;
            if (js && js.status === 'ERROR') { showError(js.message || 'Portal error'); return; }
            onOk(js || {});
        };
        xhr.open('GET', loadUrl + '?' + qs.join('&'), true);
        xhr.send(null);
    }

    function apiGet(path, onOk, onErr) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status < 200 || xhr.status >= 300) { if (onErr) onErr(xhr.status); return; }
            try { onOk(JSON.parse(xhr.responseText || '{}')); }
            catch(e) { if (onErr) onErr(e); }
        };
        var sep = path.indexOf('?') !== -1 ? '&' : '?';
        xhr.open('GET', api + path + sep + 'mac=' + encodeURIComponent(st.mac) + '&token=' + encodeURIComponent(st.token), true);
        xhr.send(null);
    }

    function apiPost(path, data, onOk, onErr) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status < 200 || xhr.status >= 300) { if (onErr) onErr(xhr.status); return; }
            try { onOk(JSON.parse(xhr.responseText || '{}')); }
            catch(e) { if (onErr) onErr(e); }
        };
        data = data || {};
        data.mac   = st.mac;
        data.token = st.token;
        xhr.open('POST', api + path, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
    }

    // ── STB VIDEO HELPERS ──────────────────────────────────────────────────
    function setVideoTop()   { try { if (win.gSTB && typeof win.gSTB.SetTopWin === 'function') win.gSTB.SetTopWin(1); } catch(e) {} }
    function setGraphicTop() { try { if (win.gSTB && typeof win.gSTB.SetTopWin === 'function') win.gSTB.SetTopWin(0); } catch(e) {} }

    // ── UTILS ──────────────────────────────────────────────────────────────
    function esc(v) {
        return String(v || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function escAttr(v) { return esc(v); }
    function norm(v)    { return String(v || '').replace(/^\s+|\s+$/g,'').toUpperCase(); }
    function normCmd(v) { return String(v || '').replace(/^(ffrt|ffmpeg|auto)\s+/i,'').replace(/^\s+|\s+$/g,''); }
    function toArr(v)   { return Object.prototype.toString.call(v) === '[object Array]' ? v : []; }

    // Remove focused class from every matching child without changing other classes
    function clearFocusInEl(container, selector, baseClass) {
        if (!container) return;
        var items = container.querySelectorAll(selector);
        for (var i = 0; i < items.length; i++) {
            // keep all classes except 'focused'
            items[i].className = items[i].className.replace(/\bfocused\b/g, '').replace(/\s+/g, ' ').trim();
        }
    }

    // Navigate to a season AND immediately load its episodes (no OK press needed)
    function selectSeasonFocused(idx) {
        var seasons = toArr(st.serContent ? st.serContent.seasons : []);
        idx = Math.max(0, Math.min(idx, seasons.length - 1));
        // update active + focused styling on season items
        var items = d.seriesSeasons ? d.seriesSeasons.querySelectorAll('.season-item') : [];
        for (var i = 0; i < items.length; i++) {
            items[i].className = 'season-item' + (i === idx ? ' active focused' : '');
        }
        st.serSeasonFocus = idx;
        st.serEpFocus = 0;
        st.serFocusZone = 'seasons';
        // update episodes panel immediately
        renderSeriesEpisodes(idx);
        if (items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
    }

    function scrollIntoView(el, container) {
        if (!el || !container) return;
        var er  = el.getBoundingClientRect();
        var cr  = container.getBoundingClientRect();
        var pad = 44;
        if (er.left - pad < cr.left) {
            container.scrollLeft -= (cr.left - er.left + pad);
        } else if (er.right + pad > cr.right) {
            container.scrollLeft += (er.right - cr.right + pad);
        }
    }

    // ── MOVIES SCREEN ─────────────────────────────────────────────────────
    function openMovies() {
        if (st.movNetworks.length) {
            showScreen('movies');
            startMovSlideTimer();
            focusMovNet(st.movNetFocus);
            return;
        }
        setBoot('Movies', 'Loading networks\u2026');
        apiGet('/movies/networks', function (res) {
            st.movNetworks   = toArr(res && res.networks ? res.networks : []);
            st.movActiveNetIdx = 0;
            st.movFocusZone  = 'networks';
            hideBoot();
            showScreen('movies');
            renderMovNetBar();
            if (st.movNetworks.length) {
                loadMovieContents(st.movNetworks[0].id, '');
            }
        }, function () { hideBoot(); showToast('Failed to load movie networks.'); });
    }

    function renderMovNetBar() {
        if (!d.movNetBar) return;
        d.movNetBar.innerHTML = '';
        var nets = st.movNetworks;
        if (!nets.length) { d.movNetBar.innerHTML = '<div style="color:rgba(255,255,255,0.6);font-size:13px;">No networks</div>'; return; }
        var html = '';
        for (var i = 0; i < nets.length; i++) {
            var n = nets[i];
            var active = i === st.movActiveNetIdx ? ' active' : '';
            var imgTag = n.logo ? '<img src="' + escAttr(n.logo) + '" alt="">' : '';
            html += '<div class="mov-net-chip' + active + '" data-idx="' + i + '">' + imgTag + esc(n.name) + '</div>';
        }
        d.movNetBar.innerHTML = html;
        var chips = d.movNetBar.querySelectorAll('.mov-net-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, idx) { chip.onclick = function () { selectMovNetwork(idx); }; })(chips[k], k);
        }
        if (st.movFocusZone === 'networks') focusMovNet(st.movNetFocus);
    }

    function focusMovNet(idx) {
        var chips = d.movNetBar ? d.movNetBar.querySelectorAll('.mov-net-chip') : [];
        st.movNetFocus = Math.max(0, Math.min(idx, chips.length - 1));
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g, '').replace(/\s+/g,' ').trim()
                               + (i === st.movNetFocus ? ' focused' : '');
        }
        if (chips[st.movNetFocus]) scrollIntoView(chips[st.movNetFocus], d.movNetBar);
    }

    function selectMovNetwork(idx) {
        st.movActiveNetIdx = idx;
        st.movNetFocus = idx;
        st.movGenreFocus = 0;
        st.movItemFocus  = 0;
        st.movFocusZone  = 'networks';
        var net = st.movNetworks[idx];
        if (!net) return;
        // update active + focused chip style
        var chips = d.movNetBar ? d.movNetBar.querySelectorAll('.mov-net-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = 'mov-net-chip' + (i === idx ? ' active focused' : '');
        }
        if (d.movBannerTitle) d.movBannerTitle.textContent = net.name;
        clearFocusInEl(d.movGenreBar, '.genre-chip', 'genre-chip');
        clearFocusInEl(d.movContentGrid, '.content-card', 'content-card');
        loadMovieContents(net.id, '');
    }

    function loadMovieContents(networkId, genre) {
        st.movActiveGenre = genre;
        apiGet('/movies/contents?network_id=' + networkId + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.movItems   = toArr(res.data);
            st.movSliders = toArr(res.sliders);
            st.movSlideIdx = 0;
            renderMovSlider();
            // Load genres for this network
            apiGet('/movies/genres?network_id=' + networkId, function (gr) {
                st.movGenres = toArr(gr.genres);
                st.movGenreFocus = 0;
                renderMovGenreBar();
                renderMovContentGrid();
            }, function () { st.movGenres = []; renderMovGenreBar(); renderMovContentGrid(); });
        }, function () { showToast('Failed to load movies.'); });
    }

    function renderMovSlider() {
        if (!d.movSliderWrap) return;
        stopMovSlideTimer();
        if (!st.movSliders.length) { d.movSliderWrap.innerHTML = ''; return; }
        var html = '';
        for (var i = 0; i < st.movSliders.length; i++) {
            var s = st.movSliders[i];
            html += '<div class="slide' + (i === 0 ? ' active' : '') + '">' +
                    '<img class="slide-img" src="' + escAttr(s.image || s.banner || '') + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title || '') + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="mov-dots">' + renderDots(st.movSliders.length, 0) + '</div>';
        d.movSliderWrap.innerHTML = html;
        startMovSlideTimer();
    }
    function startMovSlideTimer() {
        stopMovSlideTimer();
        if (!st.movSliders || st.movSliders.length < 2) return;
        st.movSlideTimer = setInterval(function () {
            st.movSlideIdx = (st.movSlideIdx + 1) % st.movSliders.length;
            if (d.movSliderWrap) updateSlide(d.movSliderWrap, st.movSlideIdx, $id('mov-dots'), st.movSliders.length);
        }, 4500);
    }
    function stopMovSlideTimer() {
        if (st.movSlideTimer) { clearInterval(st.movSlideTimer); st.movSlideTimer = null; }
    }

    function renderMovGenreBar() {
        if (!d.movGenreBar) return;
        d.movGenreBar.innerHTML = '';
        var genres = st.movGenres;
        if (!d.movGenreBar) return;
        // Always prepend "All" chip
        var allActive = st.movActiveGenre === '' ? ' active' : '';
        var html = '<div class="genre-chip' + allActive + '" data-idx="-1">All</div>';
        for (var i = 0; i < genres.length; i++) {
            var active = genres[i] === st.movActiveGenre ? ' active' : '';
            html += '<div class="genre-chip' + active + '" data-idx="' + i + '">' + esc(genres[i]) + '</div>';
        }
        d.movGenreBar.innerHTML = html;
        var chips = d.movGenreBar.querySelectorAll('.genre-chip');
        // chip 0 = All, chip 1..n = genres[0..n-1]
        for (var k = 0; k < chips.length; k++) {
            (function (chip, ki) {
                chip.onclick = function () {
                    selectMovGenre(ki === 0 ? '' : st.movGenres[ki - 1]);
                };
            })(chips[k], k);
        }
        if (st.movFocusZone === 'genres') focusMovGenre(st.movGenreFocus);
    }

    function focusMovGenre(idx) {
        // +1 total chips (All + genres)
        var total = st.movGenres.length + 1;
        st.movGenreFocus = Math.max(0, Math.min(idx, total - 1));
        var chips = d.movGenreBar ? d.movGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g,'').replace(/\s+/g,' ').trim()
                               + (i === st.movGenreFocus ? ' focused' : '');
        }
        if (chips[st.movGenreFocus]) scrollIntoView(chips[st.movGenreFocus], d.movGenreBar);
    }

    function selectMovGenre(genre) {
        st.movActiveGenre = genre;
        st.movItemFocus   = 0;
        st.movFocusZone   = 'grid';
        var net = st.movNetworks[st.movActiveNetIdx];
        if (!net) return;
        // clear focused from ALL genre chips first
        clearFocusInEl(d.movGenreBar, '.genre-chip', 'genre-chip');
        // update active chip (chip 0 = All, chip 1..n = genres)
        var chips = d.movGenreBar ? d.movGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            var isActive = (i === 0 && genre === '') || (i > 0 && st.movGenres[i - 1] === genre);
            chips[i].className = chips[i].className.replace(/\bactive\b/g,'').replace(/\s+/g,' ').trim()
                               + (isActive ? ' active' : '');
        }
        // reload content with genre filter
        apiGet('/movies/contents?network_id=' + net.id + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.movItems = toArr(res.data);
            renderMovContentGrid();
        }, function () { showToast('Failed to filter movies.'); });
    }

    function renderMovContentGrid() {
        if (!d.movContentGrid) return;
        d.movContentGrid.innerHTML = '';
        if (!st.movItems.length) {
            d.movContentGrid.innerHTML = '<div class="empty-state"><h3>No Movies</h3>No movies found.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.movItems.length; i++) {
            var item = st.movItems[i];
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#127916;</div>\'">'
                : '<div class="content-card-ph">&#127916;</div>';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    '<span class="content-type-badge">Movie</span>' + imgHtml +
                    '<div class="content-card-body"><div class="content-card-title">' + esc(item.title) + '</div>' +
                    '<div class="content-card-type">' + esc(item.subtitle || '') + '</div></div></div>';
        }
        d.movContentGrid.innerHTML = html;
        var cards = d.movContentGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, ci) { card.onclick = function () { playMovItem(ci); }; })(cards[k], k);
        }
        if (st.movFocusZone === 'grid') focusMovItem(st.movItemFocus >= 0 ? st.movItemFocus : 0);
    }

    function focusMovItem(idx) {
        st.movItemFocus = Math.max(0, Math.min(idx, st.movItems.length - 1));
        var cards = d.movContentGrid ? d.movContentGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.movItemFocus ? ' focused' : '');
        }
        if (cards[st.movItemFocus]) scrollIntoView(cards[st.movItemFocus], d.movContentGrid);
    }

    function playMovItem(idx) {
        var item = st.movItems[idx];
        if (!item) return;
        st.movItemFocus = idx;
        capiGet('/play/movie/' + item.id, function (res) {
            if (res && res.playback && res.playback.url) {
                createLink('movie', res.playback.url, item.title);
            } else {
                showToast('Playback not available.');
            }
        }, function () { showToast('Failed to load movie.'); });
    }

    // ── WEB SERIES SCREEN ─────────────────────────────────────────────────────
    function openWseries() {
        if (st.wsNetworks.length) {
            showScreen('wseries');
            startWsSlideTimer();
            focusWsNet(st.wsNetFocus);
            return;
        }
        setBoot('Web Series', 'Loading networks\u2026');
        apiGet('/webseries/networks', function (res) {
            st.wsNetworks    = toArr(res && res.networks ? res.networks : []);
            st.wsActiveNetIdx = 0;
            st.wsFocusZone   = 'networks';
            hideBoot();
            showScreen('wseries');
            renderWsNetBar();
            if (st.wsNetworks.length) {
                loadWseriesContents(st.wsNetworks[0].id, '');
            }
        }, function () { hideBoot(); showToast('Failed to load web series networks.'); });
    }

    function renderWsNetBar() {
        if (!d.wsNetBar) return;
        d.wsNetBar.innerHTML = '';
        var nets = st.wsNetworks;
        if (!nets.length) { d.wsNetBar.innerHTML = '<div style="color:rgba(255,255,255,0.6);font-size:13px;">No networks</div>'; return; }
        var html = '';
        for (var i = 0; i < nets.length; i++) {
            var n = nets[i];
            var active = i === st.wsActiveNetIdx ? ' active' : '';
            var imgTag = n.logo ? '<img src="' + escAttr(n.logo) + '" alt="">' : '';
            html += '<div class="ws-net-chip' + active + '" data-idx="' + i + '">' + imgTag + esc(n.name) + '</div>';
        }
        d.wsNetBar.innerHTML = html;
        var chips = d.wsNetBar.querySelectorAll('.ws-net-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, idx) { chip.onclick = function () { selectWsNetwork(idx); }; })(chips[k], k);
        }
        if (st.wsFocusZone === 'networks') focusWsNet(st.wsNetFocus);
    }

    function focusWsNet(idx) {
        var chips = d.wsNetBar ? d.wsNetBar.querySelectorAll('.ws-net-chip') : [];
        st.wsNetFocus = Math.max(0, Math.min(idx, chips.length - 1));
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g, '').replace(/\s+/g,' ').trim()
                               + (i === st.wsNetFocus ? ' focused' : '');
        }
        if (chips[st.wsNetFocus]) scrollIntoView(chips[st.wsNetFocus], d.wsNetBar);
    }

    function selectWsNetwork(idx) {
        st.wsActiveNetIdx = idx;
        st.wsNetFocus     = idx;
        st.wsGenreFocus   = 0;
        st.wsItemFocus    = 0;
        st.wsFocusZone    = 'networks';
        var net = st.wsNetworks[idx];
        if (!net) return;
        var chips = d.wsNetBar ? d.wsNetBar.querySelectorAll('.ws-net-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = 'ws-net-chip' + (i === idx ? ' active focused' : '');
        }
        if (d.wsBannerTitle) d.wsBannerTitle.textContent = net.name;
        clearFocusInEl(d.wsGenreBar, '.genre-chip', 'genre-chip');
        clearFocusInEl(d.wsContentGrid, '.content-card', 'content-card');
        loadWseriesContents(net.id, '');
    }

    function loadWseriesContents(networkId, genre) {
        st.wsActiveGenre = genre;
        apiGet('/webseries/contents?network_id=' + networkId + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.wsItems   = toArr(res.data);
            st.wsSliders = toArr(res.sliders);
            st.wsSlideIdx = 0;
            renderWsSlider();
            apiGet('/webseries/genres?network_id=' + networkId, function (gr) {
                st.wsGenres = toArr(gr.genres);
                st.wsGenreFocus = 0;
                renderWsGenreBar();
                renderWsContentGrid();
            }, function () { st.wsGenres = []; renderWsGenreBar(); renderWsContentGrid(); });
        }, function () { showToast('Failed to load web series.'); });
    }

    function renderWsSlider() {
        if (!d.wsSliderWrap) return;
        stopWsSlideTimer();
        if (!st.wsSliders.length) { d.wsSliderWrap.innerHTML = ''; return; }
        var html = '';
        for (var i = 0; i < st.wsSliders.length; i++) {
            var s = st.wsSliders[i];
            html += '<div class="slide' + (i === 0 ? ' active' : '') + '">' +
                    '<img class="slide-img" src="' + escAttr(s.image || s.banner || '') + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title || '') + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="ws-dots">' + renderDots(st.wsSliders.length, 0) + '</div>';
        d.wsSliderWrap.innerHTML = html;
        startWsSlideTimer();
    }
    function startWsSlideTimer() {
        stopWsSlideTimer();
        if (!st.wsSliders || st.wsSliders.length < 2) return;
        st.wsSlideTimer = setInterval(function () {
            st.wsSlideIdx = (st.wsSlideIdx + 1) % st.wsSliders.length;
            if (d.wsSliderWrap) updateSlide(d.wsSliderWrap, st.wsSlideIdx, $id('ws-dots'), st.wsSliders.length);
        }, 4500);
    }
    function stopWsSlideTimer() {
        if (st.wsSlideTimer) { clearInterval(st.wsSlideTimer); st.wsSlideTimer = null; }
    }

    function renderWsGenreBar() {
        if (!d.wsGenreBar) return;
        d.wsGenreBar.innerHTML = '';
        var genres = st.wsGenres;
        var allActive = st.wsActiveGenre === '' ? ' active' : '';
        var html = '<div class="genre-chip' + allActive + '" data-idx="-1">All</div>';
        for (var i = 0; i < genres.length; i++) {
            var active = genres[i] === st.wsActiveGenre ? ' active' : '';
            html += '<div class="genre-chip' + active + '" data-idx="' + i + '">' + esc(genres[i]) + '</div>';
        }
        d.wsGenreBar.innerHTML = html;
        var chips = d.wsGenreBar.querySelectorAll('.genre-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, ki) {
                chip.onclick = function () {
                    selectWsGenre(ki === 0 ? '' : st.wsGenres[ki - 1]);
                };
            })(chips[k], k);
        }
        if (st.wsFocusZone === 'genres') focusWsGenre(st.wsGenreFocus);
    }

    function focusWsGenre(idx) {
        var total = st.wsGenres.length + 1; // +1 for All chip
        st.wsGenreFocus = Math.max(0, Math.min(idx, total - 1));
        var chips = d.wsGenreBar ? d.wsGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g,'').replace(/\s+/g,' ').trim()
                               + (i === st.wsGenreFocus ? ' focused' : '');
        }
        if (chips[st.wsGenreFocus]) scrollIntoView(chips[st.wsGenreFocus], d.wsGenreBar);
    }

    function selectWsGenre(genre) {
        st.wsActiveGenre = genre;
        st.wsItemFocus   = 0;
        st.wsFocusZone   = 'grid';
        var net = st.wsNetworks[st.wsActiveNetIdx];
        if (!net) return;
        clearFocusInEl(d.wsGenreBar, '.genre-chip', 'genre-chip');
        var chips = d.wsGenreBar ? d.wsGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            var isActive = (i === 0 && genre === '') || (i > 0 && st.wsGenres[i - 1] === genre);
            chips[i].className = chips[i].className.replace(/\bactive\b/g,'').replace(/\s+/g,' ').trim()
                               + (isActive ? ' active' : '');
        }
        apiGet('/webseries/contents?network_id=' + net.id + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.wsItems = toArr(res.data);
            renderWsContentGrid();
        }, function () { showToast('Failed to filter web series.'); });
    }

    function renderWsContentGrid() {
        if (!d.wsContentGrid) return;
        d.wsContentGrid.innerHTML = '';
        if (!st.wsItems.length) {
            d.wsContentGrid.innerHTML = '<div class="empty-state"><h3>No Series</h3>No web series found.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.wsItems.length; i++) {
            var item = st.wsItems[i];
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#127916;</div>\'">'
                : '<div class="content-card-ph">&#127916;</div>';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    '<span class="content-type-badge">Series</span>' + imgHtml +
                    '<div class="content-card-body"><div class="content-card-title">' + esc(item.title) + '</div>' +
                    '<div class="content-card-type">' + esc(item.subtitle || '') + '</div></div></div>';
        }
        d.wsContentGrid.innerHTML = html;
        var cards = d.wsContentGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, ci) { card.onclick = function () { openWsCard(ci); }; })(cards[k], k);
        }
        if (st.wsFocusZone === 'grid') focusWsItem(st.wsItemFocus >= 0 ? st.wsItemFocus : 0);
    }

    function focusWsItem(idx) {
        st.wsItemFocus = Math.max(0, Math.min(idx, st.wsItems.length - 1));
        var cards = d.wsContentGrid ? d.wsContentGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.wsItemFocus ? ' focused' : '');
        }
        if (cards[st.wsItemFocus]) scrollIntoView(cards[st.wsItemFocus], d.wsContentGrid);
    }

    function openWsCard(idx) {
        var item = st.wsItems[idx];
        if (!item) return;
        st.wsItemFocus = idx;
        var detailType = item.detail_type || 'webseries';
        setBoot('Loading', 'Loading ' + esc(item.title) + '\u2026');
        capiGet('/content/' + detailType + '/' + item.id, function (res) {
            hideBoot();
            if (res && res.status && res.content) {
                st.serContent     = res.content;
                st.serSeasonFocus = 0;
                st.serEpFocus     = 0;
                st.serFocusZone   = 'seasons';
                st.serBackScreen  = 'wseries';
                showScreen('series');
                renderSeriesScreen();
            } else {
                showToast('Content detail not available.');
            }
        }, function () { hideBoot(); showToast('Failed to load series detail.'); });
    }

    // ── TV SHOWS SCREEN ───────────────────────────────────────────────────────
    function openTvshows() {
        if (st.tvNetworks.length) {
            showScreen('tvshows');
            startTvSlideTimer();
            focusTvNet(st.tvNetFocus);
            return;
        }
        setBoot('TV Shows', 'Loading networks\u2026');
        apiGet('/tvshows/networks', function (res) {
            st.tvNetworks    = toArr(res && res.networks ? res.networks : []);
            st.tvActiveNetIdx = 0;
            st.tvFocusZone   = 'networks';
            hideBoot();
            showScreen('tvshows');
            renderTvNetBar();
            if (st.tvNetworks.length) {
                loadTvshowContents(st.tvNetworks[0].id, '');
            }
        }, function () { hideBoot(); showToast('Failed to load TV show networks.'); });
    }

    function renderTvNetBar() {
        if (!d.tvNetBar) return;
        d.tvNetBar.innerHTML = '';
        var nets = st.tvNetworks;
        if (!nets.length) { d.tvNetBar.innerHTML = '<div style="color:rgba(255,255,255,0.6);font-size:13px;">No networks</div>'; return; }
        var html = '';
        for (var i = 0; i < nets.length; i++) {
            var n = nets[i];
            var active = i === st.tvActiveNetIdx ? ' active' : '';
            var imgTag = n.logo ? '<img src="' + escAttr(n.logo) + '" alt="">' : '';
            html += '<div class="tv-net-chip' + active + '" data-idx="' + i + '">' + imgTag + esc(n.name) + '</div>';
        }
        d.tvNetBar.innerHTML = html;
        var chips = d.tvNetBar.querySelectorAll('.tv-net-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, idx) { chip.onclick = function () { selectTvNetwork(idx); }; })(chips[k], k);
        }
        if (st.tvFocusZone === 'networks') focusTvNet(st.tvNetFocus);
    }

    function focusTvNet(idx) {
        var chips = d.tvNetBar ? d.tvNetBar.querySelectorAll('.tv-net-chip') : [];
        st.tvNetFocus = Math.max(0, Math.min(idx, chips.length - 1));
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g, '').replace(/\s+/g,' ').trim()
                               + (i === st.tvNetFocus ? ' focused' : '');
        }
        if (chips[st.tvNetFocus]) scrollIntoView(chips[st.tvNetFocus], d.tvNetBar);
    }

    function selectTvNetwork(idx) {
        st.tvActiveNetIdx = idx;
        st.tvNetFocus     = idx;
        st.tvGenreFocus   = 0;
        st.tvItemFocus    = 0;
        st.tvFocusZone    = 'networks';
        var net = st.tvNetworks[idx];
        if (!net) return;
        var chips = d.tvNetBar ? d.tvNetBar.querySelectorAll('.tv-net-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = 'tv-net-chip' + (i === idx ? ' active focused' : '');
        }
        if (d.tvBannerTitle) d.tvBannerTitle.textContent = net.name;
        clearFocusInEl(d.tvGenreBar, '.genre-chip', 'genre-chip');
        clearFocusInEl(d.tvContentGrid, '.content-card', 'content-card');
        loadTvshowContents(net.id, '');
    }

    function loadTvshowContents(networkId, genre) {
        st.tvActiveGenre = genre;
        apiGet('/tvshows/contents?network_id=' + networkId + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.tvItems   = toArr(res.data);
            st.tvSliders = toArr(res.sliders);
            st.tvSlideIdx = 0;
            renderTvSlider();
            apiGet('/tvshows/genres?network_id=' + networkId, function (gr) {
                st.tvGenres = toArr(gr.genres);
                st.tvGenreFocus = 0;
                renderTvGenreBar();
                renderTvContentGrid();
            }, function () { st.tvGenres = []; renderTvGenreBar(); renderTvContentGrid(); });
        }, function () { showToast('Failed to load TV shows.'); });
    }

    function renderTvSlider() {
        if (!d.tvSliderWrap) return;
        stopTvSlideTimer();
        if (!st.tvSliders.length) { d.tvSliderWrap.innerHTML = ''; return; }
        var html = '';
        for (var i = 0; i < st.tvSliders.length; i++) {
            var s = st.tvSliders[i];
            html += '<div class="slide' + (i === 0 ? ' active' : '') + '">' +
                    '<img class="slide-img" src="' + escAttr(s.image || s.banner || '') + '" alt="">' +
                    '<div class="slide-overlay"></div>' +
                    '<div class="slide-info"><div class="slide-title">' + esc(s.title || '') + '</div></div>' +
                    '</div>';
        }
        html += '<div class="slide-dots" id="tv-dots">' + renderDots(st.tvSliders.length, 0) + '</div>';
        d.tvSliderWrap.innerHTML = html;
        startTvSlideTimer();
    }
    function startTvSlideTimer() {
        stopTvSlideTimer();
        if (!st.tvSliders || st.tvSliders.length < 2) return;
        st.tvSlideTimer = setInterval(function () {
            st.tvSlideIdx = (st.tvSlideIdx + 1) % st.tvSliders.length;
            if (d.tvSliderWrap) updateSlide(d.tvSliderWrap, st.tvSlideIdx, $id('tv-dots'), st.tvSliders.length);
        }, 4500);
    }
    function stopTvSlideTimer() {
        if (st.tvSlideTimer) { clearInterval(st.tvSlideTimer); st.tvSlideTimer = null; }
    }

    function renderTvGenreBar() {
        if (!d.tvGenreBar) return;
        d.tvGenreBar.innerHTML = '';
        var genres = st.tvGenres;
        var allActive = st.tvActiveGenre === '' ? ' active' : '';
        var html = '<div class="genre-chip' + allActive + '" data-idx="-1">All</div>';
        for (var i = 0; i < genres.length; i++) {
            var active = genres[i] === st.tvActiveGenre ? ' active' : '';
            html += '<div class="genre-chip' + active + '" data-idx="' + i + '">' + esc(genres[i]) + '</div>';
        }
        d.tvGenreBar.innerHTML = html;
        var chips = d.tvGenreBar.querySelectorAll('.genre-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, ki) {
                chip.onclick = function () {
                    selectTvGenre(ki === 0 ? '' : st.tvGenres[ki - 1]);
                };
            })(chips[k], k);
        }
        if (st.tvFocusZone === 'genres') focusTvGenre(st.tvGenreFocus);
    }

    function focusTvGenre(idx) {
        var total = st.tvGenres.length + 1; // +1 for All chip
        st.tvGenreFocus = Math.max(0, Math.min(idx, total - 1));
        var chips = d.tvGenreBar ? d.tvGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = chips[i].className.replace(/\bfocused\b/g,'').replace(/\s+/g,' ').trim()
                               + (i === st.tvGenreFocus ? ' focused' : '');
        }
        if (chips[st.tvGenreFocus]) scrollIntoView(chips[st.tvGenreFocus], d.tvGenreBar);
    }

    function selectTvGenre(genre) {
        st.tvActiveGenre = genre;
        st.tvItemFocus   = 0;
        st.tvFocusZone   = 'grid';
        var net = st.tvNetworks[st.tvActiveNetIdx];
        if (!net) return;
        clearFocusInEl(d.tvGenreBar, '.genre-chip', 'genre-chip');
        var chips = d.tvGenreBar ? d.tvGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            var isActive = (i === 0 && genre === '') || (i > 0 && st.tvGenres[i - 1] === genre);
            chips[i].className = chips[i].className.replace(/\bactive\b/g,'').replace(/\s+/g,' ').trim()
                               + (isActive ? ' active' : '');
        }
        apiGet('/tvshows/contents?network_id=' + net.id + (genre ? '&genre=' + encodeURIComponent(genre) : '') + '&page=1&records=30',
        function (res) {
            st.tvItems = toArr(res.data);
            renderTvContentGrid();
        }, function () { showToast('Failed to filter TV shows.'); });
    }

    function renderTvContentGrid() {
        if (!d.tvContentGrid) return;
        d.tvContentGrid.innerHTML = '';
        if (!st.tvItems.length) {
            d.tvContentGrid.innerHTML = '<div class="empty-state"><h3>No Shows</h3>No TV shows found.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.tvItems.length; i++) {
            var item = st.tvItems[i];
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#128250;</div>\'">'
                : '<div class="content-card-ph">&#128250;</div>';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    '<span class="content-type-badge">TV Show</span>' + imgHtml +
                    '<div class="content-card-body"><div class="content-card-title">' + esc(item.title) + '</div>' +
                    '<div class="content-card-type">' + esc(item.subtitle || '') + '</div></div></div>';
        }
        d.tvContentGrid.innerHTML = html;
        var cards = d.tvContentGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, ci) { card.onclick = function () { openTvCard(ci); }; })(cards[k], k);
        }
        if (st.tvFocusZone === 'grid') focusTvItem(st.tvItemFocus >= 0 ? st.tvItemFocus : 0);
    }

    function focusTvItem(idx) {
        st.tvItemFocus = Math.max(0, Math.min(idx, st.tvItems.length - 1));
        var cards = d.tvContentGrid ? d.tvContentGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.tvItemFocus ? ' focused' : '');
        }
        if (cards[st.tvItemFocus]) scrollIntoView(cards[st.tvItemFocus], d.tvContentGrid);
    }

    function openTvCard(idx) {
        var item = st.tvItems[idx];
        if (!item) return;
        st.tvItemFocus = idx;
        var detailType = item.detail_type || 'tvshow';
        setBoot('Loading', 'Loading ' + esc(item.title) + '\u2026');
        capiGet('/content/' + detailType + '/' + item.id, function (res) {
            hideBoot();
            if (res && res.status && res.content) {
                st.serContent     = res.content;
                st.serSeasonFocus = 0;
                st.serEpFocus     = 0;
                st.serFocusZone   = 'seasons';
                st.serBackScreen  = 'tvshows';
                showScreen('series');
                renderSeriesScreen();
            } else {
                showToast('Content detail not available.');
            }
        }, function () { hideBoot(); showToast('Failed to load TV show detail.'); });
    }

    // ── KIDS SCREEN ────────────────────────────────────────────────────────
    function openKids() {
        if (st.kidsNetworks.length) {
            showScreen('kids');
            startKidsSlideTimer();
            focusKidsNet(st.kidsNetFocus);
            return;
        }
        setBoot('Kids', 'Loading kids networks\u2026');
        apiGet('/kids/networks', function (res) {
            st.kidsNetworks    = toArr(res && res.networks ? res.networks : []);
            st.kidsActiveNetIdx= 0;
            st.kidsNetFocus    = 0;
            st.kidsGenres      = [];
            st.kidsActiveGenre = '';
            st.kidsItems       = [];
            st.kidsItemFocus   = 0;
            st.kidsFocusZone   = 'networks';
            hideBoot();
            showScreen('kids');
            renderKidsNetBar();
            if (st.kidsNetworks.length) {
                loadKidsContents(st.kidsNetworks[0].id, '');
            }
        }, function () {
            hideBoot();
            showToast('Failed to load kids networks.');
        });
    }

    function renderKidsNetBar() {
        if (!d.kidsNetBar) return;
        var nets = st.kidsNetworks;
        var html = '';
        for (var i = 0; i < nets.length; i++) {
            var n = nets[i];
            var cls = 'kids-net-chip' + (i === st.kidsActiveNetIdx ? ' active' : '');
            var logo = n.logo ? '<img src="' + escAttr(n.logo) + '" alt="">' : '';
            html += '<div class="' + cls + '" data-idx="' + i + '">' + logo + '<span>' + esc(n.name || '') + '</span></div>';
        }
        d.kidsNetBar.innerHTML = html;
        var chips = d.kidsNetBar.querySelectorAll('.kids-net-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip, ci) { chip.onclick = function () { focusKidsNet(ci); selectKidsNetwork(ci); }; })(chips[k], k);
        }
        if (st.kidsFocusZone === 'networks') focusKidsNet(st.kidsNetFocus);
    }

    function focusKidsNet(idx) {
        if (!d.kidsNetBar) return;
        var chips = d.kidsNetBar.querySelectorAll('.kids-net-chip');
        st.kidsNetFocus = Math.max(0, Math.min(idx, chips.length - 1));
        for (var i = 0; i < chips.length; i++) {
            var base = chips[i].classList.contains('active') ? 'kids-net-chip active' : 'kids-net-chip';
            chips[i].className = base + (i === st.kidsNetFocus && st.kidsFocusZone === 'networks' ? ' focused' : '');
        }
        if (chips[st.kidsNetFocus]) scrollIntoView(chips[st.kidsNetFocus], d.kidsNetBar);
    }

    function selectKidsNetwork(idx) {
        st.kidsActiveNetIdx = idx;
        st.kidsNetFocus     = idx;
        st.kidsFocusZone    = 'networks';
        var net = st.kidsNetworks[idx];
        if (!net) return;
        var chips = d.kidsNetBar ? d.kidsNetBar.querySelectorAll('.kids-net-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = 'kids-net-chip' + (i === idx ? ' active focused' : '');
        }
        st.kidsGenres      = [];
        st.kidsActiveGenre = '';
        st.kidsItems       = [];
        if (d.kidsGenreBar)    d.kidsGenreBar.innerHTML = '';
        if (d.kidsContentGrid) d.kidsContentGrid.innerHTML = '';
        loadKidsContents(net.id, '');
    }

    function loadKidsContents(networkId, genre) {
        var url = '/kids/contents?network_id=' + networkId + (genre ? '&genre=' + encodeURIComponent(genre) : '');
        apiGet(url, function (res) {
            // sliders
            st.kidsSliders = toArr(res && res.sliders ? res.sliders : []);
            renderKidsSlider();
            startKidsSlideTimer();
            // genres
            st.kidsGenres = toArr(res && res.genres ? res.genres : []);
            renderKidsGenreBar();
            // contents
            st.kidsItems     = toArr(res && res.contents ? res.contents : []);
            st.kidsItemFocus = 0;
            renderKidsContentGrid();
        }, function () {
            showToast('Failed to load kids content.');
        });
    }

    function renderKidsSlider() {
        if (!d.kidsSliderWrap) return;
        var sliders = st.kidsSliders;
        if (!sliders.length) { d.kidsSliderWrap.innerHTML = ''; return; }
        var s = sliders[st.kidsSlideIdx % sliders.length];
        if (!s) return;
        var bg = s.backdrop || s.image || '';
        d.kidsSliderWrap.innerHTML = bg
            ? '<img src="' + escAttr(bg) + '" style="width:100%;height:100%;object-fit:cover;display:block;" alt="">'
            : '';
        if (d.kidsBannerTitle) d.kidsBannerTitle.textContent = s.title || 'Kids';
    }

    function startKidsSlideTimer() {
        stopKidsSlideTimer();
        if (st.kidsSliders.length <= 1) return;
        st.kidsSlideTimer = win.setInterval(function () {
            st.kidsSlideIdx = (st.kidsSlideIdx + 1) % st.kidsSliders.length;
            renderKidsSlider();
        }, 5000);
    }

    function stopKidsSlideTimer() {
        if (st.kidsSlideTimer) { win.clearInterval(st.kidsSlideTimer); st.kidsSlideTimer = null; }
    }

    function renderKidsGenreBar() {
        if (!d.kidsGenreBar) return;
        var html = '<div class="genre-chip' + (st.kidsActiveGenre === '' ? ' active' : '') + '" data-genre="">All</div>';
        for (var i = 0; i < st.kidsGenres.length; i++) {
            var g = st.kidsGenres[i];
            html += '<div class="genre-chip' + (st.kidsActiveGenre === g ? ' active' : '') + '" data-genre="' + escAttr(g) + '">' + esc(g) + '</div>';
        }
        d.kidsGenreBar.innerHTML = html;
        var chips = d.kidsGenreBar.querySelectorAll('.genre-chip');
        for (var k = 0; k < chips.length; k++) {
            (function (chip) {
                chip.onclick = function () {
                    var g = chip.getAttribute('data-genre');
                    var netId = st.kidsNetworks[st.kidsActiveNetIdx] ? st.kidsNetworks[st.kidsActiveNetIdx].id : 0;
                    selectKidsGenre(g, netId);
                };
            })(chips[k]);
        }
        if (st.kidsFocusZone === 'genres') focusKidsGenre(st.kidsGenreFocus);
    }

    function focusKidsGenre(idx) {
        if (!d.kidsGenreBar) return;
        var chips = d.kidsGenreBar.querySelectorAll('.genre-chip');
        st.kidsGenreFocus = Math.max(0, Math.min(idx, chips.length - 1));
        for (var i = 0; i < chips.length; i++) {
            var isActive = chips[i].getAttribute('data-genre') === st.kidsActiveGenre;
            chips[i].className = 'genre-chip' + (isActive ? ' active' : '') + (i === st.kidsGenreFocus && st.kidsFocusZone === 'genres' ? ' focused' : '');
        }
        if (chips[st.kidsGenreFocus]) scrollIntoView(chips[st.kidsGenreFocus], d.kidsGenreBar);
    }

    function selectKidsGenre(genre) {
        st.kidsActiveGenre = genre;
        st.kidsGenreFocus  = 0;
        st.kidsFocusZone   = 'genres';
        var netId = st.kidsNetworks[st.kidsActiveNetIdx] ? st.kidsNetworks[st.kidsActiveNetIdx].id : 0;
        if (!netId) return;
        var chips = d.kidsGenreBar ? d.kidsGenreBar.querySelectorAll('.genre-chip') : [];
        for (var i = 0; i < chips.length; i++) {
            chips[i].className = 'genre-chip' + (chips[i].getAttribute('data-genre') === genre ? ' active focused' : '');
        }
        var url = '/kids/contents?network_id=' + netId + (genre ? '&genre=' + encodeURIComponent(genre) : '');
        apiGet(url, function (res) {
            st.kidsItems     = toArr(res && res.contents ? res.contents : []);
            st.kidsItemFocus = 0;
            renderKidsContentGrid();
        }, function () { showToast('Failed to load kids content.'); });
    }

    function renderKidsContentGrid() {
        if (!d.kidsContentGrid) return;
        d.kidsContentGrid.innerHTML = '';
        if (!st.kidsItems.length) {
            d.kidsContentGrid.innerHTML = '<div class="empty-state"><h3>No Shows</h3>No kids shows found.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < st.kidsItems.length; i++) {
            var item = st.kidsItems[i];
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#127920;</div>\'">'
                : '<div class="content-card-ph">&#127920;</div>';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    '<span class="content-type-badge">Kids</span>' + imgHtml +
                    '<div class="content-card-body"><div class="content-card-title">' + esc(item.title) + '</div>' +
                    '<div class="content-card-type">' + esc(item.subtitle || '') + '</div></div></div>';
        }
        d.kidsContentGrid.innerHTML = html;
        var cards = d.kidsContentGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, ci) { card.onclick = function () { openKidsCard(ci); }; })(cards[k], k);
        }
        if (st.kidsFocusZone === 'grid') focusKidsItem(st.kidsItemFocus >= 0 ? st.kidsItemFocus : 0);
    }

    function focusKidsItem(idx) {
        st.kidsItemFocus = Math.max(0, Math.min(idx, st.kidsItems.length - 1));
        var cards = d.kidsContentGrid ? d.kidsContentGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.kidsItemFocus ? ' focused' : '');
        }
        if (cards[st.kidsItemFocus]) scrollIntoView(cards[st.kidsItemFocus], d.kidsContentGrid);
    }

    function openKidsCard(idx) {
        var item = st.kidsItems[idx];
        if (!item) return;
        st.kidsItemFocus = idx;
        var detailType = item.detail_type || 'kids';
        setBoot('Loading', 'Loading ' + esc(item.title) + '\u2026');
        capiGet('/content/' + detailType + '/' + item.id, function (res) {
            hideBoot();
            if (res && res.status && res.content) {
                st.serContent     = res.content;
                st.serSeasonFocus = 0;
                st.serEpFocus     = 0;
                st.serFocusZone   = 'seasons';
                st.serBackScreen  = 'kids';
                showScreen('series');
                renderSeriesScreen();
            } else {
                showToast('Content detail not available.');
            }
        }, function () { hideBoot(); showToast('Failed to load kids show detail.'); });
    }

    // ── SEARCH SCREEN ──────────────────────────────────────────────────────
    var KB_ROWS = [
        ['Q','W','E','R','T','Y','U','I','O','P'],
        ['A','S','D','F','G','H','J','K','L','\u232b'],
        ['Z','X','C','V','B','N','M'],
        ['1','2','3','4','5','6','7','8','9','0'],
        ['SPACE','DEL','CLEAR']
    ];

    function getSrchKbRows() { return KB_ROWS; }

    function openSearch() {
        showScreen('search');
        st.srchFocusZone = 'keyboard';
        st.srchKbRow     = 0;
        st.srchKbCol     = 0;
        if (!d.srchKeyboard || d.srchKeyboard.childElementCount === 0) {
            renderSearchKeyboard();
        }
        focusSrchKey(0, 0);
        if (d.srchQueryText) d.srchQueryText.textContent = st.srchQuery;
    }

    function renderSearchKeyboard() {
        if (!d.srchKeyboard) return;
        var html = '';
        for (var r = 0; r < KB_ROWS.length; r++) {
            html += '<div class="srch-kb-row">';
            for (var c = 0; c < KB_ROWS[r].length; c++) {
                var key = KB_ROWS[r][c];
                var cls = 'srch-key';
                if (key === 'SPACE') cls += ' wide';
                else if (key === 'DEL' || key === 'CLEAR') cls += ' wide del';
                else if (key === '\u232b') cls += ' del';
                html += '<div class="' + cls + '" data-row="' + r + '" data-col="' + c + '">' + esc(key) + '</div>';
            }
            html += '</div>';
        }
        d.srchKeyboard.innerHTML = html;
        var keys = d.srchKeyboard.querySelectorAll('.srch-key');
        for (var i = 0; i < keys.length; i++) {
            (function (key) {
                key.onclick = function () {
                    var row = parseInt(key.getAttribute('data-row'), 10);
                    var col = parseInt(key.getAttribute('data-col'), 10);
                    focusSrchKey(row, col);
                    pressSrchKey(KB_ROWS[row][col]);
                };
            })(keys[i]);
        }
    }

    function focusSrchKey(row, col) {
        st.srchKbRow = row;
        st.srchKbCol = col;
        if (!d.srchKeyboard) return;
        var keys = d.srchKeyboard.querySelectorAll('.srch-key');
        for (var i = 0; i < keys.length; i++) {
            var r = parseInt(keys[i].getAttribute('data-row'), 10);
            var c = parseInt(keys[i].getAttribute('data-col'), 10);
            var isFoc = (r === row && c === col && st.srchFocusZone === 'keyboard');
            var base = keys[i].getAttribute('class').replace(' focused','');
            keys[i].className = base + (isFoc ? ' focused' : '');
        }
    }

    function pressSrchKey(key) {
        if (!key) return;
        if (key === '\u232b' || key === 'DEL') {
            st.srchQuery = st.srchQuery.slice(0, -1);
        } else if (key === 'CLEAR') {
            st.srchQuery = '';
        } else if (key === 'SPACE') {
            st.srchQuery += ' ';
        } else {
            st.srchQuery += key;
        }
        if (d.srchQueryText) d.srchQueryText.textContent = st.srchQuery;
        // debounce search
        if (st.srchTimer) win.clearTimeout(st.srchTimer);
        if (st.srchQuery.length >= 2) {
            st.srchLoading = true;
            st.srchTimer = win.setTimeout(function () {
                doSearch(st.srchQuery, 1);
            }, 400);
        } else {
            st.srchResults = [];
            renderSrchResults();
        }
    }

    function doSearch(query, page) {
        st.srchLoading = true;
        capiGet('/search?q=' + encodeURIComponent(query) + '&page=' + page, function (res) {
            st.srchLoading = false;
            if (res && res.status) {
                var items = toArr(res.items || []);
                if (page === 1) {
                    st.srchResults = items;
                } else {
                    for (var i = 0; i < items.length; i++) st.srchResults.push(items[i]);
                }
                st.srchPage    = page;
                st.srchHasMore = !!(res.pagination && res.pagination.has_more);
            }
            renderSrchResults();
        }, function () {
            st.srchLoading = false;
            showToast('Search failed. Please try again.');
        });
    }

    function renderSrchResults() {
        if (!d.srchResultsGrid) return;
        if (!st.srchResults.length) {
            d.srchResultsGrid.innerHTML = '';
            return;
        }
        var html = '';
        for (var i = 0; i < st.srchResults.length; i++) {
            var item = st.srchResults[i];
            var badge = item.badge || item.type || '';
            var imgHtml = item.image
                ? '<img class="content-card-img" src="' + escAttr(item.image) + '" alt="" onerror="this.className=\'content-card-ph\';this.outerHTML=\'<div class=content-card-ph>&#128247;</div>\'">'
                : '<div class="content-card-ph">&#128247;</div>';
            html += '<div class="content-card" data-idx="' + i + '">' +
                    (badge ? '<span class="content-type-badge">' + esc(badge) + '</span>' : '') + imgHtml +
                    '<div class="content-card-body"><div class="content-card-title">' + esc(item.title || '') + '</div>' +
                    '<div class="content-card-type">' + esc(item.subtitle || '') + '</div></div></div>';
        }
        d.srchResultsGrid.innerHTML = html;
        var cards = d.srchResultsGrid.querySelectorAll('.content-card');
        for (var k = 0; k < cards.length; k++) {
            (function (card, ci) { card.onclick = function () { openSrchResult(ci); }; })(cards[k], k);
        }
        if (st.srchFocusZone === 'results') focusSrchItem(st.srchItemFocus >= 0 ? st.srchItemFocus : 0);
    }

    function focusSrchItem(idx) {
        st.srchItemFocus = Math.max(0, Math.min(idx, st.srchResults.length - 1));
        var cards = d.srchResultsGrid ? d.srchResultsGrid.querySelectorAll('.content-card') : [];
        for (var i = 0; i < cards.length; i++) {
            cards[i].className = 'content-card' + (i === st.srchItemFocus ? ' focused' : '');
        }
        if (cards[st.srchItemFocus]) scrollIntoView(cards[st.srchItemFocus], d.srchResultsGrid);
    }

    function openSrchResult(idx) {
        var item = st.srchResults[idx];
        if (!item) return;
        st.srchItemFocus = idx;

        if (item.action === 'play') {
            var pt   = item.play_type || '';
            var pType = (pt === 'itv' || pt === 'live') ? 'live' : (item.detail_type || pt || 'movie');
            var pLabel = item.title || '';
            capiGet('/play/' + pType + '/' + item.id, function (res) {
                if (res && res.playback && res.playback.url) {
                    createLink(pType, res.playback.url, pLabel);
                } else {
                    showToast('Playback not available.');
                }
            }, function () { showToast('Failed to load playback.'); });
            return;
        }

        // action === 'detail'
        var detailType = item.detail_type || item.type || 'webseries';
        setBoot('Loading', 'Loading ' + esc(item.title || '') + '\u2026');
        capiGet('/content/' + detailType + '/' + item.id, function (res) {
            hideBoot();
            if (res && res.status && res.content) {
                st.serContent     = res.content;
                st.serSeasonFocus = 0;
                st.serEpFocus     = 0;
                st.serFocusZone   = 'seasons';
                st.serBackScreen  = 'search';
                showScreen('series');
                renderSeriesScreen();
            } else {
                showToast('Content detail not available.');
            }
        }, function () { hideBoot(); showToast('Failed to load content detail.'); });
    }

    // ── KEY CODES ──────────────────────────────────────────────────────────
    function isBack(c)     { return c===8||c===27||c===461||c===10009; }
    function isOk(c)       { return c===13||c===32; }
    function isUp(c)       { return c===38||c===63232; }
    function isDown(c)     { return c===40||c===63233; }
    function isLeft(c)     { return c===37||c===63234; }
    function isRight(c)    { return c===39||c===63235; }
    function isPageUp(c)   { return c===33||c===427||c===573; }
    function isPageDown(c) { return c===34||c===428||c===574; }

    // ── INIT ───────────────────────────────────────────────────────────────
    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

}(window, document));
