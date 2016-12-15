<?php
//$AdminLevel = LEVEL_WC_POSTS;
if (!$APP || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete($DB, $WHERE_DB_AUTO_TRASH, $PLACES_AUTO_TRASH);

if($DB_IMAGE):
    //AUTO TRASH IMAGES
    $Read->FullRead("SELECT image FROM {$DB_IMAGE} WHERE {$id} NOT IN(SELECT {$ID} FROM " . $DB . ")");
    if ($Read->getResult()):
        $Delete->ExeDelete($DB_IMAGE, "WHERE id >= :id AND {$ID}  NOT IN(SELECT {$ID}  FROM {$DB})", "id=1");
        foreach ($Read->getResult() as $ImageRemove):
            if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
                unlink("../uploads/{$ImageRemove['image']}");
            endif;
        endforeach;
    endif;
endif;
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

$Search = filter_input_array(INPUT_POST);
if ($Search && $Search['s']):
    $S = urlencode($Search['s']);
    header(sprintf("Location: dashboard.php?wc=%s/search&s=%s",$HOME,$S));
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="<?=$ICON;?>"><?=$APP_TITLE;?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <?=$APP_TITLE;?>
        </p>
    </div>
    <?php if($ADD):?>
         <div class="dashboard_header_search">
        <a title="Nova Categoria" href="dashboard.php?wc=<?=$APP_NAME;?>/<?=$CREATE;?>" class="btn btn_green icon-plus">Adicionar Cadastro!</a>
    </div>
    <?php endif;?>
    <?php if($SEARCH):?>
    <div class="dashboard_header_search">
        <form name="search<?=$APP_TITLE;?>" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" name="s" placeholder="Pesquisar:" required/>
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>
<?php endif;?>

</header>

<div class="dashboard_content">
    <?php
    if($data):
        echo implode(PHP_EOL, $data);
        if($Paginator):
        $Paginator->ExePaginator($DB);
        echo $Paginator->getPaginator();
        endif;
      else:
        if($Paginator):
            $Paginator->ReturnPage();
        endif;
        Erro("<span class='al_center icon-notification'>Ainda não existem {$HOME} cadastrados {$Admin['user_name']}. Comece agora mesmo!</span>", E_USER_NOTICE);
    endif;
    ?>
</div>