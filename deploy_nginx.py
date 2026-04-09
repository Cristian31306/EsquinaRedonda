import paramiko
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

host = '167.86.72.200'
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(host, 22, 'cristian', 'Cristian_5732988$')
    
    commands = [
        "cd ~/apps/parkiapp && php artisan storage:link",
        "cd ~/apps/parkiapp && chmod -R 775 storage bootstrap/cache"
    ]
    
    for cmd in commands:
        stdin, stdout, stderr = client.exec_command(cmd)
        stdout.channel.recv_exit_status()
    
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
    cmd_nginx = f"echo '{nginx_config}' > /tmp/parkiapp.algorah.bond && echo 'Cristian_5732988$' | sudo -S mv /tmp/parkiapp.algorah.bond /etc/nginx/sites-available/parkiapp.algorah.bond && echo 'Cristian_5732988$' | sudo -S ln -sf /etc/nginx/sites-available/parkiapp.algorah.bond /etc/nginx/sites-enabled/ && echo 'Cristian_5732988$' | sudo -S systemctl reload nginx"
    client.exec_command(cmd_nginx)[1].channel.recv_exit_status()
    
    # Certbot
    cmd_certbot = "echo 'Cristian_5732988$' | sudo -S certbot --nginx -d parkiapp.algorah.bond --non-interactive --agree-tos -m cristian@algorah.bond"
    client.exec_command(cmd_certbot)[1].channel.recv_exit_status()
    
    print("Done!")
finally:
    client.close()
