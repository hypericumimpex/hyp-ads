<?php
if(isset($_GET['view']) && $_GET['view'] == 'banner')
{
	require_once( ADNI_TPL_DIR.'/single_banner.php');
}
elseif(isset($_GET['view']) && $_GET['view'] == 'adzone')
{
	require_once( ADNI_TPL_DIR.'/single_adzone.php');
}
else
{
	require_once( ADNI_TPL_DIR.'/dashboard.php');
}
?>