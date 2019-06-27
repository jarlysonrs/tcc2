<?php

require_once 'components/application.php';
require_once 'components/page/page.php';
require_once 'components/security/permission_set.php';
require_once 'components/security/user_authentication/hard_coded_user_authentication.php';
require_once 'components/security/grant_manager/hard_coded_user_grant_manager.php';

include_once 'components/security/user_identity_storage/user_identity_session_storage.php';

$users = array('usuario' => '202cb962ac59075b964b07152d234b70',
    'administrador' => 'e10adc3949ba59abbe56e057f20f883e',
    'admin' => '8ce3280fade2eab00e2376bf2ed1e43b');

$grants = array('guest' => 
        array()
    ,
    'defaultUser' => 
        array('dispositivo' => new PermissionSet(false, false, false, false),
        'tipo' => new PermissionSet(false, false, false, false),
        'status' => new PermissionSet(false, false, false, false),
        'setor' => new PermissionSet(false, false, false, false))
    ,
    'usuario' => 
        array('dispositivo' => new PermissionSet(false, false, false, false),
        'tipo' => new PermissionSet(false, false, false, false),
        'status' => new PermissionSet(false, false, false, false),
        'setor' => new PermissionSet(false, false, false, false))
    ,
    'administrador' => 
        array('dispositivo' => new PermissionSet(false, false, false, false),
        'tipo' => new PermissionSet(false, false, false, false),
        'status' => new PermissionSet(false, false, false, false),
        'setor' => new PermissionSet(false, false, false, false))
    ,
    'admin' => 
        array('dispositivo' => new PermissionSet(false, false, false, false),
        'tipo' => new PermissionSet(false, false, false, false),
        'status' => new PermissionSet(false, false, false, false),
        'setor' => new PermissionSet(false, false, false, false))
    );

$appGrants = array('guest' => new PermissionSet(false, false, false, false),
    'defaultUser' => new PermissionSet(true, false, false, false),
    'usuario' => new PermissionSet(false, false, true, false),
    'administrador' => new AdminPermissionSet(),
    'admin' => new AdminPermissionSet());

$dataSourceRecordPermissions = array();

$tableCaptions = array('dispositivo' => 'Informar problema',
'tipo' => 'Tipos de problemas',
'status' => 'Status',
'setor' => 'Setor');

function SetUpUserAuthorization()
{
    global $users;
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;

    $hasher = GetHasher('md5');
    $userAuthentication = new HardCodedUserAuthentication(new UserIdentitySessionStorage(), false, $hasher, $users);
    $grantManager = new HardCodedUserGrantManager($grants, $appGrants);

    GetApplication()->SetUserAuthentication($userAuthentication);
    GetApplication()->SetUserGrantManager($grantManager);
    GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}
