import paramiko
import sys

host = '167.86.72.200'
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(host, 22, 'cristian', 'Cristian_5732988$')
    
    commands = [
        "cp ~/apps/EsquinaRedonda_backup/.env ~/apps/parkiapp/.env",
        "sed -i 's/APP_URL=.*/APP_URL=https:\\/\\/parkiapp.algorah.bond/' ~/apps/parkiapp/.env",
        "cat ~/apps/parkiapp/.env | grep APP_URL",
        "cd ~/apps/parkiapp && composer install --no-interaction --prefer-dist --optimize-autoloader",
        "cd ~/apps/parkiapp && npm install",
        "cd ~/apps/parkiapp && npm run build",
        "cd ~/apps/parkiapp && php artisan storage:link",
        "cd ~/apps/parkiapp && chmod -R 775 storage bootstrap/cache"
    ]
    
    for cmd in commands:
        print(f"\n--- {cmd} ---")
        stdin, stdout, stderr = client.exec_command(cmd)
        exit_status = stdout.channel.recv_exit_status()
        out = stdout.read().decode(errors='replace').strip()
        err = stderr.read().decode(errors='replace').strip()
        
        if out: print(out)
        if err: print("ERR:", err)
        print(f"Status: {exit_status}")

    # Nginx config
    nginx_config = """
server {
    listen 80;
    server_name parkiapp.algorah.bond;
    root /home/cristian/apps/parkiapp/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
"""
    cmd_nginx = f"""echo '{nginx_config}' > /tmp/parkiapp.algorah.bond && echo "Cristian_5732988$" | sudo -S mv /tmp/parkiapp.algorah.bond /etc/nginx/sites-available/parkiapp.algorah.bond && echo "Cristian_5732988$" | sudo -S ln -sf /etc/nginx/sites-available/parkiapp.algorah.bond /etc/nginx/sites-enabled/ && echo "Cristian_5732988$" | sudo -S systemctl reload nginx"""
    print("\n--- Configuring Nginx ---")
    stdin, stdout, stderr = client.exec_command(cmd_nginx)
    exit_status = stdout.channel.recv_exit_status()
    print("Nginx out:", stdout.read().decode(errors='replace').strip())
    print("Nginx err:", stderr.read().decode(errors='replace').strip())
    
    # Try SSL with Certbot
    cmd_certbot = """echo "Cristian_5732988$" | sudo -S certbot --nginx -d parkiapp.algorah.bond --non-interactive --agree-tos -m cristian@algorah.bond"""
    print("\n--- Running Certbot ---")
    stdin, stdout, stderr = client.exec_command(cmd_certbot)
    exit_status = stdout.channel.recv_exit_status()
    print("Certbot out:", stdout.read().decode(errors='replace').strip())
    print("Certbot err:", stderr.read().decode(errors='replace').strip())
        
finally:
    client.close()
