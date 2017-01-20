<?php
if (!$APP || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;
usleep(50000);
//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;

$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);
    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    // AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)):
        $Update = new Update;
    endif;
    
    // AUTO INSTANCE OBJECT DELETE
    if (empty($Delete)):
        $Delete = new Delete;
    endif;
    $Upload = new Upload('../../uploads/');
    $carregado=true;
endif;

function getCover($File,$field_name,$name)
{  
//ThisPost e o registro que veio do banco
if ($field_name && file_exists("../../uploads/{$field_name}") && !is_dir("../../uploads/{$field_name}")):
    unlink("../../uploads/{$field_name}");
endif;

$Upload = new Upload('../../uploads/');
$Upload->Image($File, $name . '-' . time(), IMAGE_W);
if ($Upload->getResult()):
    return $Upload->getResult();
else:
    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!", E_USER_WARNING);
        echo json_encode($jSON);
    return;
endif;
}

function getNane($DB,$ID,$field,$name,$title,$PostId="")
{
    $name=(!empty($name) ? Check::Name($name) : Check::Name($title));
     // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;
    $Read->ExeRead($DB, "WHERE {$ID} != :id AND {$field} = :name", "id={$PostId}&name={$name}");
    if ($Read->getResult()):
        $name = "{$name}-{$PostId}";
    endif;
    return $name;
}

function getStatus($PostData,$status,$date)
{
    $PostData[$status] = (!empty($PostData[$status]) ? '1' : '0');
    $PostData[$date] = (!empty($PostData[$date]) ? Check::Data($PostData[$date]) : date('Y-m-d H:i:s'));
    return $PostData;
}

function deleteCover($DB,$ID,$cover,$value,$IMAGE=null)
{
    if (empty($Read)):
        $Read = new Read;
    endif;
    $Read->FullRead("SELECT {$cover} FROM {$DB} WHERE {$ID} = :ps", "ps={$value}");
    if ($Read->getResult() && file_exists("../../uploads/{$Read->getResult()[0][$cover]}") && !is_dir("../../uploads/{$Read->getResult()[0][$cover]}")):
        unlink("../../uploads/{$Read->getResult()[0][$cover]}");
    endif;
    if($IMAGE):
    $Read->FullRead("SELECT image FROM {$IMAGE} WHERE {$ID} = :ps", "ps={$value}");
    if ($Read->getResult()):
        foreach ($Read->getResult() as $PostImage):
            $ImageRemove = "../../uploads/{$PostImage['image']}";
            if (file_exists($ImageRemove) && !is_dir($ImageRemove)):
                unlink($ImageRemove);
            endif;
        endforeach;
    endif;
    endif;
}

function sendimage($DB_IMAGE,$ID,$title,$name,$value)
{
    $NewImage = $_FILES['image'];
    $Read->FullRead("SELECT {$title}, {$name} FROM {$DB} WHERE {$ID} = :id", "id={$value}");
    if (!$Read->getResult()):
        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o post vinculado!", E_USER_WARNING);
    else:
        $Upload = new Upload('../../uploads/');
        $Upload->Image($NewImage, $ID . '-' . time(), IMAGE_W);
        if ($Upload->getResult()):
            $PostData['image'] = $Upload->getResult();
            $Create->ExeCreate($DB_IMAGE, $PostData);
            $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0][$title]}' alt='{$Read->getResult()[0][$title]}' src='../uploads/{$PostData['image']}'/>";
        else:
            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no post!", E_USER_WARNING);
        endif;
    endif;
}