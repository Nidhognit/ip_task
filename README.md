# Ip Task
This is my Ip task implementation

Driver: PDO

Interface: REST interface based on json, CLI interface

# CLI interface
Add new:
```console
app/console ip:add {ip_address}
```

Get Ip Count

```console
app/console ip:query {ip_address}
```

# REST Interface
 - POST: /ip/add
 - GET:  /ip/query

# Tests
You can find all tests in app/phpunit-unit.xml
The test are not pure unit tests. Instead of mocks, I've added database queries to test queries validity.

# Drivers Description
 - IpV6driver - store data in database, that use BTREE index. Doctrine cant create this index, so, I added SQL code to entity description.

 - IpV4Driver - my BTREE storage implementation. 


