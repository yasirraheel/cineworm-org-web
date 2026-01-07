# Pacman Mobile Remote Control - Installation Guide

This guide will help you set up the mobile remote control feature for the Pacman game using a polling-based approach (no additional packages required).

## Prerequisites
- PHP >= 7.3
- Laravel application running
- Laravel Cache configured (file, redis, or memcached)

---

## Overview

This feature uses a **polling-based system** instead of WebSockets, which means:
- ✅ No additional Composer packages required
- ✅ No WebSocket server to manage
- ✅ Works with existing Laravel cache
- ✅ Simple to deploy and maintain

---

## Step 1: Verify Files Are in Place

The following files should already be created in your Laravel project:

### Backend Files:
- `app/Http/Controllers/GameRoomController.php` - Controller for room management and game controls
- Routes added to `routes/web.php` for game endpoints

### Frontend Files:
- `resources/views/pages/remote_control.blade.php` - Mobile control interface
- `resources/views/pages/index.blade.php` - Updated with room code display and polling logic

---

## Step 2: Clear Cache

Clear all caches to ensure fresh configuration:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Step 3: Test the Feature

### On PC/Desktop:

1. Open your homepage: `http://yoursite.com`
2. Click the **"Games"** button to show the Pacman game
3. You should see a **4-digit room code** displayed above the game in a purple gradient box
4. Note this room code (e.g., "1234")

### On Mobile:

1. Open `http://yoursite.com/game/remote-control` on your mobile device
2. Enter the 4-digit room code shown on the PC
3. Click **"Connect"**
4. You should see the D-pad controller
5. Use the arrow buttons (▲ ▼ ◄ ►) to control the game on the PC

---

## How It Works

### 1. Room Code Generation
- When user clicks "Games" button, a 4-digit room code is generated via AJAX
- Room code is stored in Laravel cache for 1 hour
- Room code is displayed above the Pacman game

### 2. Mobile Connection
- Mobile user enters room code on `/game/remote-control`
- System verifies room code exists in cache
- Mobile shows D-pad controller interface

### 3. Control Flow (Polling-Based)
```
Mobile Device                    Laravel Server                PC Browser
     |                                 |                            |
     |--[1] POST /game/control ------->|                            |
     |    (direction: up, action: press)|                           |
     |                                 |                            |
     |                    [2] Store in Cache                        |
     |                    (game_control_1234)                       |
     |                                 |                            |
     |                                 |<--[3] GET /game/controls---|
     |                                 |    (room_code=1234)        |
     |                                 |                            |
     |                                 |--[4] Return controls------>|
     |                                 |                            |
     |                                 |         [5] Simulate Keypress
     |                                 |         (Arrow Up KeyDown)
```

**Step by step:**
1. Mobile user presses D-pad button → POST to `/game/control`
2. Server stores control in cache with timestamp (expires in 2 seconds)
3. PC browser polls `/game/controls` every 100ms
4. Server returns controls newer than last timestamp
5. JavaScript simulates keyboard event for Pacman iframe
6. Game responds to the control input

---

## Configuration Details

### Cache Settings

Controls are stored in Laravel cache with these settings:
- **Key format:** `game_control_{room_code}`
- **Expiry:** 2 seconds (prevents stale controls)
- **Max controls stored:** 10 most recent
- **Polling interval:** 100ms (responsive controls)

### Room Code Settings

Room codes are stored with:
- **Key format:** `game_room_{room_code}`
- **Expiry:** 1 hour (3600 seconds)
- **Format:** 4-digit numeric code (1000-9999)

---

## Troubleshooting

### Room code shows "----" or "ERROR"
**Possible causes:**
- JavaScript error in browser console
- Route not registered properly
- CSRF token mismatch

**Solutions:**
```bash
# Clear caches
php artisan route:clear
php artisan view:clear

# Check routes exist
php artisan route:list | grep game

# Check Laravel logs
tail -f storage/logs/laravel.log
```

### Cannot connect from mobile - "Invalid room code"
**Possible causes:**
- Room code expired (1 hour timeout)
- Cache not working
- Room code entered incorrectly

**Solutions:**
```bash
# Test cache is working
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');

# Check cache driver in .env
cat .env | grep CACHE_DRIVER
```

### Controls not working
**Possible causes:**
- Iframe sandbox restrictions too strict
- Polling not running
- JavaScript errors

**Solutions:**
1. Open browser console (F12) on PC
2. Check for JavaScript errors
3. Verify polling is running (should see network requests every 100ms to `/game/controls`)
4. Check iframe has ID `pacmanGameFrame`

### Controls are laggy
**Possible causes:**
- Polling interval too slow
- Server response time slow
- Network latency

**Solutions:**
- Adjust polling interval in `index.blade.php` (line 1488): Change `100` to `50` for faster polling
- Check server cache driver (Redis/Memcached is faster than file cache)
- Ensure mobile and PC are on same local network

### "Invalid room code" on mobile but code is correct
**Possible causes:**
- Room expired (check if more than 1 hour passed)
- Cache was cleared
- Different server/domain

**Solutions:**
- Click "Games" button again on PC to generate new room code
- Verify mobile is accessing same domain as PC
- Check Laravel session/cache configuration

---

## API Endpoints

The following routes are registered:

```php
GET  /game/remote-control          - Mobile controller page
POST /game/generate-room           - Generate new room code
POST /game/verify-room             - Verify room code exists
POST /game/control                 - Send control input (mobile)
GET  /game/controls                - Poll for controls (PC)
```

### Example API Usage

**Generate Room:**
```bash
curl -X POST http://yoursite.com/game/generate-room \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token"

# Response:
# {"success":true,"room_code":"1234"}
```

**Send Control:**
```bash
curl -X POST http://yoursite.com/game/control \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{"room_code":"1234","direction":"up","action":"press"}'

# Response:
# {"success":true,"message":"Control sent"}
```

**Poll Controls:**
```bash
curl "http://yoursite.com/game/controls?room_code=1234&last_timestamp=0"

# Response:
# {"success":true,"controls":[{"direction":"up","action":"press","timestamp":1234567890.123}]}
```

---

## Performance Considerations

### Polling Frequency
- **Default:** 100ms (10 requests/second)
- **Recommended range:** 50ms - 200ms
- **Lower = more responsive but higher server load**

### Cache Driver Recommendations
- **Best:** Redis or Memcached (in-memory, fast)
- **Good:** Database cache (persistent but slower)
- **OK:** File cache (works but not ideal for production)

Update `.env`:
```env
CACHE_DRIVER=redis  # or memcached, file, database
```

### Scaling Considerations
- Each active game room generates ~10 requests/second from PC
- Mobile generates requests only when buttons are pressed
- Use Redis/Memcached for production with multiple concurrent users

---

## Security Considerations

### Rate Limiting (Recommended for Production)

Add to `app/Http/Controllers/GameRoomController.php`:

```php
public function __construct()
{
    $this->middleware('throttle:60,1')->only(['sendControl']);
    $this->middleware('throttle:600,1')->only(['getControls']);
}
```

This limits:
- Control sending to 60 requests per minute per IP
- Control polling to 600 requests per minute per IP (10/second)

### CSRF Protection
All POST endpoints are protected by Laravel's CSRF middleware. The mobile interface includes the CSRF token automatically.

### Room Code Expiry
Room codes automatically expire after 1 hour. Users must click "Games" button again to generate a new code.

---

## Files Created/Modified

### New Files:
- `app/Http/Controllers/GameRoomController.php` - Room and control management
- `resources/views/pages/remote_control.blade.php` - Mobile control interface
- `GAME_REMOTE_CONTROL_SETUP.md` - This installation guide

### Modified Files:
- `routes/web.php` - Added game control routes
- `resources/views/pages/index.blade.php` - Added room code display and polling logic

---

## Advantages of Polling Approach

✅ **No Dependencies:** Works with base Laravel installation
✅ **Simple Deployment:** No WebSocket server to maintain
✅ **Easy Debugging:** Standard HTTP requests visible in browser dev tools
✅ **Firewall Friendly:** Uses standard HTTP (no special ports)
✅ **Scalable:** Can add rate limiting and caching easily
✅ **Reliable:** No WebSocket connection drops or reconnection logic needed

---

## Support

If you encounter any issues:

1. **Check Laravel logs:** `storage/logs/laravel.log`
2. **Check browser console:** Open DevTools (F12) and look for errors
3. **Check network tab:** Verify requests to `/game/controls` are happening every 100ms
4. **Test cache:** Use `php artisan tinker` to test cache operations
5. **Verify routes:** Run `php artisan route:list | grep game`

---

## License

This feature is part of your Laravel application and follows the same license.
