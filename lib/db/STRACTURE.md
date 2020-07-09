# Database Structure


## SQL's
### Select And Sample calls
```$instance->select("*",["name/=/$value"],tableName)```  
```SELECT * FROM USERS WHERE name = 'breimer'```  
```SELECT * FROM USERS WHERE age = 12```  


## __call
This should handle the method being requested for depending
on the method called

```$instance->select([columns,...],[binds,...])```

## Query Design
1. Create connection to the database
2. Structure the SQL
3. Bind Parameters to the sql if any
4. Execute the query as requested


update multiple rows at the same time
```
UPDATE config
   SET config_value = CASE config_name 
                      WHEN 'column_value' THEN 'value' 
                      WHEN 'column_value2' THEN 'value2' 
                      ELSE config_value
                      END
 WHERE config_name IN('column_value', 'column_value2');
```