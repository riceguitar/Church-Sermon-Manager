# REST API Documentation

## Overview

Sermon Manager provides a REST API for accessing sermon data programmatically. The API follows WordPress REST API standards and provides endpoints for sermons, series, preachers, and more.

## Authentication

### Basic Authentication
```php
// Example using Basic Auth
$response = wp_remote_get('https://example.com/wp-json/sm/v1/sermons', [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode('username:password')
    ]
]);
```

### Application Passwords
```php
// Example using Application Passwords
$response = wp_remote_get('https://example.com/wp-json/sm/v1/sermons', [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode('username:application_password')
    ]
]);
```

### OAuth 1.0a
```php
// Example using OAuth
$oauth = new OAuth('consumer_key', 'consumer_secret');
$oauth->setToken('access_token', 'access_token_secret');
$response = $oauth->fetch('https://example.com/wp-json/sm/v1/sermons');
```

## Endpoints

### Sermons

#### List Sermons
```
GET /wp-json/sm/v1/sermons
```

Parameters:
- `per_page` (int) - Number of sermons per page (default: 10)
- `page` (int) - Page number (default: 1)
- `orderby` (string) - Sort by: date, title, series, preacher (default: date)
- `order` (string) - Sort order: asc, desc (default: desc)
- `series` (int) - Filter by series ID
- `preacher` (int) - Filter by preacher ID
- `topic` (int) - Filter by topic ID
- `book` (int) - Filter by book ID

Response:
```json
{
    "data": [
        {
            "id": 123,
            "title": "Sermon Title",
            "content": "Sermon content",
            "excerpt": "Sermon excerpt",
            "date": "2023-01-01T00:00:00",
            "audio_url": "https://example.com/audio.mp3",
            "video_url": "https://example.com/video.mp4",
            "duration": "45:00",
            "views": 100,
            "downloads": 50,
            "series": [
                {
                    "id": 456,
                    "name": "Series Name",
                    "slug": "series-slug"
                }
            ],
            "preacher": [
                {
                    "id": 789,
                    "name": "Preacher Name",
                    "slug": "preacher-slug"
                }
            ]
        }
    ],
    "total": 100,
    "total_pages": 10
}
```

#### Get Single Sermon
```
GET /wp-json/sm/v1/sermons/{id}
```

Response:
```json
{
    "id": 123,
    "title": "Sermon Title",
    "content": "Sermon content",
    "excerpt": "Sermon excerpt",
    "date": "2023-01-01T00:00:00",
    "audio_url": "https://example.com/audio.mp3",
    "video_url": "https://example.com/video.mp4",
    "duration": "45:00",
    "views": 100,
    "downloads": 50,
    "series": [
        {
            "id": 456,
            "name": "Series Name",
            "slug": "series-slug"
        }
    ],
    "preacher": [
        {
            "id": 789,
            "name": "Preacher Name",
            "slug": "preacher-slug"
        }
    ]
}
```

#### Create Sermon
```
POST /wp-json/sm/v1/sermons
```

Request body:
```json
{
    "title": "New Sermon",
    "content": "Sermon content",
    "excerpt": "Sermon excerpt",
    "date": "2023-01-01T00:00:00",
    "audio_url": "https://example.com/audio.mp3",
    "video_url": "https://example.com/video.mp4",
    "duration": "45:00",
    "series": [456],
    "preacher": [789]
}
```

#### Update Sermon
```
PUT /wp-json/sm/v1/sermons/{id}
```

Request body:
```json
{
    "title": "Updated Sermon",
    "content": "Updated content",
    "excerpt": "Updated excerpt"
}
```

#### Delete Sermon
```
DELETE /wp-json/sm/v1/sermons/{id}
```

### Series

#### List Series
```
GET /wp-json/sm/v1/series
```

Parameters:
- `per_page` (int) - Number of series per page (default: 10)
- `page` (int) - Page number (default: 1)
- `orderby` (string) - Sort by: name, count, id (default: name)
- `order` (string) - Sort order: asc, desc (default: asc)

Response:
```json
{
    "data": [
        {
            "id": 456,
            "name": "Series Name",
            "slug": "series-slug",
            "description": "Series description",
            "image_url": "https://example.com/image.jpg",
            "sermons_count": 10
        }
    ],
    "total": 50,
    "total_pages": 5
}
```

#### Get Single Series
```
GET /wp-json/sm/v1/series/{id}
```

Response:
```json
{
    "id": 456,
    "name": "Series Name",
    "slug": "series-slug",
    "description": "Series description",
    "image_url": "https://example.com/image.jpg",
    "sermons_count": 10,
    "sermons": [
        {
            "id": 123,
            "title": "Sermon Title",
            "date": "2023-01-01T00:00:00"
        }
    ]
}
```

### Preachers

#### List Preachers
```
GET /wp-json/sm/v1/preachers
```

Parameters:
- `per_page` (int) - Number of preachers per page (default: 10)
- `page` (int) - Page number (default: 1)
- `orderby` (string) - Sort by: name, count, id (default: name)
- `order` (string) - Sort order: asc, desc (default: asc)

Response:
```json
{
    "data": [
        {
            "id": 789,
            "name": "Preacher Name",
            "slug": "preacher-slug",
            "description": "Preacher description",
            "image_url": "https://example.com/image.jpg",
            "sermons_count": 20
        }
    ],
    "total": 30,
    "total_pages": 3
}
```

#### Get Single Preacher
```
GET /wp-json/sm/v1/preachers/{id}
```

Response:
```json
{
    "id": 789,
    "name": "Preacher Name",
    "slug": "preacher-slug",
    "description": "Preacher description",
    "image_url": "https://example.com/image.jpg",
    "sermons_count": 20,
    "sermons": [
        {
            "id": 123,
            "title": "Sermon Title",
            "date": "2023-01-01T00:00:00"
        }
    ]
}
```

## Error Handling

### Error Responses

```json
{
    "code": "rest_forbidden",
    "message": "Sorry, you are not allowed to do that.",
    "data": {
        "status": 403
    }
}
```

Common error codes:
- `rest_forbidden` - Permission denied
- `rest_invalid_param` - Invalid parameter
- `rest_missing_callback_param` - Missing required parameter
- `rest_no_route` - Route not found
- `rest_invalid_json` - Invalid JSON

### Rate Limiting

The API implements rate limiting to prevent abuse:
- 100 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

Response headers:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1625097600
```

## Best Practices

1. **Authentication**
   - Use secure authentication methods
   - Rotate API keys regularly
   - Implement IP whitelisting
   - Monitor API usage

2. **Performance**
   - Implement caching
   - Use pagination
   - Optimize queries
   - Compress responses

3. **Security**
   - Validate input
   - Sanitize output
   - Use HTTPS
   - Implement rate limiting

4. **Error Handling**
   - Provide clear error messages
   - Log errors properly
   - Handle edge cases
   - Implement retry logic

## Examples

### JavaScript Example
```javascript
// Get sermons
fetch('https://example.com/wp-json/sm/v1/sermons')
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });

// Create sermon
fetch('https://example.com/wp-json/sm/v1/sermons', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Basic ' + btoa('username:password')
    },
    body: JSON.stringify({
        title: 'New Sermon',
        content: 'Sermon content'
    })
})
.then(response => response.json())
.then(data => {
    console.log(data);
})
.catch(error => {
    console.error('Error:', error);
});
```

### PHP Example
```php
// Get sermons
$response = wp_remote_get('https://example.com/wp-json/sm/v1/sermons');
$sermons = json_decode(wp_remote_retrieve_body($response), true);

// Create sermon
$response = wp_remote_post('https://example.com/wp-json/sm/v1/sermons', [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode('username:password'),
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode([
        'title' => 'New Sermon',
        'content' => 'Sermon content'
    ])
]);
$result = json_decode(wp_remote_retrieve_body($response), true);
``` 