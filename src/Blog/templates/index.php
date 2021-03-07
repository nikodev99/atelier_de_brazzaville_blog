<?= $renderer->render('header') ?>

<h1>Welcome on the atelier de brazzaville website</h1>

<ul>
    <a href="<?= $router->setUri('blog.show', ['slug' => 'Article 1']) ?>"><li>Article 1</li></a>
    <li>Article 2</li>
    <li>Article 3</li>
    <li>Article 4</li>
    <li>Article 5</li>
    <li>Article 6</li>
    <li>Article 7</li>
    <li>Article 8</li>
    <li>Article 9</li>
    <li>Article 10</li>
</ul>

<?= $renderer->render('footer') ?>