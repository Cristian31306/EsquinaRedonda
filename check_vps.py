import paramiko

host = '167.86.72.200'
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(host, 22, 'cristian', 'Cristian_5732988$')
    
    commands = [
        "pwd && ls -la ~/apps",
        "ls -la ~/apps/parkiapp",
        "cat ~/apps/parkiapp/.env | head -n 10",
        "ls -la /etc/nginx/sites-enabled/"
    ]
    
    for cmd in commands:
        print(f"\n--- {cmd} ---")
        stdin, stdout, stderr = client.exec_command(cmd)
        print(stdout.read().decode(errors='replace').strip())
        print(stderr.read().decode(errors='replace').strip())
        
finally:
    client.close()
