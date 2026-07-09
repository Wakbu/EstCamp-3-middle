# EST Wargame Lab

Wargame assignment template served by Apache2. The site includes challenge cards, hints, flag submission, local progress storage, and small server-side PHP APIs for validation.

## Apache Deployment

Place these files under `/var/www/html`:

- index.html
- styles.css
- app.js
- robots.txt
- hidden-est-vault.html
- api/submit.php
- api/cookie.php
- api/login.php

Enable Apache and PHP:

```bash
sudo apt-get install -y apache2 php libapache2-mod-php
sudo systemctl enable --now apache2
```

## Challenges

- Welcome Source: hidden HTML comment
- Hidden Path: crawler exclusion path
- Cookie Role: client-controlled role value
- Decode Me: Base64-style encoded text
- Admin Login: weak condition-based login check

## Flags

Current flags are temporary `FLAG{sample_...}` values. Replace them in `index.html`, `hidden-est-vault.html`, and the PHP files under `api/` when the official flags are provided.
