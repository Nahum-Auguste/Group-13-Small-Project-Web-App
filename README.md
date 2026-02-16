"# Group-13-Small-Project-Web-App" 
- SQL connection stuff is in a separate .env file (in LAMPAPI); its the exact same stuff from the 
colors lab. be sure to make ur own
- In Users table, duplicate Logins are not allowed; this is the only field that's unique
- In Contacts table, "UserID" is a foreign key; it's connected to the Users table's primary
key (ID). Meaning, if you delete a User, all the Contacts associated with the deleted User
will be deleted as well. 

## AI Assistance Disclosure

This project was developed with assistance from generative AI tools:

- **Tool**: Gemini 3 (Google)
- **Dates**: February 9-15, 2026
- **Scope**: Function design and implementation of CRUD operations, debugging login errors
- **Use**: Provided logic for JavaScript state management (switching between "Add" and "Edit" modes),
developed silent login functionality to synchronize User IDs after registration

All AI-generated code was reviewed, tested, and modified to meet 
assignment requirements. Final implementation reflects my understanding 
of the concepts.