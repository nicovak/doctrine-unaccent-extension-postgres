## DOCTRINE - EXTENSION UNACCENT WITH POSTGRES ##


This is the solution I am using to add unaccent extension from postgres to doctrine with symfony 2.
I also provide the way to adapt sonata admin filter to be case insensitive (accent and lower + upper).


First you have to add the extension on the targeted database. Execute the following SQL code:


    CREATE EXTENSION unaccent;
        

Add the UnaccentString class in your project and adapt the namespace.
After that, you have to add this in your app/config/config.yml:


    doctrine:
        ...
        orm:
            ...
            dql:
                string_functions:
                    unaccent: Acme\DemoBundle\Admin\Doctrine\DQL\UnaccentString
                    
                    
Thats all, you can now use unaccent function in your DQL queries.


### BONUS ###


To use it with your sonata admin filters, you have to add the CaseInsensitiveStringFilter class to your project and adapt the namespace.
Now we can simple override the default sonata string filter, in your services.yml add:


    services:
        sonata.admin.orm.filter.type.string:
            class: Acme\DemoBundle\Admin\Filter\CaseInsensitiveStringFilter
            tags:
                - { name: sonata.admin.filter.type, alias: doctrine_orm_string }
            
            
### SOURCES ###
Unaccent function in postgres docs: http://www.postgresql.org/docs/9.1/static/unaccent.html

CaseInsensitiveStringFilter: https://gist.github.com/dbu/9524776

Unaccent string function: https://github.com/ICanBoogie/Common/blob/ec90b2d854a49882c814c84f67ed54bbb566aac0/lib/helpers.php