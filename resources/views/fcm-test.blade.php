<!DOCTYPE html>
<html>
<head>
    <title>FCM Token Generator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.23.0/firebase-app-compat.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.23.0/firebase-messaging-compat.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s;
            width: 100%;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .token-display {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-family: monospace;
            word-break: break-all;
            line-height: 1.4;
        }
        .success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .copy-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 14px;
        }
        .copy-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔥 FCM Token Generator</h1>

        <div class="info">
            <strong>Setup Instructions:</strong><br>
            1. Create a Firebase project at <a href="https://console.firebase.google.com/" target="_blank">Firebase Console</a><br>
            2. Go to Project Settings → General → Your apps → Web app<br>
            3. Copy your Firebase config object<br>
            4. Enable Cloud Messaging in Firebase Console<br>
            5. Generate a VAPID key in Cloud Messaging settings
        </div>

        <div class="form-group">
            <label for="apiKey">API Key:</label>
            <input type="text" id="apiKey" placeholder="Enter your Firebase API Key">
        </div>

        <div class="form-group">
            <label for="authDomain">Auth Domain:</label>
            <input type="text" id="authDomain" placeholder="your-project.firebaseapp.com">
        </div>

        <div class="form-group">
            <label for="projectId">Project ID:</label>
            <input type="text" id="projectId" placeholder="your-project-id">
        </div>

        <div class="form-group">
            <label for="messagingSenderId">Messaging Sender ID:</label>
            <input type="text" id="messagingSenderId" placeholder="123456789">
        </div>

        <div class="form-group">
            <label for="appId">App ID:</label>
            <input type="text" id="appId" placeholder="1:123456789:web:abcdef">
        </div>

        <div class="form-group">
            <label for="vapidKey">VAPID Key:</label>
            <input type="text" id="vapidKey" placeholder="Your VAPID Key">
        </div>

        <button onclick="generateToken()">Generate FCM Token</button>

        <div id="result"></div>
    </div>

    <script>
        let messaging;

        function generateToken() {
            const config = {
                apiKey: document.getElementById('apiKey').value,
                authDomain: document.getElementById('authDomain').value,
                projectId: document.getElementById('projectId').value,
                messagingSenderId: document.getElementById('messagingSenderId').value,
                appId: document.getElementById('appId').value
            };

            const vapidKey = document.getElementById('vapidKey').value;

            if (!config.apiKey || !config.projectId || !vapidKey) {
                showResult('Please fill in all required fields', 'error');
                return;
            }

            try {
                // Initialize Firebase
                firebase.initializeApp(config);
                messaging = firebase.messaging();

                // Request permission and get token
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        return messaging.getToken({ vapidKey: vapidKey });
                    } else {
                        throw new Error('Notification permission denied');
                    }
                }).then((token) => {
                    if (token) {
                        showResult(token, 'success');
                    } else {
                        showResult('No registration token available.', 'error');
                    }
                }).catch((err) => {
                    showResult('Error: ' + err.message, 'error');
                });

            } catch (error) {
                showResult('Error: ' + error.message, 'error');
            }
        }

        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            const isToken = type === 'success' && message.length > 50;

            resultDiv.innerHTML = `
                <div class="token-display ${type}">
                    ${isToken ? '<strong>Your FCM Token:</strong><br>' : ''}
                    ${message}
                    ${isToken ? '<br><button class="copy-btn" onclick="copyToken(\'' + message + '\')">Copy Token</button>' : ''}
                </div>
            `;
        }

        function copyToken(token) {
            navigator.clipboard.writeText(token).then(() => {
                alert('Token copied to clipboard!');
            });
        }

        // Register service worker for FCM
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    console.log('Service Worker registered');
                }).catch((err) => {
                    console.log('Service Worker registration failed');
                });
        }
    </script>
</body>
</html>