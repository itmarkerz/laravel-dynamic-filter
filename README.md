# Laravel Dynamic Filter

A flexible and powerful Laravel package for applying dynamic filters to your Eloquent models. made by[ itmarkerz technologies](https://itmarkerz.co.in/ " itmarkerz technologies")
## Installation
You can install the package via Composer:
```bash
    composer require itmarkerz/laravel-dynamic-filter
```
## Configuration

### Step 1: Publish the Configuration
Publish the package configuration:
```bash
php artisan vendor:publish --provider="Itmarkerz\LaravelDynamicFilter\Providers\DynamicFilterServiceProvider"
```
### Step 2: Add the Filterable Trait to Your Model
Add the Filterable trait to your Eloquent models and define the filterable property:
```php
use Itmarkerz\LaravelDynamicFilter\Traits\Filterable;

class YourModel extends Model
{
    use Filterable;

    protected $filterable = [
        'column_one',
        'column_two',
        // other filterable columns
    ];
}
```
### Usage
#### Applying Filters
To apply filters to a model, use the applyFilters method of the FilteringService:
```php
use Itmarkerz\LaravelDynamicFilter\Service\FilteringService;

$filters = [
    [
        'logic' => 'and',
        'filter_values' => [
            'column' => 'status',
            'condition' => 'equals',
            'value' => 'active'
        ]
    ],
    // add more filters as needed
];

$modelName = 'App\\Models\\YourModel';
$results = FilteringService::applyFilters($modelName, $filters)->get();
```
#### Getting Filterable Columns
To get the list of filterable columns for a model, use the getFilterableColumns method of the FilteringService:
```php
use Itmarkerz\LaravelDynamicFilter\Service\FilteringService;

$modelName = 'App\\Models\\YourModel';
$service = new FilteringService();

try {
    $columns = $service->getFilterableColumns($modelName);
    print_r($columns);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```
### Example Controller
Here's an example of how to use the FilteringService in your controllers:
```php
use Itmarkerz\LaravelDynamicFilter\Service\FilteringService;

class YourController extends Controller
{
    public function filter(Request $request)
    {
        $filters = $request->input('filters', []);
        $modelName = 'App\\Models\\YourModel';
        $results = FilteringService::applyFilters($modelName, $filters)->get();
        return response()->json($results);
    }

    public function getFilterableColumns(Request $request)
    {
        $modelName = 'App\\Models\\YourModel';
        $service = new FilteringService();

        try {
            $columns = $service->getFilterableColumns($modelName);
            return response()->json(['columns' => $columns]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```
### Contributing
Contributions are welcome! Please open an issue or submit a pull request to contribute.
### License
This package is open-sourced software licensed under the MIT license.