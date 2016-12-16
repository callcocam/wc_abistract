<?php
if (!$APP || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)):
    $Create = new Create;
endif;

$DataID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($DataID):
    $Read->ExeRead($DB, "WHERE {$ID} = :id", "id={$DataID}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um resgistro que não existe ou que foi removido recentemente!";
        header(sprintf("Location: dashboard.php?wc=%s/%s",$APP_NANE,$HOME));
    endif;
else:
    // $DataCreate = ['post_date' => date('Y-m-d H:i:s'), 'post_type' => 'post', 'post_status' => 0, 'post_author' => $Admin['user_id']];
    $Create->ExeCreate($DB, $DataCreate);
    header(sprintf("Location: dashboard.php?wc=%s/%s&id=%s",$APP_NANE,$CREATE,$Create->getResult()));
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-new-tab"><?= $TITLE ? $$TITLE : "Novo Cadastro"; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="<?=sprintf('dashboard.php?wc=%s/%s',$APP_NANE,$HOME);?>"><?=$APP_TITLE;?></a>
            <span class="crumb">/</span>
            Gerenciar <?=$APP_TITLE;?>
        </p>
    </div>

    <?php if($VERNO_SITE):?>
    <div class="dashboard_header_search">
        <a target="_blank" title="Ver no site" href="<?= BASE; ?>/artigo/<?= $$NAME; ?>" class="wc_view btn btn_green icon-eye">Ver artigo no site!</a>
    </div>
    <?php endif;?>
</header>
<?php if($TINYMCE):?>
<div class="workcontrol_imageupload none" id="post_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="<?=$CALLBACK;?>"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="<?=$ID;?>" value="<?= $DataID; ?>"/>
            <div class="upload_progress none" style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">0%</div>
            <div style="overflow: auto; max-height: 300px;">
                <img class="image image_default" alt="Nova Imagem" title="Nova Imagem" src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>" default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
            </div>
            <div class="workcontrol_imageupload_actions">
                <input class="wc_loadimage" type="file" name="image" required/>
                <span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control" style="margin-right: 8px;">Fechar</span>
                <button class="btn btn_green icon-image">Enviar e Inserir!</button>
                <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
<?php endif;?>
<div class="dashboard_content">
    <form class="auto_save" name="<?=$NAME_FORM;?>" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="<?=$CALLBACK;?>"/>
        <input type="hidden" name="callback_action" value="<?=$CALLBACK_ACTION;?>"/>
        <input type="hidden" name="<?=$ID;?>" value="<?= $DataID; ?>"/>
         
       <?php $file=sprintf("%s/%s/tpl/%s.tpl.php",dirname(dirname(__FILE__)),$APP_NANE,$TPL);
        if(!file_exists($file) && !is_dir($file)):
              $arquivo=require_once sprintf("%s/abstract/create.php",dirname(dirname(__FILE__)));
              file_put_contents($file, $arquivo);
                if(!file_exists($file) && !is_dir($file)):
                    Erro("<span class='al_center icon-notification'>Não foi posivel criar o arquivo {$file}. Crie este arquivo!</span>", E_USER_NOTICE);
                endif;
        endif;
         require_once $file;
        ?>
        <div class="wc_actions" style="text-align: center">
            <label class="label_check label_publish <?= ($$STATUS == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="<?=$STATUS;?>" <?= ($$STATUS == 1 ? 'checked' : ''); ?>> Publicar Agora!</label>
            <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
            <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
        </div>
        </div>
        </div>
     </form>
</div>