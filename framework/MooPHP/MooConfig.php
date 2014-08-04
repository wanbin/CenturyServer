<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooConfig5.php 395 2008-10-13 03:43:32Z kimi $
*/

//note ����PHP���?��
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//note ����Block���湦��
define('MOOPHP_ALLOW_BLOCK', true);
//note ����ϵͳ���湦��
define('MOOPHP_ALLOW_CACHE', true);
//note ϵͳ�Ƿ�ʹ��MYSQL��ݿ⣬����ֵΪTRUEʱ�������MySQL��������ò����Զ���ʼ��MYSQL����
define('MOOPHP_ALLOW_MYSQL', false);
//note ����cookieǰ׺
define('MOOPHP_COOKIE_PRE', 'Moo_');
//note ����cookie������
define('MOOPHP_COOKIE_PATH', '/');
//note ����cookie����·��
define('MOOPHP_COOKIE_DOMAIN', '');
//note �����������Key�����޸Ĵ˴�ΪΨһ
define('MOOPHP_AUTHKEY', MOOPHP_VERSION);
//note �����Ƿ���MooPHP��Debug����
define('MOOPHP_DEBUG', FALSE);

//note MySQL�������ַ��ͨ��Ϊlocalhost
$dbHost = 'localhost';
//note ϵͳʹ�õ�MySQL����ݿ������project_moophp
$dbName = 'project_moophp';
//note MySQL���û���
$dbUser = 'root';
//note MySQL���û����Ӧ������
$dbPasswd = 'root';
//note MySQL��ǰ׺
$dbTablePre = 'moophp_';
//note MySQL��ݿ��ַ��Ƽ�ΪUTF-8
$dbCharset = 'UTF-8';
//note �Ƿ�Ϊ��������
$dbPconnect = 0;

$charset = 'UTF-8';
