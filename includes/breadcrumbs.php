<?php
/**
 * Рисует хлебные крошки. $items — массив ['label'=>..,'url'=>..|null].
 * "Главная" подставляется автоматически первым пунктом.
 */
function render_breadcrumbs(array $items) {
    echo '<div class="breadcrumb-bar"><div class="container"><nav aria-label="Хлебные крошки" class="breadcrumb-nav">';
    echo '<a href="/index.php">Главная</a>';
    foreach ($items as $item) {
        echo '<span class="breadcrumb-sep">›</span>';
        if (!empty($item['url'])) {
            echo '<a href="' . h($item['url']) . '">' . h($item['label']) . '</a>';
        } else {
            echo '<span class="breadcrumb-current" aria-current="page">' . h($item['label']) . '</span>';
        }
    }
    echo '</nav></div></div>';
}
