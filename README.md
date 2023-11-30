## Project Setup

```sh
git clone git@github.com:randelpalu/7p.git project-dir
cd project-dir
git config core.fileMode false
sudo chmod 777 -R *
cp .env.example .env
```

```sh
cd laravel
cp .env.example .env
composer install
cd ..
```

```sh
cd frontend
cp .env.example .env
npm install
cd ..
```


Make sure ports used in docker-compose.yml are available on local machine
```sh
docker-compose up --build -d
```


Get laravel container name, and run migration in container:
```sh
docker ps
```
```sh
docker exec -it <container_id_or_name> /bin/bash
php artisan migrate --seed
```


### API Tests, run in laravel dir

```sh
php artisan test
```


### Starting/Stopping

```sh
docker-compose up -d
```
```sh
docker-compose down
```


### Accessing the Application

After successfully starting the Docker containers,
the applications Vue frontend will be available at [http://localhost:82](http://localhost:82).


### If you want to interact with the API

GET All Customers:
  - Endpoint: http://localhost:82/api/customers/

GET Single Customer:
  - Endpoint: http://localhost:82/api/customers/:id
  - Replace :id with the specific customer ID.

POST Create Customer:
  - Endpoint: http://localhost:82/api/customers
  - Use this endpoint to create a new customer.

PUT Update Customer:
  - Endpoint: http://localhost:82/api/customers/:id
  - Replace :id with the specific customer ID.

DELETE Delete Customer:
  - Endpoint: http://localhost:82/api/customers/:id
  - Replace :id with the specific customer ID.



**Customer Example:**
```json
{
    "id": 1,
    "created_at": "2023-11-30T02:39:20.000000Z",
    "updated_at": "2023-11-30T02:39:20.000000Z",
    "first_name": "Kylee",
    "last_name": "Johns",
    "dob": "1941-04-08",
    "username": "gmccullough",
    "password": "123PassworD"
}