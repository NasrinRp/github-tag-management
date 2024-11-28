
# GitHub Tag Management

A Laravel-based application for managing starred GitHub repositories and associating tags with them.

## Features
- Fetches and stores GitHub starred repositories for a given user.
- Allows users to search repositories by tags.
- Enables adding tags to repositories.

## Requirements
- PHP 8.0 or higher
- Composer
- MySQL (or any other supported database)
- GitHub Personal Access Token (if API rate limits are exceeded)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/NasrinRp/github-tag-management.git
```

### 2. Install dependencies

```bash
composer install
```

### 3. Set up environment variables

Copy the `.env.example` file to `.env` and update the settings, such as your database credentials and GitHub API token (optional).

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Set up the database

Create a database for the application, then run the migrations to set up the necessary tables.

```bash
php artisan migrate
```

### 6. (Optional) Configure GitHub Personal Access Token

If you're exceeding the GitHub API rate limits, you can provide a Personal Access Token to authenticate requests. Set it in the `.env` file:

```dotenv
GITHUB_TOKEN=your_personal_access_token_here
```

## Usage

### 1. Fetch Starred Repositories for a User

To fetch and save the starred repositories for a GitHub user, send a POST request to:

```bash
POST /api/{username}/starred-repositories
```

Example request:

```json
{
  "username": "testuser"
}
```

### 2. Search Repositories by Tag

To search repositories by a tag, send a GET request to:

```bash
GET /api/{username}/starred-repositories?tag={tag_name}
```

### 3. Add Tags to a Repository

To add tags to a specific repository, send a POST request to:

```bash
POST /api/{username}/starred-repositories/{repositoryId}/tags
```

Example request:

```json
{
  "tags": ["Laravel", "PHP"]
}
```

## Testing

### Run Tests

To run the tests for the application, use the following command:

```bash
php artisan test
```
## Postman Collection

For testing and interacting with the API, you can use the included [Postman Collection](./docs/Git-Tag-Management-Postman-Collection.json).

### How to Use

1. Open the collection in Postman by importing the `.json` file.
2. The collection includes three main requests:
    - **Fetch Starred Repositories**: Fetches starred repositories for a specific GitHub user.
    - **Add Tags**: Adds tags to a specific repository.
    - **Search by Tag**: Searches for repositories by tag.

