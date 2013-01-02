sql2xml
=======
The sql2xml script tranform data in database to a XML file :
- Configure the transformation with XML file
- Describe Database fetch in the transformation configuration file
- Execute PHP and your own function to transform values


Installation
------------
The installation is simple, juste download the script zip and extract it on your machine/server.
Configure the script for your installation:
1. Modify cfg/database.php with database configuration, links between tables and tables fields
2. Modify cfg/parameters.php with your own fixed parameters
3. Add XML transformation file following the guide
4. Modify cfg/function.php to add personnal value transform function (for exemple: mysubstr())
5. Test the application with index.php (change necessary to specify the XML configuration file)
