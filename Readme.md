### Requirement for application setup
* Docker
* Composer
* Yarn

### Procedure
Run every command so application can start
#### Backend
`docker-compose -d`

`composer install`

`symfony php bin/console doctrine:migrations:migrate`

`symfony serve -d`

`symfony open:local`


#### Frontend
`yarn install`

`yarn watch`
