<?php
// $Id: main.php,v 1.21 2005/07/01 13:22:31 merlin Exp $
// Id: main.php,v 1.2 2005/03/14 20:05:43 jsaucier Exp 
// Module language Japanese Version


define("_MD_FCABIX_MAIN_TITLE", "�ե����륭��ӥͥå�");

define("_MD_GO_BACK", "<a href='javascript:history.go(-1);'>Return</a>");

define("_MD_NAVIGATION_TITLE","�ʥӥ����� : ");
define("_MD_PARENTFOLDER", "Root");

define("_MD_ACTION_TITLE","���Υե�������Ф��Ʋ�ǽ����� : ");

define("_MD_FOLDER_TITLE","�ե�����Υꥹ�� : ");
define("_MD_FOLDER_NAME", "̾��");
define("_MD_FOLDER_DELETE", "�õ�");
define("_MD_FOLDER_HIDDEN", "����");
define("_MD_FOLDER_CREATE", "�ե��������");
define("_MD_FOLDER_ERASE", "�ե�����õ�");
define("_MD_FOLDER_MOD", "�ե����̾�ѹ�");
define("_MD_FOLDER_MOD2", "̾���ѹ�");


define("_MD_DOCUMENT_TITLE","ʸ��ꥹ�� : ");
define("_MD_FILE_NAME", "̾��");
define("_MD_FILE_DATE", "�ǡ����ѹ�");
define("_MD_FILE_SPACE", "�礭��");
define("_MD_FILE_SUMMARY", "����");
define("_MD_FILE_DELETE", "�õ�");
define("_MD_FILE_CREATE", "�ե������ɲ�");


define("_MD_ADMIN_TITLE", "����");

define("_MD_FOLDER_ERROR_RIGHTS", "���Υե������¸�ߤ��ޤ���"); 

define("_MD_EDITRIGHTS", "�����������Խ� : ");

define("_MD_FCABIX_CREATEFOLDER", "�ե�����򼡤β��˺������ޤ� : ");
define("_MD_CREATEFOLDER_NAME", "�ե����̾ : ");
define("_MD_CREATEFOLDER_SUBMIT", "����");
define("_MD_CREATEFOLDER_CANCEL", "���");

define("_MD_CREATEFOLDER_SAVE_GOOD", "�ե�������ɲä���ޤ���");
define("_MD_CREATEFOLDER_SAVE_ERROR_NAME", "�������ե����̾�����Ϥ��Ƥ�������");
define("_MD_CREATEFOLDER_SAVE_ERROR_BD", "BD���ɲä�����˥��顼�Ȥʤ�ޤ������ե�����Ϻ����Ǥ��ޤ���Ǥ���");
define("_MD_CREATEFOLDER_SAVE_ERROR_PATH", "�ѥ������ꥨ�顼�Ǥ����ե�����Ϻ����Ǥ��ޤ���Ǥ���");
define("_MD_CREATEFOLDER_SAVE_ERROR_WRITABLE", "�ƥե����������ԲĤǤ����ե�����Ϻ����Ǥ��ޤ���Ǥ���");


define("_MD_FCABIX_MODFOLDER", "�ե������̾���ѹ� : ");
define("_MD_MODFOLDER_NAME", "�ե�����ο�����̾�� : ");
define("_MD_MODFOLDER_SUBMIT", "̾���ѹ�");
define("_MD_MODFOLDER_CANCEL", "����󥻥�");

define("_MD_MODFOLDER_PARENT", "Root �ե�������ѹ��Ǥ��ޤ���");

define("_MD_MODFOLDER_BAD_NAME", "�������ե����̾�����Ϥ��Ƥ�������");
define("_MD_MODFOLDER_SAVE_GOOD", "�ե�������ѹ�����ޤ���");
define("_MD_MODFOLDER_SAVE_BAD", "�ե�������ѹ��˼��Ԥ��ޤ���");
define("_MD_MODFOLDER_SAVE_DONTEXIST", "���Υե������¸�ߤ��ޤ���");

define("_MD_FCABIX_ERASEFOLDER", "�ե�����ξõ�: ");
define("_MD_ERASEFOLDER_CONFIRM", "���Υե�����Ȥ�����Ȥ�õ�Ƥ��ɤ��Ǥ���?");
define("_MD_ERASEFOLDER_SUBMIT", "��ǧ");
define("_MD_ERASEFOLDER_CANCEL", "����󥻥�");

define("_MD_ERASEFOLDER_GOOD", "The folder has been erased");
define("_MD_ERASEFOLDER_BAD", "Error in erasing the folder");
define("_MD_ERASEFOLDER_ERASE_DONTEXIST", "The folder don't exist");
define("_MD_ERASEFOLDER_ERASE_RIGHTS", "The folder don't have the good permissions to be erased");

define("_MD_ERASEFOLDER_PARENT", "You cannot erase the Root folder");


define("_MD_FCABIX_CREATEFILE", "�ʲ��Υե�����˥ե������������ޤ� : ");
define("_MD_CREATEFILE_NAME", "�ɲä���ե����� : ");
define("_MD_CREATEFILE_SUBMIT", "�ɲ�");
define("_MD_CREATEFILE_CANCEL", "����󥻥�");

define("_MD_CREATEFILE_SAVE_GOOD", "The file has been added");
define("_MD_CREATEFILE_SAVE_WRITABLE", "���Υե�����ϥե�����ν�����ػߤ���Ƥ��ޤ���");
define("_MD_CREATEFILE_UPLOAD_PROBLEM", "Error in file upload");
define("_MD_CREATEFILE_UPLOAD_NOFILE", "You must specify a file");
define("_MD_CREATEFILE_UPLOAD_TOOBIG", "File too big, please choose another");
define("_MD_CREATEFILE_UPLOAD_PARTIAL", "The file has been uploaded partially");
define("_MD_CREATEFILE_UPLOAD_EXTFILTER", "���γ�ĥ�Ҥϵ��Ĥ���Ƥ��ޤ���");

define("_MD_FCABIX_ERASEFILE", "�ե������õ�ޤ� : ");
define("_MD_ERASEFILE_CONFIRM", "���Υե������õ���ɤ��Ǥ���?");
define("_MD_ERASEFILE_SUBMIT", "��ǧ");
define("_MD_ERASEFILE_CANCEL", "����󥻥�");

define("_MD_ERASEFILE_GOOD", "���Υե�����ξõ���������ޤ���.");
define("_MD_ERASEFILE_ERROR_PATH", "Error in file path, the file will not be erased");
define("_MD_ERASEFILE_ERROR_BD", "Error when erasing the file in BD, the file will not be erased");
define("_MD_ERASEFILE_ERROR_DONTEXIST", "The file don't exist");
define("_MD_ERASEFILE_ERROR_RIGHTS", "The file don't have the good permissions to be erased");

define("_MD_FILE_ERROR_RIGHTS", "This file doesn't exist");
define("_MD_FCABIX_MODFILE", "�ե������̾���ѹ� : ");
define("_MD_MODFILE_NAME", "�ե�����ο�����̾�� : ");
define("_MD_MODFILE_SUBMIT", "̾���ѹ�");
define("_MD_MODFILE_CANCEL", "����󥻥�");

define("_MD_MODFILE_BAD_NAME", "�������ե�����̾������Ƥ�������");
define("_MD_MODFILE_SAVE_GOOD", "�ե�������ѹ�����ޤ���");
define("_MD_MODFILE_SAVE_BAD", "�ե������ѹ����Υ���-");
define("_MD_MODFILE_SAVE_DONTEXIST", "���Υե�����Ϥ���ޤ���");

define("_MD_FCABIX_VIEWFILE", "�ե����� ����");
define("_MD_VIEWFILE_TYPE", "�ե����� ������ : ");
define("_MD_VIEWFILE_NAME", "�ե����� ̾ : ");
define("_MD_VIEWFILE_SPACE", "�ե����� ������ : ");
define("_MD_VIEWFILE_CREATEDDATE", "������ : ");
define("_MD_VIEWFILE_MODDATE", "������ : ");
define("_MD_VIEWFILE_OWNER", "�ե���������� : ");
define("_MD_VIEWFILE_USERMOD", "�ǽ������� : ");
define("_MD_VIEWFILE_ACTION", "��ǽ�����");
define("_MD_VIEWFILE_GET", "���������");
define("_MD_VIEWFILE_MAJ", "����");
define("_MD_VIEWFILE_RENAME", "̾���ѹ�");
define("_MD_VIEWFILE_ERASE", "�õ�");

define("_MD_VIEWFILE_BACKFOLDER", "�ե�����ˤ�ɤ�");

define("_MD_SUMMARY_TITLE", "����");


define("_MD_GETFILE_GOOD", "��������ɽ�λEnd of download");

define("_MD_FCABIX_MAJFILE", "�ե�����ι��� : ");
define("_MD_MAJFILE_NAME", "��������ե���������򤷤Ƥ������� : ");
define("_MD_MAJFILE_SUBMIT", "����");
define("_MD_MAJFILE_CANCEL", "����󥻥�");

define("_MD_MAJFILE_SAVE_GOOD", "The file has been updated");
define("_MD_MAJFILE_SAVE_ERROR_PATH", "Error in file path, the file will not be updated");
define("_MD_MAJFILE_SAVE_ERROR_BD", "Error in inserting in the BD, the file will not be updated");
define("_MD_MAJFILE_SAVE_ERROR_WRITABLE", "The file don't have the good permissions to be updated");
define("_MD_MAJFILE_SAVE_ERROR_DONTEXIST", "The file don't exist");


define("_MD_DBRIGHTS_CREATEFOLDER", "�ե��������");
define("_MD_DBRIGHTS_MODFOLDER", "�ե�����ѹ�");
define("_MD_DBRIGHTS_ERASEFOLDER", "�ե�����õ�");
define("_MD_DBRIGHTS_CREATEFILE", "�ե��������");
define("_MD_DBRIGHTS_MODFILE", "�ե������ѹ�");
define("_MD_DBRIGHTS_ERASEFILE", "�ե�����õ�");
define("_MD_DBRIGHTS_ACCESSFOLDER", "�ե����ɽ��");

define("_MD_FLEXIGRID_EXPLORER_TITLE", "�������ץ���");
define("_MD_FLEXIGRID_COL_NAME", "̾��");
define("_MD_FLEXIGRID_COL_CREATEDBY", "������");
define("_MD_FLEXIGRID_COL_CREATEDAT", "��������");
define("_MD_FLEXIGRID_COL_UPDATEDBY", "������");
define("_MD_FLEXIGRID_COL_UPDATEDAT", "��������");
define("_MD_FLEXIGRID_COL_TYPE", "����");
define("_MD_FLEXIGRID_COL_SIZE", "������");
define("_MD_FLEXIGRID_DISPLAYING", "{total} �Υե�����Τ�����{from} ���� {to} �ޤǤ�ɽ����");
define("_MD_FLEXIGRID_PROCESSING", "������Ǥ������Ф餯���Ԥ�����������");
define("_MD_FLEXIGRID_NOITEMS", "�ե�����ϸ��Ĥ���ޤ���Ǥ�����");
define("_MD_FLEXIGRID_COMMAND_MAKEDIR", "�������ե����");
define("_MD_FLEXIGRID_CONFIRM_MULTIDL", "{count} �ĤΥե�������������ɤ��ޤ�����");
define("_MD_FLEXIGRID_CONFIRM_DELETE", "{count} �ĤΥե�����������Ƥ�����Ǥ�����");
define("_MD_FLEXIGRID_CONFIRM_DELETEDIR", "�ե���� \"{name}\" �������Ƥ�����Ǥ�����");
define("_MD_FLEXIGRID_CONNECTIONERROR", "�ǡ����μ����˼��Ԥ��ޤ�����");
define("_MD_FLEXIGRID_CONFIRM_CANNOT_DELETE", "�����Υե�����������뤳�ȤϤǤ��ޤ���");
define("_MD_FLEXIGRID_COMMAND_DOWNLOAD", "���������");
define("_MD_FLEXIGRID_COMMAND_DELETE", "�ե�������");
define("_MD_FLEXIGRID_COMMAND_DELETEDIR", "�ե�������");
define("_MD_FLEXIGRID_COMMAND_PARENT", "�ƥե�����˰�ư");
define("_MD_FLEXIGRID_COMMAND_FILEPROPERTY", "�ե���������");
define("_MD_FLEXIGRID_COMMAND_DIRPROPERTY", "�ե��������");
define("_MD_FLEXIGRID_COMMAND_UPLOAD", "���åץ���");
define("_MD_UPLOADIFY_START", "���åץ��ɳ���");
define("_MD_UIDIALOG_CONTROL", "���");
define("_MD_UIDIALOG_DELETE", "���");
define("_MD_UIDIALOG_OK", "OK");
define("_MD_UIDIALOG_CANCEL", "����󥻥�");
define("_MD_UIDIALOG_GROUPPERM", "����������������");
define("_MD_UIDIALOG_TITLE_ABOUT", "Fcabix�ˤĤ���");
define("_MD_UIDIALOG_TITLE_MAKEDIR", "�������ե�����κ���");
define("_MD_UIDIALOG_TITLE_UPLOAD", "�ե����롦���åץ���");
define("_MD_UIDIALOG_TITLE_DOWNLOAD", "�ե����롦���������");
define("_MD_UIDIALOG_UPLOAD_EXT", "���åץ��ɤǤ���ե�����μ���");
define("_MD_UIDIALOG_UPLOAD_MAXSIZE", "1�ե����뤢����κ��祵����");
define("_MD_UIDIALOG_UPLOAD_MAXNUM", "1��˥��åץ��ɤǤ���ե������");
define("_MD_JSUI_FLASH_IS_DISABLED", "Flash�ץ饰����ͭ���ˤʤäƤ��ޤ���");

define("_MD_NOJSGRID_SELET", "����");
define("_MD_NOJSGRID_DELETE", "�������");
define("_MD_NOJSGRID_RENAME", "̾�����ѹ�����");
define("_MD_NOJSGRID_SETGROUPPERM", "����������������");
define("_MD_NOJSGRID_DOWNLOAD_SELETED", "���򤷤��ե��������������");

define("_MD_CREATEFILE_TITLE", "�ե����롦���åץ���");
define("_MD_CREATEFILE_SELECTFILE", "���åץ��ɤ���ե����������Ǥ�������");
define("_MD_CREATEFILE_UPLOAD", "���åץ���");

define("_MD_CREATEFOLDER_TITLE", "�������ե�����κ���");
define("_MD_CREATEFOLDER_INPUTNAME", "��������ե������̾������ꤷ�Ƥ�������");
define("_MD_CREATEFOLDER_CREATE", "�ե���������");

define("_MD_ERASEFILE_TITLE", "�ե�����κ��");
define("_MD_ERASEFILE_ERASE", "���Υե��������");
define("_MD_ERASEFILE_ERASE_COMFIRM", "�ե������%s�ɤ������ޤ�����");
define("_MD_ERASEFOLDER_TITLE", "�ե�����κ��");
define("_MD_ERASEFOLDER_ERASE", "���Υե��������");
define("_MD_ERASEFOLDER_ERASE_COMFIRM", "�ե������%s�ɤ������ޤ�����");

define("_MD_MODFILE_TITLE", "�ե������̾���ѹ�");
define("_MD_MODFILE_INPUT", "�ե������%s�ɤο�����̾�������Ϥ��Ƥ�������");
define("_MD_MODFILE_RENAME", "�ѹ�");
define("_MD_MODFOLDER_TITLE", "�ե������̾���ѹ�");
define("_MD_MODFOLDER_INPUT", "�ե������%s�ɤο�����̾�������Ϥ��Ƥ�������");
define("_MD_MODFOLDER_RENAME", "�ѹ�");


// folder controll
define("_MD_ATTRFOLDER_TITLE", "�ե����������������������");
define("_MD_ATTRFOLDER_BASIC_SETTINGS_TITLE", "��������");
define("_MD_ATTRFOLDER_INHERIT_FOLDER", "�ƥե�����Υ�����������Ѿ�����");
define("_MD_ATTRFOLDER_HIDDEN_FOLDER", "���Υե�����򱣤�");
define("_MD_ATTRFOLDER_BASIC_SETTINGS_SUBMIT", "�ѹ���Ŭ��");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_TITLE", "���롼�פ��ȤΥ���������");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_NOTICE", "��������ϡֿƥե�����Υ�����������Ѿ�����פΥ����å�������Ƥ��ʤ����Τ�ͭ���Ǥ�");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_SUBMIT", "�ѹ���Ŭ��");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_RESET", "�����᤹");

define("_MD_RIGHTS_NOVALID_FOLDER", "�������ե���������򤷤Ƥ�������");
define("_MD_RIGHTS_UPDATE_GOOD", "���� ����");
define("_MD_RIGHTS_UPDATE_BAD", "���� ����");
define("_MD_GESTION_PATH", "�ե�����Υѥ�: ");
define("_MD_GESTION_VALIDATE", "����");
define("_MD_GESTION_SAVE_GOOD", "���� ����");
define("_MD_GESTION_SAVE_BAD", "���� ����"); 

// Group permission phrases
define('_MD_PERMADDOK','���롼�ס��ѡ��ߥå��������ꤷ�ޤ�����');

define("_MD_FILE_EXIST", "���ꤵ�줿�ե�����̾�ϴ���¸�ߤ��ޤ����̤�̾������ꤷ�Ƥ���������");


?>