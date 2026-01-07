# Pacman Mobile Remote Control - Installation Guide

This guide will help you set up the mobile remote control feature for the Pacman game.

## Prerequisites
- PHP >= 7.3
- Composer installed
- Laravel application running
- Access to server terminal

---

## Step 1: Install Required Packages

Run the following command on your server:

```bash
composer require beyondcode/laravel-websockets pusher/pusher-php-server laravel/echo
```

This installs:
- `beyondcode/laravel-websockets` - WebSocket server for Laravel
- `pusher/pusher-php-server` - Pusher protocol implementation
- `laravel/echo` - Client-side broadcasting (if not already installed)

---

## Step 2: Publish Configuration Files

Publish the WebSocket configuration:

```bash
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

Run the migration:

```bash
php artisan migrate
```

---

## Step 3: Update Environment Variables

Add/update the following in your `.env` file:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_APP_CLUSTER=mt1

# WebSocket Settings
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

**Important:** Replace `127.0.0.1` with your actual server IP or domain if accessing from different devices.

---

## Step 4: Update Broadcasting Configuration

Edit `config/broadcasting.php`:

Find the `pusher` connection and update it:

```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'host' => env('PUSHER_HOST', '127.0.0.1'),
        'port' => env('PUSHER_PORT', 6001),
        'scheme' => env('PUSHER_SCHEME', 'http'),
        'encrypted' => false,
        'useTLS' => false,
    ],
],
```

---

## Step 5: Update WebSocket Configuration

Edit `config/websockets.php`:

Update the apps section:

```php
'apps' => [
    [
        'id' => env('PUSHER_APP_ID'),
        'name' => env('APP_NAME'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'path' => env('PUSHER_APP_PATH'),
        'capacity' => null,
        'enable_client_messages' => true,
        'enable_statistics' => true,
    ],
],
```

Set allowed origins (for CORS):

```php
'allowed_origins' => [
    '*', // Allow all origins (change to your domain in production)
],
```

---

## Step 6: Install JavaScript Dependencies

Add Laravel Echo and Pusher.js to your `resources/views/site_app.blade.php` **before the closing `</body>` tag**:

```html
<!-- Add before closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.min.js"></script>
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>

<script>
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        wsHost: '{{ env("PUSHER_HOST") }}',
        wsPort: {{ env("PUSHER_PORT", 6001) }},
        wssPort: {{ env("PUSHER_PORT", 6001) }},
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
</script>
```

**Alternative:** If using npm/yarn, install via package manager:

```bash
npm install --save laravel-echo pusher-js
# or
yarn add laravel-echo pusher-js
```

Then add to your `resources/js/app.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    wsHost: process.env.MIX_PUSHER_HOST,
    wsPort: process.env.MIX_PUSHER_PORT ?? 6001,
    wssPort: process.env.MIX_PUSHER_PORT ?? 6001,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});
```

---

## Step 7: Clear Cache

Clear all caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Step 8: Start WebSocket Server

Start the WebSocket server:

```bash
php artisan websockets:serve
```

This should output:
```
Starting the WebSocket server on port 6001...
```

**For Production:** Use a process manager like Supervisor to keep the WebSocket server running.

### Supervisor Configuration Example:

Create `/etc/supervisor/conf.d/websockets.conf`:

```ini
[program:websockets]
command=php /path/to/your/project/artisan websockets:serve
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/websockets.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start websockets
```

---

## Step 9: Test the Feature

1. **On PC/Desktop:**
   - Open your homepage: `http://yoursite.com`
   - Click the "Games" button to show the Pacman game
   - You should see a 4-digit room code displayed above the game

2. **On Mobile:**
   - Open `http://yoursite.com/game/remote-control` on your mobile device
   - Enter the 4-digit room code shown on the PC
   - Click "Connect"
   - Use the D-pad buttons to control the game on the PC

---

## Troubleshooting

### WebSocket server not starting
- Check if port 6001 is available: `netstat -tulpn | grep 6001`
- Make sure no firewall is blocking port 6001
- Try a different port in `.env` and `config/websockets.php`

### Cannot connect from mobile
- Ensure mobile and PC are on the same network (or use public IP)
- Update `PUSHER_HOST` in `.env` to your server's IP or domain
- Check CORS settings in `config/websockets.php`

### Controls not working
- Check browser console for JavaScript errors
- Verify Laravel Echo is loaded (check Network tab)
- Ensure the iframe allows keyboard events (sandbox attributes)

### "Invalid room code" error
- Verify cache is working: `php artisan cache:clear`
- Check Laravel logs: `storage/logs/laravel.log`
- Ensure room code is generated when clicking "Games" button

---

## WebSocket Dashboard (Optional)

Access the WebSocket dashboard to monitor connections:

```
http://yoursite.com/laravel-websockets
```

---

## Security Considerations

### For Production:

1. **Use HTTPS/WSS:**
   - Update `PUSHER_SCHEME=https` in `.env`
   - Configure SSL for WebSocket server

2. **Restrict Origins:**
   - Update `allowed_origins` in `config/websockets.php` to your domain only

3. **Rate Limiting:**
   - Add rate limiting to game control routes to prevent abuse

4. **Room Code Expiry:**
   - Room codes automatically expire after 1 hour (configured in Cache)

---

## Files Created/Modified

### New Files:
- `app/Events/GameControlEvent.php` - WebSocket event for game controls
- `app/Http/Controllers/GameRoomController.php` - Controller for room management
- `resources/views/pages/remote_control.blade.php` - Mobile control interface
- `GAME_REMOTE_CONTROL_SETUP.md` - This installation guide

### Modified Files:
- `routes/web.php` - Added game control routes
- `resources/views/pages/index.blade.php` - Added room code display and WebSocket listener

---

## How It Works

1. **Room Code Generation:**
   - When user clicks "Games" button, a 4-digit room code is generated
   - Room code is stored in Laravel cache for 1 hour
   - Room code is displayed above the Pacman game

2. **Mobile Connection:**
   - Mobile user enters room code on `/game/remote-control`
   - System verifies room code exists
   - Mobile connects to WebSocket channel: `game-room.{code}`

3. **Control Flow:**
   - Mobile user presses D-pad button
   - POST request sent to `/game/control` with direction and action
   - Server broadcasts event to WebSocket channel
   - PC browser receives event via Laravel Echo
   - JavaScript simulates keyboard event for Pacman iframe
   - Game responds to the control input

---

## Support

If you encounter any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. WebSocket logs: Check supervisor logs or console output
3. Browser console: Look for JavaScript errors
4. Network tab: Verify WebSocket connection established

---

## License

This feature is part of your Laravel application and follows the same license.
