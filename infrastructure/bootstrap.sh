#!/usr/bin/env bash
# ==============================================================================
# Cadastre AI — Production Host Bootstrap Script (Battle-Tested)
# Target: Ubuntu 24.04 LTS (x86_64)
# Executes: Hardening, 4GB Swap, Deployer Key Auth, UFW, and Coolify v4
# ==============================================================================
set -euo pipefail

echo "========================================================"
echo "🚀 [1/5] Updating Base System & Timezone..."
echo "========================================================"
export DEBIAN_FRONTEND=noninteractive
timedatectl set-timezone UTC
apt-get update -y && apt-get upgrade -y
apt-get install -y --no-install-recommends \
    curl \
    wget \
    git \
    ufw \
    tar \
    gzip \
    ca-certificates

echo "========================================================"
echo "🧠 [2/5] Creating 4GB NVMe Swapfile & Tuning Kernel..."
echo "========================================================"
if [ ! -f /swapfile ]; then
    fallocate -l 4G /swapfile || dd if=/dev/zero of=/swapfile bs=1M count=4096
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab

    # Persistent sysctl tuning in dedicated file
    cat <<EOF > /etc/sysctl.d/99-swap.conf
vm.swappiness=10
vm.vfs_cache_pressure=50
EOF
    sysctl --system
    echo "✅ 4GB Swapfile configured and active."
else
    echo "ℹ️  Swapfile already exists. Skipping."
fi

echo "========================================================"
echo "👤 [3/5] Creating Deployer User & Hardening OpenSSH..."
echo "========================================================"
if ! id "deployer" &>/dev/null; then
    adduser --gecos "" --disabled-password deployer
    usermod -aG sudo deployer

    # Passwordless sudo for key-authenticated deployer
    echo "deployer ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/deployer
    chmod 0440 /etc/sudoers.d/deployer

    # Inherit authorized SSH keys from root
    mkdir -p /home/deployer/.ssh
    if [ -f /root/.ssh/authorized_keys ]; then
        cp /root/.ssh/authorized_keys /home/deployer/.ssh/
    fi
    chown -R deployer:deployer /home/deployer/.ssh
    chmod 700 /home/deployer/.ssh
    chmod 600 /home/deployer/.ssh/authorized_keys 2>/dev/null || true
    echo "✅ User 'deployer' created with SSH key access & passwordless sudo."
fi

# Enforce key-only login with priority prefix 01
cat <<EOF > /etc/ssh/sshd_config.d/01-hardening.conf
PermitRootLogin prohibit-password
PasswordAuthentication no
PubkeyAuthentication yes
MaxAuthTries 3
EOF
systemctl restart ssh
echo "✅ SSH hardened (password authentication disabled)."

echo "========================================================"
echo "🛡️  [4/5] Enforcing UFW Firewall Rules..."
echo "========================================================"
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp comment 'SSH Key Access'
ufw allow 80/tcp comment 'Traefik HTTP'
ufw allow 443/tcp comment 'Traefik HTTPS'
ufw allow 8000/tcp comment 'Coolify Initial Setup (Close after panel SSL)'
ufw --force enable
echo "✅ UFW Firewall active (ports 22, 80, 443, 8000)."

echo "========================================================"
echo "⚡ [5/5] Installing Coolify Engine (Includes Docker)..."
echo "========================================================"
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -q "coolify"; then
    curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
    usermod -aG docker deployer 2>/dev/null || true
    echo "✅ Coolify engine installed."
else
    echo "ℹ️  Coolify is already running."
fi

echo "========================================================"
echo "🎉 SERVER BOOTSTRAP COMPLETE!"
echo "Access Coolify at: http://$(curl -s https://ifconfig.me):8000"
echo "========================================================"
