<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= isset($siteTitle) ? e($siteTitle) : 'Smart Clothing' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <?php if (isset($extraCss)): ?>
    <style><?= $extraCss ?></style>
    <?php endif; ?>
</head>
<body>
    <?php if (isset($content)): ?>
        <?= $content ?>
    <?php else: ?>
        <?php if (isset($viewContent)): ?>
            <?= $viewContent ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
