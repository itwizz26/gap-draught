# About

This is a library catalogue Application Programming Interface written in PHP 8 and Symfony 5.

## Technology used

* PHP 8.0
* Symfony 5.4.8
* MySQL 5.7
* Doctrine ORM
* Composer-based build

## Setup

After cloning the repo, run:
- **composer install**
- **Enter your database credentials in .env**
- **php bin/console doctrine:database:create**
- **php bin/console doctrine:schema:update --force**
- **php bin/console doctrine:fixtures:load**
- **symfony server:start**

## Testing

Head over to https://127.0.0.1:8000/api or alternative test using Postman.

# API Routes

-------------------------------- -------- -------- ------ ------------------------------------- 
Name                             Method   --   Path
 -------------------------------- -------- -------- ------ ------------------------------------- 
**catalogues**                       POST     --    /api/v1.0/catalogues                 
get_book                         GET      --    /api/v1.0/catalogues/{id}            
get_all_books                    GET      --    /api/v1.0/catalogues                 
update_book                      PATCH    --    /api/v1.0/catalogues/update/{id}     
delete_book                      DELETE   --    /api/v1.0/catalogues/delete/{id}     

**members**                          POST     --    /api/v1.0/members                    
get_member                       GET      --    /api/v1.0/members/{id}               
get_all_members                  GET      --    /api/v1.0/members                    
update_member                    PATCH    --    /api/v1.0/members/update/{id}        
delete_member                    DELETE   --    /api/v1.0/members/delete/{id}        

**stocks**                           POST     --    /api/v1.0/stocks                     
get_stock                        GET      --    /api/v1.0/stocks/{id}                
get_all_stock                    GET      --    /api/v1.0/stocks                     
update_stock                     PATCH    --    /api/v1.0/stocks/update/{id}         
delete_stock                     DELETE   --    /api/v1.0/stocks/delete/{id}

## References

* [Symfony](https://symfony.com/)
* [API Platform](https://api-platform.com/)

Version: 1.0