import paramiko

host = '167.86.72.200'
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(host, 22, 'cristian', 'Cristian_5732988$')
    
    commands = [
        "echo 'Cristian_5732988$' | sudo -S chown -R cristian:www-data ~/apps/parkiapp/storage ~/apps/parkiapp/bootstrap/cache",
        "echo 'Cristian_5732988$' | sudo -S chmod -R ug+rwx ~/apps/parkiapp/storage ~/apps/parkiapp/bootstrap/cache",
        "if [ -f ~/apps/EsquinaRedonda_backup/database/database.sqlite ]; then cp ~/apps/EsquinaRedonda_backup/database/database.sqlite ~/apps/parkiapp/database/database.sqlite; else touch ~/apps/parkiapp/database/database.sqlite; fi",
        "echo 'Cristian_5732988$' | sudo -S chown cristian:www-data ~/apps/parkiapp/database ~/apps/parkiapp/database/database.sqlite",
        "echo 'Cristian_5732988$' | sudo -S chmod ug+rwx ~/apps/parkiapp/database ~/apps/parkiapp/database/database.sqlite"
    ]
    
    for cmd in commands:
        client.exec_command(cmd)[1].channel.recv_exit_status()
        
    print("Correcciones aplicadas.")
finally:
    client.close()
