namespace App\Controller;

interface TokenAuthenticatedController
{
    // ...
}### TODO

##### Implementation
- DTO to persist the request Data
  + Factory, Static Constructor, Named Arguments in Constructor



##### Security
- Validation
- Tokenbased Auth
  + "Middleware" or other Security mechanism
  + Adjust the UserProvider to retrieve the User
  + Service to save API Keys encryped in DB
  
##### Testing 
  + Unit Tests
  + Integration Tests?
  + TestData (Use Fixtures for that) 

