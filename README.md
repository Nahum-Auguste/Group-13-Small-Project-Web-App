"# Group-13-Small-Project-Web-App" 
- SQL connection stuff is in a separate .env file (in LAMPAPI); its the exact same stuff from the 
colors lab. be sure to make ur own
- In Users table, duplicate Logins are not allowed; this is the only field that's unique
- In Contacts table, "UserID" is a foreign key; it's connected to the Users table's primary
key (ID). Meaning, if you delete a User, all the Contacts associated with the deleted User
will be deleted as well. 