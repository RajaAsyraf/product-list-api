### Product List API
This repository is the backend to serve api for Product List application using Laravel 10 and MySQL database.

### Installation
To install this in your local machine, we can utilize Docker container to install the defined tools and packages. Assuming Docker is ready in your machine, you can proceed with these steps:
1. Open root directory in the terminal and run this `./vendor/bin/sail up` to spin up the docker container. This will spin up Laravel container and MySQL database.
![alt text](image.png)
2. Once the container is up, you can connect into the container using the container name by running this `docker exec -it product-list-api-laravel.test-1 bash`.
3. Next, you can execute the following commands:
  - Run migration `php artisan migrate`.
  - Run seeder `php artisan db:seed`.

At this point, the API is now ready to serve the request from frontend. You may submit a request against this URL `http://localhost/api/product`.

Let's proceed with setting up the frontend webclient here: https://github.com/RajaAsyraf/product-list-webclient