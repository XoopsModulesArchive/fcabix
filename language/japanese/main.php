<?php
// $Id: main.php,v 1.21 2005/07/01 13:22:31 merlin Exp $
// Id: main.php,v 1.2 2005/03/14 20:05:43 jsaucier Exp 
// Module language Japanese Version


define("_MD_FCABIX_MAIN_TITLE", "ファイルキャビネット");

define("_MD_GO_BACK", "<a href='javascript:history.go(-1);'>Return</a>");

define("_MD_NAVIGATION_TITLE","ナビゲート : ");
define("_MD_PARENTFOLDER", "Root");

define("_MD_ACTION_TITLE","このフォルダに対して可能な操作 : ");

define("_MD_FOLDER_TITLE","フォルダのリスト : ");
define("_MD_FOLDER_NAME", "名前");
define("_MD_FOLDER_DELETE", "消去");
define("_MD_FOLDER_HIDDEN", "隠蔽");
define("_MD_FOLDER_CREATE", "フォルダ作成");
define("_MD_FOLDER_ERASE", "フォルダ消去");
define("_MD_FOLDER_MOD", "フォルダ名変更");
define("_MD_FOLDER_MOD2", "名前変更");


define("_MD_DOCUMENT_TITLE","文書リスト : ");
define("_MD_FILE_NAME", "名前");
define("_MD_FILE_DATE", "データ変更");
define("_MD_FILE_SPACE", "大きさ");
define("_MD_FILE_SUMMARY", "要約");
define("_MD_FILE_DELETE", "消去");
define("_MD_FILE_CREATE", "ファイル追加");


define("_MD_ADMIN_TITLE", "管理");

define("_MD_FOLDER_ERROR_RIGHTS", "このフォルダは存在しません"); 

define("_MD_EDITRIGHTS", "アクセス権編集 : ");

define("_MD_FCABIX_CREATEFOLDER", "フォルダを次の下に作成します : ");
define("_MD_CREATEFOLDER_NAME", "フォルダ名 : ");
define("_MD_CREATEFOLDER_SUBMIT", "作成");
define("_MD_CREATEFOLDER_CANCEL", "取消");

define("_MD_CREATEFOLDER_SAVE_GOOD", "フォルダが追加されました");
define("_MD_CREATEFOLDER_SAVE_ERROR_NAME", "正しいフォルダ名を入力してください");
define("_MD_CREATEFOLDER_SAVE_ERROR_BD", "BDへ追加する時にエラーとなりました。フォルダは作成できませんでした");
define("_MD_CREATEFOLDER_SAVE_ERROR_PATH", "パスの設定エラーです。フォルダは作成できませんでした");
define("_MD_CREATEFOLDER_SAVE_ERROR_WRITABLE", "親フォルダが書込不可です。フォルダは作成できませんでした");


define("_MD_FCABIX_MODFOLDER", "フォルダの名前変更 : ");
define("_MD_MODFOLDER_NAME", "フォルダの新しい名前 : ");
define("_MD_MODFOLDER_SUBMIT", "名前変更");
define("_MD_MODFOLDER_CANCEL", "キャンセル");

define("_MD_MODFOLDER_PARENT", "Root フォルダは変更できません");

define("_MD_MODFOLDER_BAD_NAME", "正しいフォルダ名を入力してください");
define("_MD_MODFOLDER_SAVE_GOOD", "フォルダは変更されました");
define("_MD_MODFOLDER_SAVE_BAD", "フォルダの変更に失敗しました");
define("_MD_MODFOLDER_SAVE_DONTEXIST", "そのフォルダは存在しません");

define("_MD_FCABIX_ERASEFOLDER", "フォルダの消去: ");
define("_MD_ERASEFOLDER_CONFIRM", "このフォルダとその中身を消去しても良いですか?");
define("_MD_ERASEFOLDER_SUBMIT", "確認");
define("_MD_ERASEFOLDER_CANCEL", "キャンセル");

define("_MD_ERASEFOLDER_GOOD", "The folder has been erased");
define("_MD_ERASEFOLDER_BAD", "Error in erasing the folder");
define("_MD_ERASEFOLDER_ERASE_DONTEXIST", "The folder don't exist");
define("_MD_ERASEFOLDER_ERASE_RIGHTS", "The folder don't have the good permissions to be erased");

define("_MD_ERASEFOLDER_PARENT", "You cannot erase the Root folder");


define("_MD_FCABIX_CREATEFILE", "以下のフォルダにファイルを作成します : ");
define("_MD_CREATEFILE_NAME", "追加するファイル : ");
define("_MD_CREATEFILE_SUBMIT", "追加");
define("_MD_CREATEFILE_CANCEL", "キャンセル");

define("_MD_CREATEFILE_SAVE_GOOD", "The file has been added");
define("_MD_CREATEFILE_SAVE_WRITABLE", "このフォルダはファイルの書込が禁止されています。");
define("_MD_CREATEFILE_UPLOAD_PROBLEM", "Error in file upload");
define("_MD_CREATEFILE_UPLOAD_NOFILE", "You must specify a file");
define("_MD_CREATEFILE_UPLOAD_TOOBIG", "File too big, please choose another");
define("_MD_CREATEFILE_UPLOAD_PARTIAL", "The file has been uploaded partially");
define("_MD_CREATEFILE_UPLOAD_EXTFILTER", "この拡張子は許可されていません");

define("_MD_FCABIX_ERASEFILE", "ファイルを消去します : ");
define("_MD_ERASEFILE_CONFIRM", "このファイルを消去して良いですか?");
define("_MD_ERASEFILE_SUBMIT", "確認");
define("_MD_ERASEFILE_CANCEL", "キャンセル");

define("_MD_ERASEFILE_GOOD", "このファイルの消去に成功しました.");
define("_MD_ERASEFILE_ERROR_PATH", "Error in file path, the file will not be erased");
define("_MD_ERASEFILE_ERROR_BD", "Error when erasing the file in BD, the file will not be erased");
define("_MD_ERASEFILE_ERROR_DONTEXIST", "The file don't exist");
define("_MD_ERASEFILE_ERROR_RIGHTS", "The file don't have the good permissions to be erased");

define("_MD_FILE_ERROR_RIGHTS", "This file doesn't exist");
define("_MD_FCABIX_MODFILE", "ファイルの名前変更 : ");
define("_MD_MODFILE_NAME", "ファイルの新しい名前 : ");
define("_MD_MODFILE_SUBMIT", "名前変更");
define("_MD_MODFILE_CANCEL", "キャンセル");

define("_MD_MODFILE_BAD_NAME", "正しいファイル名を入れてください");
define("_MD_MODFILE_SAVE_GOOD", "ファイルは変更されました");
define("_MD_MODFILE_SAVE_BAD", "ファイル変更時のエラ-");
define("_MD_MODFILE_SAVE_DONTEXIST", "そのファイルはありません");

define("_MD_FCABIX_VIEWFILE", "ファイル 情報");
define("_MD_VIEWFILE_TYPE", "ファイル タイプ : ");
define("_MD_VIEWFILE_NAME", "ファイル 名 : ");
define("_MD_VIEWFILE_SPACE", "ファイル サイズ : ");
define("_MD_VIEWFILE_CREATEDDATE", "作成日 : ");
define("_MD_VIEWFILE_MODDATE", "更新日 : ");
define("_MD_VIEWFILE_OWNER", "ファイル作成者 : ");
define("_MD_VIEWFILE_USERMOD", "最終更新者 : ");
define("_MD_VIEWFILE_ACTION", "可能な操作");
define("_MD_VIEWFILE_GET", "ダウンロード");
define("_MD_VIEWFILE_MAJ", "更新");
define("_MD_VIEWFILE_RENAME", "名前変更");
define("_MD_VIEWFILE_ERASE", "消去");

define("_MD_VIEWFILE_BACKFOLDER", "フォルダにもどる");

define("_MD_SUMMARY_TITLE", "要約");


define("_MD_GETFILE_GOOD", "ダウンロード終了End of download");

define("_MD_FCABIX_MAJFILE", "ファイルの更新 : ");
define("_MD_MAJFILE_NAME", "更新するファイルを選択してください : ");
define("_MD_MAJFILE_SUBMIT", "更新");
define("_MD_MAJFILE_CANCEL", "キャンセル");

define("_MD_MAJFILE_SAVE_GOOD", "The file has been updated");
define("_MD_MAJFILE_SAVE_ERROR_PATH", "Error in file path, the file will not be updated");
define("_MD_MAJFILE_SAVE_ERROR_BD", "Error in inserting in the BD, the file will not be updated");
define("_MD_MAJFILE_SAVE_ERROR_WRITABLE", "The file don't have the good permissions to be updated");
define("_MD_MAJFILE_SAVE_ERROR_DONTEXIST", "The file don't exist");


define("_MD_DBRIGHTS_CREATEFOLDER", "フォルダ作成");
define("_MD_DBRIGHTS_MODFOLDER", "フォルダ変更");
define("_MD_DBRIGHTS_ERASEFOLDER", "フォルダ消去");
define("_MD_DBRIGHTS_CREATEFILE", "ファイル作成");
define("_MD_DBRIGHTS_MODFILE", "ファイル変更");
define("_MD_DBRIGHTS_ERASEFILE", "ファイル消去");
define("_MD_DBRIGHTS_ACCESSFOLDER", "フォルダ表示");

define("_MD_FLEXIGRID_EXPLORER_TITLE", "エクスプローラ");
define("_MD_FLEXIGRID_COL_NAME", "名前");
define("_MD_FLEXIGRID_COL_CREATEDBY", "作成者");
define("_MD_FLEXIGRID_COL_CREATEDAT", "作成日時");
define("_MD_FLEXIGRID_COL_UPDATEDBY", "更新者");
define("_MD_FLEXIGRID_COL_UPDATEDAT", "更新日時");
define("_MD_FLEXIGRID_COL_TYPE", "種類");
define("_MD_FLEXIGRID_COL_SIZE", "サイズ");
define("_MD_FLEXIGRID_DISPLAYING", "{total} のファイルのうち、{from} から {to} までを表示中");
define("_MD_FLEXIGRID_PROCESSING", "処理中です。しばらくお待ちください…");
define("_MD_FLEXIGRID_NOITEMS", "ファイルは見つかりませんでした。");
define("_MD_FLEXIGRID_COMMAND_MAKEDIR", "新しいフォルダ");
define("_MD_FLEXIGRID_CONFIRM_MULTIDL", "{count} つのファイルをダウンロードしますか？");
define("_MD_FLEXIGRID_CONFIRM_DELETE", "{count} つのファイルを削除してよろしいですか？");
define("_MD_FLEXIGRID_CONFIRM_DELETEDIR", "フォルダ \"{name}\" を削除してよろしいですか？");
define("_MD_FLEXIGRID_CONNECTIONERROR", "データの取得に失敗しました。");
define("_MD_FLEXIGRID_CONFIRM_CANNOT_DELETE", "これらのファイルを削除することはできません。");
define("_MD_FLEXIGRID_COMMAND_DOWNLOAD", "ダウンロード");
define("_MD_FLEXIGRID_COMMAND_DELETE", "ファイル削除");
define("_MD_FLEXIGRID_COMMAND_DELETEDIR", "フォルダ削除");
define("_MD_FLEXIGRID_COMMAND_PARENT", "親フォルダに移動");
define("_MD_FLEXIGRID_COMMAND_FILEPROPERTY", "ファイル設定");
define("_MD_FLEXIGRID_COMMAND_DIRPROPERTY", "フォルダ設定");
define("_MD_FLEXIGRID_COMMAND_UPLOAD", "アップロード");
define("_MD_UPLOADIFY_START", "アップロード開始");
define("_MD_UIDIALOG_CONTROL", "操作");
define("_MD_UIDIALOG_DELETE", "削除");
define("_MD_UIDIALOG_OK", "OK");
define("_MD_UIDIALOG_CANCEL", "キャンセル");
define("_MD_UIDIALOG_GROUPPERM", "アクセス権の設定");
define("_MD_UIDIALOG_TITLE_ABOUT", "Fcabixについて");
define("_MD_UIDIALOG_TITLE_MAKEDIR", "新しいフォルダの作成");
define("_MD_UIDIALOG_TITLE_UPLOAD", "ファイル・アップロード");
define("_MD_UIDIALOG_TITLE_DOWNLOAD", "ファイル・ダウンロード");
define("_MD_UIDIALOG_UPLOAD_EXT", "アップロードできるファイルの種類");
define("_MD_UIDIALOG_UPLOAD_MAXSIZE", "1ファイルあたりの最大サイズ");
define("_MD_UIDIALOG_UPLOAD_MAXNUM", "1回にアップロードできるファイル数");
define("_MD_JSUI_FLASH_IS_DISABLED", "Flashプラグインが有効になっていません");

define("_MD_NOJSGRID_SELET", "選択");
define("_MD_NOJSGRID_DELETE", "削除する");
define("_MD_NOJSGRID_RENAME", "名前を変更する");
define("_MD_NOJSGRID_SETGROUPPERM", "アクセス権の設定");
define("_MD_NOJSGRID_DOWNLOAD_SELETED", "選択したファイルをダウンロード");

define("_MD_CREATEFILE_TITLE", "ファイル・アップロード");
define("_MD_CREATEFILE_SELECTFILE", "アップロードするファイルを選んでください");
define("_MD_CREATEFILE_UPLOAD", "アップロード");

define("_MD_CREATEFOLDER_TITLE", "新しいフォルダの作成");
define("_MD_CREATEFOLDER_INPUTNAME", "作成するフォルダの名前を指定してください");
define("_MD_CREATEFOLDER_CREATE", "フォルダを作成");

define("_MD_ERASEFILE_TITLE", "ファイルの削除");
define("_MD_ERASEFILE_ERASE", "このファイルを削除");
define("_MD_ERASEFILE_ERASE_COMFIRM", "ファイル“%s”を削除しますか？");
define("_MD_ERASEFOLDER_TITLE", "フォルダの削除");
define("_MD_ERASEFOLDER_ERASE", "このフォルダを削除");
define("_MD_ERASEFOLDER_ERASE_COMFIRM", "フォルダ“%s”を削除しますか？");

define("_MD_MODFILE_TITLE", "ファイルの名前変更");
define("_MD_MODFILE_INPUT", "ファイル“%s”の新しい名前を入力してください");
define("_MD_MODFILE_RENAME", "変更");
define("_MD_MODFOLDER_TITLE", "フォルダの名前変更");
define("_MD_MODFOLDER_INPUT", "フォルダ“%s”の新しい名前を入力してください");
define("_MD_MODFOLDER_RENAME", "変更");


// folder controll
define("_MD_ATTRFOLDER_TITLE", "フォルダ・アクセス権の設定");
define("_MD_ATTRFOLDER_BASIC_SETTINGS_TITLE", "基本設定");
define("_MD_ATTRFOLDER_INHERIT_FOLDER", "親フォルダのアクセス権を継承する");
define("_MD_ATTRFOLDER_HIDDEN_FOLDER", "このフォルダを隠す");
define("_MD_ATTRFOLDER_BASIC_SETTINGS_SUBMIT", "変更を適用");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_TITLE", "グループごとのアクセス権");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_NOTICE", "この設定は「親フォルダのアクセス権を継承する」のチェックがされていない場合のみ有効です");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_SUBMIT", "変更を適用");
define("_MD_ATTRFOLDER_GROUPPERM_SETTINGS_RESET", "元に戻す");

define("_MD_RIGHTS_NOVALID_FOLDER", "正しいフォルダを選択してください");
define("_MD_RIGHTS_UPDATE_GOOD", "更新 成功");
define("_MD_RIGHTS_UPDATE_BAD", "更新 失敗");
define("_MD_GESTION_PATH", "ファイルのパス: ");
define("_MD_GESTION_VALIDATE", "更新");
define("_MD_GESTION_SAVE_GOOD", "更新 成功");
define("_MD_GESTION_SAVE_BAD", "更新 失敗"); 

// Group permission phrases
define('_MD_PERMADDOK','グループ・パーミッションを設定しました。');

define("_MD_FILE_EXIST", "指定されたファイル名は既に存在します。別の名前を指定してください。");


?>