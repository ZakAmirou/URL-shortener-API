# URL-shortner
URL shortner based on Laravel 9, PHP 8.1, Docker

# Technologies used:
PHP : 8.1.13
Laravel framework version : 9.52.4
Redis : latest
MySQL : 8.0.20
Nginx : latest 
Docker : latest

# Start the app:
```docker compose up -d --build```

# How the API works:
The API provides two endpoints: /shorten and /{shortUrl}.
The /shorten endpoint accepts a POST request with a long_url parameter and returns a shortened URL that redirects to the original URL.
The /{shortUrl} endpoint accepts a GET request with a parameter that represents the shortened URL and redirects to the original URL.
When a user submits a URL to be shortened, the API generates a unique code for the URL and saves it in the database along with the original URL and the number of clicks it has received.
To optimize the performance of the API, the API stores frequently accessed URLs in Redis cache for faster lookups.

# Test the API:
We can test our API using a tool like Postman, or with a curl.
Exemple POST :


URL ENDPOINT : POST http://127.0.0.1:8000/api/shorten (replace the IP with your domain when hosted)
Headers : Content-Type = application/json
Content : 
```
  {
  "url": "https://owasp.org/Top10/"
  }
```
Response : Status code 200
```
{
    "short_url": "Y2I0Z"
}
```
the function shorten() in the controller validate the url, check if it exist into Redis(if yes, it retrives directly the shorter URL), it generate the shorter URL using a simple md5 hashing, add it to the database & Redis, before returning the shorter URL.
It also queue the URL for processing (in case of high number of requests)

Exemple GET: 

URL ENDPOINT : GET http://127.0.0.1:8000/api/{ShortUurl} (replace the IP with your domain when hosted)
Headers : Content-Type = application/json

Response : Status code 200
```
{
    "original_url": "https://owasp.org/Top10/"
}
```

# Cache system used:
The API uses Redis caching system for storing frequently accessed URLs.
The cache system is used to store frequently accessed URLs for faster lookups.
Its also used to store the generated short URLs and their corresponding original URLs for quick retrieval.

# Job system used:
The API uses Horizon job queue to manage asynchronous tasks and increase the performance of the API.
Horizon provides a dashboard that allows developers to monitor job processing and view statistics about job performance.
Jobs are used to handle tasks such as generating unique codes for URLs, storing URLs in the database, and updating the number of clicks on a URL.

# How the API handles 1000 concurrent requests:
The API uses Laravel's built-in queuing system to handle a large number of requests.
When a user submits a request, the request is added to the queue and processed in the background.
This allows the API to handle a large number of requests without slowing down the response time for individual users.

# How the database can handle up to 1 billion records:
MySQL table can store up to 65,535 bytes, its a little more than 1 billion records.
MySQL is a robust and scalable relational database management system that can handle a large number of records.
The API is designed so that the database could store data without ever going down. (Error exemple : Mysql is gone away )

max_connections is a setting in MySQL that determines the maximum number of simultaneous connections allowed to the database. max_connections is set to 1000, so the database can handle storing 1000 concurent records. this is done with the command: [ "--max_connections=1000" ] inside the configuration of the database in docker-compose.

# Tests:

A unit test, testing one API call, and a test that send 1000 calls could be found under the /tests folder.
Note that the 1000 calls test works only (theoricly) when the app is hosted.

You can try the test with the commande : 

``` php artisan test```

You can also test the API using a tool like Jmetter to simulate 1000 concurent requests.

# Docker :

All docker configuration can be found under the /docker folder. (Nginx config, PHP)



