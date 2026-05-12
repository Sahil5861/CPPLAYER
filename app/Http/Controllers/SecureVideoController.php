<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\CDNDomain;
use Illuminate\Support\Facades\Log;

class SecureVideoController extends Controller
{
    /**
     * ==================================================================
     * SOLUTION 1: SIMPLE TIME-BASED TOKEN (Basic Protection)
     * ==================================================================
     * Use: Basic video protection with time expiry
     * Security: Time-based only
     */
     
    public function generateMobileStream(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'expiry_seconds' => 'nullable|integer|max:86400' // 1 min to 24 hours
        ]);
    
        $expirySeconds = $request->expiry_seconds ?? 7200; // 2 hours
    
        $hostname = parse_url($request->url, PHP_URL_HOST);
        $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
    
        if (!$cdnDomain) {
            return response()->json([
                'status' => false,
                'message' => 'CDN domain not allowed'
            ], 403);
        }
    
        // 🔥 DIRECT m3u8 SIGNING
        $signedUrl = $this->generateCacheFlySignedUrl(
            $request->url,
            $cdnDomain->url,
            $expirySeconds,
            null // ❌ IP lock OFF for mobile
        );
    
        return response()->json([
            'status' => true,
            'url' => $signedUrl,
            'type' => 'm3u8',
            'platform' => 'mobile',
            'expires_in_seconds' => $expirySeconds,
            'solution' => 'Mobile Direct Signed Stream'
        ]);
    }

    
    /**
     * Generate simple time-based signed URL
     */
    public function tokenizeUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'expiry_seconds' => 'nullable|integer|max:86400' // 1 min to 24 hours
        ]);
        
        try {
            $expirySeconds = $request->expiry_seconds ?? 3600; // Default 1 hour
            
            // Generate signed URL
            $signedUrl = $this->signM3U8Url($request->url, $expirySeconds);
            
            return response()->json([
                'status' => true,
                'url' => $signedUrl,
                'expires_in_seconds' => $expirySeconds,
                'expires_at' => date('Y-m-d H:i:s', time() + $expirySeconds),
                'solution' => 'Simple Time-Based Token'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate signed URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate CacheFly signed URL (Simple method)
     */
    public function signM3U8Url($url, $expirySeconds = 3600, $userIp = null)
    {
        $parsedUrl = parse_url($url);
        $protocol = $parsedUrl['scheme'];
        $hostname = $parsedUrl['host'];
        $fullPath = ltrim($parsedUrl['path'], '/');
        
        // Get secret from database
        $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
        if (!$cdnDomain) {
            throw new \Exception("CDN domain not configured: {$hostname}");
        }
        
        $secret = $cdnDomain->url;
        
        $pathInfo = pathinfo($fullPath);
        $path_to_hash = $pathInfo['dirname'] . '/';
        $filename = $pathInfo['basename'];
        
        $expiretime = time() + $expirySeconds;
        
        // Build rules
        $rules = 'expiretime=' . $expiretime . ';dirmatch=true';
        if ($userIp) {
            $rules .= ';ip=' . $userIp;
        }
        
        // Generate hash
        $hash = hash_hmac('sha256', $rules . $path_to_hash, $secret, false);
        
        // Build URL
        $signedUrl = "$protocol://$hostname/$rules/$hash/$path_to_hash";
        if ($filename != '') {
            $signedUrl .= $filename;
        }
        
        return $signedUrl;
    }
    
    
    /**
     * ==================================================================
     * SOLUTION 2: SECURE ONE-TIME TOKEN (Maximum Protection)
     * ==================================================================
     * Use: Prevent link sharing, 3-second expiry
     * Security: One-time use + IP lock + Short expiry
     */
    
    /**
     * Generate secure one-time token with short expiry
     */
    /**
     * Generate secure one-time token with short expiry
     */
    // public function generateSecureToken(Request $request)
    // {
    //     $request->validate([
    //         'url' => 'required|url',
    //         'expiry_seconds' => 'nullable|integer|min:1|max:60',
    //         'enable_ip_lock' => 'nullable|boolean',
    //         'video_duration_hours' => 'nullable|integer|min:1|max:24'
    //     ]);
        
    //     try {
    //         $expirySeconds = $request->expiry_seconds ?? 3;
    //         $enableIPLock = $request->enable_ip_lock ?? true;
    //         $videoDuration = $request->video_duration_hours ?? 2;
            
    //         $hostname = parse_url($request->url, PHP_URL_HOST);
    //         $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
            
    //         if (!$cdnDomain) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'CDN domain not found: ' . $hostname
    //             ], 404);
    //         }
            
    //         $token = Str::random(64);
            
    //         $tokenData = [
    //             'original_url' => $request->url,
    //             'cdn_secret' => $cdnDomain->url,
    //             'used' => false,
    //             'user_ip' => $request->ip(),
    //             'user_agent' => $request->userAgent(),
    //             'enable_ip_lock' => $enableIPLock,
    //             'video_duration_hours' => $videoDuration,
    //             'created_at' => now()->toDateTimeString(),
    //             'expires_at' => now()->addSeconds($expirySeconds)->toDateTimeString()
    //         ];
            
    //         $cacheSeconds = max(60, $expirySeconds + 10);
    //         Cache::put('video_token:' . $token, $tokenData, $cacheSeconds);
            
    //         // ✅ FIXED: Use full URL instead of route()
    //         $proxyUrl = url('/api/video/play/' . $token);
    //         // Or hardcoded domain:
    //         // $proxyUrl = 'https://dash.getplaybox.com/video/play/' . $token;
            
    //         return response()->json([
    //             'status' => true,
    //             'url' => $proxyUrl,
    //             'token' => $token,
    //             'security' => [
    //                 'expires_in_seconds' => $expirySeconds,
    //                 'ip_locked' => $enableIPLock,
    //                 'one_time_use' => true,
    //                 'video_playback_hours' => $videoDuration
    //             ],
    //             'solution' => 'Secure One-Time Token',
    //             'note' => "Link expires in {$expirySeconds} seconds if not used. Once accessed, video plays for {$videoDuration} hours."
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to generate secure token',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function generateSecureToken(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'token_expiry_seconds' => 'nullable|integer|min:1|max:60',
            'video_duration_hours' => 'nullable|integer|min:1|max:24',
            'enable_ip_lock' => 'nullable|boolean'
        ]);
        
        try {
            $tokenExpiry = $request->token_expiry_seconds ?? 3;
            $videoDuration = $request->video_duration_hours ?? 2;
            $enableIPLock = $request->enable_ip_lock ?? true;
            
            $hostname = parse_url($request->url, PHP_URL_HOST);
            $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
            
            if (!$cdnDomain) {
                return response()->json([
                    'status' => false,
                    'message' => 'CDN domain not found'
                ], 404);
            }
            
            // ✅ Generate token
            $token = Str::random(64);
            
            // ✅ Store data
            $tokenData = [
                'original_url' => $request->url,
                'cdn_secret' => $cdnDomain->url,
                'used' => false,
                'user_ip' => $request->ip(),
                'enable_ip_lock' => $enableIPLock,
                'video_duration_hours' => $videoDuration,
                'created_at' => now()->toDateTimeString(),
                'expires_at' => now()->addSeconds($tokenExpiry)->toDateTimeString()
            ];
            
            Cache::put('video_token:' . $token, $tokenData, $tokenExpiry + 10);
            
            // 🔥 TRICK: Add .m3u8 extension to proxy URL
            $proxyUrl = url('/api/video/play/' . $token . '.m3u8');
            
            return response()->json([
                'status' => true,
                'url' => $proxyUrl, // ✅ Looks like: /api/video/play/abc123.m3u8
                'token' => $token,
                'security' => [
                    'token_expires_in_seconds' => $tokenExpiry,
                    'video_playback_hours' => $videoDuration,
                    'ip_locked' => $enableIPLock,
                    'one_time_use' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate token and return player-ready URL
     */
    public function generatePlayerToken(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'expiry_seconds' => 'nullable|integer|min:1|max:60'
        ]);
        
        try {
            $expirySeconds = $request->expiry_seconds ?? 3;
            
            $hostname = parse_url($request->url, PHP_URL_HOST);
            $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
            
            if (!$cdnDomain) {
                return response()->json([
                    'status' => false,
                    'message' => 'CDN domain not found'
                ], 404);
            }
            
            // Generate ONE-TIME token
            $token = Str::random(64);
            
            // Pre-generate the CacheFly URL with long expiry
            $signedUrl = $this->generateCacheFlySignedUrl(
                $request->url,
                $cdnDomain->url,
                7200, // 2 hours
                request()->ip() // IP locked
            );
            
            // Store in cache with short expiry
            Cache::put('player_token:' . $token, [
                'signed_url' => $signedUrl,
                'used' => false,
                'created_at' => now()->toDateTimeString()
            ], $expirySeconds);
            
            // Return token-based URL that looks like m3u8
            return response()->json([
                'status' => true,
                'url' => url('/stream/' . $token . '.m3u8'),
                'note' => 'Use this URL in your player. Expires in ' . $expirySeconds . ' seconds.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Stream endpoint - serves the actual m3u8
     */
    public function stream($token)
    {
        // Remove .m3u8 extension if present
        $token = str_replace('.m3u8', '', $token);
        
        $data = Cache::get('player_token:' . $token);
        
        if (!$data || $data['used']) {
            abort(403, 'Invalid or expired stream token');
        }
        
        // Mark as used
        $data['used'] = true;
        Cache::put('player_token:' . $token, $data, 60);
        
        // Redirect to actual signed URL
        return redirect($data['signed_url']);
    }
    /**
     * Play video - validates token and redirects to actual video
     */
    // public function playVideo($token)
    // {
    //     try {
    //         // Get token data from cache
    //         $data = Cache::get('video_token:' . $token);
            
    //         // Validation 1: Check if token exists
    //         if (!$data) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Invalid or expired token',
    //                 'code' => 'TOKEN_NOT_FOUND'
    //             ], 403);
    //         }
            
    //         // Validation 2: Check if already used (one-time use)
    //         if ($data['used']) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Token already used. Each token can only be used once.',
    //                 'code' => 'TOKEN_ALREADY_USED',
    //                 'used_at' => $data['used_at'] ?? 'unknown'
    //             ], 403);
    //         }
            
    //         // Validation 3: Check if expired (time-based)
    //         if (now()->greaterThan($data['expires_at'])) {
    //             Cache::forget('video_token:' . $token);
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Token expired. Please request a new link.',
    //                 'code' => 'TOKEN_EXPIRED',
    //                 'expired_at' => $data['expires_at']
    //             ], 403);
    //         }
            
    //         // Validation 4: IP lock check (if enabled)
    //         if ($data['enable_ip_lock'] && $data['user_ip'] !== request()->ip()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'IP address mismatch. This link cannot be shared.',
    //                 'code' => 'IP_MISMATCH',
    //                 'original_ip' => $data['user_ip'],
    //                 'current_ip' => request()->ip()
    //             ], 403);
    //         }
            
    //         // Mark token as used (prevent reuse)
    //         $data['used'] = true;
    //         $data['used_at'] = now()->toDateTimeString();
    //         Cache::put('video_token:' . $token, $data, 300); // Keep for 5 min for logging
            
    //         // Generate actual CacheFly signed URL with long expiry
    //         $videoDurationSeconds = $data['video_duration_hours'] * 3600;
    //         $signedUrl = $this->generateCacheFlySignedUrl(
    //             $data['original_url'],
    //             $data['cdn_secret'],
    //             $videoDurationSeconds,
    //             $data['enable_ip_lock'] ? request()->ip() : null
    //         );
            
    //         // Log access (optional - for analytics)
    //         Log::info('Video token used successfully', [
    //             'token' => substr($token, 0, 10) . '...', // Partial token for security
    //             'ip' => request()->ip(),
    //             'user_agent' => request()->userAgent(),
    //             'url' => $data['original_url'],
    //             'time_taken_seconds' => now()->diffInSeconds($data['created_at'])
    //         ]);
            
    //         // Redirect to actual video URL
    //         return redirect($signedUrl);
            
    //     } catch (\Exception $e) {
    //         Log::error('Video play error', [
    //             'token' => substr($token, 0, 10) . '...',
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to play video',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function playVideo($token)
    {
        try {
            // 🔥 Remove .m3u8 extension if present
            $token = str_replace('.m3u8', '', $token);
            
            // ✅ Get token from cache
            $data = Cache::get('video_token:' . $token);
            
            // Validation 1: Token exists?
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => '⚠️ Token expired or invalid',
                    'code' => 'TOKEN_EXPIRED'
                ], 403);
            }
            
            // Validation 2: Already used?
            if ($data['used']) {
                return response()->json([
                    'status' => false,
                    'message' => '⚠️ Token already used',
                    'code' => 'TOKEN_ALREADY_USED'
                ], 403);
            }
            
            // Validation 3: Time expired?
            if (now()->greaterThan($data['expires_at'])) {
                Cache::forget('video_token:' . $token);
                return response()->json([
                    'status' => false,
                    'message' => '⚠️ Token expired',
                    'code' => 'TOKEN_EXPIRED',
                    'expired_at' => $data['expires_at'],
                    'current_time' => now()->toDateTimeString()
                ], 403);
            }
            
            // Validation 4: IP lock?
            if ($data['enable_ip_lock'] && $data['user_ip'] !== request()->ip()) {
                return response()->json([
                    'status' => false,
                    'message' => '⚠️ IP mismatch - Cannot share link',
                    'code' => 'IP_MISMATCH',
                    'original_ip' => $data['user_ip'],
                    'current_ip' => request()->ip()
                ], 403);
            }
            
            // ✅ Mark as USED
            $data['used'] = true;
            $data['used_at'] = now()->toDateTimeString();
            Cache::put('video_token:' . $token, $data, 300);
            
            // ✅ Generate real CacheFly URL
            $videoDurationSeconds = $data['video_duration_hours'] * 3600;
            $signedUrl = $this->generateCacheFlySignedUrl(
                $data['original_url'],
                $data['cdn_secret'],
                $videoDurationSeconds,
                $data['enable_ip_lock'] ? request()->ip() : null
            );
            
            // ✅ Log
            Log::info('✅ Token validated', [
                'token' => substr($token, 0, 10) . '...',
                'ip' => request()->ip(),
                'delay' => now()->diffInSeconds($data['created_at']) . 's'
            ]);
            
            // 🔥 Redirect with proper headers for m3u8
            return redirect($signedUrl)
                ->header('Content-Type', 'application/vnd.apple.mpegurl')
                ->header('Access-Control-Allow-Origin', '*');
            
        } catch (\Exception $e) {
            Log::error('❌ Play error', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to play video',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate CacheFly signed URL (Internal helper method)
     */
    private function generateCacheFlySignedUrl($url, $secret, $expirySeconds, $userIp = null)
    {
        $parsedUrl = parse_url($url);
        $protocol = $parsedUrl['scheme'];
        $hostname = $parsedUrl['host'];
        $fullPath = ltrim($parsedUrl['path'], '/');
        
        $pathInfo = pathinfo($fullPath);
        $path_to_hash = $pathInfo['dirname'] . '/';
        $filename = $pathInfo['basename'];
        
        $expiretime = time() + $expirySeconds;
        
        // Build rules
        $rules = 'expiretime=' . $expiretime . ';dirmatch=true';
        if ($userIp) {
            $rules .= ';ip=' . $userIp;
        }
        
        // Generate HMAC-SHA256 hash
        $hash = hash_hmac('sha256', $rules . $path_to_hash, $secret, false);
        
        // Build final signed URL
        $signedUrl = "$protocol://$hostname/$rules/$hash/$path_to_hash";
        if ($filename != '') {
            $signedUrl .= $filename;
        }
        
        return $signedUrl;
    }
    
    
    /**
     * ==================================================================
     * HELPER / DEBUG METHODS
     * ==================================================================
     */
    
    /**
     * Check token status (for debugging/testing)
     */
    public function checkTokenStatus($token)
    {
        $data = Cache::get('video_token:' . $token);
        
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Token not found or expired'
            ], 404);
        }
        
        $secondsRemaining = max(0, strtotime($data['expires_at']) - time());
        
        return response()->json([
            'status' => true,
            'token_data' => [
                'used' => $data['used'],
                'used_at' => $data['used_at'] ?? null,
                'created_at' => $data['created_at'],
                'expires_at' => $data['expires_at'],
                'ip_locked' => $data['enable_ip_lock'],
                'user_ip' => $data['user_ip'],
                'seconds_remaining' => $secondsRemaining,
                'expired' => $secondsRemaining <= 0
            ]
        ]);
    }
    
    /**
     * Advanced signing with custom options (for flexibility)
     */
    public function signM3U8UrlAdvanced($url, $expirySeconds = 3600, $userIp = null)
    {
        return $this->signM3U8Url($url, $expirySeconds, $userIp);
    }
    
    /**
     * Batch tokenize multiple URLs at once
     */
    public function batchTokenize(Request $request)
    {
        $request->validate([
            'urls' => 'required|array|min:1|max:100',
            'urls.*' => 'required|url',
            'expiry_seconds' => 'nullable|integer|min:60|max:86400'
        ]);
        
        try {
            $expirySeconds = $request->expiry_seconds ?? 3600;
            $results = [];
            
            foreach ($request->urls as $url) {
                try {
                    $signedUrl = $this->signM3U8Url($url, $expirySeconds);
                    $results[] = [
                        'original_url' => $url,
                        'signed_url' => $signedUrl,
                        'status' => 'success'
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'original_url' => $url,
                        'signed_url' => null,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return response()->json([
                'status' => true,
                'total' => count($request->urls),
                'successful' => count(array_filter($results, fn($r) => $r['status'] === 'success')),
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Batch tokenize failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}