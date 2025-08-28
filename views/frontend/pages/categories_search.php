<div class="top-bar">
    <div class="search-bar">
        <form method="GET" action="index.php">
            <input type="hidden" name="route" value="client">
            <input type="hidden" name="pages" value="categories_search">
            <input type="text" pattern=".{3,}" name="search_q" id="search_q"
                   value="<?php if (!empty($_GET['search_q'])) echo e($_GET['search_q']); ?>"
                   required title="3 characters minimum" placeholder="Search categories..." />
            <input type="submit" name="submit" value="Search"/>
        </form>
    </div>
</div>

<?php
$columns = 4;
$categories = array_values(array_filter($cats, fn($cat) => $cat['title'] !== 'none'));
$total = count($categories);
$rows = ceil($total / $columns);

// desktop (column-major) grid maths
$column_major = [];
for ($i = 0; $i < $rows; $i++) {
    for ($j = 0; $j < $columns; $j++) {
        $index = $i + $j * $rows;
        $column_major[$i][$j] = $categories[$index] ?? null;
    }
}
?>

<!-- Desktop layout -->
<div class="category-grid-desktop">
    <?php foreach ($column_major as $row): ?>
        <div class="category-row">
            <?php foreach ($row as $cat): ?>
                <div class="category-cell">
                    <?php if ($cat): ?>
                        <a href="index.php?<?php echo http_build_query([
                            'route' => 'client',
                            'pages' => 'category',
                            'category' => $cat['title'],
                            'page' => 1
                        ]); ?>">
                            <?php echo e($cat['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Mobile layout -->
<div class="category-grid-mobile">
    <?php foreach ($categories as $cat): ?>
        <div class="category-cell">
            <a href="index.php?<?php echo http_build_query([
                'route' => 'client',
                'pages' => 'category',
                'category' => $cat['title'],
                'page' => 1
            ]); ?>">
                <?php echo e($cat['title']); ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <li class="pagination__li">
        <a class="pagination__link  
            <?php if ($page === $pag) : ?> 
                pagination__link--active
                <?php $current_active_page = $pag; ?> 
            <?php endif; ?>" 
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'categories_search', 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>