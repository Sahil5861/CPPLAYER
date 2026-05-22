(function (win, doc) {
    'use strict';

    var config = win.MAG_PORTAL_CONFIG || {};
    var loadUrl = config.loadUrl || '/mag/server/load.php';
    var apiBaseUrl = config.apiBaseUrl || '/c/api';

    var MENU_LABELS = {
        'contents': 'OTT Apps',
        'recently-added': 'Latest Movies',
        'kids': 'Kids Zone',
        'stage-shows': 'Stage Shows'
    };

    var MENU_ORDER = {
        'search': 0,
        'live': 1,
        'contents': 2,
        'recently-added': 3,
        'web-series': 4,
        'tv-shows': 5,
        'tv-shows-pak': 6,
        'religious': 7,
        'sports': 8,
        'kids': 9,
        'stage-shows': 10,
        'settings': 11
    };

    var KEYBOARD_ROWS = [
        ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
        ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'],
        ['U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3'],
        ['4', '5', '6', '7', '8', '9', 'SPACE', 'DEL'],
        ['CLEAR', 'SEARCH', 'CLOSE']
    ];

    var state = {
        mac: '',
        token: '',
        profile: null,
        sections: [],
        menuIndex: 0,
        activeSectionSlug: '',
        focusArea: 'menu',
        sectionPayload: null,
        sectionParams: {},
        networkFocusIndex: 0,
        genreFocusIndex: 0,
        channelFocusIndex: 0,
        itemFocusIndex: 0,
        visibleCards: [],
        slideIndex: 0,
        slideTimer: null,
        detailVisible: false,
        detailData: null,
        detailSeasonIndex: 0,
        detailEpisodeIndex: 0,
        detailPanel: 'seasons',
        keyboardVisible: false,
        keyboardRow: 0,
        keyboardCol: 0,
        searchQuery: '',
        searchFilters: [],
        searchRows: [],
        searchActiveFilterIndex: 0,
        searchFocusFilterIndex: 0,
        searchItemFocusIndex: 0,
        searchPagination: {
            current_page: 1,
            last_page: 1,
            per_page: 18,
            total: 0,
            has_more: false
        },
        searchHero: null,
        searchDebounce: null,
        isPlaying: false,
        activePlayerMode: '',
        playbackType: '',
        playbackTitle: '',
        playbackMeta: '',
        hudVisible: false,
        toastTimer: null,
        sectionRequestSerial: 0,
        searchRequestSerial: 0
    };

    var dom = {};

    function byId(id) {
        return doc.getElementById(id);
    }

    function safeTrim(value) {
        return String(value || '').replace(/^\s+|\s+$/g, '');
    }

    function normalizeClassName(value) {
        return safeTrim(String(value || '').replace(/\s+/g, ' '));
    }

    function hasClass(node, className) {
        var source;

        if (!node || !className) {
            return false;
        }

        source = ' ' + normalizeClassName(node.className) + ' ';
        return source.indexOf(' ' + className + ' ') !== -1;
    }

    function addClass(node, className) {
        if (!node || !className || hasClass(node, className)) {
            return;
        }

        node.className = normalizeClassName((node.className || '') + ' ' + className);
    }

    function removeClass(node, className) {
        var source;
        var token;

        if (!node || !className) {
            return;
        }

        source = ' ' + normalizeClassName(node.className) + ' ';
        token = ' ' + className + ' ';

        while (source.indexOf(token) !== -1) {
            source = source.replace(token, ' ');
        }

        node.className = normalizeClassName(source);
    }

    function setClassState(node, className, enabled) {
        if (enabled) {
            addClass(node, className);
            return;
        }

        removeClass(node, className);
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function escapeAttribute(value) {
        return escapeHtml(value);
    }

    function escapeCssUrl(value) {
        return String(value || '')
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/\(/g, '\\(')
            .replace(/\)/g, '\\)');
    }

    function isArray(value) {
        return Object.prototype.toString.call(value) === '[object Array]';
    }

    function toArray(value) {
        return isArray(value) ? value : [];
    }

    function clamp(value, min, max) {
        if (value < min) {
            return min;
        }

        if (value > max) {
            return max;
        }

        return value;
    }

    function indexOfSection(slug) {
        var i;

        for (i = 0; i < state.sections.length; i += 1) {
            if ((state.sections[i].slug || '') === slug) {
                return i;
            }
        }

        return -1;
    }

    function indexOfById(items, expectedId) {
        var i;

        for (i = 0; i < items.length; i += 1) {
            if (String(items[i].id) === String(expectedId)) {
                return i;
            }
        }

        return -1;
    }

    function buildQuery(params) {
        var parts = [];
        var key;

        for (key in params) {
            if (!params.hasOwnProperty(key)) {
                continue;
            }

            if (params[key] === '' || params[key] === null || typeof params[key] === 'undefined') {
                continue;
            }

            parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
        }

        return parts.join('&');
    }

    function stopEvent(event) {
        if (!event) {
            return false;
        }

        if (event.preventDefault) {
            event.preventDefault();
        }

        if (event.stopPropagation) {
            event.stopPropagation();
        }

        event.returnValue = false;
        return false;
    }

    function cacheDom() {
        dom.app = byId('app');
        dom.menuScroll = byId('menu-scroll');
        dom.menuList = byId('menu-list');
        dom.main = byId('main');
        dom.networkShell = byId('network-shell');
        dom.networksLabel = byId('networks-label');
        dom.networkRow = byId('network-row');
        dom.heroShell = byId('hero-shell');
        dom.heroBackdrop = byId('hero-backdrop');
        dom.heroBadge = byId('hero-badge');
        dom.heroTitle = byId('hero-title');
        dom.heroSubtitle = byId('hero-subtitle');
        dom.heroMeta = byId('hero-meta');
        dom.heroDots = byId('hero-dots');
        dom.sectionLabel = byId('section-label');
        dom.sectionContext = byId('section-context');
        dom.sectionFocus = byId('section-focus');
        dom.sectionPage = byId('section-page');
        dom.searchShell = byId('search-shell');
        dom.searchInput = byId('search-input');
        dom.searchText = byId('search-text');
        dom.genreShell = byId('genre-shell');
        dom.genreLabel = byId('genre-label');
        dom.genreRow = byId('genre-row');
        dom.channelShell = byId('channel-shell');
        dom.channelLabel = byId('channel-label');
        dom.channelRow = byId('channel-row');
        dom.contentShell = byId('content-shell');
        dom.itemsShell = byId('items-shell');
        dom.itemsRow = byId('items-row');
        dom.emptyState = byId('empty-state');
        dom.detailOverlay = byId('detail-overlay');
        dom.detailBackdrop = byId('detail-backdrop');
        dom.detailKicker = byId('detail-kicker');
        dom.detailTitle = byId('detail-title');
        dom.detailPlot = byId('detail-plot');
        dom.detailMeta = byId('detail-meta');
        dom.detailSeasonsWrap = byId('detail-seasons-wrap');
        dom.detailEpisodesWrap = byId('detail-episodes-wrap');
        dom.detailSeasons = byId('detail-seasons');
        dom.detailEpisodes = byId('detail-episodes');
        dom.keyboardOverlay = byId('keyboard-overlay');
        dom.keyboardQuery = byId('keyboard-query');
        dom.keyboardGrid = byId('keyboard-grid');
        dom.bootOverlay = byId('boot-overlay');
        dom.bootTitle = byId('boot-title');
        dom.bootMsg = byId('boot-msg');
        dom.toast = byId('toast');
        dom.playbackHud = byId('playback-hud');
        dom.hudLabel = byId('hud-label');
        dom.hudTitle = byId('hud-title');
        dom.hudMeta = byId('hud-meta');
        dom.hudHelp = byId('hud-help');
    }

    function boot() {
        cacheDom();
        bindKeys();
        bindWindowEvents();
        applyResponsiveLayout();
        renderKeyboard();
        setGraphicOnTop();
        setBoot('Starting CP Players', 'Detecting your MAG device and loading your TV portal.', true);
        detectMacAndStart();
    }

    function bindKeys() {
        doc.onkeydown = function (event) {
            return handleKey(event || win.event);
        };
    }

    function bindWindowEvents() {
        var handler = function () {
            applyResponsiveLayout();

            if (state.sections.length || state.sectionPayload || state.activeSectionSlug) {
                renderCurrentView();
            }
        };

        if (win.addEventListener) {
            win.addEventListener('resize', handler, false);
            return;
        }

        win.onresize = handler;
    }

    function hasNativeStbRuntime() {
        return !!(win.gSTB || win.stb || win.STB || win.stbPlayerManager);
    }

    function getRawViewport() {
        return {
            width: win.innerWidth || (doc.documentElement && doc.documentElement.clientWidth) || 1280,
            height: win.innerHeight || (doc.documentElement && doc.documentElement.clientHeight) || 720
        };
    }

    function getSafeInsets(rawViewport) {
        var width = rawViewport && rawViewport.width ? rawViewport.width : 1280;
        var height = rawViewport && rawViewport.height ? rawViewport.height : 720;

        if (!hasNativeStbRuntime()) {
            return {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            };
        }

        return {
            left: Math.max(24, Math.round(width * 0.028)),
            right: Math.max(24, Math.round(width * 0.028)),
            top: Math.max(18, Math.round(height * 0.026)),
            bottom: Math.max(18, Math.round(height * 0.03))
        };
    }

    function applySafeArea(insets, profile) {
        var sidebarWidth = profile && profile.tightLayout ? 174 : 190;
        var hudLeft = profile && profile.tightLayout ? 22 : 28;

        if (dom.app) {
            dom.app.style.left = insets.left + 'px';
            dom.app.style.right = insets.right + 'px';
            dom.app.style.top = insets.top + 'px';
            dom.app.style.bottom = insets.bottom + 'px';
        }

        if (dom.detailOverlay) {
            dom.detailOverlay.style.left = (insets.left + sidebarWidth) + 'px';
            dom.detailOverlay.style.right = insets.right + 'px';
            dom.detailOverlay.style.top = insets.top + 'px';
            dom.detailOverlay.style.bottom = insets.bottom + 'px';
        }

        if (dom.keyboardOverlay) {
            dom.keyboardOverlay.style.left = insets.left + 'px';
            dom.keyboardOverlay.style.right = insets.right + 'px';
            dom.keyboardOverlay.style.top = insets.top + 'px';
            dom.keyboardOverlay.style.bottom = insets.bottom + 'px';
        }

        if (dom.bootOverlay) {
            dom.bootOverlay.style.left = insets.left + 'px';
            dom.bootOverlay.style.right = insets.right + 'px';
            dom.bootOverlay.style.top = insets.top + 'px';
            dom.bootOverlay.style.bottom = insets.bottom + 'px';
        }

        if (dom.toast) {
            dom.toast.style.bottom = (insets.bottom + 26) + 'px';
        }

        if (dom.playbackHud) {
            dom.playbackHud.style.left = (insets.left + hudLeft) + 'px';
            dom.playbackHud.style.bottom = (insets.bottom + 24) + 'px';
        }
    }

    function getLayoutProfile(height) {
        if (height <= 860) {
            return {
                shortLayout: true,
                tightLayout: true,
                menuTop: 116,
                menuBottom: 54,
                networkTop: 12,
                networkHeight: 52,
                networkGap: 10,
                heroGap: 12,
                bottomPad: 10,
                contentPaddingTop: 10,
                sectionBarHeight: 36,
                sectionBarGap: 4,
                searchControlHeight: 44,
                searchGap: 6,
                labelHeight: 14,
                labelGap: 8,
                chipRowHeight: 38,
                filterGap: 6,
                itemsAreaHeight: 174,
                heroMin: 200
            };
        }

        if (height <= 980) {
            return {
                shortLayout: true,
                tightLayout: false,
                menuTop: 136,
                menuBottom: 62,
                networkTop: 14,
                networkHeight: 54,
                networkGap: 12,
                heroGap: 14,
                bottomPad: 12,
                contentPaddingTop: 12,
                sectionBarHeight: 40,
                sectionBarGap: 6,
                searchControlHeight: 48,
                searchGap: 8,
                labelHeight: 15,
                labelGap: 8,
                chipRowHeight: 40,
                filterGap: 8,
                itemsAreaHeight: 196,
                heroMin: 236
            };
        }

        return {
            shortLayout: false,
            tightLayout: false,
            menuTop: 150,
            menuBottom: 70,
            networkTop: 18,
            networkHeight: 58,
            networkGap: 14,
            heroGap: 18,
            bottomPad: 16,
            contentPaddingTop: 14,
            sectionBarHeight: 44,
            sectionBarGap: 8,
            searchControlHeight: 52,
            searchGap: 12,
            labelHeight: 16,
            labelGap: 8,
            chipRowHeight: 44,
            filterGap: 12,
            itemsAreaHeight: 224,
            heroMin: 280
        };
    }

    function searchShellHeight(profile) {
        return profile.searchControlHeight + profile.searchGap;
    }

    function filterShellHeight(profile) {
        return profile.labelHeight + profile.labelGap + profile.chipRowHeight + profile.filterGap;
    }

    function currentContentShellHeight(profile) {
        var height = profile.contentPaddingTop + profile.sectionBarHeight + profile.sectionBarGap;

        if (state.activeSectionSlug === 'search') {
            height += searchShellHeight(profile);

            if (state.searchFilters.length > 0) {
                height += filterShellHeight(profile);
            }
        } else {
            if (hasVisibleGenres()) {
                height += filterShellHeight(profile);
            }

            if (hasVisibleChannels()) {
                height += filterShellHeight(profile);
            }
        }

        height += profile.itemsAreaHeight;

        return height;
    }

    function applyResponsiveLayout() {
        var rawViewport = getRawViewport();
        var viewport = getScreenViewport();
        var height = viewport.height || 720;
        var profile = getLayoutProfile(height);
        var heroTop = profile.networkTop + profile.networkHeight + profile.networkGap;
        var contentHeight = currentContentShellHeight(profile);
        var contentTop = height - profile.bottomPad - contentHeight;
        var heroHeight = contentTop - profile.heroGap - heroTop;

        if (heroHeight < profile.heroMin) {
            heroHeight = profile.heroMin;
            contentTop = heroTop + profile.heroGap + heroHeight;
            contentHeight = Math.max(height - profile.bottomPad - contentTop, profile.itemsAreaHeight + profile.contentPaddingTop + profile.sectionBarHeight);
        }

        setClassState(doc.body, 'layout-short', profile.shortLayout);
        setClassState(doc.body, 'layout-tight', profile.tightLayout);
        applySafeArea(getSafeInsets(rawViewport), profile);

        dom.menuScroll.style.top = profile.menuTop + 'px';
        dom.menuScroll.style.bottom = profile.menuBottom + 'px';
        dom.networkShell.style.top = profile.networkTop + 'px';
        dom.networkShell.style.height = profile.networkHeight + 'px';
        dom.heroShell.style.top = heroTop + 'px';
        dom.heroShell.style.height = heroHeight + 'px';
        dom.heroShell.style.bottom = 'auto';
        dom.contentShell.style.top = contentTop + 'px';
        dom.contentShell.style.height = contentHeight + 'px';
        dom.contentShell.style.bottom = 'auto';
        dom.contentShell.style.paddingTop = profile.contentPaddingTop + 'px';
    }

    function handleKey(event) {
        var code = event && (event.which || event.keyCode);

        if (state.keyboardVisible) {
            return handleKeyboardKey(code, event);
        }

        if (isBackKey(code)) {
            if (state.isPlaying) {
                stopPlayback(true);
                return stopEvent(event);
            }

            if (state.detailVisible) {
                closeDetail();
                return stopEvent(event);
            }

            if (state.focusArea !== 'menu') {
                state.focusArea = 'menu';
                renderCurrentView();
                return stopEvent(event);
            }

            return stopEvent(event);
        }

        if (state.isPlaying) {
            if (isOkKey(code)) {
                togglePlaybackHud();
                return stopEvent(event);
            }

            if (isUpKey(code)) {
                if (state.playbackType === 'live') {
                    moveLivePlaybackChannel(-1);
                }
                return stopEvent(event);
            }

            if (isDownKey(code)) {
                if (state.playbackType === 'live') {
                    moveLivePlaybackChannel(1);
                }
                return stopEvent(event);
            }

            if (isLeftKey(code) || isRightKey(code) || isPageUpKey(code) || isPageDownKey(code)) {
                showPlaybackHud(true);
                return stopEvent(event);
            }
        }

        if (state.detailVisible) {
            return handleDetailKey(code, event);
        }

        if (state.activeSectionSlug === 'search' && handleSearchTyping(code)) {
            return stopEvent(event);
        }

        if (state.focusArea === 'menu') {
            return handleMenuKey(code, event);
        }

        return handleMainKey(code, event);
    }

    function handleMenuKey(code, event) {
        var selectedSlug = state.sections[state.menuIndex] ? state.sections[state.menuIndex].slug : '';

        if (isUpKey(code)) {
            moveMenu(-1);
            return stopEvent(event);
        }

        if (isDownKey(code)) {
            moveMenu(1);
            return stopEvent(event);
        }

        if (isOkKey(code)) {
            activateMenuSelection();
            return stopEvent(event);
        }

        if (isRightKey(code)) {
            if (selectedSlug && selectedSlug !== state.activeSectionSlug) {
                showToast('Press OK to open this section.');
                return stopEvent(event);
            }

            enterMainArea();
            return stopEvent(event);
        }

        return true;
    }

    function handleMainKey(code, event) {
        if (isUpKey(code)) {
            moveFocusVertical(-1);
            return stopEvent(event);
        }

        if (isDownKey(code)) {
            moveFocusVertical(1);
            return stopEvent(event);
        }

        if (isLeftKey(code)) {
            moveFocusHorizontal(-1);
            return stopEvent(event);
        }

        if (isRightKey(code)) {
            moveFocusHorizontal(1);
            return stopEvent(event);
        }

        if (isOkKey(code)) {
            activateFocusedArea();
            return stopEvent(event);
        }

        if (isPageUpKey(code)) {
            handlePaging(-1);
            return stopEvent(event);
        }

        if (isPageDownKey(code)) {
            handlePaging(1);
            return stopEvent(event);
        }

        return true;
    }

    function handleSearchTyping(code) {
        var character = '';

        if (state.focusArea !== 'search-input') {
            return false;
        }

        if (code === 8) {
            if (state.searchQuery.length > 0) {
                state.searchQuery = state.searchQuery.slice(0, -1);
                onSearchQueryChanged();
            }
            return true;
        }

        if (code >= 48 && code <= 57) {
            character = String.fromCharCode(code);
        } else if (code >= 65 && code <= 90) {
            character = String.fromCharCode(code);
        }

        if (!character) {
            return false;
        }

        state.searchQuery += character;
        onSearchQueryChanged();
        return true;
    }

    function handleDetailKey(code, event) {
        if (isLeftKey(code)) {
            if (state.detailPanel === 'episodes') {
                state.detailPanel = 'seasons';
                renderDetail();
                return stopEvent(event);
            }

            closeDetail();
            return stopEvent(event);
        }

        if (isRightKey(code)) {
            if (state.detailPanel === 'seasons') {
                state.detailPanel = 'episodes';
                renderDetail();
            }
            return stopEvent(event);
        }

        if (isUpKey(code)) {
            if (state.detailPanel === 'seasons') {
                state.detailSeasonIndex = clamp(state.detailSeasonIndex - 1, 0, currentDetailSeasons().length - 1);
                state.detailEpisodeIndex = 0;
            } else {
                state.detailEpisodeIndex = clamp(state.detailEpisodeIndex - 1, 0, currentDetailEpisodes().length - 1);
            }
            renderDetail();
            return stopEvent(event);
        }

        if (isDownKey(code)) {
            if (state.detailPanel === 'seasons') {
                state.detailSeasonIndex = clamp(state.detailSeasonIndex + 1, 0, currentDetailSeasons().length - 1);
                state.detailEpisodeIndex = 0;
            } else {
                state.detailEpisodeIndex = clamp(state.detailEpisodeIndex + 1, 0, currentDetailEpisodes().length - 1);
            }
            renderDetail();
            return stopEvent(event);
        }

        if (isOkKey(code)) {
            if (state.detailPanel === 'seasons') {
                state.detailPanel = 'episodes';
                state.detailEpisodeIndex = 0;
                renderDetail();
                return stopEvent(event);
            }

            playDetailEpisode();
            return stopEvent(event);
        }

        if (isPageUpKey(code) || isPageDownKey(code)) {
            return stopEvent(event);
        }

        return true;
    }

    function handleKeyboardKey(code, event) {
        if (isBackKey(code)) {
            closeKeyboard();
            return stopEvent(event);
        }

        if (isUpKey(code)) {
            moveKeyboard(-1, 0);
            return stopEvent(event);
        }

        if (isDownKey(code)) {
            moveKeyboard(1, 0);
            return stopEvent(event);
        }

        if (isLeftKey(code)) {
            moveKeyboard(0, -1);
            return stopEvent(event);
        }

        if (isRightKey(code)) {
            moveKeyboard(0, 1);
            return stopEvent(event);
        }

        if (isOkKey(code)) {
            pressKeyboardKey(getKeyboardKey(state.keyboardRow, state.keyboardCol));
            return stopEvent(event);
        }

        return true;
    }

    function detectMacAndStart() {
        var mac = readMacFromQuery() || readMacFromDevice();

        if (!mac) {
            showFatal('MAC address required', 'Open this page with ?mac=YOUR_DEVICE_MAC in a browser, or verify the portal is running on a mapped MAG / STB device.');
            return;
        }

        state.mac = normalizeMac(mac);
        runHandshake();
    }

    function readMacFromQuery() {
        var match = String(win.location.search || '').match(/[?&]mac=([^&]+)/i);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function readMacFromDevice() {
        var providers = [win.stb, win.gSTB, win.STB];
        var methods = ['GetMacAddress', 'getMacAddress', 'GetMACAddress', 'getMacAddr', 'GetMacAddr'];
        var i;
        var j;

        for (i = 0; i < providers.length; i += 1) {
            if (!providers[i]) {
                continue;
            }

            for (j = 0; j < methods.length; j += 1) {
                if (typeof providers[i][methods[j]] === 'function') {
                    try {
                        return providers[i][methods[j]]();
                    } catch (error) {
                    }
                }
            }
        }

        return '';
    }

    function normalizeMac(value) {
        return safeTrim(value).toUpperCase();
    }

    function runHandshake() {
        setBoot('Authenticating Device', 'Connecting ' + escapeHtml(state.mac) + ' to the CP Players portal.', true);
        portalRequest({
            type: 'stb',
            action: 'handshake'
        }, function (data) {
            state.token = data && data.token ? data.token : '';

            if (!state.token) {
                showFatal('Authentication failed', 'The portal did not return a valid session token.');
                return;
            }

            loadBootstrap();
        }, function (message) {
            showFatal('Handshake failed', message || 'Unable to authenticate this device.');
        });
    }

    function loadBootstrap() {
        setBoot('Loading Portal', 'Fetching available sections and the mapped subscription.', true);
        apiGet('/bootstrap', {}, function (data) {
            state.profile = data.profile || null;
            state.sections = normalizeSections(data.sections || []);

            if (!state.sections.length) {
                showFatal('No Sections Available', 'This account did not return any TV sections.');
                return;
            }

            initializeDefaultSection();
        }, function (message) {
            showFatal('Portal load failed', message || 'Unable to load portal bootstrap data.');
        });
    }

    function normalizeSections(sections) {
        var source = toArray(sections).slice(0);
        var list = [];
        var i;

        for (i = 0; i < source.length; i += 1) {
            if ((source[i].slug || '') === 'settings') {
                continue;
            }

            list.push(source[i]);
        }

        list.sort(function (left, right) {
            var leftOrder = typeof MENU_ORDER[left.slug] === 'number' ? MENU_ORDER[left.slug] : 500;
            var rightOrder = typeof MENU_ORDER[right.slug] === 'number' ? MENU_ORDER[right.slug] : 500;

            if (leftOrder < rightOrder) {
                return -1;
            }

            if (leftOrder > rightOrder) {
                return 1;
            }

            return 0;
        });

        return list;
    }

    function initializeDefaultSection() {
        var liveIndex = indexOfSection('live');

        if (liveIndex === -1) {
            liveIndex = 0;
        }

        state.menuIndex = liveIndex;
        state.activeSectionSlug = state.sections[liveIndex].slug;
        state.focusArea = 'menu';
        renderMenu();
        openSection(state.activeSectionSlug, true, true);
    }

    function renderMenu() {
        var html = [];
        var i;
        var section;
        var activeSlug = state.activeSectionSlug;

        for (i = 0; i < state.sections.length; i += 1) {
            section = state.sections[i];
            html.push(
                '<div class="menu-item' +
                    (section.slug === activeSlug ? ' current' : '') +
                    (i === state.menuIndex && state.focusArea === 'menu' ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    escapeHtml(menuLabel(section)) +
                '</div>'
            );
        }

        dom.menuList.innerHTML = html.join('');

        bindMenuClicks();
        ensureMenuFocusVisible();
    }

    function bindMenuClicks() {
        var nodes = dom.menuList.getElementsByTagName('div');
        var i;

        for (i = 0; i < nodes.length; i += 1) {
            (function (index) {
                nodes[index].onclick = function () {
                    state.menuIndex = index;
                    state.focusArea = 'menu';
                    openSection(state.sections[index].slug, true, true);
                };
            })(i);
        }
    }

    function menuLabel(section) {
        return MENU_LABELS[section.slug] || section.label || section.title || section.slug || 'Section';
    }

    function moveMenu(direction) {
        state.menuIndex = clamp(state.menuIndex + direction, 0, state.sections.length - 1);
        renderMenu();
        dom.sectionFocus.innerHTML = escapeHtml(focusAreaLabel());
    }

    function activateMenuSelection() {
        var selectedSection = state.sections[state.menuIndex];

        if (!selectedSection || !selectedSection.slug) {
            return;
        }

        if (selectedSection.slug === state.activeSectionSlug) {
            enterMainArea();
            return;
        }

        openSection(selectedSection.slug, true, true);
    }

    function enterMainArea() {
        if (state.activeSectionSlug === 'search') {
            state.focusArea = 'search-input';
            renderCurrentView();
            return;
        }

        state.focusArea = firstMainZone();
        renderCurrentView();
    }

    function firstMainZone() {
        var zones = getMainZones();
        return zones.length ? zones[0] : 'items';
    }

    function getMainZones() {
        var zones = [];

        if (state.activeSectionSlug === 'search') {
            zones.push('search-input');

            if (state.searchFilters.length > 0) {
                zones.push('search-filters');
            }

            zones.push('items');
            return zones;
        }

        if (hasVisibleNetworks()) {
            zones.push('networks');
        }

        if (hasVisibleGenres()) {
            zones.push('genres');
        }

        if (hasVisibleChannels()) {
            zones.push('channels');
        }

        zones.push('items');
        return zones;
    }

    function hasVisibleNetworks() {
        var payload = state.sectionPayload || {};
        return toArray(payload.networks).length > 0;
    }

    function hasVisibleGenres() {
        var genres = toArray((state.sectionPayload || {}).genres);
        return genres.length > 1;
    }

    function hasVisibleChannels() {
        var channels = toArray((state.sectionPayload || {}).channels);

        if (channels.length <= 1) {
            return false;
        }

        return true;
    }

    function openSection(slug, resetPage, preserveMenuFocus) {
        var params;
        var requestSerial;

        stopHeroTimer();
        state.detailVisible = false;
        state.keyboardVisible = false;
        state.visibleCards = [];
        state.activeSectionSlug = slug;

        if (!preserveMenuFocus) {
            state.menuIndex = indexOfSection(slug);
        }

        renderMenu();

        if (slug === 'search') {
            setBoot('', '', false);
            state.sectionPayload = null;
            renderCurrentView();

            if (state.searchQuery.length >= 2 && !state.searchRows.length) {
                requestSearch(1, false);
            }

            return;
        }

        params = getSectionParams(slug);

        if (resetPage) {
            params.page = 1;
        }

        state.sectionParams[slug] = params;

        setBoot('Loading ' + escapeHtml(menuLabel(getSectionBySlug(slug))), 'Preparing the requested section for this device.', true);
        state.sectionRequestSerial += 1;
        requestSerial = state.sectionRequestSerial;

        apiGet('/section/' + slug, params, function (data) {
            if (requestSerial !== state.sectionRequestSerial || state.activeSectionSlug !== slug) {
                return;
            }

            state.sectionPayload = data || {};
            syncFocusStateFromPayload();
            renderCurrentView();
            setBoot('', '', false);
        }, function (message) {
            if (requestSerial !== state.sectionRequestSerial || state.activeSectionSlug !== slug) {
                return;
            }

            showFatal('Section load failed', message || 'Unable to load the requested section.');
        });
    }

    function getSectionBySlug(slug) {
        var index = indexOfSection(slug);
        return index >= 0 ? state.sections[index] : {};
    }

    function getSectionParams(slug) {
        if (!state.sectionParams[slug]) {
            state.sectionParams[slug] = {
                network: 0,
                genre: '',
                channel: 0,
                page: 1
            };
        }

        return {
            network: state.sectionParams[slug].network || 0,
            genre: state.sectionParams[slug].genre || '',
            channel: state.sectionParams[slug].channel || 0,
            page: state.sectionParams[slug].page || 1
        };
    }

    function syncFocusStateFromPayload() {
        var payload = state.sectionPayload || {};
        var networks = toArray(payload.networks);
        var genres = toArray(payload.genres);
        var channels = toArray(payload.channels);
        var cards = buildVisibleCards();

        state.networkFocusIndex = Math.max(0, indexOfById(networks, payload.selected_network_id));
        state.genreFocusIndex = Math.max(0, indexOfById(genres, payload.selected_genre));
        state.channelFocusIndex = Math.max(0, indexOfById(channels, payload.selected_channel_id));
        state.itemFocusIndex = clamp(state.itemFocusIndex, 0, Math.max(cards.length - 1, 0));
        state.visibleCards = cards;
        state.slideIndex = 0;

        if (state.focusArea !== 'menu') {
            if (state.focusArea === 'search-input' || state.focusArea === 'search-filters') {
                return;
            }

            if (getMainZones().indexOf(state.focusArea) === -1) {
                state.focusArea = firstMainZone();
            }
        }
    }

    function renderCurrentView() {
        applyResponsiveLayout();
        renderMenu();
        renderHero();
        renderSectionBar();
        renderSearchShell();
        renderContentShellLayout();
        renderNetworkRow();
        renderGenreRow();
        renderChannelRow();
        renderItemsRow();
        renderFocusZones();
        renderDetail();
        renderKeyboardState();
    }

    function renderHero() {
        var hero = currentHero();
        var slideList = currentSlides();
        var activeSlide = slideList.length ? slideList[state.slideIndex] : null;
        var backdrop = activeSlide && activeSlide.image ? activeSlide.image : (hero.backdrop || hero.image || '');
        var title = activeSlide && activeSlide.title ? activeSlide.title : (hero.title || menuLabel(getSectionBySlug(state.activeSectionSlug)));
        var subtitle = activeSlide && activeSlide.subtitle ? activeSlide.subtitle : (hero.subtitle || '');
        var badge = hero.badge || menuLabel(getSectionBySlug(state.activeSectionSlug));
        var meta = toArray(hero.meta);
        var dots = [];
        var i;

        dom.heroBackdrop.style.backgroundImage = backdrop ? "url('" + escapeCssUrl(backdrop) + "')" : 'none';
        dom.heroBadge.innerHTML = badge ? escapeHtml(badge) : '';
        dom.heroTitle.innerHTML = escapeHtml(title || '');
        dom.heroSubtitle.innerHTML = escapeHtml(subtitle || '');
        dom.heroMeta.innerHTML = renderMeta(meta);

        if (slideList.length > 1) {
            for (i = 0; i < slideList.length; i += 1) {
                dots.push('<span class="hero-dot' + (i === state.slideIndex ? ' active' : '') + '"></span>');
            }
            dom.heroDots.innerHTML = dots.join('');
            startHeroTimer();
        } else {
            stopHeroTimer();
            dom.heroDots.innerHTML = '';
        }
    }

    function currentHero() {
        if (state.activeSectionSlug === 'search') {
            return state.searchHero || {
                title: 'Search',
                subtitle: 'Search across Live TV, movies, web series, TV shows, kids, religious, sports and stage shows.',
                badge: 'Universal Search',
                meta: []
            };
        }

        return (state.sectionPayload && state.sectionPayload.hero) ? state.sectionPayload.hero : {
            title: menuLabel(getSectionBySlug(state.activeSectionSlug)),
            subtitle: '',
            badge: menuLabel(getSectionBySlug(state.activeSectionSlug)),
            meta: []
        };
    }

    function currentSlides() {
        if (state.activeSectionSlug === 'search') {
            return [];
        }

        return toArray((state.sectionPayload || {}).slides);
    }

    function startHeroTimer() {
        var slides = currentSlides();

        stopHeroTimer();

        if (slides.length < 2) {
            return;
        }

        state.slideTimer = win.setInterval(function () {
            state.slideIndex = (state.slideIndex + 1) % slides.length;
            renderHero();
        }, 4500);
    }

    function stopHeroTimer() {
        if (state.slideTimer) {
            win.clearInterval(state.slideTimer);
            state.slideTimer = null;
        }
    }

    function renderSectionBar() {
        var section = getSectionBySlug(state.activeSectionSlug);
        var context = '';
        var pageText = '';
        var pagination = currentPagination();
        var selectedNetwork = selectedNetworkLabel();

        dom.sectionLabel.style.color = section && section.accent ? section.accent : '#1f9bb0';
        dom.sectionLabel.innerHTML = escapeHtml(menuLabel(section));

        if (state.activeSectionSlug === 'search') {
            context = state.searchQuery ? '"' + state.searchQuery + '"' : 'Universal Search';
        } else if (selectedNetwork) {
            context = selectedNetwork;
        } else if (section && section.copy) {
            context = section.copy;
        }

        if (pagination.total) {
            pageText = 'Page ' + pagination.current_page + ' / ' + pagination.last_page;
        } else if (pagination.current_page > 1) {
            pageText = 'Page ' + pagination.current_page;
        }

        dom.sectionContext.innerHTML = escapeHtml(context || '');
        dom.sectionFocus.innerHTML = escapeHtml(focusAreaLabel());
        dom.sectionPage.innerHTML = escapeHtml(pageText);
    }

    function focusAreaLabel() {
        var selectedSlug = state.sections[state.menuIndex] ? state.sections[state.menuIndex].slug : '';

        if (state.keyboardVisible) {
            return 'Focus: Keyboard';
        }

        if (state.detailVisible) {
            return state.detailPanel === 'episodes' ? 'Focus: Episodes' : 'Focus: Seasons';
        }

        if (state.focusArea === 'search-input') {
            return 'Focus: Search';
        }

        if (state.focusArea === 'search-filters') {
            return 'Focus: Filters';
        }

        if (state.focusArea === 'networks') {
            return state.activeSectionSlug === 'live' ? 'Focus: Languages' : 'Focus: Networks';
        }

        if (state.focusArea === 'genres') {
            return 'Focus: Genres';
        }

        if (state.focusArea === 'channels') {
            return 'Focus: Channels';
        }

        if (state.focusArea === 'items') {
            return 'Focus: Titles';
        }

        if (state.focusArea === 'menu' && selectedSlug && selectedSlug !== state.activeSectionSlug) {
            return 'Focus: Menu - Press OK';
        }

        return 'Focus: Menu';
    }

    function renderFocusZones() {
        var isSearch = state.activeSectionSlug === 'search';

        setClassState(dom.networkShell, 'focused-zone', !isSearch && state.focusArea === 'networks');
        setClassState(dom.searchShell, 'focused-zone', isSearch && state.focusArea === 'search-input');
        setClassState(dom.genreShell, 'focused-zone', (isSearch && state.focusArea === 'search-filters') || (!isSearch && state.focusArea === 'genres'));
        setClassState(dom.channelShell, 'focused-zone', !isSearch && state.focusArea === 'channels');
        setClassState(dom.itemsShell, 'focused-zone', state.focusArea === 'items');
    }

    function selectedNetworkLabel() {
        var payload = state.sectionPayload || {};
        var networks = toArray(payload.networks);
        var selectedId = payload.selected_network_id;
        var i;

        for (i = 0; i < networks.length; i += 1) {
            if (String(networks[i].id) === String(selectedId)) {
                return networks[i].label || '';
            }
        }

        return '';
    }

    function renderSearchShell() {
        if (state.activeSectionSlug === 'search') {
            dom.searchShell.style.display = 'block';
            dom.searchText.innerHTML = escapeHtml(state.searchQuery || 'Search Live TV, movies, web series and more');
            setClassState(dom.searchInput, 'focused', state.focusArea === 'search-input');
            return;
        }

        dom.searchShell.style.display = 'none';
        removeClass(dom.searchInput, 'focused');
    }

    function renderContentShellLayout() {
        setClassState(dom.contentShell, 'no-genre-tabs', state.activeSectionSlug !== 'search' && !hasVisibleGenres());
        setClassState(dom.contentShell, 'no-channel-tabs', state.activeSectionSlug !== 'search' && !hasVisibleChannels());
    }

    function renderNetworkRow() {
        var items;
        var html = [];
        var i;

        if (state.activeSectionSlug === 'search') {
            dom.networkShell.style.display = 'none';
            dom.networkRow.innerHTML = '';
            return;
        }

        items = toArray((state.sectionPayload || {}).networks);

        if (!items.length) {
            dom.networkShell.style.display = 'none';
            dom.networkRow.innerHTML = '';
            return;
        }

        dom.networkShell.style.display = 'block';
        dom.networksLabel.innerHTML = escapeHtml(state.activeSectionSlug === 'live' ? 'Languages' : 'Networks');

        for (i = 0; i < items.length; i += 1) {
            html.push(
                '<div class="filter-chip' +
                    (String(items[i].id) === String((state.sectionPayload || {}).selected_network_id) ? ' active' : '') +
                    (state.focusArea === 'networks' && i === state.networkFocusIndex ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    escapeHtml(items[i].label || '') +
                '</div>'
            );
        }

        dom.networkRow.innerHTML = html.join('');
        bindChipClicks(dom.networkRow, 'networks');
        ensureFocusVisible();
    }

    function renderGenreRow() {
        var items;
        var html = [];
        var i;

        if (state.activeSectionSlug === 'search') {
            items = state.searchFilters;

            if (!items.length) {
                dom.genreShell.style.display = 'none';
                dom.genreRow.innerHTML = '';
                return;
            }

            dom.genreShell.style.display = 'block';
            dom.genreLabel.innerHTML = 'Results';

            for (i = 0; i < items.length; i += 1) {
                html.push(
                    '<div class="filter-chip' +
                        (i === state.searchActiveFilterIndex ? ' active' : '') +
                        (state.focusArea === 'search-filters' && i === state.searchFocusFilterIndex ? ' focused' : '') +
                    '" data-index="' + i + '">' +
                        escapeHtml(items[i].label || '') +
                    '</div>'
                );
            }

            dom.genreRow.innerHTML = html.join('');
            bindChipClicks(dom.genreRow, 'search-filters');
            ensureFocusVisible();
            return;
        }

        items = toArray((state.sectionPayload || {}).genres);

        if (items.length <= 1) {
            dom.genreShell.style.display = 'none';
            dom.genreRow.innerHTML = '';
            return;
        }

        dom.genreShell.style.display = 'block';
        dom.genreLabel.innerHTML = 'Genres';

        for (i = 0; i < items.length; i += 1) {
            html.push(
                '<div class="filter-chip' +
                    (String(items[i].id) === String((state.sectionPayload || {}).selected_genre) ? ' active' : '') +
                    (state.focusArea === 'genres' && i === state.genreFocusIndex ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    escapeHtml(items[i].label || '') +
                '</div>'
            );
        }

        dom.genreRow.innerHTML = html.join('');
        bindChipClicks(dom.genreRow, 'genres');
        ensureFocusVisible();
    }

    function renderChannelRow() {
        var items = toArray((state.sectionPayload || {}).channels);
        var html = [];
        var i;

        if (state.activeSectionSlug === 'search' || items.length <= 1) {
            dom.channelShell.style.display = 'none';
            dom.channelRow.innerHTML = '';
            return;
        }

        dom.channelShell.style.display = 'block';
        dom.channelLabel.innerHTML = 'Channels';

        for (i = 0; i < items.length; i += 1) {
            html.push(
                '<div class="filter-chip' +
                    (String(items[i].id) === String((state.sectionPayload || {}).selected_channel_id) ? ' active' : '') +
                    (state.focusArea === 'channels' && i === state.channelFocusIndex ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    escapeHtml(items[i].label || '') +
                '</div>'
            );
        }

        dom.channelRow.innerHTML = html.join('');
        bindChipClicks(dom.channelRow, 'channels');
        ensureFocusVisible();
    }

    function renderItemsRow() {
        var cards = buildVisibleCards();
        var html = [];
        var i;
        var item;
        var imageHtml;
        var isSearch = state.activeSectionSlug === 'search';

        state.visibleCards = cards;

        if (isSearch) {
            state.searchItemFocusIndex = clamp(state.searchItemFocusIndex, 0, Math.max(cards.length - 1, 0));
        } else {
            state.itemFocusIndex = clamp(state.itemFocusIndex, 0, Math.max(cards.length - 1, 0));
        }

        dom.itemsShell.style.top = computeItemsShellTop() + 'px';

        if (!cards.length) {
            dom.itemsRow.innerHTML = '';
            dom.emptyState.style.display = 'block';
            dom.emptyState.innerHTML = escapeHtml(emptyStateText());
            return;
        }

        dom.emptyState.style.display = 'none';
        dom.emptyState.innerHTML = '';

        for (i = 0; i < cards.length; i += 1) {
            item = cards[i];

            if (item.__pageAction) {
                html.push(
                    '<div class="media-card more-card' + (isCardFocused(i) ? ' focused' : '') + '" data-index="' + i + '">' +
                        '<div class="media-card-empty">' + (item.__pageAction === 'prev' ? '&#8249;' : '&#8250;') + '</div>' +
                        '<div class="media-card-body">' +
                            '<div class="media-card-title">' + escapeHtml(item.title || '') + '</div>' +
                            '<div class="media-card-subtitle">' + escapeHtml(item.subtitle || '') + '</div>' +
                        '</div>' +
                    '</div>'
                );
                continue;
            }

            if (item.image) {
                imageHtml = '<div class="media-card-thumb" style="background-image:url(\'' + escapeAttribute(item.image) + '\')"></div>';
            } else {
                imageHtml = '<div class="media-card-empty">&#9638;</div>';
            }

            html.push(
                '<div class="media-card' + (isCardFocused(i) ? ' focused' : '') + '" data-index="' + i + '">' +
                    (item.badge ? '<div class="media-card-badge">' + escapeHtml(item.badge) + '</div>' : '') +
                    imageHtml +
                    '<div class="media-card-body">' +
                        '<div class="media-card-title">' + escapeHtml(item.title || '') + '</div>' +
                        '<div class="media-card-subtitle">' + escapeHtml(item.subtitle || item.description || '') + '</div>' +
                    '</div>' +
                '</div>'
            );
        }

        dom.itemsRow.innerHTML = html.join('');
        bindCardClicks();
        ensureFocusVisible();
    }

    function computeItemsShellTop() {
        var profile = getLayoutProfile((getScreenViewport().height || 720));
        var top = profile.sectionBarHeight + profile.sectionBarGap;

        if (state.activeSectionSlug === 'search') {
            top += searchShellHeight(profile);

            if (state.searchFilters.length) {
                top += filterShellHeight(profile);
            }

            return top;
        }

        if (hasVisibleGenres()) {
            top += filterShellHeight(profile);
        }

        if (hasVisibleChannels()) {
            top += filterShellHeight(profile);
        }

        return top;
    }

    function emptyStateText() {
        if (state.activeSectionSlug === 'search') {
            if (state.searchQuery.length < 2) {
                return 'Enter at least two characters to search the catalog.';
            }

            return 'No results were found for this search query.';
        }

        return 'No items are available in this section for the mapped device right now.';
    }

    function bindChipClicks(container, area) {
        var nodes = container.getElementsByTagName('div');
        var i;

        for (i = 0; i < nodes.length; i += 1) {
            (function (index) {
                nodes[index].onclick = function () {
                    if (area === 'networks') {
                        state.focusArea = 'networks';
                        state.networkFocusIndex = index;
                        activateNetwork();
                        return;
                    }

                    if (area === 'genres') {
                        state.focusArea = 'genres';
                        state.genreFocusIndex = index;
                        activateGenre();
                        return;
                    }

                    if (area === 'channels') {
                        state.focusArea = 'channels';
                        state.channelFocusIndex = index;
                        activateChannel();
                        return;
                    }

                    if (area === 'search-filters') {
                        state.focusArea = 'search-filters';
                        state.searchFocusFilterIndex = index;
                        activateSearchFilter();
                    }
                };
            })(i);
        }
    }

    function bindCardClicks() {
        var nodes = dom.itemsRow.getElementsByTagName('div');
        var i;
        var cardIndex;

        for (i = 0; i < nodes.length; i += 1) {
            cardIndex = nodes[i].getAttribute('data-index');

            if (cardIndex === null) {
                continue;
            }

            (function (index) {
                nodes[index].onclick = function () {
                    setItemFocus(parseInt(nodes[index].getAttribute('data-index'), 10));
                    activateCurrentCard();
                };
            })(i);
        }
    }

    function isCardFocused(index) {
        if (state.focusArea !== 'items') {
            return false;
        }

        if (state.activeSectionSlug === 'search') {
            return index === state.searchItemFocusIndex;
        }

        return index === state.itemFocusIndex;
    }

    function buildVisibleCards() {
        var cards;
        var pagination;

        if (state.activeSectionSlug === 'search') {
            cards = currentSearchItems().slice(0);
            pagination = state.searchPagination || {};
        } else {
            cards = toArray((state.sectionPayload || {}).items).slice(0);
            pagination = currentPagination();
        }

        if ((pagination.current_page || 1) > 1) {
            cards.unshift({
                __pageAction: 'prev',
                title: 'Previous Page',
                subtitle: 'Open the previous set of items'
            });
        }

        if (pagination.has_more) {
            cards.push({
                __pageAction: 'next',
                title: 'View All',
                subtitle: 'Open the next set of items'
            });
        }

        return cards;
    }

    function currentPagination() {
        if (state.activeSectionSlug === 'search') {
            return state.searchPagination || {
                current_page: 1,
                last_page: 1,
                has_more: false,
                total: 0
            };
        }

        return (state.sectionPayload && state.sectionPayload.pagination) ? state.sectionPayload.pagination : {
            current_page: 1,
            last_page: 1,
            has_more: false,
            total: 0
        };
    }

    function currentSearchItems() {
        var row = state.searchRows[state.searchActiveFilterIndex];
        return row && row.items ? row.items : [];
    }

    function moveFocusVertical(direction) {
        var zones = getMainZones();
        var currentIndex = zones.indexOf(state.focusArea);
        var nextIndex;

        if (currentIndex === -1) {
            state.focusArea = firstMainZone();
            renderCurrentView();
            return;
        }

        nextIndex = clamp(currentIndex + direction, 0, zones.length - 1);
        state.focusArea = zones[nextIndex];
        renderCurrentView();
    }

    function moveFocusHorizontal(direction) {
        if (state.focusArea === 'search-input') {
            if (direction < 0) {
                state.focusArea = 'menu';
            }
            renderCurrentView();
            return;
        }

        if (state.focusArea === 'networks') {
            if (direction < 0 && state.networkFocusIndex === 0) {
                state.focusArea = 'menu';
            } else {
                state.networkFocusIndex = clamp(state.networkFocusIndex + direction, 0, Math.max(toArray((state.sectionPayload || {}).networks).length - 1, 0));
            }
            renderCurrentView();
            return;
        }

        if (state.focusArea === 'genres') {
            if (direction < 0 && state.genreFocusIndex === 0) {
                state.focusArea = hasVisibleNetworks() ? 'networks' : 'menu';
            } else {
                state.genreFocusIndex = clamp(state.genreFocusIndex + direction, 0, Math.max(toArray((state.sectionPayload || {}).genres).length - 1, 0));
            }
            renderCurrentView();
            return;
        }

        if (state.focusArea === 'channels') {
            if (direction < 0 && state.channelFocusIndex === 0) {
                state.focusArea = hasVisibleGenres() ? 'genres' : (hasVisibleNetworks() ? 'networks' : 'menu');
            } else {
                state.channelFocusIndex = clamp(state.channelFocusIndex + direction, 0, Math.max(toArray((state.sectionPayload || {}).channels).length - 1, 0));
            }
            renderCurrentView();
            return;
        }

        if (state.focusArea === 'search-filters') {
            if (direction < 0 && state.searchFocusFilterIndex === 0) {
                state.focusArea = 'search-input';
            } else {
                state.searchFocusFilterIndex = clamp(state.searchFocusFilterIndex + direction, 0, Math.max(state.searchFilters.length - 1, 0));
            }
            renderCurrentView();
            return;
        }

        if (state.focusArea === 'items') {
            if (direction < 0 && currentCardFocusIndex() === 0) {
                state.focusArea = previousZoneForItems();
            } else {
                moveItemFocus(direction);
            }
            renderCurrentView();
        }
    }

    function previousZoneForItems() {
        if (state.activeSectionSlug === 'search') {
            if (state.searchFilters.length) {
                return 'search-filters';
            }

            return 'search-input';
        }

        if (hasVisibleChannels()) {
            return 'channels';
        }

        if (hasVisibleGenres()) {
            return 'genres';
        }

        if (hasVisibleNetworks()) {
            return 'networks';
        }

        return 'menu';
    }

    function currentCardFocusIndex() {
        return state.activeSectionSlug === 'search' ? state.searchItemFocusIndex : state.itemFocusIndex;
    }

    function setItemFocus(index) {
        var maxIndex = Math.max(state.visibleCards.length - 1, 0);

        if (state.activeSectionSlug === 'search') {
            state.searchItemFocusIndex = clamp(index, 0, maxIndex);
            return;
        }

        state.itemFocusIndex = clamp(index, 0, maxIndex);
    }

    function moveItemFocus(direction) {
        setItemFocus(currentCardFocusIndex() + direction);
    }

    function activateFocusedArea() {
        if (state.focusArea === 'search-input') {
            openKeyboard();
            return;
        }

        if (state.focusArea === 'networks') {
            activateNetwork();
            return;
        }

        if (state.focusArea === 'genres') {
            activateGenre();
            return;
        }

        if (state.focusArea === 'channels') {
            activateChannel();
            return;
        }

        if (state.focusArea === 'search-filters') {
            activateSearchFilter();
            return;
        }

        if (state.focusArea === 'items') {
            activateCurrentCard();
        }
    }

    function activateNetwork() {
        var payload = state.sectionPayload || {};
        var networks = toArray(payload.networks);
        var selected = networks[state.networkFocusIndex];
        var params;

        if (!selected) {
            return;
        }

        params = getSectionParams(state.activeSectionSlug);
        params.network = selected.id;
        params.genre = '';
        params.channel = 0;
        params.page = 1;
        state.sectionParams[state.activeSectionSlug] = params;

        openSection(state.activeSectionSlug, false, true);
        state.focusArea = 'networks';
    }

    function activateGenre() {
        var payload = state.sectionPayload || {};
        var genres = toArray(payload.genres);
        var selected = genres[state.genreFocusIndex];
        var params;

        if (!selected) {
            return;
        }

        params = getSectionParams(state.activeSectionSlug);
        params.genre = selected.id;
        params.page = 1;
        state.sectionParams[state.activeSectionSlug] = params;

        openSection(state.activeSectionSlug, false, true);
        state.focusArea = 'genres';
    }

    function activateChannel() {
        var payload = state.sectionPayload || {};
        var channels = toArray(payload.channels);
        var selected = channels[state.channelFocusIndex];
        var params;

        if (!selected) {
            return;
        }

        params = getSectionParams(state.activeSectionSlug);
        params.channel = selected.id;
        params.page = 1;
        state.sectionParams[state.activeSectionSlug] = params;

        openSection(state.activeSectionSlug, false, true);
        state.focusArea = 'channels';
    }

    function activateSearchFilter() {
        state.searchActiveFilterIndex = state.searchFocusFilterIndex;
        state.searchItemFocusIndex = 0;
        state.focusArea = 'items';
        renderCurrentView();
    }

    function activateCurrentCard() {
        var cards = state.visibleCards;
        var index = currentCardFocusIndex();
        var card = cards[index];

        if (!card) {
            return;
        }

        if (card.__pageAction) {
            changePage(card.__pageAction === 'next' ? 1 : -1);
            return;
        }

        if (card.action === 'open-section' && card.section_slug) {
            navigateToMenuSection(card.section_slug);
            return;
        }

        if (card.action === 'detail') {
            loadDetail(card);
            return;
        }

        if (card.action === 'info') {
            showToast(card.description || 'This item is available in the catalog.');
            return;
        }

        startPlaybackForCard(card);
    }

    function navigateToMenuSection(slug) {
        var index = indexOfSection(slug);

        if (index === -1) {
            showToast('This section is not available for the current portal.');
            return;
        }

        state.menuIndex = index;
        state.focusArea = 'menu';
        openSection(slug, true, true);
    }

    function changePage(direction) {
        var pagination = currentPagination();
        var current = pagination.current_page || 1;
        var next = current + direction;

        if (next < 1) {
            next = 1;
        }

        if (pagination.last_page && next > pagination.last_page) {
            next = pagination.last_page;
        }

        if (next === current) {
            return;
        }

        if (state.activeSectionSlug === 'search') {
            requestSearch(next, false);
            return;
        }

        state.sectionParams[state.activeSectionSlug].page = next;
        state.itemFocusIndex = 0;
        openSection(state.activeSectionSlug, false, true);
    }

    function handlePaging(direction) {
        if (state.focusArea !== 'items') {
            return;
        }

        if (state.isPlaying && state.playbackType === 'live') {
            moveLivePlaybackChannel(direction);
            return;
        }

        changePage(direction);
    }

    function startPlaybackForCard(card) {
        var playType = card.play_type || card.type || 'movie';

        setBoot('Opening Stream', 'Preparing a playback URL for ' + escapeHtml(card.title || 'this item') + '.', true);

        apiGet('/play/' + playType + '/' + card.id, {}, function (data) {
            var playback = data && data.playback ? data.playback : null;

            if (!playback || !playback.url) {
                showToast('Playback is not available for this item.');
                setBoot('', '', false);
                return;
            }

            state.playbackType = playback.type || playType;
            state.playbackTitle = playback.title || card.title || 'Playback';
            state.playbackMeta = playback.description || card.subtitle || '';
            setBoot('', '', false);
            playUrl(playback.url);
        }, function (message) {
            setBoot('', '', false);
            showToast(message || 'Failed to open playback.');
        });
    }

    function moveLivePlaybackChannel(direction) {
        var cards = toArray((state.sectionPayload || {}).items);
        var activeId = currentPlayingCardId();
        var index = 0;
        var i;

        for (i = 0; i < cards.length; i += 1) {
            if (String(cards[i].id) === String(activeId)) {
                index = i;
                break;
            }
        }

        index = clamp(index + direction, 0, Math.max(cards.length - 1, 0));

        if (!cards[index]) {
            return;
        }

        state.itemFocusIndex = index;
        startPlaybackForCard(cards[index]);
    }

    function currentPlayingCardId() {
        var cards = toArray((state.sectionPayload || {}).items);
        var focused = cards[state.itemFocusIndex];
        return focused ? focused.id : 0;
    }

    function loadDetail(card) {
        var detailType = card.detail_type || card.type || 'webseries';

        setBoot('Loading Detail', 'Fetching seasons and episodes for ' + escapeHtml(card.title || 'this item') + '.', true);

        apiGet('/content/' + detailType + '/' + card.id, {}, function (data) {
            if (!data || !data.status || !data.content) {
                setBoot('', '', false);
                showToast('Detail is not available for this item.');
                return;
            }

            state.detailData = data.content;
            state.detailVisible = true;
            state.detailSeasonIndex = 0;
            state.detailEpisodeIndex = 0;
            state.detailPanel = 'seasons';
            setBoot('', '', false);
            renderDetail();
        }, function (message) {
            setBoot('', '', false);
            showToast(message || 'Failed to load detail.');
        });
    }

    function closeDetail() {
        state.detailVisible = false;
        state.detailData = null;
        state.detailSeasonIndex = 0;
        state.detailEpisodeIndex = 0;
        state.detailPanel = 'seasons';
        renderDetail();
        state.focusArea = 'items';
        renderCurrentView();
    }

    function renderDetail() {
        var content = state.detailData;
        var seasons;
        var episodes;
        var seasonHtml = [];
        var episodeHtml = [];
        var i;
        var season;
        var episode;

        if (!state.detailVisible || !content) {
            removeClass(dom.detailOverlay, 'visible');
            return;
        }

        addClass(dom.detailOverlay, 'visible');
        dom.detailBackdrop.style.backgroundImage = content.backdrop ? "url('" + escapeCssUrl(content.backdrop) + "')" : 'none';
        dom.detailKicker.innerHTML = escapeHtml(content.badge || content.type || 'Details');
        dom.detailTitle.innerHTML = escapeHtml(content.title || '');
        dom.detailPlot.innerHTML = escapeHtml(content.plot || content.description || '');
        dom.detailMeta.innerHTML = renderMeta(toArray(content.meta));
        setClassState(dom.detailSeasonsWrap, 'focused', state.detailPanel === 'seasons');
        setClassState(dom.detailEpisodesWrap, 'focused', state.detailPanel === 'episodes');

        seasons = currentDetailSeasons();
        episodes = currentDetailEpisodes();

        for (i = 0; i < seasons.length; i += 1) {
            season = seasons[i];
            seasonHtml.push(
                '<div class="detail-item' +
                    (i === state.detailSeasonIndex ? ' active' : '') +
                    (state.detailPanel === 'seasons' && i === state.detailSeasonIndex ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    '<div class="detail-item-image"' + backgroundStyle(season.image) + '></div>' +
                    '<div class="detail-item-title">' + escapeHtml(season.title || '') + '</div>' +
                    '<div class="detail-item-subtitle">' + escapeHtml(season.subtitle || '') + '</div>' +
                '</div>'
            );
        }

        for (i = 0; i < episodes.length; i += 1) {
            episode = episodes[i];
            episodeHtml.push(
                '<div class="detail-item' +
                    (state.detailPanel === 'episodes' && i === state.detailEpisodeIndex ? ' focused' : '') +
                '" data-index="' + i + '">' +
                    '<div class="detail-item-image"' + backgroundStyle(episode.image) + '></div>' +
                    '<div class="detail-item-title">' + escapeHtml(episode.title || '') + '</div>' +
                    '<div class="detail-item-subtitle">' + escapeHtml(episode.subtitle || '') + '</div>' +
                '</div>'
            );
        }

        dom.detailSeasons.innerHTML = seasonHtml.join('') || '<div class="detail-item"><div class="detail-item-title">No seasons available</div></div>';
        dom.detailEpisodes.innerHTML = episodeHtml.join('') || '<div class="detail-item"><div class="detail-item-title">No episodes available</div></div>';

        bindDetailClicks();
        ensureFocusVisible();
    }

    function bindDetailClicks() {
        bindDetailListClicks(dom.detailSeasons, 'seasons');
        bindDetailListClicks(dom.detailEpisodes, 'episodes');
    }

    function bindDetailListClicks(container, panel) {
        var nodes = container.getElementsByTagName('div');
        var i;
        var index;

        for (i = 0; i < nodes.length; i += 1) {
            index = nodes[i].getAttribute('data-index');

            if (index === null) {
                continue;
            }

            (function (value) {
                nodes[i].onclick = function () {
                    if (panel === 'seasons') {
                        state.detailSeasonIndex = value;
                        state.detailEpisodeIndex = 0;
                        state.detailPanel = 'episodes';
                        renderDetail();
                        return;
                    }

                    state.detailEpisodeIndex = value;
                    state.detailPanel = 'episodes';
                    playDetailEpisode();
                };
            })(parseInt(index, 10));
        }
    }

    function currentDetailSeasons() {
        var seasons = toArray((state.detailData || {}).seasons);
        return seasons;
    }

    function currentDetailEpisodes() {
        var seasons = currentDetailSeasons();
        var season = seasons[state.detailSeasonIndex];
        return season && season.episodes ? toArray(season.episodes) : [];
    }

    function playDetailEpisode() {
        var episodes = currentDetailEpisodes();
        var episode = episodes[state.detailEpisodeIndex];
        var playType;

        if (!episode) {
            return;
        }

        playType = episode.play_type || '';

        if (!playType) {
            showToast('Playback is not configured for this episode.');
            return;
        }

        setBoot('Opening Episode', 'Preparing playback for ' + escapeHtml(episode.title || 'this episode') + '.', true);

        apiGet('/play/' + playType + '/' + episode.id, {}, function (data) {
            var playback = data && data.playback ? data.playback : null;

            if (!playback || !playback.url) {
                setBoot('', '', false);
                showToast('Playback is not available for this episode.');
                return;
            }

            state.playbackType = playback.type || playType;
            state.playbackTitle = playback.title || episode.title || 'Playback';
            state.playbackMeta = playback.description || episode.subtitle || '';
            setBoot('', '', false);
            playUrl(playback.url);
        }, function (message) {
            setBoot('', '', false);
            showToast(message || 'Failed to open episode playback.');
        });
    }

    function openKeyboard() {
        state.keyboardVisible = true;
        state.keyboardRow = 0;
        state.keyboardCol = 0;
        renderKeyboardState();
    }

    function closeKeyboard() {
        state.keyboardVisible = false;
        renderKeyboardState();
        renderCurrentView();
    }

    function renderKeyboard() {
        var html = [];
        var rowIndex;
        var colIndex;
        var row;
        var key;
        var className;

        for (rowIndex = 0; rowIndex < KEYBOARD_ROWS.length; rowIndex += 1) {
            row = KEYBOARD_ROWS[rowIndex];
            html.push('<div class="keyboard-row">');

            for (colIndex = 0; colIndex < row.length; colIndex += 1) {
                key = row[colIndex];
                className = 'keyboard-key';

                if (key === 'SPACE') {
                    className += ' xwide';
                } else if (key === 'SEARCH' || key === 'CLEAR' || key === 'CLOSE') {
                    className += ' wide';
                }

                html.push(
                    '<div class="' + className + '" data-row="' + rowIndex + '" data-col="' + colIndex + '">' +
                        escapeHtml(key === 'DEL' ? '⌫' : key) +
                    '</div>'
                );
            }

            html.push('</div>');
        }

        dom.keyboardGrid.innerHTML = html.join('');
        bindKeyboardClicks();
    }

    function bindKeyboardClicks() {
        var nodes = dom.keyboardGrid.getElementsByTagName('div');
        var i;
        var row;
        var col;

        for (i = 0; i < nodes.length; i += 1) {
            row = nodes[i].getAttribute('data-row');
            col = nodes[i].getAttribute('data-col');

            if (row === null || col === null) {
                continue;
            }

            (function (targetRow, targetCol) {
                nodes[i].onclick = function () {
                    state.keyboardRow = targetRow;
                    state.keyboardCol = targetCol;
                    renderKeyboardState();
                    pressKeyboardKey(getKeyboardKey(targetRow, targetCol));
                };
            })(parseInt(row, 10), parseInt(col, 10));
        }
    }

    function renderKeyboardState() {
        var nodes;
        var i;
        var row;
        var col;
        var keyText = state.searchQuery || '';

        dom.keyboardOverlay.className = state.keyboardVisible ? 'visible' : '';
        dom.keyboardQuery.innerHTML = escapeHtml(keyText || 'Type to search the catalog');

        if (!state.keyboardVisible) {
            return;
        }

        nodes = dom.keyboardGrid.getElementsByTagName('div');

        for (i = 0; i < nodes.length; i += 1) {
            row = nodes[i].getAttribute('data-row');
            col = nodes[i].getAttribute('data-col');

            if (row === null || col === null) {
                continue;
            }

            setClassState(nodes[i], 'focused', parseInt(row, 10) === state.keyboardRow && parseInt(col, 10) === state.keyboardCol);
        }

        ensureFocusVisible();
    }

    function getKeyboardKey(row, col) {
        if (!KEYBOARD_ROWS[row] || typeof KEYBOARD_ROWS[row][col] === 'undefined') {
            return '';
        }

        return KEYBOARD_ROWS[row][col];
    }

    function moveKeyboard(rowDirection, colDirection) {
        var targetRow = clamp(state.keyboardRow + rowDirection, 0, KEYBOARD_ROWS.length - 1);
        var targetCol = state.keyboardCol + colDirection;
        var rowLength = KEYBOARD_ROWS[targetRow].length;

        if (targetCol < 0) {
            targetCol = 0;
        }

        if (targetCol >= rowLength) {
            targetCol = rowLength - 1;
        }

        state.keyboardRow = targetRow;
        state.keyboardCol = targetCol;
        renderKeyboardState();
    }

    function pressKeyboardKey(key) {
        if (!key) {
            return;
        }

        if (key === 'DEL') {
            state.searchQuery = state.searchQuery.slice(0, -1);
            onSearchQueryChanged();
            return;
        }

        if (key === 'CLEAR') {
            state.searchQuery = '';
            onSearchQueryChanged();
            return;
        }

        if (key === 'SPACE') {
            state.searchQuery += ' ';
            onSearchQueryChanged();
            return;
        }

        if (key === 'SEARCH') {
            if (state.searchQuery.length >= 2) {
                requestSearch(1, false);
            }
            closeKeyboard();
            return;
        }

        if (key === 'CLOSE') {
            closeKeyboard();
            return;
        }

        state.searchQuery += key;
        onSearchQueryChanged();
    }

    function onSearchQueryChanged() {
        if (state.searchDebounce) {
            win.clearTimeout(state.searchDebounce);
            state.searchDebounce = null;
        }

        state.searchRequestSerial += 1;

        state.searchFilters = [];
        state.searchRows = [];
        state.searchActiveFilterIndex = 0;
        state.searchFocusFilterIndex = 0;
        state.searchItemFocusIndex = 0;
        state.searchPagination = {
            current_page: 1,
            last_page: 1,
            per_page: 18,
            total: 0,
            has_more: false
        };

        if (state.searchQuery.length < 2) {
            state.searchHero = {
                title: 'Search',
                subtitle: 'Enter at least two characters to search Live TV, movies, web series, TV shows and more.',
                badge: 'Universal Search',
                meta: []
            };
            renderCurrentView();
            return;
        }

        state.searchDebounce = win.setTimeout(function () {
            requestSearch(1, false);
        }, 250);

        state.searchHero = {
            title: 'Search',
            subtitle: 'Searching the TV catalog for "' + state.searchQuery + '".',
            badge: 'Universal Search',
            meta: []
        };
        renderCurrentView();
    }

    function requestSearch(page, append) {
        var requestSerial;

        state.searchRequestSerial += 1;
        requestSerial = state.searchRequestSerial;
        setBoot('Searching', 'Looking for matches for "' + escapeHtml(state.searchQuery) + '".', true);

        apiGet('/search', {
            q: state.searchQuery,
            page: page || 1
        }, function (data) {
            if (requestSerial !== state.searchRequestSerial) {
                return;
            }

            mergeSearchResponse(data || {}, append);
            setBoot('', '', false);
            renderCurrentView();
        }, function (message) {
            if (requestSerial !== state.searchRequestSerial) {
                return;
            }

            setBoot('', '', false);
            showToast(message || 'Search failed.');
        });
    }

    function mergeSearchResponse(data, append) {
        var newRows = toArray(data.rows);
        var merged = [];
        var i;
        var j;
        var existing;
        var row;

        state.searchHero = data.hero || {
            title: 'Search',
            subtitle: '',
            badge: 'Universal Search',
            meta: []
        };

        if (!append) {
            state.searchRows = newRows;
            state.searchFilters = toArray(data.filters);
        } else {
            merged = state.searchRows.slice(0);

            for (i = 0; i < newRows.length; i += 1) {
                row = newRows[i];
                existing = null;

                for (j = 0; j < merged.length; j += 1) {
                    if (String(merged[j].id) === String(row.id)) {
                        existing = merged[j];
                        break;
                    }
                }

                if (!existing) {
                    merged.push(row);
                    continue;
                }

                existing.items = toArray(existing.items).concat(toArray(row.items));
            }

            state.searchRows = merged;
            state.searchFilters = toArray(data.filters);
        }

        state.searchPagination = data.pagination || state.searchPagination;
        state.searchActiveFilterIndex = clamp(state.searchActiveFilterIndex, 0, Math.max(state.searchFilters.length - 1, 0));
        state.searchFocusFilterIndex = state.searchActiveFilterIndex;
        state.searchItemFocusIndex = 0;
    }

    function portalRequest(params, onSuccess, onError) {
        var query = [];
        var key;
        var xhr = new XMLHttpRequest();
        var payload;
        var response;

        params = params || {};
        params.mac = state.mac;
        params.JsHttpRequest = '1-xml';

        if (state.token && !params.token) {
            params.token = state.token;
        }

        for (key in params) {
            if (params.hasOwnProperty(key) && params[key] !== '' && params[key] !== null && typeof params[key] !== 'undefined') {
                query.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
            }
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }

            if (xhr.status < 200 || xhr.status >= 300) {
                if (onError) {
                    onError('Portal request failed with HTTP ' + xhr.status + '.');
                }
                return;
            }

            try {
                payload = JSON.parse(xhr.responseText || '{}');
            } catch (error) {
                if (onError) {
                    onError('Portal returned invalid JSON.');
                }
                return;
            }

            response = payload && payload.js ? payload.js : {};

            if (response && response.status === 'ERROR') {
                if (onError) {
                    onError(response.message || 'Portal request failed.');
                }
                return;
            }

            if (onSuccess) {
                onSuccess(response || {});
            }
        };

        xhr.open('GET', loadUrl + '?' + query.join('&'), true);
        xhr.send(null);
    }

    function apiGet(path, params, onSuccess, onError) {
        var xhr = new XMLHttpRequest();
        var queryParams = params || {};
        var query;
        var url;
        var payload;
        var message;

        queryParams.mac = state.mac;
        queryParams.token = state.token;

        query = buildQuery(queryParams);
        url = apiBaseUrl + path + (query ? '?' + query : '');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }

            if (xhr.status < 200 || xhr.status >= 300) {
                message = 'TV API request failed with HTTP ' + xhr.status + '.';
                try {
                    payload = JSON.parse(xhr.responseText || '{}');
                    if (payload && payload.message) {
                        message = payload.message;
                    }
                } catch (error) {
                }

                if (onError) {
                    onError(message);
                }
                return;
            }

            try {
                payload = JSON.parse(xhr.responseText || '{}');
            } catch (error) {
                if (onError) {
                    onError('TV API returned invalid JSON.');
                }
                return;
            }

            if (payload && payload.status === false) {
                if (onError) {
                    onError(payload.message || 'TV API request failed.');
                }
                return;
            }

            if (onSuccess) {
                onSuccess(payload || {});
            }
        };

        xhr.open('GET', url, true);
        xhr.send(null);
    }

    function renderMeta(items) {
        var list = toArray(items);
        var html = [];
        var i;

        for (i = 0; i < list.length; i += 1) {
            if (!list[i]) {
                continue;
            }

            html.push('<span class="meta-chip">' + escapeHtml(list[i]) + '</span>');
        }

        return html.join('');
    }

    function backgroundStyle(image) {
        if (!image) {
            return '';
        }

        return ' style="background-image:url(\'' + escapeCssUrl(image) + '\')"';
    }

    function ensureMenuFocusVisible() {
        var nodes = dom.menuList.getElementsByTagName('div');
        if (nodes[state.menuIndex]) {
            ensureVisible(dom.menuScroll, nodes[state.menuIndex]);
        }
    }

    function ensureFocusVisible() {
        var nodes;

        if (state.detailVisible) {
            if (state.detailPanel === 'seasons') {
                nodes = dom.detailSeasons.getElementsByTagName('div');
                if (nodes[state.detailSeasonIndex]) {
                    ensureVisible(dom.detailSeasons, nodes[state.detailSeasonIndex]);
                }
            } else {
                nodes = dom.detailEpisodes.getElementsByTagName('div');
                if (nodes[state.detailEpisodeIndex]) {
                    ensureVisible(dom.detailEpisodes, nodes[state.detailEpisodeIndex]);
                }
            }
            return;
        }

        if (state.keyboardVisible) {
            nodes = dom.keyboardGrid.getElementsByTagName('div');
            ensureFocusedKeyboardVisible(nodes);
            return;
        }

        if (state.focusArea === 'menu') {
            ensureMenuFocusVisible();
            return;
        }

        if (state.focusArea === 'networks') {
            nodes = dom.networkRow.getElementsByTagName('div');
            if (nodes[state.networkFocusIndex]) {
                ensureVisible(dom.networkRow, nodes[state.networkFocusIndex]);
            }
            return;
        }

        if (state.focusArea === 'genres' || state.focusArea === 'search-filters') {
            nodes = dom.genreRow.getElementsByTagName('div');
            if (state.activeSectionSlug === 'search') {
                if (nodes[state.searchFocusFilterIndex]) {
                    ensureVisible(dom.genreRow, nodes[state.searchFocusFilterIndex]);
                }
            } else if (nodes[state.genreFocusIndex]) {
                ensureVisible(dom.genreRow, nodes[state.genreFocusIndex]);
            }
            return;
        }

        if (state.focusArea === 'channels') {
            nodes = dom.channelRow.getElementsByTagName('div');
            if (nodes[state.channelFocusIndex]) {
                ensureVisible(dom.channelRow, nodes[state.channelFocusIndex]);
            }
            return;
        }

        if (state.focusArea === 'items') {
            nodes = dom.itemsRow.getElementsByTagName('div');
            if (nodes[currentCardFocusIndex()]) {
                ensureVisible(dom.itemsRow, nodes[currentCardFocusIndex()]);
            }
        }
    }

    function ensureFocusedKeyboardVisible(nodes) {
        var i;
        var row;
        var col;

        for (i = 0; i < nodes.length; i += 1) {
            row = nodes[i].getAttribute('data-row');
            col = nodes[i].getAttribute('data-col');

            if (row === null || col === null) {
                continue;
            }

            if (parseInt(row, 10) === state.keyboardRow && parseInt(col, 10) === state.keyboardCol) {
                ensureVisible(dom.keyboardGrid, nodes[i]);
                return;
            }
        }
    }

    function ensureVisible(container, element) {
        var left = 0;
        var top = 0;
        var node = element;
        var right;
        var bottom;
        var visibleLeft;
        var visibleTop;
        var visibleRight;
        var visibleBottom;

        if (!container || !element) {
            return;
        }

        while (node && node !== container) {
            left += node.offsetLeft || 0;
            top += node.offsetTop || 0;
            node = node.offsetParent;
        }

        right = left + (element.offsetWidth || 0);
        bottom = top + (element.offsetHeight || 0);
        visibleLeft = container.scrollLeft || 0;
        visibleTop = container.scrollTop || 0;
        visibleRight = visibleLeft + (container.clientWidth || 0);
        visibleBottom = visibleTop + (container.clientHeight || 0);

        if (left < visibleLeft) {
            container.scrollLeft = left;
        } else if (right > visibleRight) {
            container.scrollLeft = right - (container.clientWidth || 0);
        }

        if (top < visibleTop) {
            container.scrollTop = top;
        } else if (bottom > visibleBottom) {
            container.scrollTop = bottom - (container.clientHeight || 0);
        }
    }

    function setBoot(title, message, visible) {
        if (typeof visible === 'boolean') {
            dom.bootOverlay.style.display = visible ? 'block' : 'none';
        }

        if (typeof title !== 'undefined' && title !== '') {
            dom.bootTitle.innerHTML = title;
        }

        if (typeof message !== 'undefined' && message !== '') {
            dom.bootMsg.innerHTML = message;
        }
    }

    function showFatal(title, message) {
        stopHeroTimer();
        stopPlaybackEngine();
        setGraphicOnTop();
        state.detailVisible = false;
        state.keyboardVisible = false;
        setBoot(title, escapeHtml(message), true);
    }

    function showToast(message) {
        if (state.toastTimer) {
            win.clearTimeout(state.toastTimer);
            state.toastTimer = null;
        }

        dom.toast.innerHTML = escapeHtml(message || '');
        dom.toast.style.display = 'block';

        state.toastTimer = win.setTimeout(function () {
            dom.toast.style.display = 'none';
        }, 2600);
    }

    function playUrl(url) {
        stopPlaybackEngine();

        if (tryModernPlayback(url)) {
            return;
        }

        if (tryLegacyPlayback(url)) {
            return;
        }

        win.location.href = url;
    }

    function tryModernPlayback(url) {
        var manager = win.stbPlayerManager;
        var player = manager && manager.list && manager.list.length ? manager.list[0] : null;
        var viewport = getScreenViewport();
        var rawViewport = getRawViewport();
        var usePartialViewport = viewport.x > 0 || viewport.y > 0 || viewport.width < rawViewport.width || viewport.height < rawViewport.height;

        if (!player || typeof player.play !== 'function') {
            return false;
        }

        try {
            setVideoOnTop();
            if (typeof player.stop === 'function') {
                player.stop();
            }
            player.aspectConversion = 1;
            player.videoWindowMode = 0;
            player.fullscreen = false;
            if (typeof player.setViewport === 'function') {
                player.setViewport(viewport);
            }
            if (!usePartialViewport) {
                player.fullscreen = true;
            }
            player.onPlayStart = function () {
                enterPlayback();
            };
            player.onPlayError = function () {
                exitPlayback();
                showToast('Playback could not be started on this MAG device.');
            };
            player.play({
                solution: 'auto',
                uri: url
            });
            state.activePlayerMode = 'modern';
            return true;
        } catch (error) {
            return false;
        }
    }

    function tryLegacyPlayback(url) {
        var providers = [win.stb, win.gSTB, win.STB];
        var playMethods = ['Play', 'play', 'Start', 'start', 'PlayUrl', 'playUrl', 'Open', 'open'];
        var stopMethods = ['Stop', 'stop'];
        var commands = [url, 'auto ' + url, 'ffrt ' + url, 'ffmpeg ' + url];
        var i;
        var j;
        var k;
        var provider;

        for (i = 0; i < providers.length; i += 1) {
            provider = providers[i];

            if (!provider) {
                continue;
            }

            try {
                setVideoOnTop();
            } catch (error) {
            }

            for (j = 0; j < stopMethods.length; j += 1) {
                if (typeof provider[stopMethods[j]] === 'function') {
                    try {
                        provider[stopMethods[j]]();
                    } catch (error) {
                    }
                }
            }

            if (typeof provider.SetVideoScreen === 'function') {
                try {
                    setLegacyVideoScreen(provider);
                } catch (error) {
                }
            }

            for (j = 0; j < playMethods.length; j += 1) {
                if (typeof provider[playMethods[j]] !== 'function') {
                    continue;
                }

                for (k = 0; k < commands.length; k += 1) {
                    try {
                        provider[playMethods[j]](commands[k]);
                        state.activePlayerMode = 'legacy';
                        enterPlayback();
                        return true;
                    } catch (error) {
                    }
                }
            }
        }

        return false;
    }

    function stopPlaybackEngine() {
        var manager = win.stbPlayerManager;
        var player = manager && manager.list && manager.list.length ? manager.list[0] : null;

        if (player) {
            try {
                player.onPlayStart = null;
                player.onPlayError = null;
            } catch (error) {
            }

            try {
                if (typeof player.stop === 'function') {
                    player.stop();
                }
            } catch (error) {
            }

            try {
                player.videoWindowMode = 2;
                player.fullscreen = false;
            } catch (error) {
            }
        }

        try {
            if (win.gSTB && typeof win.gSTB.Stop === 'function') {
                win.gSTB.Stop();
            }
        } catch (error) {
        }

        try {
            if (win.gSTB && typeof win.gSTB.DeinitPlayer === 'function') {
                win.gSTB.DeinitPlayer();
            }
        } catch (error) {
        }

        try {
            if (win.gSTB && typeof win.gSTB.SetVideoState === 'function') {
                win.gSTB.SetVideoState(0);
            }
        } catch (error) {
        }

        state.activePlayerMode = '';
    }

    function stopPlayback(showUi) {
        stopPlaybackEngine();
        exitPlayback();

        if (showUi) {
            renderCurrentView();
        }
    }

    function enterPlayback() {
        state.isPlaying = true;
        showPlaybackHud(true);
        setVideoOnTop();
        addClass(doc.body, 'playback-mode');
    }

    function exitPlayback() {
        state.isPlaying = false;
        state.playbackType = '';
        state.playbackTitle = '';
        state.playbackMeta = '';
        hidePlaybackHud();
        setGraphicOnTop();
        removeClass(doc.body, 'playback-mode');
    }

    function showPlaybackHud(force) {
        if (!force && !state.isPlaying) {
            return;
        }

        dom.hudLabel.innerHTML = escapeHtml(state.playbackType === 'live' ? 'Live TV' : 'Playback');
        dom.hudTitle.innerHTML = escapeHtml(state.playbackTitle || 'Playback');
        dom.hudMeta.innerHTML = escapeHtml(state.playbackMeta || 'Playback in progress');
        dom.hudHelp.innerHTML = escapeHtml(state.playbackType === 'live'
            ? 'Back stops playback. Up and Down switch channels. OK toggles this panel.'
            : 'Back stops playback. OK toggles this panel.');
        dom.playbackHud.className = 'visible';
        state.hudVisible = true;
    }

    function hidePlaybackHud() {
        dom.playbackHud.className = '';
        state.hudVisible = false;
    }

    function togglePlaybackHud() {
        if (!state.hudVisible) {
            showPlaybackHud(true);
            return;
        }

        hidePlaybackHud();
    }

    function setVideoOnTop() {
        try {
            if (win.gSTB && typeof win.gSTB.SetTopWin === 'function') {
                win.gSTB.SetTopWin(1);
            }
        } catch (error) {
        }
    }

    function setGraphicOnTop() {
        try {
            if (win.gSTB && typeof win.gSTB.SetTopWin === 'function') {
                win.gSTB.SetTopWin(0);
            }
        } catch (error) {
        }
    }

    function getScreenViewport() {
        var rawViewport = getRawViewport();
        var insets = getSafeInsets(rawViewport);

        return {
            x: insets.left,
            y: insets.top,
            width: Math.max(rawViewport.width - insets.left - insets.right, 320),
            height: Math.max(rawViewport.height - insets.top - insets.bottom, 240)
        };
    }

    function setLegacyVideoScreen(provider) {
        var viewport = getScreenViewport();

        provider.SetVideoScreen(
            viewport.x,
            viewport.y,
            viewport.width,
            viewport.height
        );
    }

    function isBackKey(code) {
        return code === 8 || code === 27 || code === 461 || code === 10009;
    }

    function isOkKey(code) {
        return code === 13 || code === 32;
    }

    function isUpKey(code) {
        return code === 38 || code === 63232;
    }

    function isDownKey(code) {
        return code === 40 || code === 63233;
    }

    function isLeftKey(code) {
        return code === 37 || code === 63234;
    }

    function isRightKey(code) {
        return code === 39 || code === 63235;
    }

    function isPageUpKey(code) {
        return code === 33 || code === 427 || code === 573;
    }

    function isPageDownKey(code) {
        return code === 34 || code === 428 || code === 574;
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
}(window, document));
