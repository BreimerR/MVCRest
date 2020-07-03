#MVC REST
This is a rest api that uses MVC structure to produce a simple and 
reliable rest service with quick and easy set up.

# Setup
This are settings important to how the application it to function.
There are a few ways to define settings
1. Define settings in *[settings.json](file:///opt/htdocs/MVCRest/configs/settings.json)*
    > This is should be used to define all the setting that are important to the project in a single place
2. Define settings in their respective files.
    > Sometimes projects might get big, and you might not be able to keep up with some settings.
    Thus, one might need to define settings in a singular files to avoid the clutter of most of the other files.

# Url 
The api uses REST url model (Structured URL's)
> https://example.com/Users  returns all users
> https://example.com/Users/3  return user with id 3
> https://example.com/Users/3/name returns user 3, with the name.

## Samples
__https://baseurl.com/Items__
> This should return all the requested data type items i.e Users or Places

__https://baseurl.com/Item__
> This should handle single item operations i.e User/2 

### Abstract url rules


## Side note 
table/column/value/amount
> This structure can also be embraced, but you'd have to have 
> column abstraction just for security
> <br><em>NB</em><br>The structure does not canter for multiple column rules  
> That can be handled with ?getters

 
###### Settings priority
The settings file should be considered as the main source of settings.
Else, In a situation where explicit settings files are defined. The explicit definitions will be concidered first
as they are less cluttered and should be less prone to errors.     
                                                                                          
## ALL *[settings.json](file:///opt/htdocs/MVCRest/configs/settings.json)*
```json
{
  "db": {
  },
  "auth": {
  },
  "api": {
  }
}
```
## API configs *[api.json](file:///opt/htdocs/MVCRest/configs/api.json)*
```json
{
  "": "" 
}
```
## DB Configs *[db.json](file:///opt/htdocs/MVCRest/configs/db.json)*
```json
{
  "host": "your_host_name",
  "database_name": "your_database_name",
  "password": "your_database_password"
}
```

## Security configs *[auth.json](file:///opt/lampp/htdocs/MVCRest/configs/auth.json)*
> This should handle things like api keys and how they are going to be handle
> from storage to generation rules
