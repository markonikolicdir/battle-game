### Requirement for application setup
* Docker
* Composer
* Yarn
* PHP >=7.2.5

### Procedure
Run every command so application can start
#### Backend
`docker-compose up -d`

`composer install`

`symfony php bin/console doctrine:migrations:migrate`

`symfony serve -d`

`symfony open:local`


#### Frontend
`yarn install`

`yarn watch`
