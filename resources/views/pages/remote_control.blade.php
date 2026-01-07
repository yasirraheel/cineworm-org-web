<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pacman Remote Control</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        .container {
            width: 100%;
            max-width: 500px;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .connection-panel {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: bold;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .connect-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .connect-btn:active {
            transform: scale(0.95);
        }

        .connect-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status-message {
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
        }

        .status-message.success {
            background: #d4edda;
            color: #155724;
        }

        .status-message.error {
            background: #f8d7da;
            color: #721c24;
        }

        .controller {
            display: none;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .controller.active {
            display: block;
        }

        .connection-info {
            text-align: center;
            margin-bottom: 30px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 10px;
        }

        .connection-info .room-code {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 4px;
        }

        .connection-info .status {
            margin-top: 10px;
            color: #28a745;
            font-weight: bold;
        }

        .dpad-container {
            width: 280px;
            height: 280px;
            margin: 0 auto;
            position: relative;
        }

        .dpad-button {
            position: absolute;
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-size: 36px;
            cursor: pointer;
            transition: all 0.1s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            user-select: none;
        }

        .dpad-button:active {
            transform: scale(0.9);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .dpad-button.up {
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 15px 15px 5px 5px;
        }

        .dpad-button.down {
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px 5px 15px 15px;
        }

        .dpad-button.left {
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 15px 5px 5px 15px;
        }

        .dpad-button.right {
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 5px 15px 15px 5px;
        }

        .dpad-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90px;
            height: 90px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.1);
        }

        .disconnect-btn {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .disconnect-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Pacman Remote Control</h1>

        <!-- Connection Panel -->
        <div class="connection-panel" id="connectionPanel">
            <div class="input-group">
                <label for="roomCode">Enter Room Code</label>
                <input type="text" id="roomCode" maxlength="4" placeholder="0000" inputmode="numeric">
            </div>
            <button class="connect-btn" id="connectBtn">Connect</button>
            <div id="statusMessage"></div>
        </div>

        <!-- Controller Panel -->
        <div class="controller" id="controllerPanel">
            <div class="connection-info">
                <div>Connected to Room:</div>
                <div class="room-code" id="connectedRoom">0000</div>
                <div class="status">● Connected</div>
            </div>

            <div class="dpad-container">
                <button class="dpad-button up" data-direction="up">▲</button>
                <button class="dpad-button down" data-direction="down">▼</button>
                <button class="dpad-button left" data-direction="left">◄</button>
                <button class="dpad-button right" data-direction="right">►</button>
                <div class="dpad-center">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 24 24'%3E%3Cpath fill='%23667eea' d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z'/%3E%3C/svg%3E" alt="Center">
                </div>
            </div>

            <button class="disconnect-btn" id="disconnectBtn">Disconnect</button>
        </div>
    </div>

    <script>
        let currentRoomCode = null;
        let pressedButtons = new Set();

        const roomCodeInput = document.getElementById('roomCode');
        const connectBtn = document.getElementById('connectBtn');
        const statusMessage = document.getElementById('statusMessage');
        const connectionPanel = document.getElementById('connectionPanel');
        const controllerPanel = document.getElementById('controllerPanel');
        const connectedRoom = document.getElementById('connectedRoom');
        const disconnectBtn = document.getElementById('disconnectBtn');
        const dpadButtons = document.querySelectorAll('.dpad-button');

        // Auto-focus on room code input
        roomCodeInput.focus();

        // Format input to only accept numbers
        roomCodeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Connect on Enter key
        roomCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 4) {
                connectBtn.click();
            }
        });

        // Connect button handler
        connectBtn.addEventListener('click', async function() {
            const roomCode = roomCodeInput.value.trim();

            if (roomCode.length !== 4) {
                showStatus('Please enter a 4-digit room code', 'error');
                return;
            }

            connectBtn.disabled = true;
            connectBtn.textContent = 'Connecting...';

            try {
                const response = await fetch('{{ route("game.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ room_code: roomCode })
                });

                const data = await response.json();

                if (data.success) {
                    currentRoomCode = roomCode;
                    connectedRoom.textContent = roomCode;
                    connectionPanel.style.display = 'none';
                    controllerPanel.classList.add('active');
                    // Prevent screen from sleeping
                    if ('wakeLock' in navigator) {
                        try {
                            await navigator.wakeLock.request('screen');
                        } catch (err) {
                            console.log('Wake lock not available');
                        }
                    }
                } else {
                    showStatus('Invalid room code. Please check and try again.', 'error');
                }
            } catch (error) {
                console.error('Connection error:', error);
                showStatus('Connection failed. Please try again.', 'error');
            } finally {
                connectBtn.disabled = false;
                connectBtn.textContent = 'Connect';
            }
        });

        // Disconnect button handler
        disconnectBtn.addEventListener('click', function() {
            currentRoomCode = null;
            roomCodeInput.value = '';
            controllerPanel.classList.remove('active');
            connectionPanel.style.display = 'block';
            statusMessage.innerHTML = '';
            roomCodeInput.focus();
        });

        // D-pad button handlers
        dpadButtons.forEach(button => {
            const direction = button.dataset.direction;

            // Touch events for mobile
            button.addEventListener('touchstart', function(e) {
                e.preventDefault();
                handleButtonPress(direction);
            });

            button.addEventListener('touchend', function(e) {
                e.preventDefault();
                handleButtonRelease(direction);
            });

            // Mouse events for desktop testing
            button.addEventListener('mousedown', function(e) {
                e.preventDefault();
                handleButtonPress(direction);
            });

            button.addEventListener('mouseup', function(e) {
                e.preventDefault();
                handleButtonRelease(direction);
            });

            // Prevent dragging
            button.addEventListener('touchmove', function(e) {
                e.preventDefault();
            });
        });

        async function handleButtonPress(direction) {
            if (pressedButtons.has(direction)) return;
            pressedButtons.add(direction);

            await sendControl(direction, 'press');
        }

        async function handleButtonRelease(direction) {
            if (!pressedButtons.has(direction)) return;
            pressedButtons.delete(direction);

            await sendControl(direction, 'release');
        }

        async function sendControl(direction, action) {
            if (!currentRoomCode) return;

            try {
                await fetch('{{ route("game.control") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        room_code: currentRoomCode,
                        direction: direction,
                        action: action
                    })
                });
            } catch (error) {
                console.error('Control send error:', error);
            }
        }

        function showStatus(message, type) {
            statusMessage.textContent = message;
            statusMessage.className = 'status-message ' + type;
            setTimeout(() => {
                statusMessage.textContent = '';
                statusMessage.className = 'status-message';
            }, 3000);
        }
    </script>
</body>
</html>
