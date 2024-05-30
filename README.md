# Test Symfony REST API task

## Technical Info
- PHP   - 8.2.1
- MySQL - 5.7.41

## Available Routes
### General Info
#### Public
- /api/v1/auth/register - (POST) - registration of user in system
- /api/v1/auth/login - (POST) - authentication namely obtaining Bearer token
#### Private - use Authorization header (Bearer token)
- /api/v1/users/{id?} - (GET) - if {id} is not provided, it returns authenticated User object. If id is provided (only for admin), returns another User instance
- /api/v1/users/ - (POST) - creation of user (only for admin)
- /api/v1/users/{id} - (PATCH) - update your profile (admin can update another profile)
- /api/v1/users/{id} - (DELETE) - delete your profile (admin can delete another profile)

### Payload Expected Keys (JSON format)
- /api/v1/auth/register - email,password,name
- /api/v1/auth/login - email,password
- /api/v1/users/{id?} - ---
- /api/v1/users/ - email,password,name
- /api/v1/users/{id} - name
- /api/v1/users/{id} - ---


## Time Spent
This task took approximately 6.5 - 7 hours

## Personal comments
### Things to improve
1. Would be nice to replace raw values with ValueObjects (e.g. email)
2. Trait HasJsonRequestTrait - is not good thing here at all. I would not definitely do this in real project.
I did it only in order to save some time as ideal implementation I`d like to go with 
(inject something like RegistrationRequest in controller method which would validate everything automatically) would take much more time to implement
3. Tests - I have written only one Unit test because this task took quite much time,
so it would be perfect to cover all functionality with unit and integration tests

### DB Dump
./dump.sql
