<?php
$is_frontend = 1;
$view = isset($_GET['view']) && !empty($_GET['view']) ? $_GET['view'] : '';

require_once(ADNI_TPL_DIR.'/frontend_manager/header.php');
?>

<div class="main">
    <div class="page_content">
        

        <div class="page_container">
            <?php 
            if( $view === 'banner')
            {
                require_once(ADNI_TPL_DIR.'/single_banner.php'); 
            }
            ?>
        </div>

        <div class="boxed">
            <div class="inner">
                
            </div>
        </div>
    </div>
</div>

<?php
require_once(ADNI_TPL_DIR.'/frontend_manager/footer.php');
?>