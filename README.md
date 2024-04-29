[![](https://img.shields.io/packagist/v/inspiredminds/contao-turbo-helper.svg)](https://packagist.org/packages/inspiredminds/contao-turbo-helper)
[![](https://img.shields.io/packagist/dt/inspiredminds/contao-turbo-helper.svg)](https://packagist.org/packages/inspiredminds/contao-turbo-helper)

Contao Turbo Helper
===================

This extension helps with the usage of Turbo in Contao applications.

* It provides a Turbo Frame wrapper as content elements.
* It forces the status code to 422 Unprocessable Entity when a Contao form does not validate.
* It forces a JavaScript load of a form's target URL, in case it would redirect to a URL outside the current domain.

## Streams

You can also create streams within your legacy Contao PHP templates:

```php
<!-- templates/mod_newslist.html5 -->
<?php $this->startTurboStream(); ?>
  <turbo-stream action="append" target="mod-newslist-articles-<?= $this->id ?>">
    <template><?= implode('', $this->articles) ?></template>
  </turbo-stream>
  <turbo-stream action="replace" target="mod-newslist-pagination-<?= $this->id ?>">
    <template><?= $this->pagination ?></template>
  </turbo-stream>
<?php $this->endTurboStream(); ?>

<?php $this->extend('mod_newslist'); ?>

<?php $this->block('content'); ?>

  <?php if (empty($this->articles)): ?>
    <p class="empty"><?= $this->empty ?></p>
  <?php else: ?>
    <div id="mod-newslist-articles-<?= $this->id ?>">
      <?= implode('', $this->articles) ?>
    </div>
    <div id="mod-newslist-pagination-<?= $this->id ?>">
      <?= $this->pagination ?>
    </div>
  <?php endif; ?>

<?php $this->endblock(); ?>
```

The server will then respond with these streams if there was a request with `Accept: text/vnd.turbo-stream.html` (e.g.
through a link with `data-turbo-stream`).
