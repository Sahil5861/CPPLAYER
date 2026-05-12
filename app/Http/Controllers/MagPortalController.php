<?php

namespace App\Http\Controllers;

use App\Models\AdminSuperAdminPlan;
use App\Models\AppDomainContent;
use App\Models\Channel;
use App\Models\Slider;
use App\Models\ClientUser;
use App\Models\ContentNetwork;
use App\Models\ContentSlider;
use App\Models\Genre;
use App\Models\KidsChannel;
use App\Models\KidShowsSeason;
use App\Models\KidshowsEpisode;
use App\Models\KidsShow;
use App\Models\Language;
use App\Models\Movie;
use App\Models\MovieLink;
use App\Models\ResellerAdminPlan;
use App\Models\RelChannel;
use App\Models\RelShow;
use App\Models\RelshowsEpisode;
use App\Models\SportsCategory;
use App\Models\SportsTournament;
use App\Models\StageshowPak;
use App\Models\TvChannel;
use App\Models\TvChannelPak;
use App\Models\TvShow;
use App\Models\TvShowEpisode;
use App\Models\TvShowEpisodePak;
use App\Models\TvShowPak;
use App\Models\TvShowSeason;
use App\Models\TvShowSeasonPak;
use App\Models\User;
use App\Models\UserPlanDetails;
use App\Models\WebSeries;
use App\Models\WebSeriesEpisode;
use App\Models\WebSeriesSeason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MagPortalController extends Controller
{
    public function frontend(Request $request): Response
    {
        return response()->view('mag.portal', [
            'assetBaseUrl' => $this->frontendBaseUrl($request),
            'apiBaseUrl' => $this->frontendApiBaseUrl($request),
            'loadUrl' => url('/mag/server/load.php'),
            'magApiBase' => url('/mag/api'),
        ], 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function frontendAsset(Request $request, string $asset): Response
    {
        $asset = trim($asset, '/');

        if ($asset === '' || $asset === 'index.html') {
            return $this->frontend($request);
        }

        if ($asset === 'version.js') {
            return response()->view('mag.version', [], 200, [
                'Content-Type' => 'application/javascript; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        }

        if ($asset === 'xpcom.common.js') {
            return response()->view('mag.xpcom-common', [
                'loadUrl' => url('/mag/server/load.php'),
            ], 200, [
                'Content-Type' => 'application/javascript; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        }

        abort(404);
    }

    public function handle(Request $request): JsonResponse
    {
        $type = strtolower((string) ($request->input('type') ?? $request->query('type', 'stb')));
        $action = strtolower((string) ($request->input('action') ?? $request->query('action', 'handshake')));
        $type = $type === 'video' ? 'vod' : $type;

        try {
            return match (true) {
                $action === 'handshake' => $this->handshake($request),
                $action === 'get_profile' => $this->getProfile($request),
                $action === 'get_main_info' => $this->getMainInfo($request),
                $action === 'get_modules' => $this->getModules($request),
                $type === 'itv' && $action === 'get_genres' => $this->getLiveGenres($request),
                $type === 'itv' && $action === 'get_all_channels' => $this->getAllLiveChannels($request),
                $type === 'itv' && $action === 'create_link' => $this->createLiveLink($request),
                $type === 'vod' && $action === 'get_categories' => $this->getVodCategories($request),
                $type === 'vod' && $action === 'get_ordered_list' => $this->getVodOrderedList($request),
                $type === 'vod' && $action === 'create_link' => $this->createVodLink($request),
                default => $this->portalError("Unsupported MAG request: {$type}/{$action}"),
            };
        } catch (\Throwable $exception) {
            Log::warning('MAG portal request failed', [
                'type' => $type,
                'action' => $action,
                'message' => $exception->getMessage(),
            ]);

            return $this->portalError($exception->getMessage());
        }
    }

    public function stream(string $token): RedirectResponse
    {
        $payload = Cache::get($this->streamCacheKey($token));

        if (!$payload || empty($payload['url'])) {
            abort(403, 'Invalid or expired MAG stream token.');
        }

        return redirect()->away($payload['url']);
    }

    protected function handshake(Request $request): JsonResponse
    {
        [$user, $mac] = $this->resolvePortalUser($request, false);

        $token = $this->issuePortalToken($user, $mac);

        return $this->portalResponse([
            'token' => $token,
            'random' => Str::random(32),
            'mac' => $mac,
            'token_expires_at' => now()->addHours(12)->timestamp,
        ]);
    }

    protected function getProfile(Request $request): JsonResponse
    {
        [$user, $mac, $plans] = $this->resolvePortalUser($request, true);
        $primaryPlan = $plans->first();

        return $this->portalResponse([
            'id' => (string) $user->id,
            'name' => (string) $user->name,
            'mac' => $mac,
            'fname' => (string) $user->name,
            'login' => (string) ($user->email ?: $user->mobile ?: $user->id),
            'status' => 1,
            'stb_type' => $this->detectStbType($request),
            'client_type' => 'STB',
            'default_timezone' => config('app.timezone'),
            'default_language' => 'en',
            'phone' => (string) ($user->mobile ?: ''),
            'ls' => 'en',
            'tariff_plan' => $this->formatPlanName($primaryPlan),
            'end_date' => optional($primaryPlan)->plan_end_date,
            'comment' => 'Mapped via Laravel MAG portal',
        ]);
    }

    protected function getMainInfo(Request $request): JsonResponse
    {
        [$user, , $plans] = $this->resolvePortalUser($request, true);
        $primaryPlan = $plans->first();

        return $this->portalResponse([
            'phone' => (string) ($user->mobile ?: ''),
            'name' => (string) $user->name,
            'fname' => (string) $user->name,
            'account_balance' => (string) number_format((float) ($user->current_amount ?? 0), 2, '.', ''),
            'tariff_plan' => $this->formatPlanName($primaryPlan),
            'end_date' => optional($primaryPlan)->plan_end_date,
            'account_state' => 1,
            'status' => 1,
        ]);
    }

    protected function getModules(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        return $this->portalResponse([
            'itv' => true,
            'vod' => true,
            'radio' => false,
            'karaoke' => false,
            'timeshift' => false,
        ]);
    }

    protected function getLiveGenres(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $channels = $this->getLiveChannelsForPlans($plans, $request);
        $genres = $this->buildLiveGenreMap($channels);

        return $this->portalResponse($genres->values()->all());
    }

    protected function getAllLiveChannels(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $channels = $this->getLiveChannelsForPlans($plans, $request);
        $genres = $this->buildLiveGenreMap($channels);
        $selectedGenreId = (int) ($request->input('genre') ?? $request->query('genre') ?? 0);
        $selectedGenre = $genres->firstWhere('id', $selectedGenreId);
        $selectedGenreTitle = is_array($selectedGenre) ? ($selectedGenre['title'] ?? null) : null;

        if ($selectedGenreTitle && $selectedGenreId !== 0) {
            $channels = $channels->filter(function ($channel) use ($selectedGenreTitle) {
                return collect($this->parseCsvValues($channel->genres ?? ''))
                    ->contains(fn ($value) => strcasecmp($value, $selectedGenreTitle) === 0);
            })->values();
        }

        $genreIdLookup = $genres->pluck('id', 'title');

        $payload = $channels->map(function ($channel, $index) use ($genreIdLookup) {
            $primaryGenre = $this->parseCsvValues($channel->genres ?? '')[0] ?? 'All Channels';
            $channelNumber = (int) ($channel->channel_number ?: ($index + 1));

            return [
                'id' => (string) $channel->id,
                'name' => (string) $channel->channel_name,
                'number' => $channelNumber,
                'tv_genre_id' => (int) ($genreIdLookup[$primaryGenre] ?? 0),
                'cmd' => sprintf('ffrt http://localhost/ch/%d_', $channel->id),
                'logo' => $this->normalizeAssetUrl($channel->channel_logo ?? ''),
                'use_http_tmp_link' => 1,
                'xmltv_id' => (string) $channel->channel_name,
                'description' => (string) ($channel->channel_description ?? ''),
                'stream_type' => (string) ($channel->stream_type ?? ''),
            ];
        })->values();

        return $this->portalResponse($payload->all());
    }

    protected function createLiveLink(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $channels = $this->getLiveChannelsForPlans($plans, $request);
        $command = $this->extractCommand($request);
        $channelId = $this->extractIdFromCommand($command, '#/(?:ch|live)/(\d+)_?#');

        if (!$channelId) {
            return $this->portalError('Unable to resolve the requested live channel.');
        }

        $channel = $channels->firstWhere('id', $channelId);

        if (!$channel || empty($channel->channel_link)) {
            return $this->portalError('Requested live channel is not available for this MAG device.');
        }

        $token = $this->issueStreamToken([
            'type' => 'itv',
            'resource_id' => $channel->id,
            'url' => $channel->channel_link,
        ]);

        return $this->portalResponse([
            'id' => (string) $channel->id,
            'cmd' => 'ffrt ' . url('/mag/stream/' . $token),
            'streamer_id' => (string) $channel->id,
            'link_id' => (string) $channel->id,
            'use_http_tmp_link' => 1,
        ]);
    }

    protected function getVodCategories(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $movies = $this->getVodMoviesForPlans($plans);
        $categories = $this->buildVodGenreMap($movies);

        return $this->portalResponse($categories->values()->all());
    }

    protected function getVodOrderedList(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $movies = $this->getVodMoviesForPlans($plans);
        $categories = $this->buildVodGenreMap($movies);

        $selectedCategoryId = (int) ($request->input('category') ?? $request->query('category') ?? 0);
        if ($selectedCategoryId !== 0) {
            $movies = $movies->filter(function ($movie) use ($selectedCategoryId) {
                return collect($this->parseNumericCsvValues($movie->genres ?? ''))->contains($selectedCategoryId);
            })->values();
        }

        $page = max(1, (int) ($request->input('p') ?? $request->query('p') ?? 1));
        $perPage = max(1, (int) ($request->input('item_cnt') ?? $request->query('item_cnt') ?? 50));
        $totalItems = $movies->count();
        $items = $movies->slice(($page - 1) * $perPage, $perPage)->values();

        $payload = $items->map(function ($movie) use ($categories) {
            $genreIds = $this->parseNumericCsvValues($movie->genres ?? '');
            $primaryGenreId = $genreIds[0] ?? 0;
            $firstActiveLink = MovieLink::where('movie_id', $movie->id)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderByRaw('COALESCE(link_order, `order`, id) asc')
                ->first();

            $playbackReference = $firstActiveLink ? $firstActiveLink->id : 0;

            return [
                'id' => (string) $movie->id,
                'title' => (string) $movie->name,
                'name' => (string) $movie->name,
                'description' => (string) ($movie->description ?? ''),
                'category_id' => $primaryGenreId,
                'screenshot_uri' => $this->normalizeAssetUrl($movie->banner ?? ''),
                'cover_big' => $this->normalizeAssetUrl($movie->banner ?? ''),
                'cmd' => sprintf('ffrt http://localhost/vod/%d_%d', $movie->id, $playbackReference),
                'genres' => $categories
                    ->whereIn('id', $genreIds)
                    ->pluck('title')
                    ->values()
                    ->all(),
            ];
        })->values();

        return $this->portalResponse([
            'total_items' => $totalItems,
            'max_page_items' => $perPage,
            'selected_item' => 0,
            'data' => $payload->all(),
        ]);
    }

    protected function createVodLink(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $movies = $this->getVodMoviesForPlans($plans);
        $command = $this->extractCommand($request);
        $match = [];

        if (!preg_match('#/(?:vod|movie)/(\d+)(?:_(\d+))?#', $command, $match)) {
            return $this->portalError('Unable to resolve the requested VOD item.');
        }

        $movieId = (int) $match[1];
        $linkId = isset($match[2]) ? (int) $match[2] : 0;
        $movie = $movies->firstWhere('id', $movieId);

        if (!$movie) {
            return $this->portalError('Requested VOD item is not available for this MAG device.');
        }

        $playbackUrl = $this->resolveVodPlaybackUrl($movie, $linkId);
        if (!$playbackUrl) {
            return $this->portalError('No playback URL is configured for this VOD item.');
        }

        $token = $this->issueStreamToken([
            'type' => 'vod',
            'resource_id' => $movie->id,
            'url' => $playbackUrl,
        ]);

        return $this->portalResponse([
            'id' => (string) $movie->id,
            'cmd' => 'ffrt ' . url('/mag/stream/' . $token),
            'streamer_id' => (string) $movie->id,
            'link_id' => (string) ($linkId ?: $movie->id),
            'use_http_tmp_link' => 1,
        ]);
    }

    public function frontendBootstrap(Request $request): JsonResponse
    {
        [$user, $mac, $plans] = $this->resolvePortalUser($request, true);
        $primaryPlan = $plans->first();

        return response()->json([
            'status' => true,
            'profile' => [
                'id' => (int) $user->id,
                'name' => (string) $user->name,
                'mac' => $mac,
                'plan' => $this->formatPlanName($primaryPlan),
                'expires_at' => optional($primaryPlan)->plan_end_date,
                'balance' => (float) ($user->current_amount ?? 0),
                'phone' => (string) ($user->mobile ?: ''),
            ],
            'device' => [
                'stb_type' => $this->detectStbType($request),
                'portal' => 'CP Players TV',
                'version' => '2.0.0',
            ],
            'sections' => $this->frontendSections(),
        ]);
    }

    public function frontendSection(Request $request, string $section): JsonResponse
    {
        [$user, $mac, $plans] = $this->resolvePortalUser($request, true);
        $sectionConfig = $this->findFrontendSection($section);

        if (!$sectionConfig) {
            return response()->json([
                'status' => false,
                'message' => 'Unknown TV section requested.',
            ], 404);
        }

        if ($sectionConfig['slug'] === 'live') {
            return $this->frontendLiveBrowserSection($request, $sectionConfig, $plans);
        }

        if ($sectionConfig['slug'] === 'search') {
            return response()->json([
                'status' => true,
                'screen' => 'search',
                'section' => $sectionConfig,
                'hero' => [
                    'title' => 'Global Search',
                    'subtitle' => 'Search across live TV, movies, web series, TV shows, kids content, religious content, and stage shows.',
                    'backdrop' => '',
                    'badge' => 'Search',
                    'meta' => ['Universal Search'],
                ],
                'query' => '',
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 18,
                    'total' => 0,
                    'has_more' => false,
                ],
            ]);
        }

        if ($sectionConfig['slug'] === 'settings') {
            $primaryPlan = $plans->first();

            return response()->json([
                'status' => true,
                'screen' => 'section',
                'section' => $sectionConfig,
                'hero' => [
                    'title' => 'Settings',
                    'subtitle' => 'Account, device, and remote guidance for this mapped TV box.',
                    'backdrop' => '',
                    'badge' => 'CP Players',
                    'meta' => array_values(array_filter([
                        $this->formatPlanName($primaryPlan),
                        optional($primaryPlan)->plan_end_date ? 'Expires ' . optional($primaryPlan)->plan_end_date : null,
                    ])),
                ],
                'filters' => [[
                    'id' => 'settings',
                    'label' => 'Overview',
                ]],
                'rows' => [[
                    'id' => 'settings',
                    'title' => 'TV Box Settings',
                    'accent' => $sectionConfig['accent'],
                    'items' => [
                        [
                            'id' => 1,
                            'type' => 'info',
                            'title' => (string) $user->name,
                            'subtitle' => 'Customer Account',
                            'description' => 'Mapped account for this device.',
                            'image' => '',
                            'backdrop' => '',
                            'badge' => 'Account',
                            'meta' => array_values(array_filter([
                                $this->formatPlanName($primaryPlan),
                                (string) ($user->mobile ?: ''),
                            ])),
                            'action' => 'info',
                        ],
                        [
                            'id' => 2,
                            'type' => 'info',
                            'title' => (string) $mac,
                            'subtitle' => 'Box MAC Address',
                            'description' => 'This is the device identifier currently mapped to the account.',
                            'image' => '',
                            'backdrop' => '',
                            'badge' => 'Device',
                            'meta' => ['MAG / STB Mapping'],
                            'action' => 'info',
                        ],
                        [
                            'id' => 3,
                            'type' => 'info',
                            'title' => optional($primaryPlan)->plan_end_date ?: 'Active',
                            'subtitle' => 'Subscription',
                            'description' => 'Plan expiry and account status for the current device.',
                            'image' => '',
                            'backdrop' => '',
                            'badge' => 'Plan',
                            'meta' => array_values(array_filter([
                                $this->formatPlanName($primaryPlan),
                                'Balance ' . number_format((float) ($user->current_amount ?? 0), 2, '.', ''),
                            ])),
                            'action' => 'info',
                        ],
                        [
                            'id' => 4,
                            'type' => 'info',
                            'title' => 'Remote Help',
                            'subtitle' => 'Navigation Guide',
                            'description' => 'Up/Down moves, Left/Right switches focus, OK opens or plays, and Back returns or stops playback.',
                            'image' => '',
                            'backdrop' => '',
                            'badge' => 'Remote',
                            'meta' => ['Universal Navigation'],
                            'action' => 'info',
                        ],
                    ],
                ]],
                'empty' => false,
            ]);
        }

        if (($sectionConfig['screen'] ?? 'legacy') === 'browser') {
            return $this->frontendBrowserSection($request, $sectionConfig);
        }

        $networks = $this->getFrontendNetworksForSection($sectionConfig['data_for'] ?? null);
        $networkCards = $networks
            ->map(fn ($network, $index) => $this->mapFrontendNetworkCard($network, $index))
            ->values()
            ->all();

        $hero = $this->buildSectionHero($sectionConfig, $networkCards);
        $rows = [[
            'id' => 'networks',
            'title' => $sectionConfig['rail_title'] ?? 'Browse Networks',
            'accent' => $sectionConfig['accent'],
            'items' => $networkCards,
        ]];

        return response()->json([
            'status' => true,
            'screen' => 'section',
            'section' => $sectionConfig,
            'hero' => $hero,
            'filters' => [[
                'id' => 'networks',
                'label' => 'All Networks',
            ]],
            'rows' => $rows,
            'empty' => empty($networkCards),
        ]);
    }

    public function frontendNetwork(Request $request, int $networkId): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $sectionConfig = $this->findFrontendSection((string) $request->query('section', 'contents'))
            ?? $this->findFrontendSection('contents');

        $network = ContentNetwork::query()
            ->where('id', $networkId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$network) {
            return response()->json([
                'status' => false,
                'message' => 'Requested content network is not available.',
            ], 404);
        }

        $payload = $this->buildFrontendNetworkPayload($network, $sectionConfig['data_for'] ?? null);

        return response()->json([
            'status' => true,
            'screen' => 'network',
            'section' => $sectionConfig,
            'network' => $this->mapFrontendNetworkCard($network, 0),
            'hero' => $payload['hero'],
            'filters' => $payload['filters'],
            'rows' => $payload['rows'],
            'empty' => empty($payload['rows']),
        ]);
    }

    public function frontendContentDetail(Request $request, string $type, int $id): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $type = strtolower($type);

        return match ($type) {
            'movie' => $this->frontendMovieDetail($id),
            'webseries' => $this->frontendWebSeriesDetail($id),
            'tvshow' => $this->frontendTvShowDetail($id),
            'tvshowpak' => $this->frontendTvShowPakDetail($id),
            'kids' => $this->frontendKidsDetail($id),
            'religious' => $this->frontendReligiousDetail($id),
            default => response()->json([
                'status' => false,
                'message' => 'Unsupported content detail request.',
            ], 404),
        };
    }

    public function frontendPlay(Request $request, string $type, int $id): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);
        $type = strtolower($type);

        return match ($type) {
            'live' => $this->frontendPlayLive($plans, $id),
            'movie' => $this->frontendPlayMovie($plans, $id, (int) $request->query('link_id', 0)),
            'webseries-episode' => $this->frontendPlayWebSeriesEpisode($id),
            'tvshow-episode' => $this->frontendPlayTvShowEpisode($id),
            'tvshowpak-episode' => $this->frontendPlayTvShowPakEpisode($id),
            'kids-episode' => $this->frontendPlayKidsEpisode($id),
            'religious-episode' => $this->frontendPlayReligiousEpisode($id),
            'stageshow' => $this->frontendPlayStageShow($id),
            default => response()->json([
                'status' => false,
                'message' => 'Unsupported playback request.',
            ], 404),
        };
    }

    public function frontendSearch(Request $request): JsonResponse
    {
        [, , $plans] = $this->resolvePortalUser($request, true);

        $query = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 18;

        if (mb_strlen($query) < 2) {
            return response()->json([
                'status' => true,
                'screen' => 'search',
                'query' => $query,
                'hero' => [
                    'title' => 'Search',
                    'subtitle' => 'Enter at least two characters to search across live channels, movies, series, and shows.',
                    'backdrop' => '',
                    'badge' => 'Universal Search',
                    'meta' => [],
                ],
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'has_more' => false,
                ],
                'filters' => [],
                'rows' => [],
            ]);
        }

        $results = collect();

        $results = $results->merge(
            $this->getLiveChannelsForPlans($plans)
                ->filter(fn ($channel) => stripos((string) ($channel->channel_name ?? ''), $query) !== false)
                ->map(fn ($channel) => $this->mapFrontendLiveChannel($channel))
                ->values()
        );

        $results = $results->merge(
            Movie::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get(['id'])
                ->map(fn ($movie) => $this->loadFrontendMovieCard((int) $movie->id))
                ->filter()
                ->values()
        );

        $results = $results->merge(
            WebSeries::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get()
                ->map(fn ($series) => $this->mapFrontendWebSeriesResult($series))
                ->values()
        );

        $results = $results->merge(
            TvShow::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get()
                ->map(fn ($show) => $this->mapFrontendTvShowResult($show))
                ->values()
        );

        $results = $results->merge(
            TvShowPak::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get()
                ->map(fn ($show) => $this->mapFrontendTvShowPakResult($show))
                ->values()
        );

        $results = $results->merge(
            KidsShow::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get()
                ->map(fn ($show) => $this->mapFrontendKidsResult($show))
                ->values()
        );

        $results = $results->merge(
            RelShow::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('title', 'LIKE', '%' . $query . '%');
                })
                ->limit(40)
                ->get()
                ->map(fn ($show) => $this->mapFrontendReligiousResult($show))
                ->values()
        );

        $results = $results->merge(
            SportsCategory::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('title', 'LIKE', '%' . $query . '%');
                })
                ->limit(40)
                ->get()
                ->map(function ($category) {
                    $card = $this->loadFrontendSportsCard((int) $category->id);

                    if (!$card) {
                        return null;
                    }

                    $card['action'] = 'open-section';
                    $card['section_slug'] = 'sports';

                    return $card;
                })
                ->filter()
                ->values()
        );

        $results = $results->merge(
            StageshowPak::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->limit(40)
                ->get(['id'])
                ->map(fn ($show) => $this->loadFrontendStageShowCard((int) $show->id))
                ->filter()
                ->values()
        );

        $results = $results
            ->filter()
            ->unique(fn ($item) => ($item['type'] ?? 'item') . ':' . ($item['id'] ?? 0))
            ->sortByDesc('sort_at')
            ->values();

        $total = $results->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($page, $lastPage);
        $pagedItems = $results
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values()
            ->all();
        $rows = $this->buildFrontendSearchRows(collect($pagedItems));

        return response()->json([
            'status' => true,
            'screen' => 'search',
            'query' => $query,
            'hero' => [
                'title' => 'Search Results',
                'subtitle' => 'Universal search across live channels and on-demand content.',
                'backdrop' => $pagedItems[0]['backdrop'] ?? ($pagedItems[0]['image'] ?? ''),
                'badge' => 'Search',
                'meta' => array_values(array_filter([
                    '"' . $query . '"',
                    $total > 0 ? $total . ' matches' : null,
                    $lastPage > 1 ? 'Page ' . $currentPage . ' of ' . $lastPage : null,
                ])),
            ],
            'items' => $pagedItems,
            'filters' => collect($rows)
                ->map(fn ($row) => [
                    'id' => (string) ($row['id'] ?? 'all'),
                    'label' => (string) ($row['title'] ?? 'Items'),
                ])
                ->values()
                ->all(),
            'rows' => $rows,
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => $currentPage < $lastPage,
            ],
        ]);
    }

    protected function buildFrontendSearchRows(Collection $items): array
    {
        $definitions = [
            'live' => ['title' => 'Live TV', 'accent' => '#5f89ff'],
            'movie' => ['title' => 'Movies', 'accent' => '#ffb045'],
            'webseries' => ['title' => 'Web Series', 'accent' => '#7b8cff'],
            'tvshow' => ['title' => 'TV Shows', 'accent' => '#f55f7e'],
            'tvshowpak' => ['title' => 'TV Shows Pak', 'accent' => '#6fa96f'],
            'kids' => ['title' => 'Kids', 'accent' => '#f4c452'],
            'religious' => ['title' => 'Religious', 'accent' => '#42c8b4'],
            'sports' => ['title' => 'Sports', 'accent' => '#59a6ff'],
            'stageshow' => ['title' => 'Stage Shows', 'accent' => '#ff7d6b'],
        ];

        $rows = [];

        foreach ($definitions as $id => $definition) {
            $rowItems = $items
                ->filter(fn ($item) => (string) ($item['type'] ?? '') === $id)
                ->values()
                ->all();

            if (empty($rowItems)) {
                continue;
            }

            $rows[] = [
                'id' => $id,
                'title' => $definition['title'],
                'accent' => $definition['accent'],
                'items' => $rowItems,
            ];
        }

        return $rows;
    }

    protected function frontendBrowserSection(Request $request, array $sectionConfig): JsonResponse
    {
        $networks = $this->getFrontendNetworksForSection($sectionConfig['data_for'] ?? null);
        $selectedNetwork = $networks->firstWhere('id', (int) $request->query('network'))
            ?? $networks->first();

        if (!$selectedNetwork) {
            return response()->json([
                'status' => true,
                'screen' => 'browser',
                'section' => $sectionConfig,
                'hero' => $this->buildSectionHero($sectionConfig, []),
                'networks' => [],
                'selected_network_id' => 0,
                'genres' => [['id' => '', 'label' => 'All']],
                'selected_genre' => '',
                'channels' => [['id' => 0, 'label' => 'All']],
                'selected_channel_id' => 0,
                'slides' => [],
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 12,
                    'total' => 0,
                    'has_more' => false,
                ],
                'empty' => true,
            ]);
        }

        $payload = $this->buildFrontendBrowserPayload(
            $selectedNetwork,
            $sectionConfig,
            (string) $request->query('genre', ''),
            (int) $request->query('channel', 0),
            max(1, (int) $request->query('page', 1)),
            12
        );

        return response()->json([
            'status' => true,
            'screen' => 'browser',
            'section' => $sectionConfig,
            'hero' => $payload['hero'],
            'browser_primary_kind' => (string) ($sectionConfig['browser_primary_kind'] ?? 'genre'),
            'networks' => $networks
                ->values()
                ->map(fn ($network) => [
                    'id' => (int) $network->id,
                    'label' => (string) $network->name,
                ])
                ->all(),
            'selected_network_id' => (int) $selectedNetwork->id,
            'genres' => $payload['genres'],
            'selected_genre' => $payload['selected_genre'],
            'channels' => $payload['channels'],
            'selected_channel_id' => $payload['selected_channel_id'],
            'slides' => $payload['slides'],
            'items' => $payload['items'],
            'pagination' => $payload['pagination'],
            'empty' => empty($payload['items']),
        ]);
    }

    protected function frontendLiveBrowserSection(Request $request, array $sectionConfig, Collection $plans): JsonResponse
    {
        $channels = $this->getLiveChannelsForPlans($plans, $request);
        $languages = $this->buildFrontendLiveLanguageTabs($channels);
        $selectedLanguageId = collect($languages)->pluck('id')->contains((int) $request->query('network'))
            ? (int) $request->query('network')
            : (int) ($languages[0]['id'] ?? 0);

        $payload = $this->buildFrontendLiveBrowserPayload(
            $channels,
            $sectionConfig,
            $selectedLanguageId,
            (string) $request->query('genre', ''),
            max(1, (int) $request->query('page', 1)),
            18
        );

        return response()->json([
            'status' => true,
            'screen' => 'browser',
            'section' => $sectionConfig,
            'hero' => $payload['hero'],
            'browser_primary_kind' => 'genre',
            'networks' => $languages,
            'selected_network_id' => $payload['selected_network_id'],
            'genres' => $payload['genres'],
            'selected_genre' => $payload['selected_genre'],
            'channels' => [],
            'selected_channel_id' => 0,
            'slides' => $payload['slides'],
            'items' => $payload['items'],
            'pagination' => $payload['pagination'],
            'empty' => empty($payload['items']),
        ]);
    }

    protected function buildFrontendBrowserPayload(
        ContentNetwork $network,
        array $sectionConfig,
        string $genre,
        int $channelId,
        int $page,
        int $perPage
    ): array {
        $dataFor = $sectionConfig['data_for'] ?? null;
        $taxonomy = $this->buildFrontendBrowserTaxonomy((int) $network->id, $dataFor);

        $selectedGenre = collect($taxonomy['genres'])->contains($genre) ? $genre : '';
        $selectedChannelId = collect($taxonomy['channels'])->pluck('id')->contains($channelId) ? $channelId : 0;

        $items = collect($this->buildFrontendBrowserItems((int) $network->id, $dataFor, $selectedGenre, $selectedChannelId))
            ->unique(fn ($item) => ($item['type'] ?? 'item') . ':' . ($item['id'] ?? 0))
            ->sortByDesc('sort_at')
            ->values();

        $total = $items->count();
        $lastPage = max(1, (int) ceil($total / max($perPage, 1)));
        $currentPage = min(max($page, 1), $lastPage);
        $pagedItems = $items
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values()
            ->all();

        $slides = $this->getFrontendSlidesForNetwork((int) $network->id, $dataFor);
        $heroSource = $slides[0] ?? $pagedItems[0] ?? $items->first();

        $hero = [
            'title' => $heroSource['title'] ?? ($sectionConfig['title'] ?? (string) $network->name),
            'subtitle' => $heroSource['subtitle'] ?? ($heroSource['description'] ?? ($sectionConfig['copy'] ?? 'Browse the available TV catalog.')),
            'backdrop' => $heroSource['image'] ?? ($heroSource['backdrop'] ?? $this->normalizeAssetUrl($network->logo ?? '')),
            'badge' => (string) $network->name,
            'meta' => array_values(array_filter([
                $sectionConfig['label'] ?? null,
                $total > 0 ? $total . ' titles' : null,
                $lastPage > 1 ? 'Page ' . $currentPage . ' of ' . $lastPage : null,
            ])),
        ];

        return [
            'hero' => $hero,
            'genres' => collect([['id' => '', 'label' => 'All']])
                ->merge(collect($taxonomy['genres'])->map(fn ($value) => [
                    'id' => (string) $value,
                    'label' => (string) $value,
                ]))
                ->values()
                ->all(),
            'selected_genre' => $selectedGenre,
            'channels' => collect([['id' => 0, 'label' => 'All']])
                ->merge(collect($taxonomy['channels'])->map(fn ($channel) => [
                    'id' => (int) ($channel['id'] ?? 0),
                    'label' => (string) ($channel['label'] ?? 'Channel'),
                ]))
                ->values()
                ->all(),
            'selected_channel_id' => $selectedChannelId,
            'slides' => $slides,
            'items' => $pagedItems,
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => $currentPage < $lastPage,
            ],
        ];
    }

    protected function buildFrontendLiveBrowserPayload(
        Collection $channels,
        array $sectionConfig,
        int $languageId,
        string $genre,
        int $page,
        int $perPage
    ): array {
        $languages = $this->buildFrontendLiveLanguageTabs($channels);
        $selectedLanguageId = collect($languages)->pluck('id')->contains($languageId)
            ? $languageId
            : (int) ($languages[0]['id'] ?? 0);

        $filteredChannels = $channels
            ->when($selectedLanguageId > 0, fn (Collection $items) => $items->filter(
                fn ($channel) => (int) ($channel->channel_language ?? 0) === $selectedLanguageId
            ))
            ->values();

        $selectedGenre = collect($this->buildFrontendLiveGenreTabs($filteredChannels))
            ->pluck('id')
            ->contains($genre)
                ? $genre
                : '';

        $filteredChannels = $filteredChannels
            ->when($selectedGenre !== '', function (Collection $items) use ($selectedGenre) {
                return $items->filter(function ($channel) use ($selectedGenre) {
                    return collect($this->parseCsvValues((string) ($channel->genres ?? '')))
                        ->contains(fn ($value) => strcasecmp((string) $value, $selectedGenre) === 0);
                });
            })
            ->values();

        $mappedChannels = $filteredChannels
            ->map(fn ($channel) => $this->mapFrontendLiveChannel($channel))
            ->sortByDesc('sort_at')
            ->values();

        $total = $mappedChannels->count();
        $lastPage = max(1, (int) ceil($total / max($perPage, 1)));
        $currentPage = min(max($page, 1), $lastPage);
        $pagedItems = $mappedChannels
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values()
            ->all();

        $slides = $this->getFrontendLiveSlidesForLanguage($selectedLanguageId);
        $heroSource = $slides[0] ?? $pagedItems[0] ?? $mappedChannels->first();
        $languageLabel = collect($languages)
            ->firstWhere('id', $selectedLanguageId)['label'] ?? 'All Languages';

        return [
            'hero' => [
                'title' => $heroSource['title'] ?? $sectionConfig['title'],
                'subtitle' => $heroSource['subtitle'] ?? ($heroSource['description'] ?? $sectionConfig['copy']),
                'backdrop' => $heroSource['image'] ?? ($heroSource['backdrop'] ?? ''),
                'badge' => $languageLabel,
                'meta' => array_values(array_filter([
                    $selectedGenre !== '' ? $selectedGenre : 'All Genres',
                    $total > 0 ? $total . ' channels' : null,
                    $lastPage > 1 ? 'Page ' . $currentPage . ' of ' . $lastPage : null,
                ])),
            ],
            'selected_network_id' => $selectedLanguageId,
            'genres' => collect([['id' => '', 'label' => 'All']])
                ->merge(collect($this->buildFrontendLiveGenreTabs($filteredChannels))->map(fn ($tab) => [
                    'id' => (string) ($tab['id'] ?? ''),
                    'label' => (string) ($tab['label'] ?? ''),
                ]))
                ->unique('id')
                ->values()
                ->all(),
            'selected_genre' => $selectedGenre,
            'slides' => $slides,
            'items' => $pagedItems,
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => $currentPage < $lastPage,
            ],
        ];
    }

    protected function buildFrontendLiveLanguageTabs(Collection $channels): array
    {
        $languageIds = $channels
            ->map(fn ($channel) => (int) ($channel->channel_language ?? 0))
            ->filter()
            ->unique()
            ->values();

        if ($languageIds->isEmpty()) {
            return [];
        }

        return Language::query()
            ->whereIn('id', $languageIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn ($language) => [
                'id' => (int) $language->id,
                'label' => (string) $language->title,
            ])
            ->values()
            ->all();
    }

    protected function buildFrontendLiveGenreTabs(Collection $channels): array
    {
        return $channels
            ->flatMap(fn ($channel) => $this->parseCsvValues((string) ($channel->genres ?? '')))
            ->filter()
            ->map(fn ($genre) => trim((string) $genre))
            ->filter()
            ->unique(fn ($genre) => mb_strtolower($genre))
            ->sortBy(fn ($genre) => mb_strtolower($genre))
            ->values()
            ->map(fn ($genre) => [
                'id' => (string) $genre,
                'label' => (string) $genre,
            ])
            ->all();
    }

    protected function getFrontendLiveSlidesForLanguage(int $languageId): array
    {
        if ($languageId <= 0) {
            return [];
        }

        $language = Language::query()
            ->where('id', $languageId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->with('slider')
            ->first();

        if (!$language) {
            return [];
        }

        return collect($language->slider ?? [])
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->sortByDesc('id')
            ->map(fn ($slider) => [
                'id' => (int) $slider->id,
                'title' => (string) ($slider->title ?: $language->title),
                'subtitle' => 'Featured live channels for this language.',
                'image' => $this->normalizeAssetUrl($slider->banner ?? ''),
            ])
            ->values()
            ->all();
    }

    protected function buildFrontendBrowserTaxonomy(int $networkId, ?string $dataFor): array
    {
        $logs = DB::table('content_network_log')
            ->where('network_id', $networkId)
            ->when(!empty($this->frontendContentTypesForDataFor($dataFor)), function ($query) use ($dataFor) {
                $query->whereIn('content_type', $this->frontendContentTypesForDataFor($dataFor));
            })
            ->get();

        $genres = collect();
        $channels = collect();

        foreach ($logs as $log) {
            if ((int) $log->content_type === 1) {
                $movie = Movie::query()
                    ->where('id', (int) $log->content_id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                if ($movie && ($dataFor !== 'movies' || (int) ($movie->is_recent ?? 0) === 1)) {
                    $genres = $genres->merge($this->parseCsvValues((string) ($movie->genres ?? '')));
                }
                continue;
            }

            if ((int) $log->content_type === 2) {
                $series = WebSeries::query()
                    ->where('id', (int) $log->content_id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                if ($series) {
                    $genres = $genres->merge($this->parseCsvValues((string) ($series->genres ?? '')));
                }
                continue;
            }

            if ((int) $log->content_type === 4) {
                $channel = TvChannel::query()->where('id', (int) $log->content_id)->where('status', 1)->whereNull('deleted_at')->first();
                if ($channel) {
                    $channels->push([
                        'id' => (int) $channel->id,
                        'label' => (string) $channel->name,
                    ]);
                    $genres = $genres->merge(
                        TvShow::query()
                            ->where('tv_channel_id', $channel->id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->pluck('genre')
                            ->flatMap(fn ($value) => $this->parseCsvValues((string) $value))
                            ->all()
                    );
                }
                continue;
            }

            if ((int) $log->content_type === 5) {
                $channel = TvChannelPak::query()->where('id', (int) $log->content_id)->where('status', 1)->whereNull('deleted_at')->first();
                if ($channel) {
                    $channels->push([
                        'id' => (int) $channel->id,
                        'label' => (string) $channel->name,
                    ]);
                    $genres = $genres->merge(
                        TvShowPak::query()
                            ->where('tv_channel_id', $channel->id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->pluck('genre')
                            ->flatMap(fn ($value) => $this->parseCsvValues((string) $value))
                            ->all()
                    );
                }
                continue;
            }

            if ((int) $log->content_type === 6) {
                $channel = KidsChannel::query()->where('id', (int) $log->content_id)->where('status', 1)->whereNull('deleted_at')->first();
                if ($channel) {
                    $channels->push([
                        'id' => (int) $channel->id,
                        'label' => (string) $channel->name,
                    ]);
                    $genres = $genres->merge(
                        KidsShow::query()
                            ->where('kid_channel_id', $channel->id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->pluck('genre')
                            ->flatMap(fn ($value) => $this->parseCsvValues((string) $value))
                            ->all()
                    );
                }
                continue;
            }

            if ((int) $log->content_type === 7) {
                $channel = RelChannel::query()->where('id', (int) $log->content_id)->where('status', 1)->whereNull('deleted_at')->first();
                if ($channel) {
                    $channels->push([
                        'id' => (int) $channel->id,
                        'label' => (string) $channel->title,
                    ]);
                    $genres = $genres->merge(
                        RelShow::query()
                            ->where('channel_id', $channel->id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->pluck('genre')
                            ->flatMap(fn ($value) => $this->parseCsvValues((string) $value))
                            ->all()
                    );
                }
                continue;
            }

            if ((int) $log->content_type === 8) {
                $category = SportsCategory::query()
                    ->where('id', (int) $log->content_id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                if ($category) {
                    $genres = $genres->merge($this->parseCsvValues((string) ($category->genre ?? '')));
                }
                continue;
            }

            if ((int) $log->content_type === 9) {
                $show = StageshowPak::query()
                    ->where('id', (int) $log->content_id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                if ($show) {
                    $genres = $genres->merge($this->parseCsvValues((string) ($show->genres ?? '')));
                }
            }
        }

        return [
            'genres' => $genres
                ->filter()
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique(fn ($value) => mb_strtolower($value))
                ->sortBy(fn ($value) => mb_strtolower($value))
                ->values()
                ->all(),
            'channels' => $channels
                ->filter(fn ($channel) => !empty($channel['id']))
                ->unique('id')
                ->values()
                ->all(),
        ];
    }

    protected function buildFrontendBrowserItems(int $networkId, ?string $dataFor, string $genre, int $channelId): array
    {
        $logs = DB::table('content_network_log')
            ->where('network_id', $networkId)
            ->when(!empty($this->frontendContentTypesForDataFor($dataFor)), function ($query) use ($dataFor) {
                $query->whereIn('content_type', $this->frontendContentTypesForDataFor($dataFor));
            })
            ->get();

        $items = [];

        foreach ($logs as $log) {
            if ((int) $log->content_type === 1) {
                $item = $this->loadFrontendMovieCard((int) $log->content_id, $dataFor, $genre);
                if ($item) {
                    $items[] = $item;
                }
                continue;
            }

            if ((int) $log->content_type === 2) {
                $item = $this->loadFrontendWebSeriesCard((int) $log->content_id, $genre);
                if ($item) {
                    $items[] = $item;
                }
                continue;
            }

            if ((int) $log->content_type === 4) {
                $items = array_merge($items, $this->loadFrontendTvShowCards((int) $log->content_id, $genre, $channelId));
                continue;
            }

            if ((int) $log->content_type === 5) {
                $items = array_merge($items, $this->loadFrontendTvShowPakCards((int) $log->content_id, $genre, $channelId));
                continue;
            }

            if ((int) $log->content_type === 6) {
                $items = array_merge($items, $this->loadFrontendKidsCards((int) $log->content_id, $genre, $channelId));
                continue;
            }

            if ((int) $log->content_type === 7) {
                $items = array_merge($items, $this->loadFrontendReligiousCards((int) $log->content_id, $genre, $channelId));
                continue;
            }

            if ((int) $log->content_type === 8) {
                $item = $this->loadFrontendSportsCard((int) $log->content_id, $genre);
                if ($item) {
                    $items[] = $item;
                }
                continue;
            }

            if ((int) $log->content_type === 9) {
                $item = $this->loadFrontendStageShowCard((int) $log->content_id, $genre);
                if ($item) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    protected function getFrontendSlidesForNetwork(int $networkId, ?string $dataFor): array
    {
        return ContentSlider::query()
            ->where('content_network_id', $networkId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($dataFor && $dataFor !== 'content', function ($query) use ($dataFor) {
                $sliderFor = $this->frontendSliderKeyForDataFor($dataFor);
                if ($sliderFor) {
                    $query->where('slider_for', $sliderFor);
                }
            })
            ->orderByDesc('id')
            ->get()
            ->map(fn ($slider) => [
                'id' => (int) $slider->id,
                'title' => (string) ($slider->title ?: 'Featured'),
                'subtitle' => 'Featured selection for the current category.',
                'image' => $this->normalizeAssetUrl($slider->banner ?? ''),
            ])
            ->values()
            ->all();
    }

    protected function resolvePortalUser(Request $request, bool $requireToken): array
    {
        $mac = $this->extractMac($request);
        if (!$mac) {
            throw new \RuntimeException('MAG device MAC address was not provided.');
        }

        $user = ClientUser::query()
            ->whereRaw('UPPER(TRIM(box_mac_address)) = ?', [$mac])
            ->where(function ($query) {
                $query->whereNull('deleted_at')
                    ->orWhere('deleted_at', '')
                    ->orWhere('deleted_at', 0);
            })
            ->first();

        if (!$user) {
            throw new \RuntimeException('This MAG device is not mapped to any customer account.');
        }

        if ((int) $user->status !== 1) {
            throw new \RuntimeException('The mapped customer account is inactive.');
        }

        $plans = UserPlanDetails::query()
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->whereDate('plan_end_date', '>=', date('Y-m-d'))
            ->orderByDesc('id')
            ->get();

        if ($plans->isEmpty()) {
            throw new \RuntimeException('No active subscription is available for this MAG device.');
        }

        if ($requireToken) {
            $token = $this->extractPortalToken($request);
            if (!$token) {
                throw new \RuntimeException('MAG portal token is missing. Run handshake first.');
            }

            $session = Cache::get($this->sessionCacheKey($token));
            if (!$session || (int) ($session['user_id'] ?? 0) !== (int) $user->id || ($session['mac'] ?? '') !== $mac) {
                throw new \RuntimeException('MAG portal token is invalid or expired.');
            }
        }

        return [$user, $mac, $plans];
    }

    protected function getLiveChannelsForPlans(Collection $plans, ?Request $request = null): Collection
    {
        $channels = collect();

        foreach ($plans as $plan) {
            $owner = User::find($plan->plan_purchased_by);
            if (!$owner) {
                continue;
            }

            if ((int) $owner->role === 2) {
                $superAdminPlanIds = AdminSuperAdminPlan::where('admin_plan_id', $plan->plan_id)
                    ->where('status', 1)
                    ->pluck('super_admin_plan_id');

                if ($superAdminPlanIds->isNotEmpty()) {
                    $channels = $channels->merge(
                        DB::table('package_channels')
                            ->leftJoin('channels', 'channels.id', '=', 'package_channels.channel_id')
                            ->select(
                                'channels.id',
                                'channels.channel_number',
                                'channels.channel_name',
                                'channels.channel_logo',
                                'channels.channel_bg',
                                'channels.channel_language',
                                'channels.channel_index',
                                'channels.position_locked',
                                'channels.status',
                                'channels.channel_description',
                                'channels.view_count',
                                'channels.created_at',
                                'channels.channel_link',
                                'channels.stream_type',
                                'channels.genres'
                            )
                            ->whereIn('package_channels.plan_id', $superAdminPlanIds)
                            ->whereNull('channels.deleted_at')
                            ->where('channels.status', 1)
                            ->get()
                    );
                }

                continue;
            }

            if ((int) $owner->role === 3) {
                $superAdminPlanIds = ResellerAdminPlan::query()
                    ->leftJoin('admin_super_admin_plans', 'admin_super_admin_plans.admin_plan_id', '=', 'reseller_admin_plans.admin_plan_id')
                    ->where('reseller_admin_plans.reseller_plan_id', $plan->plan_id)
                    ->pluck('admin_super_admin_plans.super_admin_plan_id')
                    ->filter();

                if ($superAdminPlanIds->isNotEmpty()) {
                    $channels = $channels->merge(
                        DB::table('package_channels')
                            ->leftJoin('channels', 'channels.id', '=', 'package_channels.channel_id')
                            ->select(
                                'channels.id',
                                'channels.channel_number',
                                'channels.channel_name',
                                'channels.channel_logo',
                                'channels.channel_bg',
                                'channels.channel_language',
                                'channels.channel_index',
                                'channels.position_locked',
                                'channels.status',
                                'channels.channel_description',
                                'channels.view_count',
                                'channels.created_at',
                                'channels.channel_link',
                                'channels.stream_type',
                                'channels.genres'
                            )
                            ->whereIn('package_channels.plan_id', $superAdminPlanIds)
                            ->whereNull('channels.deleted_at')
                            ->where('channels.status', 1)
                            ->get()
                    );
                }

                continue;
            }

            if ((int) $owner->role === 6) {
                $channels = $channels->merge(
                    DB::table('netadmin_channels as nc')
                        ->leftJoin('channels as c', 'c.id', '=', 'nc.channel_id')
                        ->select(
                            'c.id',
                            'c.channel_number',
                            'c.channel_name',
                            'c.channel_logo',
                            'c.channel_bg',
                            'c.channel_language',
                            'c.channel_index',
                            'c.position_locked',
                            'c.status',
                            'c.channel_description',
                            'c.view_count',
                            'c.created_at',
                            'nc.link as channel_link',
                            'c.stream_type',
                            'c.genres'
                        )
                        ->where('nc.plan_id', $plan->plan_id)
                        ->where('nc.status', 1)
                        ->where('nc.link', '<>', '')
                        ->whereNull('c.deleted_at')
                        ->where('c.status', 1)
                        ->get()
                );
            }
        }

        $channels = $channels
            ->filter(fn ($channel) => !empty($channel->id) && !empty($channel->channel_link))
            ->keyBy('id')
            ->sortBy(fn ($channel) => (int) ($channel->channel_number ?? 0))
            ->values();

        $domainRow = $request ? $this->resolveDomainContent($request) : null;
        if ($domainRow && !empty($domainRow->live_channels)) {
            $allowedIds = collect(explode(',', (string) $domainRow->live_channels))
                ->map(fn ($id) => (int) trim($id))
                ->filter()
                ->values();

            if ($allowedIds->isNotEmpty()) {
                $channels = $channels
                    ->filter(fn ($channel) => $allowedIds->contains((int) $channel->id))
                    ->values();
            }
        }

        return $channels;
    }

    protected function getVodMoviesForPlans(Collection $plans): Collection
    {
        if ($plans->isEmpty()) {
            return collect();
        }

        return Movie::query()
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'name',
                'banner',
                'description',
                'movie_url',
                'backup_url',
                'source_type',
                'genres',
                'created_at',
            ]);
    }

    protected function buildLiveGenreMap(Collection $channels): Collection
    {
        $genres = collect();

        foreach ($channels as $channel) {
            $genres = $genres->merge($this->parseCsvValues($channel->genres ?? ''));
        }

        $genreRows = $genres
            ->filter()
            ->map(fn ($genre) => trim((string) $genre))
            ->filter()
            ->unique(fn ($genre) => mb_strtolower($genre))
            ->sortBy(fn ($genre) => mb_strtolower($genre))
            ->values()
            ->map(fn ($genre, $index) => [
                'id' => $index + 1,
                'title' => $genre,
                'alias' => Str::slug($genre),
            ]);

        return collect([[
            'id' => 0,
            'title' => 'All Channels',
            'alias' => 'all-channels',
        ]])->merge($genreRows)->values();
    }

    protected function buildVodGenreMap(Collection $movies): Collection
    {
        $genreIds = $movies
            ->flatMap(fn ($movie) => $this->parseNumericCsvValues($movie->genres ?? ''))
            ->unique()
            ->values();

        $genres = Genre::query()
            ->whereIn('id', $genreIds)
            ->where('status', 1)
            ->orderBy('title')
            ->get(['id', 'title']);

        return collect([[
            'id' => 0,
            'title' => 'All Movies',
            'alias' => 'all-movies',
        ]])->merge(
            $genres->map(fn ($genre) => [
                'id' => (int) $genre->id,
                'title' => (string) $genre->title,
                'alias' => Str::slug($genre->title),
            ])
        )->values();
    }

    protected function resolveVodPlaybackUrl($movie, int $linkId): ?string
    {
        $linkQuery = MovieLink::query()
            ->where('movie_id', $movie->id)
            ->where('status', 1)
            ->whereNull('deleted_at');

        if ($linkId > 0) {
            $linkQuery->where('id', $linkId);
        }

        $movieLink = $linkQuery
            ->orderByRaw('COALESCE(link_order, `order`, id) asc')
            ->first();

        if ($movieLink && !empty($movieLink->source_url)) {
            return $movieLink->source_url;
        }

        if (!empty($movie->movie_url)) {
            return $movie->movie_url;
        }

        if (!empty($movie->backup_url)) {
            return $movie->backup_url;
        }

        return null;
    }

    protected function frontendSections(): array
    {
        return [
            [
                'slug' => 'search',
                'label' => 'Search',
                'title' => 'Search',
                'copy' => 'Universal search across live TV and all supported on-demand categories.',
                'accent' => '#ffffff',
                'screen' => 'search',
                'data_for' => null,
                'rail_title' => 'Search',
            ],
            [
                'slug' => 'live',
                'label' => 'Live TV',
                'title' => 'Live Channels',
                'copy' => 'Direct playback for active plan channels with remote-friendly browsing.',
                'accent' => '#5f89ff',
                'screen' => 'browser',
                'data_for' => null,
                'rail_title' => 'Live Channels',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'contents',
                'label' => 'Contents',
                'title' => 'Content Networks',
                'copy' => 'Mixed movie, web-series and TV-show libraries grouped by content networks.',
                'accent' => '#18c7b5',
                'screen' => 'browser',
                'data_for' => 'content',
                'rail_title' => 'Content Networks',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'recently-added',
                'label' => 'Recently Added',
                'title' => 'Recently Added',
                'copy' => 'Recent movie networks with direct-play titles for quick access.',
                'accent' => '#ffb045',
                'screen' => 'browser',
                'data_for' => 'movies',
                'rail_title' => 'Recent Movie Networks',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'web-series',
                'label' => 'Web Series',
                'title' => 'Web Series',
                'copy' => 'Episode-driven binge sections with season wise navigation.',
                'accent' => '#7b8cff',
                'screen' => 'browser',
                'data_for' => 'webseries',
                'rail_title' => 'Web Series Networks',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'tv-shows',
                'label' => 'TV Shows',
                'title' => 'TV Shows',
                'copy' => 'Channel-based TV shows with seasons and episode drilldown.',
                'accent' => '#f55f7e',
                'screen' => 'browser',
                'data_for' => 'tvshows',
                'rail_title' => 'TV Show Networks',
                'browser_primary_kind' => 'channel',
            ],
            [
                'slug' => 'tv-shows-pak',
                'label' => 'TV Shows Pak',
                'title' => 'TV Shows Pak',
                'copy' => 'Pakistani show libraries prepared for larger catalog expansion.',
                'accent' => '#6fa96f',
                'screen' => 'browser',
                'data_for' => 'tvshowspak',
                'rail_title' => 'TV Shows Pak Networks',
                'browser_primary_kind' => 'channel',
            ],
            [
                'slug' => 'kids',
                'label' => 'Kids',
                'title' => 'Kids',
                'copy' => 'Kids channels with show-based browsing and episode playback.',
                'accent' => '#f2c24b',
                'screen' => 'browser',
                'data_for' => 'kidchannels',
                'rail_title' => 'Kids Networks',
                'browser_primary_kind' => 'channel',
            ],
            [
                'slug' => 'religious',
                'label' => 'Religious',
                'title' => 'Religious',
                'copy' => 'Religious catalog section ready for episode-wise browsing.',
                'accent' => '#3ec8d4',
                'screen' => 'browser',
                'data_for' => 'religiouschannels',
                'rail_title' => 'Religious Networks',
                'browser_primary_kind' => 'channel',
            ],
            [
                'slug' => 'stage-shows',
                'label' => 'Stage Shows',
                'title' => 'Stage Shows',
                'copy' => 'Direct-play stage shows grouped by networks and genres.',
                'accent' => '#f08d53',
                'screen' => 'browser',
                'data_for' => 'stageshowspak',
                'rail_title' => 'Stage Show Networks',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'sports',
                'label' => 'Sports',
                'title' => 'Sports',
                'copy' => 'Sports libraries and tournament-led shelves for future expansion.',
                'accent' => '#ff6b57',
                'screen' => 'browser',
                'data_for' => 'sports',
                'rail_title' => 'Sports Networks',
                'browser_primary_kind' => 'genre',
            ],
            [
                'slug' => 'settings',
                'label' => 'Settings',
                'title' => 'Settings',
                'copy' => 'Account, subscription, device, and remote details for this TV box.',
                'accent' => '#f2f1ec',
                'screen' => 'legacy',
                'data_for' => null,
                'rail_title' => 'Settings',
            ],
        ];
    }

    protected function findFrontendSection(string $slug): ?array
    {
        foreach ($this->frontendSections() as $section) {
            if ($section['slug'] === $slug) {
                return $section;
            }
        }

        return null;
    }

    protected function getFrontendNetworksForSection(?string $dataFor): Collection
    {
        if ($dataFor === 'content') {
            return ContentNetwork::query()
                ->where('status', 1)
                ->where('is_content', 1)
                ->whereNull('deleted_at')
                ->orderBy('networks_order')
                ->get();
        }

        $networkIds = collect();

        if ($dataFor === 'movies') {
            $recentMovieIds = Movie::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('is_recent', 1)
                ->pluck('id');

            $networkIds = DB::table('movie_content_network')
                ->whereIn('movie_id', $recentMovieIds)
                ->pluck('network_id');
        } elseif ($dataFor === 'webseries') {
            $networkIds = DB::table('web_series_content_network')->pluck('network_id');
        } elseif ($dataFor === 'tvshows') {
            $networkIds = DB::table('tv_show_content_network')->pluck('network_id');
        } elseif ($dataFor === 'tvshowspak') {
            $networkIds = DB::table('tv_show_pak_content_network')->pluck('network_id');
        } elseif ($dataFor === 'kidchannels') {
            $networkIds = DB::table('kids_channel_content_network')->pluck('network_id');
        } elseif ($dataFor === 'religiouschannels') {
            $networkIds = DB::table('rel_channel_content_network')->pluck('network_id');
        } elseif ($dataFor === 'stageshowspak') {
            $networkIds = DB::table('state_show_pak_content_network')->pluck('network_id');
        } elseif ($dataFor === 'sports') {
            $networkIds = DB::table('sports_category_content_network')->pluck('network_id');
        } else {
            $networkIds = collect()
                ->merge(DB::table('movie_content_network')->pluck('network_id'))
                ->merge(DB::table('web_series_content_network')->pluck('network_id'))
                ->merge(DB::table('tv_show_content_network')->pluck('network_id'))
                ->merge(DB::table('tv_show_pak_content_network')->pluck('network_id'))
                ->merge(DB::table('kids_channel_content_network')->pluck('network_id'))
                ->merge(DB::table('rel_channel_content_network')->pluck('network_id'))
                ->merge(DB::table('sports_category_content_network')->pluck('network_id'))
                ->merge(DB::table('state_show_pak_content_network')->pluck('network_id'));
        }

        $ids = $networkIds->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return ContentNetwork::query()
            ->whereIn('id', $ids)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('networks_order')
            ->get();
    }

    protected function buildFrontendLiveRows(Collection $channels): array
    {
        if ($channels->isEmpty()) {
            return [];
        }

        $mapped = $channels
            ->map(fn ($channel) => $this->mapFrontendLiveChannel($channel))
            ->values();

        $rows = [[
            'id' => 'all',
            'title' => 'Live Channels',
            'accent' => '#5f89ff',
            'items' => $mapped->take(30)->values()->all(),
        ]];

        foreach ($this->buildLiveGenreMap($channels)->slice(1, 5) as $genre) {
            $genreTitle = (string) ($genre['title'] ?? '');
            $genreItems = $channels
                ->filter(function ($channel) use ($genreTitle) {
                    return collect($this->parseCsvValues($channel->genres ?? ''))
                        ->contains(fn ($value) => strcasecmp($value, $genreTitle) === 0);
                })
                ->map(fn ($channel) => $this->mapFrontendLiveChannel($channel))
                ->take(18)
                ->values()
                ->all();

            if (!empty($genreItems)) {
                $rows[] = [
                    'id' => 'genre-' . Str::slug($genreTitle),
                    'title' => $genreTitle,
                    'accent' => '#7ea6ff',
                    'items' => $genreItems,
                ];
            }
        }

        return $rows;
    }

    protected function buildLiveHero($channel, array $section): array
    {
        if (!$channel) {
            return [
                'title' => $section['title'],
                'subtitle' => $section['copy'],
                'backdrop' => '',
                'badge' => 'Live',
                'meta' => [],
            ];
        }

        return [
            'title' => (string) $channel->channel_name,
            'subtitle' => (string) ($channel->channel_description ?: $section['copy']),
            'backdrop' => $this->normalizeAssetUrl($channel->channel_bg ?: $channel->channel_logo ?: ''),
            'badge' => 'Live Channel',
            'meta' => array_values(array_filter([
                $this->parseCsvValues($channel->genres ?? '')[0] ?? null,
                !empty($channel->channel_language) ? 'Language #' . $channel->channel_language : null,
                !empty($channel->stream_type) ? strtoupper((string) $channel->stream_type) : 'Stream Ready',
            ])),
        ];
    }

    protected function buildSectionHero(array $section, array $items): array
    {
        $first = $items[0] ?? null;

        return [
            'title' => $first['title'] ?? $section['title'],
            'subtitle' => $first['description'] ?? $section['copy'],
            'backdrop' => $first['backdrop'] ?? ($first['image'] ?? ''),
            'badge' => $section['label'],
            'meta' => $first['meta'] ?? [],
        ];
    }

    protected function mapFrontendNetworkCard($network, int $index): array
    {
        $name = (string) ($network->name ?? 'Network');

        return [
            'id' => (int) $network->id,
            'type' => 'network',
            'title' => $name,
            'subtitle' => 'Content Network',
            'description' => $name . ' library prepared for remote browsing.',
            'image' => $this->normalizeAssetUrl($network->logo ?? ''),
            'backdrop' => $this->normalizeAssetUrl($network->logo ?? ''),
            'badge' => 'Network',
            'meta' => array_values(array_filter([
                'Order ' . (int) ($network->networks_order ?: ($index + 1)),
                (int) ($network->is_content ?? 0) === 1 ? 'Mixed Library' : null,
            ])),
            'action' => 'open-network',
        ];
    }

    protected function buildFrontendNetworkPayload($network, ?string $dataFor): array
    {
        $contentTypes = $this->frontendContentTypesForDataFor($dataFor);
        $logs = DB::table('content_network_log')
            ->where('network_id', $network->id)
            ->when(!empty($contentTypes), fn ($query) => $query->whereIn('content_type', $contentTypes))
            ->get();

        $rows = [
            'movies' => [],
            'webseries' => [],
            'tvshows' => [],
            'tvshowspak' => [],
            'religious' => [],
            'sports' => [],
        ];

        foreach ($logs as $log) {
            if ((int) $log->content_type === 1) {
                $item = $this->loadFrontendMovieCard((int) $log->content_id, $dataFor);
                if ($item) {
                    $rows['movies'][] = $item;
                }
                continue;
            }

            if ((int) $log->content_type === 2) {
                $item = $this->loadFrontendWebSeriesCard((int) $log->content_id);
                if ($item) {
                    $rows['webseries'][] = $item;
                }
                continue;
            }

            if ((int) $log->content_type === 4) {
                $rows['tvshows'] = array_merge(
                    $rows['tvshows'],
                    $this->loadFrontendTvShowCards((int) $log->content_id)
                );
                continue;
            }

            if ((int) $log->content_type === 5) {
                $rows['tvshowspak'] = array_merge(
                    $rows['tvshowspak'],
                    $this->loadFrontendTvShowPakCards((int) $log->content_id)
                );
                continue;
            }

            if ((int) $log->content_type === 7) {
                $rows['religious'] = array_merge(
                    $rows['religious'],
                    $this->loadFrontendReligiousCards((int) $log->content_id)
                );
                continue;
            }

            if ((int) $log->content_type === 8) {
                $item = $this->loadFrontendSportsCard((int) $log->content_id);
                if ($item) {
                    $rows['sports'][] = $item;
                }
            }
        }

        $rowConfig = [
            'movies' => ['title' => 'Movies', 'accent' => '#ffbf5b'],
            'webseries' => ['title' => 'Web Series', 'accent' => '#8c83ff'],
            'tvshows' => ['title' => 'TV Shows', 'accent' => '#22c5b1'],
            'tvshowspak' => ['title' => 'TV Shows Pak', 'accent' => '#6da36d'],
            'religious' => ['title' => 'Religious', 'accent' => '#48c2d6'],
            'sports' => ['title' => 'Sports', 'accent' => '#ff7258'],
        ];

        $finalRows = [];
        foreach ($rows as $rowKey => $items) {
            $items = collect($items)
                ->unique(fn ($item) => $item['type'] . ':' . $item['id'])
                ->sortByDesc('sort_at')
                ->values()
                ->all();

            if (!empty($items)) {
                $finalRows[] = [
                    'id' => $rowKey,
                    'title' => $rowConfig[$rowKey]['title'],
                    'accent' => $rowConfig[$rowKey]['accent'],
                    'items' => $items,
                ];
            }
        }

        $sliders = ContentSlider::query()
            ->where('content_network_id', $network->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($dataFor && $dataFor !== 'content', function ($query) use ($dataFor) {
                $sliderFor = $this->frontendSliderKeyForDataFor($dataFor);
                if ($sliderFor) {
                    $query->where('slider_for', $sliderFor);
                }
            })
            ->orderByDesc('id')
            ->get();

        $slides = $sliders->map(function ($slider) {
            return [
                'title' => (string) ($slider->title ?: 'Featured'),
                'subtitle' => 'Curated highlight for your TV app.',
                'image' => $this->normalizeAssetUrl($slider->banner ?? ''),
                'url' => $slider->url ?? null,
            ];
        })->values()->all();

        $heroSource = $slides[0] ?? null;
        $firstItem = $finalRows[0]['items'][0] ?? null;
        $hero = [
            'title' => $heroSource['title'] ?? ($firstItem['title'] ?? (string) $network->name),
            'subtitle' => $heroSource['subtitle'] ?? ($firstItem['description'] ?? 'Handpicked titles ready for big-screen viewing.'),
            'backdrop' => $heroSource['image'] ?? ($firstItem['backdrop'] ?? ($firstItem['image'] ?? $this->normalizeAssetUrl($network->logo ?? ''))),
            'badge' => (string) $network->name,
            'meta' => [
                count($finalRows) . ' shelves',
                collect($finalRows)->sum(fn ($row) => count($row['items'])) . ' titles',
            ],
        ];

        $filters = collect($finalRows)->map(fn ($row) => [
            'id' => $row['id'],
            'label' => $row['title'],
        ])->values()->all();

        return [
            'hero' => $hero,
            'slides' => $slides,
            'filters' => $filters,
            'rows' => $finalRows,
        ];
    }

    protected function frontendContentTypesForDataFor(?string $dataFor): array
    {
        return match ($dataFor) {
            'movies' => [1],
            'webseries' => [2],
            'tvshows' => [4],
            'tvshowspak' => [5],
            'kidchannels' => [6],
            'religiouschannels' => [7],
            'sports' => [8],
            'stageshowspak' => [9],
            default => [1, 2, 4, 5, 6, 7, 8, 9],
        };
    }

    protected function frontendSliderKeyForDataFor(?string $dataFor): ?string
    {
        return match ($dataFor) {
            'movies' => 'movies',
            'webseries' => 'webseries',
            'tvshows' => 'tvshows',
            'tvshowspak' => 'tvshowspak',
            'kidchannels' => 'kidchannels',
            'religiouschannels' => 'religiouschannels',
            'sports' => 'sports',
            'stageshowspak' => 'stageshowspak',
            default => null,
        };
    }

    protected function mapFrontendLiveChannel($channel): array
    {
        $genres = $this->parseCsvValues($channel->genres ?? '');

        return [
            'id' => (int) $channel->id,
            'type' => 'live',
            'content_type' => 3,
            'title' => (string) $channel->channel_name,
            'subtitle' => $genres[0] ?? 'Live Channel',
            'description' => (string) ($channel->channel_description ?? 'Live stream ready for direct playback.'),
            'image' => $this->normalizeAssetUrl($channel->channel_logo ?? ''),
            'backdrop' => $this->normalizeAssetUrl($channel->channel_bg ?: $channel->channel_logo ?: ''),
            'badge' => 'Live',
            'meta' => array_values(array_filter([
                !empty($channel->channel_number) ? 'CH ' . $channel->channel_number : null,
                $genres[0] ?? null,
            ])),
            'action' => 'play',
            'play_type' => 'live',
            'sort_at' => !empty($channel->created_at) ? strtotime((string) $channel->created_at) : 0,
        ];
    }

    protected function loadFrontendMovieCard(int $movieId, ?string $dataFor = null, ?string $genre = null): ?array
    {
        $movie = Movie::query()
            ->where('id', $movieId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->with('networks')
            ->first();

        if (!$movie) {
            return null;
        }

        if ($dataFor === 'movies' && (int) ($movie->is_recent ?? 0) !== 1) {
            return null;
        }

        $genres = $this->parseCsvValues((string) ($movie->genres ?? ''));
        if (!$this->matchesFrontendGenre($genres, $genre)) {
            return null;
        }
        $image = $this->normalizeAssetUrl($movie->poster ?? $movie->banner ?? '');
        $backdrop = $this->normalizeAssetUrl($movie->banner ?? $movie->poster ?? '');

        return [
            'id' => (int) $movie->id,
            'type' => 'movie',
            'content_type' => 1,
            'title' => (string) $movie->name,
            'subtitle' => $genres[0] ?? 'Movie',
            'description' => (string) ($movie->description ?? 'Movie playback is ready for this TV device.'),
            'image' => $image,
            'backdrop' => $backdrop,
            'badge' => 'Movie',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                !empty($movie->runtime) ? $movie->runtime . ' min' : null,
                !empty($movie->release_date) ? date('Y', strtotime((string) $movie->release_date)) : null,
            ])),
            'action' => 'play',
            'play_type' => 'movie',
            'detail_type' => 'movie',
            'sort_at' => !empty($movie->created_at) ? strtotime((string) $movie->created_at) : 0,
        ];
    }

    protected function loadFrontendWebSeriesCard(int $seriesId, ?string $genre = null): ?array
    {
        $series = WebSeries::query()
            ->where('id', $seriesId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$series) {
            return null;
        }

        $seasonCount = WebSeriesSeason::query()
            ->where('web_series_id', $series->id)
            ->where('status', 1)
            ->count();

        $genres = $this->parseCsvValues((string) ($series->genres ?? ''));
        if (!$this->matchesFrontendGenre($genres, $genre)) {
            return null;
        }

        return [
            'id' => (int) $series->id,
            'type' => 'webseries',
            'content_type' => 2,
            'title' => (string) $series->name,
            'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'Series',
            'description' => (string) ($series->description ?? 'Open to browse seasons and episodes.'),
            'image' => $this->normalizeAssetUrl($series->poster ?? $series->banner ?? ''),
            'backdrop' => $this->normalizeAssetUrl($series->banner ?? $series->poster ?? ''),
            'badge' => 'Series',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
                !empty($series->release_date) ? date('Y', strtotime((string) $series->release_date)) : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'webseries',
            'sort_at' => !empty($series->created_at) ? strtotime((string) $series->created_at) : 0,
        ];
    }

    protected function loadFrontendTvShowCards(int $channelId, ?string $genre = null, int $selectedChannelId = 0): array
    {
        if ($selectedChannelId > 0 && $selectedChannelId !== $channelId) {
            return [];
        }

        return TvShow::query()
            ->where('tv_channel_id', $channelId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($genre !== null && $genre !== '', fn ($query) => $query->where('genre', 'LIKE', '%' . $genre . '%'))
            ->get()
            ->map(function ($show) {
                $genres = $this->parseCsvValues((string) ($show->genre ?? ''));
                $seasonCount = TvShowSeason::query()
                    ->where('show_id', $show->id)
                    ->where('status', 1)
                    ->count();

                return [
                    'id' => (int) $show->id,
                    'type' => 'tvshow',
                    'content_type' => 4,
                    'title' => (string) $show->name,
                    'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'TV Show',
                    'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
                    'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'badge' => 'TV Show',
                    'meta' => array_values(array_filter([
                        $genres[0] ?? null,
                        $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
                    ])),
                    'action' => 'detail',
                    'detail_type' => 'tvshow',
                    'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
                ];
            })
            ->values()
            ->all();
    }

    protected function loadFrontendTvShowPakCards(int $channelId, ?string $genre = null, int $selectedChannelId = 0): array
    {
        if ($selectedChannelId > 0 && $selectedChannelId !== $channelId) {
            return [];
        }

        return TvShowPak::query()
            ->where('tv_channel_id', $channelId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($genre !== null && $genre !== '', fn ($query) => $query->where('genre', 'LIKE', '%' . $genre . '%'))
            ->get()
            ->map(function ($show) {
                $genres = $this->parseCsvValues((string) ($show->genre ?? ''));
                $seasonCount = TvShowSeasonPak::query()
                    ->where('show_id', $show->id)
                    ->where('status', 1)
                    ->count();

                return [
                    'id' => (int) $show->id,
                    'type' => 'tvshowpak',
                    'content_type' => 5,
                    'title' => (string) $show->name,
                    'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'TV Show Pak',
                    'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
                    'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'badge' => 'TV Show Pak',
                    'meta' => array_values(array_filter([
                        $genres[0] ?? null,
                        $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
                    ])),
                    'action' => 'detail',
                    'detail_type' => 'tvshowpak',
                    'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
                ];
            })
            ->values()
            ->all();
    }

    protected function loadFrontendKidsCards(int $channelId, ?string $genre = null, int $selectedChannelId = 0): array
    {
        if ($selectedChannelId > 0 && $selectedChannelId !== $channelId) {
            return [];
        }

        return KidsShow::query()
            ->where('kid_channel_id', $channelId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($genre !== null && $genre !== '', fn ($query) => $query->where('genre', 'LIKE', '%' . $genre . '%'))
            ->get()
            ->map(function ($show) {
                $genres = $this->parseCsvValues((string) ($show->genre ?? ''));
                $seasonCount = KidShowsSeason::query()
                    ->where('show_id', $show->id)
                    ->where('status', 1)
                    ->count();

                return [
                    'id' => (int) $show->id,
                    'type' => 'kids',
                    'content_type' => 6,
                    'title' => (string) $show->name,
                    'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'Kids Show',
                    'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
                    'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                    'badge' => 'Kids',
                    'meta' => array_values(array_filter([
                        $genres[0] ?? null,
                        $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
                    ])),
                    'action' => 'detail',
                    'detail_type' => 'kids',
                    'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
                ];
            })
            ->values()
            ->all();
    }

    protected function loadFrontendReligiousCards(int $channelId, ?string $genre = null, int $selectedChannelId = 0): array
    {
        if ($selectedChannelId > 0 && $selectedChannelId !== $channelId) {
            return [];
        }

        return RelShow::query()
            ->where('channel_id', $channelId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when($genre !== null && $genre !== '', fn ($query) => $query->where('genre', 'LIKE', '%' . $genre . '%'))
            ->get()
            ->map(function ($show) {
                $genres = $this->parseCsvValues((string) ($show->genre ?? ''));
                $episodeCount = RelshowsEpisode::query()
                    ->where('show_id', $show->id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->count();

                return [
                    'id' => (int) $show->id,
                    'type' => 'religious',
                    'content_type' => 7,
                    'title' => (string) $show->name,
                    'subtitle' => $episodeCount > 0 ? $episodeCount . ' episodes' : 'Episode Library',
                    'description' => (string) ($show->description ?? 'Religious library ready for episode browsing.'),
                    'image' => $this->normalizeAssetUrl($show->thumbnail ?? $show->banner ?? ''),
                    'backdrop' => $this->normalizeAssetUrl($show->banner ?? $show->thumbnail ?? ''),
                    'badge' => 'Religious',
                    'meta' => array_values(array_filter([
                        $genres[0] ?? null,
                        $episodeCount > 0 ? $episodeCount . ' Episodes' : null,
                    ])),
                    'action' => 'detail',
                    'detail_type' => 'religious',
                    'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
                ];
            })
            ->values()
            ->all();
    }

    protected function loadFrontendSportsCard(int $categoryId, ?string $genre = null): ?array
    {
        $category = SportsCategory::query()
            ->where('id', $categoryId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$category) {
            return null;
        }

        if (!$this->matchesFrontendGenre($this->parseCsvValues((string) ($category->genre ?? '')), $genre)) {
            return null;
        }

        return [
            'id' => (int) $category->id,
            'type' => 'sports',
            'content_type' => 8,
            'title' => (string) ($category->name ?? 'Sports'),
            'subtitle' => 'Sports Category',
            'description' => (string) ($category->description ?? 'Sports browsing support can be expanded from this section.'),
            'image' => $this->normalizeAssetUrl($category->banner ?? $category->thumbnail ?? ''),
            'backdrop' => $this->normalizeAssetUrl($category->banner ?? $category->thumbnail ?? ''),
            'badge' => 'Sports',
            'meta' => [],
            'action' => 'info',
            'detail_type' => 'sports',
            'sort_at' => !empty($category->created_at) ? strtotime((string) $category->created_at) : 0,
        ];
    }

    protected function loadFrontendStageShowCard(int $id, ?string $genre = null): ?array
    {
        $show = StageshowPak::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$show) {
            return null;
        }

        $genres = $this->parseCsvValues((string) ($show->genres ?? ''));
        if (!$this->matchesFrontendGenre($genres, $genre)) {
            return null;
        }

        return [
            'id' => (int) $show->id,
            'type' => 'stageshow',
            'content_type' => 9,
            'title' => (string) $show->name,
            'subtitle' => $genres[0] ?? 'Stage Show',
            'description' => (string) ($show->description ?? 'Stage show playback is ready for this TV device.'),
            'image' => $this->normalizeAssetUrl($show->banner ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->banner ?? ''),
            'badge' => 'Stage Show',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                !empty($show->release_date) ? date('Y', strtotime((string) $show->release_date)) : null,
            ])),
            'action' => 'play',
            'play_type' => 'stageshow',
            'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
        ];
    }

    protected function mapFrontendWebSeriesResult($series): array
    {
        $seasonCount = WebSeriesSeason::query()
            ->where('web_series_id', $series->id)
            ->where('status', 1)
            ->count();

        $genres = $this->parseCsvValues((string) ($series->genres ?? ''));

        return [
            'id' => (int) $series->id,
            'type' => 'webseries',
            'content_type' => 2,
            'title' => (string) $series->name,
            'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'Series',
            'description' => (string) ($series->description ?? 'Open to browse seasons and episodes.'),
            'image' => $this->normalizeAssetUrl($series->poster ?? $series->banner ?? ''),
            'backdrop' => $this->normalizeAssetUrl($series->banner ?? $series->poster ?? ''),
            'badge' => 'Series',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'webseries',
            'sort_at' => !empty($series->created_at) ? strtotime((string) $series->created_at) : 0,
        ];
    }

    protected function mapFrontendTvShowResult($show): array
    {
        $seasonCount = TvShowSeason::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->count();

        $genres = $this->parseCsvValues((string) ($show->genre ?? ''));

        return [
            'id' => (int) $show->id,
            'type' => 'tvshow',
            'content_type' => 4,
            'title' => (string) $show->name,
            'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'TV Show',
            'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
            'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'badge' => 'TV Show',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'tvshow',
            'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
        ];
    }

    protected function mapFrontendTvShowPakResult($show): array
    {
        $seasonCount = TvShowSeasonPak::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->count();

        $genres = $this->parseCsvValues((string) ($show->genre ?? ''));

        return [
            'id' => (int) $show->id,
            'type' => 'tvshowpak',
            'content_type' => 5,
            'title' => (string) $show->name,
            'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'TV Show Pak',
            'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
            'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'badge' => 'TV Show Pak',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'tvshowpak',
            'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
        ];
    }

    protected function mapFrontendKidsResult($show): array
    {
        $seasonCount = KidShowsSeason::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->count();

        $genres = $this->parseCsvValues((string) ($show->genre ?? ''));

        return [
            'id' => (int) $show->id,
            'type' => 'kids',
            'content_type' => 6,
            'title' => (string) $show->name,
            'subtitle' => $seasonCount > 0 ? $seasonCount . ' seasons' : 'Kids Show',
            'description' => (string) ($show->description ?? 'Browse seasons and play episodes on TV.'),
            'image' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->thumbnail ?? ''),
            'badge' => 'Kids',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $seasonCount > 0 ? $seasonCount . ' Seasons' : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'kids',
            'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
        ];
    }

    protected function mapFrontendReligiousResult($show): array
    {
        $episodeCount = RelshowsEpisode::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->count();

        $genres = $this->parseCsvValues((string) ($show->genre ?? ''));

        return [
            'id' => (int) $show->id,
            'type' => 'religious',
            'content_type' => 7,
            'title' => (string) ($show->name ?? $show->title ?? 'Religious Show'),
            'subtitle' => $episodeCount > 0 ? $episodeCount . ' episodes' : 'Episode Library',
            'description' => (string) ($show->description ?? 'Religious library ready for episode browsing.'),
            'image' => $this->normalizeAssetUrl($show->thumbnail ?? $show->banner ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->banner ?? $show->thumbnail ?? ''),
            'badge' => 'Religious',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                $episodeCount > 0 ? $episodeCount . ' Episodes' : null,
            ])),
            'action' => 'detail',
            'detail_type' => 'religious',
            'sort_at' => !empty($show->created_at) ? strtotime((string) $show->created_at) : 0,
        ];
    }

    protected function frontendMovieDetail(int $id): JsonResponse
    {
        $movie = Movie::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->with('networks')
            ->first();

        if (!$movie) {
            return response()->json([
                'status' => false,
                'message' => 'Movie not found.',
            ], 404);
        }

        $card = $this->loadFrontendMovieCard($id) ?: [];

        return response()->json([
            'status' => true,
            'content' => array_merge($card, [
                'networks' => $movie->networks->pluck('name')->values()->all(),
                'plot' => (string) ($movie->description ?? ''),
            ]),
        ]);
    }

    protected function frontendWebSeriesDetail(int $id): JsonResponse
    {
        $series = WebSeries::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->with('networks')
            ->first();

        if (!$series) {
            return response()->json([
                'status' => false,
                'message' => 'Web series not found.',
            ], 404);
        }

        $seasons = WebSeriesSeason::query()
            ->where('web_series_id', $series->id)
            ->where('status', 1)
            ->orderBy('season_order')
            ->get()
            ->map(function ($season) {
                $episodes = WebSeriesEpisode::query()
                    ->where('season_id', $season->id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('episoade_order')
                    ->get()
                    ->map(function ($episode) {
                        return [
                            'id' => (int) $episode->id,
                            'title' => (string) ($episode->Episoade_Name ?? ('Episode ' . $episode->episoade_order)),
                            'subtitle' => 'Episode ' . (int) ($episode->episoade_order ?: 0),
                            'description' => (string) ($episode->episoade_description ?? ''),
                            'image' => $this->normalizeAssetUrl($episode->episoade_image ?? ''),
                            'play_type' => 'webseries-episode',
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => (int) $season->id,
                    'title' => (string) ($season->Session_Name ?? ('Season ' . ($season->season_order ?: 1))),
                    'subtitle' => count($episodes) . ' episodes',
                    'image' => $this->normalizeAssetUrl($season->banner ?? ''),
                    'episodes' => $episodes,
                ];
            })
            ->values()
            ->all();

        $card = $this->loadFrontendWebSeriesCard($series->id) ?: [];

        return response()->json([
            'status' => true,
            'content' => array_merge($card, [
                'plot' => (string) ($series->description ?? ''),
                'networks' => $series->networks->pluck('name')->values()->all(),
                'seasons' => $seasons,
            ]),
        ]);
    }

    protected function frontendTvShowDetail(int $id): JsonResponse
    {
        $show = TvShow::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$show) {
            return response()->json([
                'status' => false,
                'message' => 'TV show not found.',
            ], 404);
        }

        $seasons = TvShowSeason::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('season_order')
            ->get()
            ->map(function ($season) {
                $episodes = TvShowEpisode::query()
                    ->where('season_id', $season->id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('episoade_order')
                    ->get()
                    ->map(function ($episode) {
                        return [
                            'id' => (int) $episode->id,
                            'title' => (string) ($episode->title ?? ('Episode ' . $episode->episode_number)),
                            'subtitle' => 'Episode ' . (int) ($episode->episode_number ?: $episode->episoade_order ?: 0),
                            'description' => (string) ($episode->description ?? ''),
                            'image' => $this->normalizeAssetUrl($episode->thumbnail ?? ''),
                            'play_type' => 'tvshow-episode',
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => (int) $season->id,
                    'title' => (string) ($season->title ?? ('Season ' . ($season->season_order ?: 1))),
                    'subtitle' => count($episodes) . ' episodes',
                    'image' => $this->normalizeAssetUrl($season->poster ?? ''),
                    'episodes' => $episodes,
                ];
            })
            ->values()
            ->all();

        $card = collect($this->loadFrontendTvShowCards((int) $show->tv_channel_id))
            ->firstWhere('id', (int) $show->id) ?: [];

        return response()->json([
            'status' => true,
            'content' => array_merge($card, [
                'plot' => (string) ($show->description ?? ''),
                'seasons' => $seasons,
            ]),
        ]);
    }

    protected function frontendReligiousDetail(int $id): JsonResponse
    {
        $show = RelShow::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$show) {
            return response()->json([
                'status' => false,
                'message' => 'Religious show not found.',
            ], 404);
        }

        $episodes = RelshowsEpisode::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get()
            ->map(function ($episode) {
                return [
                    'id' => (int) $episode->id,
                    'title' => (string) ($episode->title ?? $episode->name ?? ('Episode ' . $episode->id)),
                    'subtitle' => 'Episode',
                    'description' => (string) ($episode->description ?? ''),
                    'image' => $this->normalizeAssetUrl($episode->thumbnail ?? $episode->banner ?? ''),
                    'play_type' => 'religious-episode',
                ];
            })
            ->values()
            ->all();

        $genres = $this->parseCsvValues((string) ($show->genre ?? ''));
        $content = [
            'id' => (int) $show->id,
            'type' => 'religious',
            'title' => (string) $show->name,
            'subtitle' => count($episodes) . ' episodes',
            'description' => (string) ($show->description ?? 'Religious library ready for episode browsing.'),
            'image' => $this->normalizeAssetUrl($show->thumbnail ?? $show->banner ?? ''),
            'backdrop' => $this->normalizeAssetUrl($show->banner ?? $show->thumbnail ?? ''),
            'badge' => 'Religious',
            'meta' => array_values(array_filter([
                $genres[0] ?? null,
                count($episodes) > 0 ? count($episodes) . ' Episodes' : null,
            ])),
            'plot' => (string) ($show->description ?? ''),
            'seasons' => [[
                'id' => (int) $show->id,
                'title' => 'All Episodes',
                'subtitle' => count($episodes) . ' episodes',
                'image' => $this->normalizeAssetUrl($show->banner ?? $show->thumbnail ?? ''),
                'episodes' => $episodes,
            ]],
        ];

        return response()->json([
            'status' => true,
            'content' => $content,
        ]);
    }

    protected function frontendTvShowPakDetail(int $id): JsonResponse
    {
        $show = TvShowPak::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$show) {
            return response()->json([
                'status' => false,
                'message' => 'TV show pak not found.',
            ], 404);
        }

        $seasons = TvShowSeasonPak::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('season_order')
            ->get()
            ->map(function ($season) {
                $episodes = TvShowEpisodePak::query()
                    ->where('season_id', $season->id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('episoade_order')
                    ->get()
                    ->map(function ($episode) {
                        return [
                            'id' => (int) $episode->id,
                            'title' => (string) ($episode->title ?? ('Episode ' . $episode->episode_number)),
                            'subtitle' => 'Episode ' . (int) ($episode->episode_number ?: $episode->episoade_order ?: 0),
                            'description' => (string) ($episode->description ?? ''),
                            'image' => $this->normalizeAssetUrl($episode->thumbnail ?? ''),
                            'play_type' => 'tvshowpak-episode',
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => (int) $season->id,
                    'title' => (string) ($season->title ?? ('Season ' . ($season->season_order ?: 1))),
                    'subtitle' => count($episodes) . ' episodes',
                    'image' => $this->normalizeAssetUrl($season->poster ?? ''),
                    'episodes' => $episodes,
                ];
            })
            ->values()
            ->all();

        $card = collect($this->loadFrontendTvShowPakCards((int) $show->tv_channel_id))
            ->firstWhere('id', (int) $show->id) ?: [];

        return response()->json([
            'status' => true,
            'content' => array_merge($card, [
                'plot' => (string) ($show->description ?? ''),
                'seasons' => $seasons,
            ]),
        ]);
    }

    protected function frontendKidsDetail(int $id): JsonResponse
    {
        $show = KidsShow::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$show) {
            return response()->json([
                'status' => false,
                'message' => 'Kids show not found.',
            ], 404);
        }

        $seasons = KidShowsSeason::query()
            ->where('show_id', $show->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('season_order')
            ->get()
            ->map(function ($season) {
                $episodes = KidshowsEpisode::query()
                    ->where('season_id', $season->id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('episoade_order')
                    ->get()
                    ->map(function ($episode) {
                        return [
                            'id' => (int) $episode->id,
                            'title' => (string) ($episode->title ?? $episode->name ?? ('Episode ' . $episode->id)),
                            'subtitle' => 'Episode ' . (int) ($episode->episode_number ?: $episode->episoade_order ?: 0),
                            'description' => (string) ($episode->description ?? ''),
                            'image' => $this->normalizeAssetUrl($episode->thumbnail ?? ''),
                            'play_type' => 'kids-episode',
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => (int) $season->id,
                    'title' => (string) ($season->title ?? ('Season ' . ($season->season_order ?: 1))),
                    'subtitle' => count($episodes) . ' episodes',
                    'image' => $this->normalizeAssetUrl($season->poster ?? ''),
                    'episodes' => $episodes,
                ];
            })
            ->values()
            ->all();

        $card = collect($this->loadFrontendKidsCards((int) $show->kid_channel_id))
            ->firstWhere('id', (int) $show->id) ?: [];

        return response()->json([
            'status' => true,
            'content' => array_merge($card, [
                'plot' => (string) ($show->description ?? ''),
                'seasons' => $seasons,
            ]),
        ]);
    }

    protected function frontendPlayLive(Collection $plans, int $id): JsonResponse
    {
        $channel = $this->getLiveChannelsForPlans($plans)->firstWhere('id', $id);

        if (!$channel || empty($channel->channel_link)) {
            return response()->json([
                'status' => false,
                'message' => 'Live channel stream is not available.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'live',
            $id,
            $this->normalizePlaybackUrl($channel->channel_link, $channel->stream_type ?? ''),
            (string) $channel->channel_name,
            $channel->channel_description ?? ''
        );
    }

    protected function frontendPlayMovie(Collection $plans, int $id, int $linkId): JsonResponse
    {
        $movie = $this->getVodMoviesForPlans($plans)->firstWhere('id', $id);

        if (!$movie) {
            return response()->json([
                'status' => false,
                'message' => 'Movie is not available for this account.',
            ], 404);
        }

        $playbackUrl = $this->resolveVodPlaybackUrl($movie, $linkId);

        if (!$playbackUrl) {
            return response()->json([
                'status' => false,
                'message' => 'Movie playback URL is not configured.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'movie',
            $id,
            $this->normalizePlaybackUrl($playbackUrl, $movie->source_type ?? ''),
            (string) $movie->name,
            $movie->description ?? ''
        );
    }

    protected function frontendPlayWebSeriesEpisode(int $id): JsonResponse
    {
        $episode = WebSeriesEpisode::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$episode) {
            return response()->json([
                'status' => false,
                'message' => 'Episode not found.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'webseries-episode',
            $id,
            $this->normalizePlaybackUrl($episode->url ?? $episode->backup_url, $episode->source ?? ''),
            (string) ($episode->Episoade_Name ?? 'Episode'),
            $episode->episoade_description ?? ''
        );
    }

    protected function frontendPlayTvShowEpisode(int $id): JsonResponse
    {
        $episode = TvShowEpisode::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$episode) {
            return response()->json([
                'status' => false,
                'message' => 'Episode not found.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'tvshow-episode',
            $id,
            $this->normalizePlaybackUrl($episode->video_url ?? $episode->backup_url, $episode->streaming_type ?? ''),
            (string) ($episode->title ?? 'Episode'),
            $episode->description ?? ''
        );
    }

    protected function frontendPlayTvShowPakEpisode(int $id): JsonResponse
    {
        $episode = TvShowEpisodePak::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$episode) {
            return response()->json([
                'status' => false,
                'message' => 'Episode not found.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'tvshowpak-episode',
            $id,
            $this->normalizePlaybackUrl($episode->video_url ?? $episode->backup_url, $episode->streaming_type ?? ''),
            (string) ($episode->title ?? 'Episode'),
            $episode->description ?? ''
        );
    }

    protected function frontendPlayReligiousEpisode(int $id): JsonResponse
    {
        $episode = RelshowsEpisode::query()
            ->where('id', $id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$episode) {
            return response()->json([
                'status' => false,
                'message' => 'Episode not found.',
            ], 404);
        }

        return $this->issueFrontendPlayback(
            'religious-episode',
            $id,
            $this->normalizePlaybackUrl($episode->url ?? $episode->backup_url, $episode->source ?? ''),
            (string) ($episode->title ?? 'Episode'),
            $episode->episode_description ?? ''
        );
    }

    protected function issueFrontendPlayback(string $type, int $id, ?string $url, string $title, string $description = ''): JsonResponse
    {
        if (!$url) {
            return response()->json([
                'status' => false,
                'message' => 'Playback URL is missing for this item.',
            ], 404);
        }

        $token = $this->issueStreamToken([
            'type' => $type,
            'resource_id' => $id,
            'url' => $url,
        ]);

        return response()->json([
            'status' => true,
            'playback' => [
                'type' => $type,
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'url' => url('/mag/stream/' . $token),
            ],
        ]);
    }

    protected function normalizePlaybackUrl(?string $url, ?string $sourceType = null): ?string
    {
        if (!$url) {
            return null;
        }

        $sourceType = strtolower(trim((string) $sourceType));
        $url = trim((string) $url);

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (Str::contains($sourceType, 'youtube')) {
            return 'https://www.youtube.com/watch?v=' . ltrim($url, '/');
        }

        return url(ltrim($url, '/'));
    }

    protected function issuePortalToken(ClientUser $user, string $mac): string
    {
        $token = Str::random(60);

        Cache::put($this->sessionCacheKey($token), [
            'user_id' => (int) $user->id,
            'mac' => $mac,
        ], now()->addHours(12));

        return $token;
    }

    protected function issueStreamToken(array $payload): string
    {
        $token = Str::random(48);

        Cache::put($this->streamCacheKey($token), $payload, now()->addMinutes(10));

        return $token;
    }

    protected function extractMac(Request $request): ?string
    {
        $candidates = [
            $request->input('mac'),
            $request->query('mac'),
            $request->input('device_mac'),
            $request->header('X-MAC'),
            $request->cookie('mac'),
        ];

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeMac($candidate);
            if ($normalized) {
                return $normalized;
            }
        }

        $rawCookie = (string) $request->header('Cookie', '');
        if (preg_match('/(?:^|;\s*)mac=([^;]+)/i', $rawCookie, $match)) {
            return $this->normalizeMac($match[1]);
        }

        return null;
    }

    protected function normalizeMac(?string $mac): ?string
    {
        if ($mac === null) {
            return null;
        }

        $normalized = strtoupper(trim(urldecode($mac)));

        return $normalized !== '' ? $normalized : null;
    }

    protected function extractPortalToken(Request $request): ?string
    {
        $candidates = [
            $request->bearerToken(),
            $request->input('token'),
            $request->query('token'),
            $request->header('X-User-Token'),
        ];

        foreach ($candidates as $candidate) {
            if (!empty($candidate)) {
                return trim((string) $candidate);
            }
        }

        return null;
    }

    protected function extractCommand(Request $request): string
    {
        $command = (string) ($request->input('cmd') ?? $request->query('cmd', ''));
        $command = trim($command);

        foreach (['ffrt ', 'ffmpeg ', 'auto '] as $prefix) {
            if (Str::startsWith(strtolower($command), $prefix)) {
                return trim(substr($command, strlen($prefix)));
            }
        }

        return $command;
    }

    protected function extractIdFromCommand(string $command, string $pattern): ?int
    {
        $match = [];

        if (!preg_match($pattern, $command, $match)) {
            return null;
        }

        return isset($match[1]) ? (int) $match[1] : null;
    }

    protected function matchesFrontendGenre(array $genres, ?string $genre): bool
    {
        $needle = trim((string) $genre);
        if ($needle === '') {
            return true;
        }

        return collect($genres)->contains(fn ($value) => strcasecmp((string) $value, $needle) === 0);
    }

    protected function parseCsvValues(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    protected function parseNumericCsvValues(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter(fn ($item) => $item !== '' && is_numeric($item))
            ->map(fn ($item) => (int) $item)
            ->values()
            ->all();
    }

    protected function normalizeAssetUrl(?string $value): string
    {
        if (!$value) {
            return '';
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return url(ltrim($value, '/'));
    }

    protected function detectStbType(Request $request): string
    {
        $agent = strtoupper((string) $request->userAgent());
        if (preg_match('/MAG[ _-]?(\d{3,4})/', $agent, $match)) {
            return 'MAG' . $match[1];
        }

        return 'MAG';
    }

    protected function formatPlanName($plan): string
    {
        if (!$plan) {
            return 'Default';
        }

        return strtoupper((string) ($plan->role ?: 'plan')) . '-' . $plan->plan_id;
    }

    protected function portalResponse($payload): JsonResponse
    {
        return response()->json(['js' => $payload]);
    }

    protected function portalError(string $message): JsonResponse
    {
        return response()->json([
            'js' => [
                'status' => 'ERROR',
                'message' => $message,
            ],
        ]);
    }

    protected function sessionCacheKey(string $token): string
    {
        return 'mag:session:' . $token;
    }

    protected function streamCacheKey(string $token): string
    {
        return 'mag:stream:' . $token;
    }

    protected function frontendBaseUrl(Request $request): string
    {
        return Str::startsWith($request->path(), 'public/')
            ? url('/public/c')
            : url('/c');
    }

    protected function frontendApiBaseUrl(Request $request): string
    {
        return Str::startsWith($request->path(), 'public/')
            ? url('/public/c/api')
            : url('/c/api');
    }

    protected function resolveDomainContent(Request $request): ?AppDomainContent
    {
        $host = strtolower((string) $request->getHost());
        $host = preg_replace('/^www\./', '', $host);

        return AppDomainContent::query()
            ->whereRaw('LOWER(TRIM(domain)) = ?', [$host])
            ->first();
    }

    // ─────────────────────────────────────────────
    //  Android-TV Live TV portal APIs (MAC-authed)
    // ─────────────────────────────────────────────

    public function apiSlider(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $sliders = Slider::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '')
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
            })
            ->get()
            ->map(function ($s) {
                return [
                    'id'           => $s->id,
                    'title'        => (string) ($s->title ?? ''),
                    'banner'       => $this->normalizeAssetUrl($s->banner ?? ''),
                    'content_type' => $s->content_type ?? null,
                    'content_id'   => $s->content_id ?? null,
                    'url'          => $s->url ?? null,
                    'source_type'  => $s->source_type ?? null,
                ];
            });

        return response()->json(['status' => true, 'data' => $sliders]);
    }

    public function apiLanguages(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $languages = Language::whereNull('deleted_at')
            ->where('status', 1)
            ->with('slider')
            ->get()
            ->map(function ($lang) {
                return [
                    'id'      => $lang->id,
                    'title'   => (string) $lang->title,
                    'logo'    => $this->normalizeAssetUrl($lang->logo ?? ''),
                    'sliders' => collect($lang->slider)->map(fn($s) => [
                        'id'     => $s->id,
                        'title'  => (string) ($s->title ?? ''),
                        'banner' => $this->normalizeAssetUrl($s->banner ?? ''),
                    ])->values()->all(),
                ];
            });

        return response()->json(['status' => true, 'languages' => $languages]);
    }

    public function apiGenres(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $languageId = $request->query('language_id');

        $query = Channel::where('channels.status', 1)
            ->whereNull('channels.deleted_at')
            ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id');

        if (!empty($languageId) && is_numeric($languageId)) {
            $query->where('languages.id', (int) $languageId);
        }

        $channels = $query->get(['channels.genres']);

        $genreSet = collect();
        foreach ($channels as $ch) {
            foreach (array_filter(array_map('trim', explode(',', $ch->genres ?? ''))) as $g) {
                if (!$genreSet->contains('title', $g)) {
                    $genreSet->push(['id' => $genreSet->count() + 1, 'title' => $g]);
                }
            }
        }

        $genres = collect([['id' => 0, 'title' => 'All']])->merge($genreSet->sortBy('title')->values())->values();

        return response()->json(['status' => true, 'data' => $genres]);
    }

    public function apiChannels(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $languageId = $request->input('language_id');
        $genre      = $request->input('genre', '');

        $query = Channel::where('channels.status', 1)
            ->whereNull('channels.deleted_at')
            ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id')
            ->select(
                'channels.id',
                'channels.channel_name',
                'channels.channel_logo',
                'channels.channel_number',
                'channels.stream_type',
                'channels.genres',
                'channels.channel_description',
                'languages.id as language_id',
                'languages.title as language_title'
            );

        if (!empty($languageId) && is_numeric($languageId)) {
            $query->where('languages.id', (int) $languageId);
        }

        if (!empty($genre)) {
            $query->where('channels.genres', 'like', '%' . $genre . '%');
        }

        $channels = $query->orderBy('channels.channel_number', 'asc')->get()
            ->map(fn($ch) => [
                'id'          => $ch->id,
                'name'        => (string) $ch->channel_name,
                'logo'        => $this->normalizeAssetUrl($ch->channel_logo ?? ''),
                'number'      => (int) ($ch->channel_number ?? 0),
                'stream_type' => (string) ($ch->stream_type ?? ''),
                'genres'      => array_values(array_filter(array_map('trim', explode(',', $ch->genres ?? '')))),
                'description' => (string) ($ch->channel_description ?? ''),
                'cmd'         => sprintf('ffrt %s', url('/ch/' . $ch->id . '_')),
            ]);

        // Language sliders
        $langSliders = collect();
        if (!empty($languageId) && is_numeric($languageId)) {
            $lang = Language::with('slider')->find((int) $languageId);
            if ($lang) {
                $langSliders = collect($lang->slider)->map(fn($s) => [
                    'id'     => $s->id,
                    'title'  => (string) ($s->title ?? ''),
                    'banner' => $this->normalizeAssetUrl($s->banner ?? ''),
                ]);
            }
        }

        return response()->json([
            'status'   => true,
            'sliders'  => $langSliders->values(),
            'channels' => $channels->values(),
        ]);
    }

    // ── Movies: networks list ─────────────────────────────────────────────
    public function apiMovieNetworks(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        // Get network IDs that have movies with is_recent=1
        $networkIds = DB::table('movie_content_network')
            ->whereIn('movie_id', Movie::where('is_recent', 1)->pluck('id'))
            ->distinct()
            ->pluck('network_id')
            ->toArray();

        $networks = ContentNetwork::whereIn('id', $networkIds)
            ->whereNull('deleted_at')
            ->where('is_content', 1)
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get()
            ->map(fn($n) => [
                'id'   => (int) $n->id,
                'name' => (string) ($n->name ?? ''),
                'logo' => $this->normalizeAssetUrl($n->logo ?? ''),
            ]);

        return response()->json(['status' => true, 'networks' => $networks->values()]);
    }

    // ── Movies: genres for a network ─────────────────────────────────────
    public function apiMovieGenres(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);

        $movieIds = DB::table('movie_content_network')
            ->where('network_id', $networkId)
            ->pluck('movie_id')
            ->toArray();

        $genres = Movie::whereIn('id', $movieIds)
            ->where('status', 1)
            ->where('is_recent', 1)
            ->whereNull('deleted_at')
            ->pluck('genres')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->filter()
            ->values();

        return response()->json(['status' => true, 'genres' => $genres]);
    }

    // ── Movies: contents for a network + optional genre ───────────────────
    public function apiMovieContents(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);
        $genre     = $request->query('genre', '');
        $page      = max(1, (int) $request->query('page', 1));
        $records   = max(1, min(100, (int) $request->query('records', 30)));

        // Content
        $movieIds = DB::table('movie_content_network')
            ->where('network_id', $networkId)
            ->pluck('movie_id')
            ->toArray();

        $query = Movie::whereIn('id', $movieIds)
            ->where('status', 1)
            ->where('is_recent', 1)
            ->whereNull('deleted_at');

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }

        $total   = $query->count();
        $movies  = $query->latest()->forPage($page, $records)->get();

        $items = $movies->map(function ($m) {
            $genreList = array_values(array_filter(array_map('trim', explode(',', $m->genres ?? ''))));
            return [
                'id'          => (int) $m->id,
                'type'        => 'movie',
                'content_type'=> 1,
                'title'       => (string) $m->name,
                'subtitle'    => $genreList[0] ?? 'Movie',
                'image'       => $this->normalizeAssetUrl($m->poster ?? $m->banner ?? ''),
                'backdrop'    => $this->normalizeAssetUrl($m->banner ?? $m->poster ?? ''),
                'badge'       => 'Movie',
                'action'      => 'play',
                'play_type'   => 'movie',
            ];
        });

        // Sliders for this network
        $sliders = DB::table('content_network_slider')
            ->where('content_network_id', $networkId)
            ->where('slider_for', 'movies')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'title' => (string) ($s->title ?? ''),
                'image' => $this->normalizeAssetUrl($s->image ?? $s->banner ?? ''),
            ]);

        return response()->json([
            'status'   => true,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $records,
            'sliders'  => $sliders->values(),
            'data'     => $items->values(),
        ]);
    }

    // ── Web Series: networks list ─────────────────────────────────────────
    public function apiWebSeriesNetworks(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $networkIds = \App\Models\WebSeriesContentNetwork::distinct()->pluck('network_id')->toArray();

        $networks = ContentNetwork::whereIn('id', $networkIds)
            ->whereNull('deleted_at')
            ->where('is_content', 1)
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get()
            ->map(fn($n) => [
                'id'   => (int) $n->id,
                'name' => (string) ($n->name ?? ''),
                'logo' => $this->normalizeAssetUrl($n->logo ?? ''),
            ]);

        return response()->json(['status' => true, 'networks' => $networks->values()]);
    }

    // ── Web Series: genres for a network ──────────────────────────────────
    public function apiWebSeriesGenres(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);

        $seriesIds = DB::table('web_series_content_network')
            ->where('network_id', $networkId)
            ->pluck('webseries_id')
            ->toArray();

        $genres = WebSeries::whereIn('id', $seriesIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->pluck('genres')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->filter()
            ->values();

        return response()->json(['status' => true, 'genres' => $genres]);
    }

    // ── Web Series: contents for a network + optional genre ───────────────
    public function apiWebSeriesContents(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);
        $genre     = $request->query('genre', '');
        $page      = max(1, (int) $request->query('page', 1));
        $records   = max(1, min(100, (int) $request->query('records', 30)));

        $seriesIds = DB::table('web_series_content_network')
            ->where('network_id', $networkId)
            ->pluck('webseries_id')
            ->toArray();

        $query = WebSeries::whereIn('id', $seriesIds)
            ->where('status', 1)
            ->whereNull('deleted_at');

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }

        $total  = $query->count();
        $series = $query->latest()->forPage($page, $records)->get();

        $items = $series->map(function ($s) {
            $genreList = array_values(array_filter(array_map('trim', explode(',', $s->genres ?? ''))));
            return [
                'id'          => (int) $s->id,
                'type'        => 'webseries',
                'content_type'=> 2,
                'title'       => (string) $s->name,
                'subtitle'    => $genreList[0] ?? 'Web Series',
                'image'       => $this->normalizeAssetUrl($s->poster ?? $s->banner ?? ''),
                'badge'       => 'Series',
                'action'      => 'detail',
                'detail_type' => 'webseries',
            ];
        });

        // Sliders for this network (webseries)
        $sliders = DB::table('content_network_slider')
            ->where('content_network_id', $networkId)
            ->where('slider_for', 'webseries')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'title' => (string) ($s->title ?? ''),
                'image' => $this->normalizeAssetUrl($s->image ?? $s->banner ?? ''),
            ]);

        return response()->json([
            'status'   => true,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $records,
            'sliders'  => $sliders->values(),
            'data'     => $items->values(),
        ]);
    }

    // ── OTT Networks (simple list with logos) ─────────────────────────────
    public function apiOttNetworks(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $networks = ContentNetwork::whereNull('deleted_at')
            ->where('is_content', 1)
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get()
            ->map(fn($n) => [
                'id'   => (int) $n->id,
                'name' => (string) ($n->name ?? ''),
                'logo' => $this->normalizeAssetUrl($n->logo ?? ''),
            ]);

        return response()->json(['status' => true, 'networks' => $networks->values()]);
    }

    // ── TV Shows: networks list ───────────────────────────────────────────
    // Same source as v3 getNetworks (data_for:"tvshows") → tv_show_content_network
    public function apiTvShowNetworks(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $networkIds = \App\Models\TvShowContentNetwork::distinct()
            ->pluck('network_id')
            ->toArray();

        $networks = ContentNetwork::whereIn('id', $networkIds)
            ->whereNull('deleted_at')
            ->where('is_content', 1)
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get()
            ->map(fn($n) => [
                'id'   => (int) $n->id,
                'name' => (string) ($n->name ?? ''),
                'logo' => $this->normalizeAssetUrl($n->logo ?? ''),
            ]);

        return response()->json(['status' => true, 'networks' => $networks->values()]);
    }

    // ── TV Shows: genres for a network ────────────────────────────────────
    // Same source as v3 getGenreByContentNetwork (data_for:"tvshows") → tv_show_content_network
    public function apiTvShowGenres(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);

        $channelIds = DB::table('tv_show_content_network')
            ->where('network_id', $networkId)
            ->pluck('show_id')
            ->toArray();

        $genres = TvShow::whereIn('tv_channel_id', $channelIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->pluck('genre')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->filter()
            ->values();

        return response()->json(['status' => true, 'genres' => $genres]);
    }

    // ── TV Shows: contents for a network + optional genre ─────────────────
    // Same source as v3 getAllContentsOfNetworkNew (data_for:"tvshows") → content_network_log content_type=4
    public function apiTvShowContents(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);
        $genre     = $request->query('genre', '');
        $page      = max(1, (int) $request->query('page', 1));
        $records   = max(1, min(100, (int) $request->query('records', 30)));

        $channelIds = DB::table('content_network_log')
            ->where('network_id', $networkId)
            ->where('content_type', 4)
            ->pluck('content_id')
            ->toArray();

        $query = TvShow::whereIn('tv_channel_id', $channelIds)
            ->where('status', 1)
            ->whereNull('deleted_at');

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        $total = $query->count();
        $shows = $query->latest()->forPage($page, $records)->get();

        $items = $shows->map(function ($show) {
            $seasonCount = TvShowSeason::query()
                ->where('show_id', $show->id)
                ->where('status', 1)
                ->count();
            return [
                'id'          => (int) $show->id,
                'type'        => 'tvshow',
                'content_type'=> 4,
                'title'       => (string) $show->name,
                'subtitle'    => $seasonCount > 0 ? $seasonCount . ' seasons' : 'TV Show',
                'image'       => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                'badge'       => 'TV Show',
                'action'      => 'detail',
                'detail_type' => 'tvshow',
            ];
        });

        // Sliders for this network (tvshows)
        $sliders = DB::table('content_network_slider')
            ->where('content_network_id', $networkId)
            ->where('slider_for', 'tvshows')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'title' => (string) ($s->title ?? ''),
                'image' => $this->normalizeAssetUrl($s->image ?? $s->banner ?? ''),
            ]);

        return response()->json([
            'status'   => true,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $records,
            'sliders'  => $sliders->values(),
            'data'     => $items->values(),
        ]);
    }

    // ── Kids: networks list ───────────────────────────────────────────────
    // Same source as v3 getNetworks (data_for:"kidchannels") → kids_channel_content_network
    public function apiKidsNetworks(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);

        $networkIds = DB::table('kids_channel_content_network')
            ->distinct()
            ->pluck('network_id')
            ->toArray();

        $networks = ContentNetwork::whereIn('id', $networkIds)
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get()
            ->map(fn($n) => [
                'id'   => (int) $n->id,
                'name' => (string) ($n->name ?? ''),
                'logo' => $this->normalizeAssetUrl($n->logo ?? ''),
            ]);

        return response()->json(['status' => true, 'networks' => $networks->values()]);
    }

    // ── Kids: genres for a network ────────────────────────────────────────
    public function apiKidsGenres(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);

        // kids_channel_content_network.show_id → KidsShow.id
        $showIds = DB::table('kids_channel_content_network')
            ->where('network_id', $networkId)
            ->pluck('show_id')
            ->toArray();

        $genres = KidsShow::whereIn('id', $showIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->pluck('genre')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->filter()
            ->values();

        return response()->json(['status' => true, 'genres' => $genres]);
    }

    // ── Kids: contents for a network + optional genre ─────────────────────
    public function apiKidsContents(Request $request): JsonResponse
    {
        $this->resolvePortalUser($request, true);
        $networkId = (int) $request->query('network_id', 0);
        $genre     = $request->query('genre', '');
        $page      = max(1, (int) $request->query('page', 1));
        $records   = max(1, min(100, (int) $request->query('records', 30)));

        // kids_channel_content_network.show_id → KidsShow.id
        $showIds = DB::table('kids_channel_content_network')
            ->where('network_id', $networkId)
            ->pluck('show_id')
            ->toArray();

        $query = KidsShow::whereIn('id', $showIds)
            ->where('status', 1)
            ->whereNull('deleted_at');

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        $total = $query->count();
        $shows = $query->latest()->forPage($page, $records)->get();

        $items = $shows->map(function ($show) {
            $seasonCount = KidShowsSeason::query()
                ->where('show_id', $show->id)
                ->where('status', 1)
                ->count();
            return [
                'id'          => (int) $show->id,
                'type'        => 'kids',
                'content_type'=> 6,
                'title'       => (string) $show->name,
                'subtitle'    => $seasonCount > 0 ? $seasonCount . ' seasons' : 'Kids Show',
                'image'       => $this->normalizeAssetUrl($show->thumbnail ?? ''),
                'badge'       => 'Kids',
                'action'      => 'detail',
                'detail_type' => 'kids',
            ];
        });

        $sliders = DB::table('content_network_slider')
            ->where('content_network_id', $networkId)
            ->where('slider_for', 'kids')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'title' => (string) ($s->title ?? ''),
                'image' => $this->normalizeAssetUrl($s->image ?? $s->banner ?? ''),
            ]);

        // genres from the same show set
        $genres = KidsShow::whereIn('id', $showIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->pluck('genre')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->filter()
            ->values();

        return response()->json([
            'status'   => true,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $records,
            'genres'   => $genres,
            'sliders'  => $sliders->values(),
            'contents' => $items->values(),
        ]);
    }
}
