# Pkdx-api

## Installation
    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console app:load-csv

## Running
    symfony server:start --port 8005

## Getting started
Access the API via the [/api](https://localhost:8005) URL.  

### Translations
By default, results are in english. Use `Accept-Language` header to replace it with one of supported locales.  
eg:
    
    curl -X 'GET' \
    'https://localhost:8005/api/pokemons?page=1' \
    -H 'accept-language: jp'

By adding the group `translations` all values of translatable properties will be serialized.
eg:

    curl -X 'GET' \
    'https://localhost:8005/api/pokemons/1?groups=[]translations'

### Examples
Fetch collection of Pokémons:
```http request
GET https://localhost:8005/api/pokemons
```
Fetch a single Pokémon:
```http request
GET https://localhost:8005/api/pokemons/{id}
```
Fetch collection of Pokémons with their types:
```http request
GET https://localhost:8005/api/pokemons?groups=type
```
Fetch collection of Pokémons with their types and translations:
```http request
GET https://localhost:8005/api/pokemons?groups[]=type&groups=translations
```
