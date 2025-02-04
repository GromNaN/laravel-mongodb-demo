# Demo of Laravel Scout with Meilisearch and MongoDB

- Connect to a MongoDB Atlas cluster with [Sample Data](https://www.mongodb.com/docs/atlas/sample-data/).
- Use 2 models: Movies and Comments, with a one-to-many relationship.
- Laravel Scout is configured to index documents from MongoDB into Meilisearch.

## Usage 

Configuration for indexing in Meilisearch from MongoDB

    # Target search engine
    SCOUT_DRIVER=mongodb # or meilisearch

    MEILISEARCH_HOST=http://localhost:7700

    MONGODB_URI=mongodb+srv://<user>:<password>@cluster*.***.mongodb.net/
    MONGODB_DATABASE=sample_mflix

Start Meilisearch (in a docker container)

    docker-compose up -d

Create Meilisearch index

    php artisan scout:index 'App\Models\Movie'

Index documents from MongoDB model into Meilisearch

    php artisan scout:import 'App\Models\Movie'


Check the documents in the meilisearch preview page: http://localhost:7700/

Start Laravel development server

    php artisan serve

Open the following URL in a browser: http://localhost:8000/movies


##

Important files:
- `app/Models/Movie.php` for the Movie model and its indexation
- `config/database.php` for MongoDB connection
- `config/scout.php` for Meilisearch configuration
- `tests/Feature/ExampleTest.php` for indexation mapping and search tests

