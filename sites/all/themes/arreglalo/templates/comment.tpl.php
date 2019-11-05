<div class="<?php print $classes; ?> clear"<?php print $attributes; ?>>

  <?php if ($new): ?>
    <span class="new"><?php print $new ?></span>
  <?php endif; ?>

  <div class="submitted">
    <?php print $submitted; ?>  
      <button class="btn btn-default"><?php print $permalink; ?></button>
  </div>

  <div class="content"<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['links']);
      print render($content);
    ?>
  </div>

  <?php print render($content['links']) ?>
</div>
