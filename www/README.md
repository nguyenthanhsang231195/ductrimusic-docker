# Duc Tri Music

=== Build Docker ===

1. Install Docker Desktop
2. Build & run:
   cd /devops
   docker-compose up

=== Hotel Proxy ===

1. Install NodeJS

2. Install Proxy:
   npm install -g hotel
   hotel start

hotel add http://127.0.0.1:36001 -n ductrimusic
hotel add http://127.0.0.1:36001 -n www.ductrimusic
hotel add http://127.0.0.1:36003 -n admin.ductrimusic

3. Configuring local domain
   Edit ~/.hotel/conf.json
   {
   "tld": "qsv"
   }

4. System configuration
   Proxy auto-config file: http://localhost:2000/proxy.pac
   OS X: Network Preferences > Advanced > Proxies > Automatic Proxy Configuration
   Windows: Settings > Network and Internet > Proxy > Use setup script
   Linux / Ubuntu: System Settings > Network > Network Proxy > Automatic

5. Open browser
   Website: http://www.ductrimusic.qsv

=== Import database ===
Open phpMyAdmin: http://admin.ductrimusic.qsv
Create database: ductri
Import ductri.sql.gz to database

=== Ductri Administrator ===
Admin: http://www.ductrimusic.qsv/admin
Email: qsvteam@gmail.com
Password: Ductri@2020

Cập nhật yarn theo đường dẫn \www\views -> yarn upgrade -> yarn watch (để sửa file css khi cần thiết) -> yarn release