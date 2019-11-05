<?php
function arreglaloatumedida_preprocess_page(&$vars) { 

    // template file called page--contenttype.tpl.php
    if (isset($vars['node']->type)) { 
        $vars['theme_hook_suggestions'][] = 'page__' . $vars['node']->type; 
    } 
} 
?>