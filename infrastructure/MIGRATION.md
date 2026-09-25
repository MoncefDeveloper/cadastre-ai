# Cadastre AI — 10-Minute Production Migration Playbook

This playbook documents the exact procedure to migrate Cadastre AI, its MySQL 8 database, persistent storage assets, and Coolify control panel from the evaluation VPS to a permanent production server in under 10 minutes.

---

## Architecture Summary
* **Source Host:** OVHcloud VPS-1 (141.94.245.4) — Ubuntu 24.04 LTS
* **Target Host:** Clean Ubuntu 24.04 LTS (x86_64)
* **Applications:** Cadastre AI (Laravel 13 / Filament v5 / PHP 8.4-FPM), MySQL 8, Global File Browser
* **Routing & SSL:** Traefik v3 Reverse Proxy with automated Let's Encrypt certificates

---

## Migration Timeline at a Glance

```text
┌────────────────────────────────────────────────────────┐
│ T-24 Hours: Drop DNS TTL to 300 seconds in cPanel      │
├────────────────────────────────────────────────────────┤
│ T-0: Step A: Extract Data Payloads from Current Server │
│   ├── Coolify MySQL ➔ Download cadastre_ai.sql.gz      │
│   ├── File Browser  ➔ Download storage.zip             │
│   └── Coolify App   ➔ Copy production .env (APP_KEY)   │
├────────────────────────────────────────────────────────┤
│ Step B: Provision New Server via bootstrap.sh          │
│   └── Unattended script runs in ~2.5 minutes           │
├────────────────────────────────────────────────────────┤
│ Step C: Restore Services in Coolify                    │
│   ├── Deploy MySQL ➔ Import cadastre_ai.sql.gz         │
│   ├── Deploy Cadastre AI ➔ Mount volume & set Port 80  │
│   └── Deploy File Browser ➔ Upload & unpack storage.zip│
├────────────────────────────────────────────────────────┤
│ Step D: DNS Cutover & Port 8000 Lockdown               │
│   ├── Point A-Records to New IP (propagates in ~5 min) │
│   └── Run: sudo ufw delete allow 8000/tcp              │
└────────────────────────────────────────────────────────┘
```

---

## Phase 1: Pre-Migration Preparation (T-24 Hours)

To prevent split-brain traffic where visitors hit the old server while DNS caches update, lower your TTL 24 hours prior:

1. Log into **Namecheap cPanel** → **Zone Editor**.
2. Locate the following A-records for `moncefdev.me`:
   * `cadastre-app`
   * `panel`
   * `filemanager`
3. Edit each record: change **TTL** from `14400` (4 hours) to **`300`** (5 minutes).
4. Save all records.

---

## Phase 2: Migration Day Execution (T-0)

### Part A: Extract Data Payloads (~2 Minutes)

1. **Database Snapshot:**
   * Go to `https://panel.moncefdev.me` → **My first project** → **production** → **mysql**.
   * Navigate to **Backups** → click **Back Up Now**.
   * In the **Executions** list, click the download icon to save `cadastre_ai.sql.gz` to your computer.

2. **Storage Volume Archive:**
   * Open `https://filemanager.moncefdev.me`.
   * Navigate into the persistent storage volume directory (`cadastre-storage`).
   * Select `public` and `private`.
   * Click **Download (Zip)** to save `storage.zip` to your computer.

3. **Preserve Environment Secrets:**
   * Go to Coolify → **Cadastre AI** → **Environment Variables** → switch to **Developer View**.
   * Copy the entire `.env` content to a local text file.
   * *Critical:* The new server must retain the identical `APP_KEY` to prevent Laravel decryption exceptions (`DecryptException`).

---

### Part B: Provision the New Server (~3 Minutes)

1. Provision the new Ubuntu 24.04 VPS and obtain its static IPv4 address (`<NEW_SERVER_IP>`).
2. Ensure your local public SSH key (`id_ed25519.pub`) is registered on the new machine during creation.
3. SSH into the new server as `root`:
   ```bash
   ssh root@<NEW_SERVER_IP>
   ```
4. Run the automated bootstrap script:
   ```bash
   curl -fsSL https://raw.githubusercontent.com/MoncefDeveloper/cadastre-ai/main/infrastructure/bootstrap.sh | bash
   ```
5. Wait until it prints: `🎉 SERVER BOOTSTRAP COMPLETE!`.

---

### Part C: Restore Services in Coolify (~3 Minutes)

1. Open your browser and navigate to: `http://<NEW_SERVER_IP>:8000`.
2. Register your administrative account and click **This machine**.
3. **Restore MySQL 8:**
   * Create a new database resource: **MySQL** (Name: `mysql`, Image: `mysql:8`).
   * Set **Initial Database** to `cadastre_ai`.
   * Note the newly generated **Normal user password** (you will need this for `.env`).
   * Click **Save** → click **Start**.
   * Go to the **Import Backup** tab → upload `cadastre_ai.sql.gz`.
4. **Deploy Cadastre AI (Creates the Volume Target):**
   * Click **+ New** → **Application** → **Public Repository**.
   * Repo: `MoncefDeveloper/cadastre-ai`, Branch: `main`, Build Pack: `Dockerfile`.
   * Set Domain: `https://cadastre-app.moncefdev.me`.
   * **Crucial Port Setting:** Under **Networking**, ensure **Ports exposes** is set to **`80`** (do not leave as 3000).
   * **Attach Volume:** Under **Persistent Storage**, add:
     * Name: `cadastre-storage`
     * Mount Path: `/var/www/html/storage/app`
   * **Inject Secrets:** Under **Environment Variables**, switch to **Developer View**:
     * Paste your saved `.env` block.
     * Update `DB_PASSWORD` to match the new MySQL password generated in Step 3.
     * Verify `DB_HOST` points to your new MySQL container name.
   * Click **Deploy**.
5. **Restore Storage Files:**
   * Open File Manager.
   * Upload `storage.zip` into the `cadastre-storage` volume.
   * Unpack the zip file so `/var/www/html/storage/app/public` and `private` are restored.

---

### Part D: DNS Cutover & Security Lockdown (~2 Minutes)

1. In Namecheap cPanel **Zone Editor**, update the target IP of your A-records to `<NEW_SERVER_IP>`:
   * `cadastre-app.moncefdev.me` → `<NEW_SERVER_IP>`
   * `panel.moncefdev.me` → `<NEW_SERVER_IP>`
   * `filemanager.moncefdev.me` → `<NEW_SERVER_IP>`
2. Because TTL was set to 300, traffic transitions globally within **5 minutes**.
3. Traefik automatically receives incoming requests on port 443 and issues new Let's Encrypt SSL certificates.
4. SSH into the new server as `deployer` and close the setup port:
   ```bash
   sudo ufw delete allow 8000/tcp
   ```

---

## Rollback Plan (Zero Risk)

If the new server encounters unforeseen hardware issues during setup, your old server at `141.94.245.4` remains running untouched. Simply revert the Namecheap A-records back to `141.94.245.4` and all traffic returns to the original host within 5 minutes.
