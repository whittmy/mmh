<?php
define('MAC_ROOT', substr(__FILE__, 0, -13));

require(MAC_ROOT.'/inc/config/config.php');
require(MAC_ROOT.'/inc/config/cache.php');
require(MAC_ROOT.'/inc/common/class.php');

require(MAC_ROOT.'/inc/common/function.php');
require(MAC_ROOT.'/inc/common/template.php');
require(MAC_ROOT."/inc/common/template_diy.php");

define('MAC_PATH', $MAC['site']['installdir']);
define('MAC_ROOT_TEMPLATE', MAC_ROOT.'/template/'.$MAC['site']['templatedir'].'/'.$GLOBALS['MAC']['site']['htmldir']);
define('MAC_STARTTIME',execTime());
define('MAC_URL','#');
define('MAC_NAME','��ϵ����ϵͳ');


// ��������ò��ַǳ���Ҫ�� ���� error_reporting(7)�����еľ���(����������ڣ�����ָ�벻����)��Ϣ��������ʾ�������ᱨ��
//���������� �Զ���Ĵ������� my_error_handler,�Ա�ͳһ��ʽ���������Ȼ�Ƿ����Լ��趨�Ĵ���ȼ�(�� 7)
@session_start();  // session

@header('Content-Type:text/html;Charset=utf-8');
@date_default_timezone_set('Etc/GMT-8');
@ini_set('display_errors','On');
@error_reporting(7);
//@error_reporting(E_ALL);
@set_error_handler('my_error_handler');

//�򿪻�����, ��Ҫ��� header֮ǰ����������������
/* С���ӣ� ��費�� ob_start()�������ִ�г��� ����Ϊ�����˻���������ֱ�ӷ�������������ܱ������
ob_start(); //�򿪻�����
echo \"Hellon\"; //���
header("location:index.php"); //��������ض���index.php
ob_end_flush();//���ȫ�����ݵ������
*/

@ob_start();
?>