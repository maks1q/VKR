$ObjOU=[ADSI]"LDAP://CN=Администратор,OU=Пользователи,DC=server,DC=com"
$guid = ObjOU.GetGUID