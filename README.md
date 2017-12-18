# Ip Task
This is my realization of Ip task

Driver: PDO

Interface: REST interface based on json

# Tests
You can find all tests in app/phpunit-unit.xml
These are not pure unit tests, I left queries to the database to test their validity

# Drivers Explain
 - IpV6driver - store data in database, that use BTREE index. Doctrine cant create this index, so, I added SQL code to entity description.

 - IpV4Driver - use my realization of BTREE. 
In this case, I store full tree in database.
Ofcourse, in real app this is not the best solution.
The best way - store each IP address in separate row, and join it with "child" IPs.
If this is important, I can replace it with this implementation.


