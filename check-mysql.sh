#!/bin/bash
echo "=== MySQL/MariaDB Status Check ==="
echo ""
echo "1. Checking if MySQL service is running:"
if command -v systemctl &> /dev/null; then
    systemctl is-active mysql 2>/dev/null || systemctl is-active mariadb 2>/dev/null || echo "   ❌ Not running via systemctl"
else
    service mysql status 2>/dev/null || service mariadb status >/dev/null || echo "   ❌ Not running via service"
fi
echo ""
echo "2. Checking if port 3306 is listening:"
ss -tlnp | grep 3306 || netstat -tlnp 2>/dev/null | grep 3306 || echo "   ❌ Port 3306 not listening"
echo ""
echo "3. Trying direct CLI connection:"
mysql -u root -p'' -h 127.0.0.1 -P 3306 -e "SELECT 'CLI OK' AS status; SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='restaurant';" 2>&1 || echo "   ❌ CLI connection failed"
echo ""
echo "4. Checking MySQL socket (if using localhost):"
ls -la /run/mysqld/mysqld.sock /var/run/mysqld/mysqld.sock /tmp/mysql.sock 2>/dev/null || echo "   ❌ No common socket files found"
