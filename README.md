# Ip Task
This is my Ip task implementation

Driver: PDO

Interface: REST interface based on json

# Tests
You can find all tests in app/phpunit-unit.xml
The test are not pure unit tests. Instead of mocks, I've added database queries to test queries validity.

# Drivers Description
 - IpV6driver - store data in database, that use BTREE index. Doctrine cant create this index, so, I added SQL code to entity description.

 - IpV4Driver - my BTREE storage implementation. 


