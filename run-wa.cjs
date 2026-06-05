const { create, ev } = require('@open-wa/wa-automate');
const fs = require('fs');
const express = require('express');
const path = require('path');

// Session folders are persisted to reuse the authenticated state

// Setup public/qr.html initially
const qrHtmlPath = path.join(__dirname, 'public', 'qr.html');
if (!fs.existsSync(path.join(__dirname, 'public'))) {
  fs.mkdirSync(path.join(__dirname, 'public'), { recursive: true });
}
fs.writeFileSync(qrHtmlPath, `
<!DOCTYPE html>
<html>
<head>
  <title>Scan WhatsApp QR Code</title>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #0f172a; color: white; }
    .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 20px auto; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  </style>
</head>
<body>
  <h1>Initializing WhatsApp Web...</h1>
  <div class="loader"></div>
  <p>Please wait while the browser launches and generates the QR code.</p>
</body>
</html>
`);

// Register QR event listener before create()
ev.on('qr.session', async (qrcode) => {
  console.log("QR Code generated!");
  // Remove space bug in case it exists in raw output
  const cleanUri = qrcode.replace(/\s+/g, '');
  const htmlContent = `
  <!DOCTYPE html>
  <html>
  <head>
    <title>Scan WhatsApp QR Code</title>
    <style>
      body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #0f172a; color: white; }
      img { border: 10px solid white; border-radius: 10px; margin: 20px; max-width: 300px; }
      .instructions { font-size: 1.1em; color: #cbd5e1; }
    </style>
    <script>
      // Reload page every 15 seconds to ensure we show the latest QR code if it changes
      setTimeout(() => { window.location.reload(); }, 15000);
    </script>
  </head>
  <body>
    <h1>Scan this QR code with WhatsApp</h1>
    <img src="${cleanUri}" />
    <p class="instructions">Open WhatsApp on your phone &gt; Settings &gt; Linked Devices &gt; Link a Device</p>
    <p style="color: #64748b; font-size: 0.8em;">This page will auto-refresh if the QR code updates.</p>
  </body>
  </html>
  `;
  fs.writeFileSync(qrHtmlPath, htmlContent);
});

// Start the client with clean parameters
create({
  sessionId: "session",
  useChrome: true,
  headless: true, // headless is fine since we capture the QR code and render it to public/qr.html
  qrTimeout: 0,
  authTimeout: 0,
  timeout: 120000, // wait 2 minutes for slow systems
  customUserAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
  chromiumArgs: [
    '--no-sandbox',
    '--disable-setuid-sandbox'
  ]
}).then(client => {
  console.log("WhatsApp Web Client successfully initialized!");
  // Update public/qr.html to success message
  fs.writeFileSync(qrHtmlPath, `
  <!DOCTYPE html>
  <html>
  <head>
    <title>WhatsApp Connected</title>
    <style>
      body { font-family: sans-serif; text-align: center; padding: 100px; background-color: #0f172a; color: #10b981; }
    </style>
  </head>
  <body>
    <h1>✓ WhatsApp Connected Successfully!</h1>
    <p style="color: white;">You can close this tab now. The outreach pipeline is running.</p>
  </body>
  </html>
  `);
  
  // Start the REST API server on port 8080
  const app = express();
  app.use(express.json({ limit: '99mb' }));
  app.use('/api', client.middleware(false, 8080));
  app.listen(8080, () => {
    console.log("WhatsApp REST API server is running on port 8080");
  });
}).catch(err => {
  console.error("Error creating WhatsApp client:", err);
  fs.writeFileSync(qrHtmlPath, `
  <!DOCTYPE html>
  <html>
  <head>
    <title>WhatsApp Error</title>
    <style>
      body { font-family: sans-serif; text-align: center; padding: 100px; background-color: #0f172a; color: #ef4444; }
    </style>
  </head>
  <body>
    <h1>✗ WhatsApp Initialization Failed</h1>
    <pre style="color: white; text-align: left; max-width: 600px; margin: auto; background: #1e293b; padding: 15px; border-radius: 8px; overflow-x: auto;">${err.message || err}</pre>
  </body>
  </html>
  `);
});
