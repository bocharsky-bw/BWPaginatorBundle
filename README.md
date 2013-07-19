# BWPaginatorBundle

Simple and Fast Pagination - Paginator Bundle for Symfony2

## Features

* Google Classic Pagination
* Modern Pagination with First and Last pages on the sides

## Installation

 1. Add manualy ```BW/PaginatorBundle``` to your ```src``` directory

 2. Register the bundle in you ```AppKernel``` in the production section

        // app/AppKernel.php
        public function registerBundles()
        {
            $bundles = array(
                // other bundles here...
            );

			$bundles[] = new BW\PaginatorBundle\BWPaginatorBundle();

            return $bundles;
        }

 3. run the ```assets:install``` command to install the ```css``` and ```js``` files

        ./app/console assets:install web
		
### Configuration example

You can configure default pagination parameters in configuration file
```yaml
bw_paginator:
    #...
```

## Usage examples:

### Controller

```php
// src\Acme\MainBundle\Controller\ArticleController.php

public function listAction() {
        $request = $this->get('request');
        $conn = $this->get('database_connection');
        
        $allRowCount = $conn->fetchColumn("SELECT COUNT(id) FROM articles WHERE is_published = 1");
        $page = $request->get('page'); 	// Получаем текущую старницу из $_GET
        $linkCount = 9; 				// Количество ссылок в постраничной навигации
        $rowCount = 10;					// Количество записей на страницу
        $paginator = $this->get('bw_paginator')->_initialize($allRowCount, $page, $linkCount, $rowCount);

        $articles = $conn->fetchAll("SELECT 
                * 
            FROM articles
            WHERE is_published = 1
            ORDER BY date DESC 
            LIMIT {$paginator->getOffset()}, {$paginator->getRowCount()} ");
            
        return $this->render('AcmeMainBundle:Article:articles-list.html.twig', array(
			'articles' 		=> $articles,
			'pagination' 	=> $paginator->getPagination(),
		));
	}
```

### View

```jinja
// src\Acme\MainBundle\Resources\views\Article\articles-list.html.twig

<table>
	<tr>
		<th>ID</th>
		<th>Heading</th>
	</tr>
	{% for article in articles %}
		<tr>
			<td>{{ article.id }}</td>
			<td>{{ article.heading }}</td>
		</tr>
	{% endfor %}
</table>

{# display navigation #}
<div class="pagination">
	{{ pagination|raw }}
</div>
```