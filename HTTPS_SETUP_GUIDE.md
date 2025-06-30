# HTTPS Configuration for Local Development

## Option 1: Enable HTTPS in XAMPP

1. Open XAMPP Control Panel
2. Click "Config" for Apache
3. Select "httpd-ssl.conf"
4. Find the line with "omni.local" or add a new VirtualHost:

```apache
<VirtualHost *:443>
    DocumentRoot "C:/xampp/htdocs/privatevault"
    ServerName omni.local
    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"
</VirtualHost>
```

5. Restart Apache
6. Access your site via https://omni.local

## Option 2: Use mkcert for Local SSL Certificates

1. Install mkcert: https://github.com/FiloSottile/mkcert
2. Run: `mkcert omni.local`
3. Update your Apache SSL configuration to use the generated certificates

## Option 3: Browser Override (Not Recommended for Production)

For development only, you can disable mixed content warnings in Chrome:
- Visit chrome://flags/#allow-running-insecure-content
- Enable "Allow running insecure content"
- Restart Chrome

## Current Fix Applied

The code has been updated to force HTTPS URLs for all file uploads and downloads using the `getFileUrl()` and `getSecureUrl()` functions in config.php.
