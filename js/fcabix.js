
(function($){

$(document).ready(
	function(){
		
		// Fcabix settings.
		var fcabix = {}, LANGS, JSON, FUNCS;
		
		LANGS = {
			main_title:           "<{$fcabix.langs.main_title|default:'File Cabinet for Xoops'}>",
			explorer_title:       "<{$fcabix.langs.explorer_title|default:'File List'}>",
			root:                 "<{$fcabix.langs.root|default:'Root'}>",
			id:                   "<{$fcabix.langs.id|default:'ID'}>",
			name:                 "<{$fcabix.langs.name|default:'Name'}>",
			created_at:           "<{$fcabix.langs.created_at|default:'Created at'}>",
			created_by:           "<{$fcabix.langs.created_by|default:'Owner'}>",
			updated_at:           "<{$fcabix.langs.updated_at|default:'Updated at'}>",
			updated_by:           "<{$fcabix.langs.updated_by|default:'User'}>",
			size:                 "<{$fcabix.langs.size|default:'Size'}>",
			type:                 "<{$fcabix.langs.type|default:'Type'}>",
			hidden:               "<{$fcabix.langs.hidden|default:'Hidden'}>",
			erasable:             "<{$fcabix.langs.erasable|default:'Erasable'}>",
			modifiable:           "<{$fcabix.langs.modifiable|default:'Modifiable'}>",
			folder_writable:      "<{$fcabix.langs.folder_writable|default:'Folder-writable'}>",
			file_writable:        "<{$fcabix.langs.file_writable|default:'File-writable'}>",
			displaying:           "<{$fcabix.langs.displaying|default:'Displaying {from} to {to} of {total} items'}>",
			processing:           "<{$fcabix.langs.processing|default:'Processing, please wait ...'}>",
			nomsg:                "<{$fcabix.langs.noitems|default:'No items'}>",
			newfolder:            "<{$fcabix.langs.newfolder|default:'New folder'}>",
			confirm_multidl:      "<{$fcabix.langs.confirm_multidl|default:'Download {count} items?'}>",
			confirm_delete:       "<{$fcabix.langs.confirm_delete|default:'Delete {count} items?'}>",
			confirm_deletedir:    "<{$fcabix.langs.confirm_deletedir|default:'Delete \"{name}\" folder?'}>",
			connection_error:     "<{$fcabix.langs.connection_error|default:'Connection Error'}>",
			command_download:     "<{$fcabix.langs.command_download|default:'Download'}>",
			command_delete:       "<{$fcabix.langs.command_delete|default:'Delete'}>",
			command_deletedir:    "<{$fcabix.langs.command_deletedir|default:'Delete this folder'}>",
			command_parent:       "<{$fcabix.langs.command_parent|default:'Parent f'}>",
			command_upload:       "<{$fcabix.langs.command_upload|default:'Upload'}>",
			command_fileproperty: "<{$fcabix.langs.command_fileproperty|default:'Rename f'}>",
			command_dirproperty:  "<{$fcabix.langs.command_dirproperty|default:'Rename d'}>",
			command_makedir:      "<{$fcabix.langs.command_makedir|default:'Make d'}>",
			dialog_title_about:         "<{$fcabix.langs.dialog_title_about|default:'About fcabix'}>",
			dialog_title_upload:        "<{$fcabix.langs.dialog_title_upload|default:'Upload'}>",
			dialog_title_download:      "<{$fcabix.langs.dialog_title_download|default:'Download'}>",
			dialog_title_deletefile:    "<{$fcabix.langs.dialog_title_deletefile|default:'Delete'}>",
			dialog_title_deletedir:     "<{$fcabix.langs.dialog_title_deletedir|default:'Delete'}>",
			dialog_title_fileproperty:  "<{$fcabix.langs.dialog_title_fileproperty|default:'Property'}>",
			dialog_title_dirproperty:   "<{$fcabix.langs.dialog_title_dirproperty|default:'Property'}>",
			dialog_title_makedir:       "<{$fcabix.langs.dialog_title_makedir|default:'Create new folder'}>",
			dialog_title_setperm:       "<{$fcabix.langs.dialog_title_setperm|default:'Permissions'}>",
			dialog_title_about:         "<{$fcabix.langs.dialog_title_about|default:'About fcabix'}>",
			dialog_control:             "<{$fcabix.langs.dialog_control|default:'Control'}>",
			dialog_delete:              "<{$fcabix.langs.dialog_delete|default:'Delete'}>",
			dialog_hide:                "<{$fcabix.langs.dialog_hide|default:'Hide'}>",
			dialog_inherit:             "<{$fcabix.langs.dialog_inherit|default:'Inherit'}>",
			dialog_groupperm:           "<{$fcabix.langs.dialog_groupperm|default:'Permissions'}>",
			dialog_button_ok:           "<{$fcabix.langs.dialog_ok|default:'Ok'}>",
			dialog_button_cancel:       "<{$fcabix.langs.dialog_cancel|default:'Cancel'}>",
			dialog_button_upload:       "<{$fcabix.langs.dialog_upload|default:'Upload'}>",
			dialog_upload_ext:          "<{$fcabix.langs.dialog_upload_ext}>",
			dialog_upload_maxnum:       "<{$fcabix.langs.dialog_upload_maxnum}>",
			dialog_upload_maxsize:      "<{$fcabix.langs.dialog_upload_maxsize}>"
		};
		
		FUNCS = {
				
			// Flexigrid's event-listeners
			
			flexigrid_preProcess: function(data){
				JSON = data;
			//	if(console && console.log){ console.log(JSON); }
				return JSON;
			},
			
			flexigrid_onSuccess: function(){
				var path = $("#fcabix-jsui-path").empty();
				$.each(JSON.folder_path, function(index, value){
					if(value.id == JSON.current_folder.id){
						path.append('<span class="fcabix-path-node fcabix-path-node-'+(value.id == 1?'root':'folder-open')+'">'+ (value.id == 1 ? LANGS.root : value.name) +' &gt;</span></span>');
					}else{
						path.append($('<span class="fcabix-path-node fcabix-path-node-'+(value.id == 1?'root':'folder')+'"><span class="fcabix-path-node-label">'+ (value.id == 1 ? LANGS.root : value.name) +' &gt;</span></span>').click(function(){
							fcabix.flexigrid.options.query = value.id;
							fcabix.flexigrid.ui.flexOptions(fcabix.flexigrid.options).flexReload();
						}));
					}
				});
				fcabix.flexigrid.options.query = JSON.current_folder.id; // important!
				FUNCS.refreshMimeTypeIcons();
			},
			
			flexigrid_onError: function(XMLHttpRequest, textStatus, errorThrown){
				//alert('Flexigrid Error');
			},
			
			// Uploadify's event-listeners
			
			uploadify_oncomplete: function(event, data){
				window.setTimeout(function(){
					DIALOGS.upload.dialog('close');
					DIALOGS.upload.empty(); // for Opera 9
				}, 1000); // for Opera and Safari
				fcabix.flexigrid.ui.flexReload();
			},
			
			// Show data & confirm user's command.
			
			download: function(){
				var trSelected = $(".trSelected",fcabix.flexigrid.ui);
					//trSelected = trSelected.filter("[id*='-f-']");
					if(trSelected.length>0){
						if(trSelected.length == 1){
							location.href = "get_file.php?curent_dir=" + JSON.current_folder.id +"&curent_file=" + trSelected.get(0).id.split('-')[2];
							$(".trSelected",fcabix.flexigrid.ui).removeClass("trSelected");
						}else{
							DIALOGS.download.append(LANGS.confirm_multidl.replace(/\{count\}/, trSelected.length)).dialog('open');
						}
					}
			},
			deletefile: function(){
				var trSelected = $(".trSelected",fcabix.flexigrid.ui);
				if(trSelected.length>0){
					DIALOGS.deletefile
						.append(LANGS.confirm_delete.replace(/\{count\}/, trSelected.length))
						.dialog('open');
				}
			},
/*			parent: function(){
				if(JSON.current_folder.id != 1){
					fcabix.flexigrid.options.query = JSON.current_folder.parent;
					fcabix.flexigrid.ui.flexOptions(fcabix.flexigrid.options).flexReload();
				}
			},*/
			upload: $('body').fileUpload && <{$fcabix.config.use_flash}>
				? function(){ // Flash Upload
					if(JSON.current_folder.file_writable){
						fcabix.uploadify.ui = $('<div id="fcabix-uploader"></div>').appendTo(DIALOGS.upload);
						fcabix.uploadify.ui.fileUpload(fcabix.uploadify.options);
						
						DIALOGS.upload.append(upLimit).dialog('open');
						$(".trSelected",fcabix.flexigrid.ui).removeClass("trSelected");
						
					}
				}
				: function(){ // NoFla Upload
					if(JSON.current_folder.file_writable){
						DIALOGS.upload
							.append('<form method="post" action="insert_info.php" enctype="multipart/form-data" '+
							'target="fcabix_jsui_nofla_uploader_iframe" id="fcabix-jsui-nofla-uploader-form">'+
								'<input type="hidden" name="folder_id" value="'+JSON.current_folder.id+'" />'+
								'<input type="hidden" name="op" value="insert_file" />'+
								'<input type="hidden" name="MAX_FILE_SIZE" value="'+<{$fcabix.config.upload_maxsize}>+'" />'+
							'</form>')
							.append('<iframe name="fcabix_jsui_nofla_uploader_iframe"></iframe>').append(upLimit);
						FUNCS.addNoFlaUploaderInput(true);
						DIALOGS.upload.dialog('open');
					}
				},
			fileproperty: function(){
				
				var trSelected = $(".trSelected",fcabix.flexigrid.ui);
				if(trSelected.length < 1) return;
				var data = $.data(trSelected.get(0), 'fcabixdata'),
					properties = [/*'type', */'size', /*'created_at', 'created_by__uname',*/ 'updated_at', 'updated_by__uname'],
					dialog = DIALOGS.fileproperty;
				if(data.modifiable == 1){
					var table = '<table><tbody><tr><th>' +LANGS.name + '</th><td><input type="text" name="filename" value="' + data.name + '" />'
							  + '<input type="hidden" name="id" value="' + data.id + '" />'
							  + '<input type="hidden" name="default" value="' + data.name + '" /></td></tr>';
				}else{
					var table = '<table><tbody><tr><th>' +LANGS.name + '</th><td>' + data.name + '</td></tr>';
				}
				$.each(properties, function(key, value){
					table += '<tr><th nowrap="nowrap">' +LANGS[value.replace(/__uname/, '')] + '</th><td>' + data[value] + '</td></tr>';
				});
				table += '</tbody></table>';
				dialog.append(table).dialog('open');
				$(".trSelected:gt(1)",fcabix.flexigrid.ui).removeClass("trSelected");
//				fcabix.uploadify.ui.fileUploadClearQueue();
			},
			dirproperty: function(){
				var data = JSON.current_folder,
					properties = [/*'created_at', 'created_by__uname',*/ 'updated_at', 'updated_by__uname'],
					dialog = DIALOGS.dirproperty;
				if(data.id > 1 && data.modifiable == 1){
					var table = '<table><tbody><tr><th>' +LANGS.name + '</th><td><input type="text" name="filename" value="' + data.name + '" />'
							  + '<input type="hidden" name="id" value="' + data.id + '" />'
							  + '<input type="hidden" name="default" value="' + data.name + '" /></td></tr>';
				}else{
					var table = '<table><tbody><tr><th>' +LANGS.name + '</th><td>' + data.name + '</td></tr>';
				}
				$.each(properties, function(key, value){
					table += '<tr><th nowrap="nowrap">' +LANGS[value.replace(/__uname/, '')] + '</th><td>' + data[value] + '</td></tr>';
				});
				table += '<tr><th>' +LANGS.dialog_control + '</th><td><button id="fcabix-dialog-dirproperty-delete" class="ui-state-default'+(data.id > 1 && data.erasable?'':' ui-state-disabled')+' ui-corner-all"'+(data.id > 1 && data.erasable?'':' disabled="disabled"')+'>' +LANGS.dialog_delete + '</button><button id="fcabix-dialog-dirproperty-groupperm" class="ui-state-default'+(data.id > /*1*/0 && data.perm_settable?'':' ui-state-disabled')+' ui-corner-all"'+(data.id > /*1*/0 && data.perm_settable?'':' disabled="disabled"')+'>' +LANGS.dialog_groupperm + '</button></td></tr>';
				table += '</tbody></table>';
				dialog.append(table).dialog('open');
				if(data.id > 1 && data.erasable){
					$('#fcabix-dialog-dirproperty-delete').click(FUNCS.deleteDir);
				}
				if(data.id > /*1*/0 && data.perm_settable){
					$('#fcabix-dialog-dirproperty-groupperm').click(FUNCS.setperm);
				}
				$(".trSelected:gt(1)",fcabix.flexigrid.ui).removeClass("trSelected");
			},
			makedir: function(){
				var data = JSON.current_folder;
				
				if(data.folder_writable){
					var dialog = DIALOGS.makedir;
					var table = '<table><tbody><tr><th>' +LANGS.name + '</th><td><input type="text" value="' +LANGS.newfolder + '" /></td></tr></tbody></table>';
					dialog.append(table).dialog('open');
					$('input', dialog).select();
					$(".trSelected:gt(1)",fcabix.flexigrid.ui).removeClass("trSelected");
				}
			},
			setperm: function(){
				if(JSON.current_folder.id > /*1*/0 && JSON.current_folder.perm_settable){
					var data = JSON.groupperm,
						dialog = DIALOGS.setperm,
						forms = $('<div id="fcabix-dialog-setperm-forms"></div>').appendTo(dialog),
						tbody = $('<tbody></tbody>');
					for(var i in data.groups){
						if(i==0){
							var rights = data.groups[i].rights;
							for(var j in rights){
								forms.append('<input type="hidden" name="'+rights[j].checkbox.name+'" value="'+rights[j].checkbox.value+'" />');
								var hiddens = rights[j].hiddens;
								for(var k in hiddens){
									forms.append('<input type="hidden" name="'+hiddens[k].name+'" value="'+hiddens[k].value+'" />');
								}
							}
						}else{
							var tr = $('<tr></tr>'), th = $('<th nowrap="nowrap">'+data.groups[i].name+'</th>');
							var td = $('<td></td>'), rights = data.groups[i].rights;
							for(var j in rights){
								var checkbox = '<input type="checkbox" name="'+rights[j].checkbox.name+'" value="'+rights[j].checkbox.value+'"'+(rights[j].checkbox.checked?' checked="checked"':'')+(JSON.current_folder.inherit?' disabled="disabled"':'')+' />';
								var label = '<label>'+checkbox+rights[j].checkbox.label+'</label>';
								td.append(label);
								var hiddens = rights[j].hiddens;
								for(var k in hiddens){
									td.append('<input type="hidden" name="'+hiddens[k].name+'" value="'+hiddens[k].value+(JSON.current_folder.inherit?' disabled="disabled"':'')+'" />');
								}
								td.append('<span>&nbsp;</span>');
								if(j == 3){ td.append('<br />'); }
							}
							tbody.append(tr.append(th).append(td));
						}
					}
					for(var i in data.hiddens){
						forms.append('<input type="hidden" name="'+data.hiddens[i].name+'" value="'+data.hiddens[i].value+'" />');
					}
					var input_hide = $('<input type="checkbox" name="is_hidden" value="1" />').appendTo(forms);
					var input_inherit = $('<input type="checkbox" name="is_inherit" value="1" />').appendTo(forms);
					if(JSON.current_folder.hidden){ input_hide.attr('checked', 'checked'); }
					if(JSON.current_folder.inherit){ input_inherit.attr('checked', 'checked'); }
					input_inherit.change(function(){
						if(this.checked){
							$('input[name^=perms]', tbody).attr('disabled', 'disabled');
						}else{
							$('input[name^=perms]', tbody).removeAttr('disabled');
						}
					});
					forms.append($('<label></label>').append(input_hide).append(LANGS.dialog_hide));
					forms.append('<br />');
					forms.append($('<label></label>').append(input_inherit).append(LANGS.dialog_inherit));
					forms.append($('<table></table>').append(tbody));
					dialog.dialog('open');
				}
			},
			deleteDir: function(dirprop_dialog){
				if(JSON.current_folder.id > 1 && JSON.current_folder.erasable){
					DIALOGS.deletedir.append(LANGS.confirm_deletedir.replace(/\{name\}/, JSON.current_folder.name)).dialog('open');
				}
			},
			
			// Bring user's command into practice.
			
			executeUpload: $('body').fileUpload && <{$fcabix.config.use_flash}>
				? function(dialog){ // Flash Upload
					var params = $.param({
							op: 'insert_file',
							folder_id: JSON.current_folder.id,
							upload_ticket: JSON.upload_ticket,
							XOOPS_G_TICKET: JSON.XOOPS_G_TICKET
					});
					fcabix.uploadify.ui.fileUploadSettings('scriptData', params);
					fcabix.uploadify.ui.fileUploadStart();
				}
				: function(dialog){ // NoFla Upload
					$('#fcabix-jsui-nofla-uploader-form').submit();
				},
			executeDownload: function(dialog){
				var trSelected = $(".trSelected", fcabix.flexigrid.ui);
				if(trSelected.length>0){
					var itemlist ="";
					trSelected.each(function(){
						itemlist += this.id.split('-')[2] + ",";
					});
					location.href = "get_file.php?curent_dir=" + JSON.current_folder.id +"&curent_file=" + itemlist;
					trSelected.removeClass("trSelected");
				}
			},
			executeDeleteFile: function(dialog){
				var trSelected = $(".trSelected", fcabix.flexigrid.ui), itemlist = "";
				trSelected.each(function(){
					itemlist += this.id.split('-')[2] + ",";
				});
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "json_crud.php",
					data: {
						op: 'erase_file',
						folder_id: JSON.current_folder.id,
						items: itemlist
					},
					success: function(data){
						fcabix.flexigrid.ui.flexReload();
					}
				 });

			},
			executeRenameFile: function(dialog){
				var fid = $(dialog).find(':hidden[name=id]').val();
				var default_fn = $(dialog).find(':hidden[name=default]').val();
				var new_fn = $(dialog).find(':text').val();
				if(new_fn && new_fn.length > 0 && default_fn != new_fn){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "json_crud.php",
						data: {
							op: 'mod_file',
							folder_id: JSON.current_folder.id,
							file_id: fid,
							new_file_name: new_fn
						},
						success: function(data){ fcabix.flexigrid.ui.flexReload(); $(dialog).dialog('close'); }
					});
				}else{
					$(dialog).dialog('close');
				}
			},
			executeRenameDir: function(dialog){
				var default_dn = $(dialog).find(':hidden[name=default]').val();
				var new_dn = $(dialog).find(':text').val();
				if(new_dn && new_dn.length > 1 && JSON.current_folder.id > 1 && default_dn != new_dn){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "json_crud.php",
						data: {
							op: 'mod_folder',
							folder_id: JSON.current_folder.id,
							new_folder_name: new_dn
						},
						success: function(data){ fcabix.flexigrid.ui.flexReload(); $(dialog).dialog('close'); }
					});
					this.refreshFolderTree();
				}else{
					$(dialog).dialog('close');
				}
			},
			executeMakeDir: function(dialog){
				var new_dn = $(':text', dialog).val();
				if(new_dn){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "json_crud.php",
						data: {
							op: 'insert_folder',
							folder_id: JSON.current_folder.id,
							folder_name: new_dn
						},
						success: function(data){
							fcabix.flexigrid.ui.flexReload();
							FUNCS.refreshFolderTree();
							$(dialog).dialog('close');
						}
					});
					
				}
			},
			executeDeleteDir: function(dialog){
				if(JSON.current_folder.id > 1 && JSON.current_folder.erasable){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "json_crud.php",
						data: {
							op: 'erase_folder',
							folder_id: JSON.current_folder.id
						},
						success: function(data){
							if(DIALOGS.dirproperty.dialog('isOpen')){
								DIALOGS.dirproperty.dialog('close');
							}
							fcabix.flexigrid.options.query = JSON.current_folder.parent;
							fcabix.flexigrid.ui.flexOptions(fcabix.flexigrid.options).flexReload();
							FUNCS.refreshFolderTree();
							$(dialog).dialog('close');
						}
					});
				}
			},
			executeSetPerm: function(dialog){
				if(JSON.current_folder.id > /*1*/0 && JSON.current_folder.perm_settable){
					var forms = $('#fcabix-dialog-setperm-forms'), obj = {};
					obj['folder_id'] = JSON.current_folder.id;
					forms.find('input').each(function(){
						if(this.type == 'checkbox'){
							obj[this.name] = this.checked ? 1 : 0;
						}else{
							obj[this.name] = this.value;
						}
					});
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "json_perm.php",
						data: obj
					});
				}
				$(dialog).dialog('close');
				fcabix.flexigrid.ui.flexReload();
			},
			
			// Etc.
			
			refreshFolderTree: function(){
				var func = arguments.callee, treedata = JSON.folder_tree, tree = $('<ul class="filetree"></ul>');
				var root = $('<li><span class="folder fcabix-foldertree-folder-'+treedata.id+(1 == 1 ? ' fcabix-foldertree-currentfolder' : '')+'">'+treedata.name+'</span></li>').appendTo(tree);
				if(!func.addFolders){
					func.addFolders = function(data, node){
						if(data.folders && data.folders.length > 0){
							var tree = $('<ul></ul>').appendTo(node);
							for(var i in data.folders){
								var li = '<li><span class="folder fcabix-foldertree-folder-'+data.folders[i].id+(JSON.current_folder.id == data.folders[i].id ? ' fcabix-foldertree-currentfolder' : '')+'">'+data.folders[i].name+'</span></li>';
								var node = $(li).appendTo(tree);
								if(data.folders[i].folders && data.folders[i].folders.length){
									func.addFolders(data.folders[i], node)
								}
							}
						}
					}
				}
				func.addFolders(treedata, root);
				$('#fcabix-treeview-inner').empty().append(tree.treeview({unique: <{if $fcabix.config.treeview_unique}>true<{else}>false<{/if}>, collapsed: true}));
				$('#fcabix-treeview span.folder').click(function(){
					if(this.className.match(/fcabix-foldertree-folder-(\d+)/)){
						var n = RegExp.$1 - 0;
						if(!isNaN(n)){
							if(fcabix.flexigrid.options.query != n){
								fcabix.flexigrid.options.query = n;
								fcabix.flexigrid.ui.flexOptions(fcabix.flexigrid.options).flexReload();
								$('#fcabix-treeview .fcabix-foldertree-currentfolder').removeClass('fcabix-foldertree-currentfolder');
								$(this).addClass('fcabix-foldertree-currentfolder');
							}
						}
					}
				});
				var pathdata = JSON.folder_path;
				for(var i in pathdata){
					$('#fcabix-jsui .fcabix-foldertree-folder-' + pathdata[i].id).dblclick();
				}
			},
			
			refreshMimeTypeIcons: function(){
				$("#fcabix-flexigrid").find('tr').each(function(){
					var data = $.data(this, 'fcabixdata'), td0 = $(this).find('td:first');
					td0.addClass('fcabix-flexigrid-filename fcabix-flexigrid-mimetype-'+(data.type||'etc'));
				});
			},
			
			addNoFlaUploaderInput: function(onDialogInit){
				if(!arguments.callee._input){
					arguments.callee._input = $('<div></div>').append(
						$('<input type="file" name="file_to_add[]"'+
						' class="ui-state-default ui-priority-primary ui-corner-all" />').change(arguments.callee)
					).append(
						$('<button type="button" class="ui-state-default ui-corner-all">x</button>').click(function(){ FUNCS.removeNoFlaUploaderInput(this); })
					);
				}
				
				var elm = $(this);
				
				if(onDialogInit === true){
					$('#fcabix-jsui-nofla-uploader-form').append(arguments.callee._input.clone(true));
				}else if(elm.val() && elm.parent().siblings('div').length < (<{$fcabix.config.upload_maxnum|default:10}>-1)){
					if('<{$fcabix.config.upload_ext}>'.indexOf(elm.val().split('.').pop()) == -1){
						elm.parent().remove();
					}
					$('#fcabix-jsui-nofla-uploader-form').append(arguments.callee._input.clone(true));
				}
			},
			
			removeNoFlaUploaderInput: function(elm){
				var div = $(elm).parent();
				if(div.siblings('div').length > 0){
					div.remove();
				}
			}
		};
		
		fcabix.flexigrid = {
			ui: undefined, // flexigrid instance
			options: {
				url: "json_filelist.php",
				dataType: "json",
				colModel : [

// index.phpで配列で指定された順序にしたがって、カラムの順序を制御する。json_filelist.phpのほうで出力するデータ順も同様に変更する必要があるので注意。

<{capture assign=flexigrid_col_name}>
{display:LANGS.name, name : "files_name", width: <{$fcabix.config.flexigrid_col_name_width|default:200}>, sortable : true, align: "left" }
<{/capture}>
<{capture assign=flexigrid_col_updatedat}>
{display:LANGS.updated_at, name : "files_modificationdate", width: <{$fcabix.config.flexigrid_col_updatedat_width|default:150}>, sortable : true, align: "left" }
<{/capture}>
<{capture assign=flexigrid_col_updatedby}>
{display:LANGS.updated_by, name : "files_usermod", width: <{$fcabix.config.flexigrid_col_updatedby_width|default:50}>, sortable : true, align: "left" }
<{/capture}>
<{capture assign=flexigrid_col_size}>
{display:LANGS.size, name : "files_space", width: <{$fcabix.config.flexigrid_col_size_width|default:50}>, sortable : true, align: "right" }
<{/capture}>
					
<{foreach from=$fcabix.config.flexigrid_cols_order item=col name=flexigrid_cols_order}>
	<{if !$smarty.foreach.flexigrid_cols_order.first}>,<{/if}>
	<{if $col == 'name'}>
		<{$flexigrid_col_name}>
	<{elseif $col == 'updatedat'}>
		<{$flexigrid_col_updatedat}>
	<{elseif $col == 'updatedby'}>
		<{$flexigrid_col_updatedby}>
	<{elseif $col == 'size'}>
		<{$flexigrid_col_size}>
	<{/if}>
<{/foreach}>
					
				],
				buttons: false,
/*				buttons : [
					{name:LANGS.command_download, bclass: "download", onpress : FUNCS.download},
					{name:LANGS.command_delete, bclass: "delete", onpress : FUNCS.deletefile},
					{name:LANGS.command_parent, bclass: "parent", onpress : FUNCS.parent},
					{name:LANGS.command_fileproperty, bclass: "renamefile", onpress : FUNCS.fileproperty},
					{name:LANGS.command_upload, bclass: "upload", onpress : FUNCS.upload},
					{name:LANGS.command_dirproperty, bclass: "renamedir", onpress : FUNCS.dirproperty},
					{name:LANGS.command_makedir, bclass: "makedir", onpress : FUNCS.makedir},
					{name:LANGS.command_deletedir, bclass: "deletedir", onpress : FUNCS.deletedir}
				],*/
//			test: (function(){console.log('test'); return true;})(),
//				searchitems : [
//					{display:LANGS.name, name : "files_name"},
//					{display:LANGS.updated_at, name : "files_modificationdate"}
//				],
				sortname: "files_name",
				sortorder: "asc",
				usepager: true,
				title: false,
				pagestat:LANGS.displaying,
				procmsg:LANGS.processing,
				errormsg:LANGS.connection_error,
				qtype: "files_foldersid",
				query: <{$fcabix.config.current_folder.id|default:1}>,
				useRp: <{$fcabix.config.flexigrid_useRp|default:true}>,
				rp: <{$fcabix.config.flexigrid_rp|default:20}>,
				rpOptions: [<{$fcabix.config.flexigrid_rpOptions|default:'10, 20, 40, 80'}>],
				showTableToggleBtn: false,
				resizable: false,
//				width: <{$fcabix.config.flexigrid_width|default:520}>,
				height: <{$fcabix.config.flexigrid_height|default:412}>,
				preProcess: FUNCS.flexigrid_preProcess,
				onSuccess: FUNCS.flexigrid_onSuccess,
				onNoItems: FUNCS.flexigrid_onSuccess,
				onError: FUNCS.flexigrid_onError
			}
		};
		
		var ext = ''
		$.each('<{$fcabix.config.upload_ext}>'.split(','), function(i, v){
			ext += '*.'+ v + ';'
		})
		
		fcabix.uploadify = {
			ui: undefined,
			options: {
				uploader: 'js/uploadify/uploader.swf',
				script: 'upload.php',
				fileDataName: 'file_to_add',
				folder: '/uploads',
				multi: true,
				simUploadLimit: <{$fcabix.config.upload_maxnum}>,
				sizeLimit: <{$fcabix.config.upload_maxsize}>,
				fileDesc: ext,
				fileExt: ext,
				auto: false,
				buttonText: 'Select',
				displayData: 'percentage',
				cancelImg: 'images/tango/16x16/emblem-unreadable.png',
				scriptData: {},
				onAllComplete: FUNCS.uploadify_oncomplete/*,
				onError: function(event, queueID, fileObj, errorObj){
					if(console && console.log){
						console.log('[Error]-------------------------');
						console.log('event:');
						console.log(event);
						console.log('queueID:');
						console.log(queueID);
						console.log('fileObj:');
						console.log(fileObj);
						console.log('errorObj:');
						console.log(errorObj);
						console.log('-------------------------[/Error]');
					}
				},
				onComplete: function(event, queueID, fileObj, response, data){
					if(console && console.log){
						console.log('[Success]-------------------------');
						console.log('event:');
						console.log(event);
						console.log('queueID:');
						console.log(queueID);
						console.log('fileObj:');
						console.log(fileObj);
						console.log('response:');
						console.log(response);
						console.log('data:');
						console.log(data);
						console.log('-------------------------[/Success]');
					}
					
				}*/
			}
		};
		DIALOGS = {
			upload: {
				title:LANGS.dialog_title_upload,
				buttons: {
					upload: function(){ FUNCS.executeUpload(this); },
					cancel: function(){ $(this).empty(); $(this).dialog('close'); }
				},
				width: 360,
				height: 400
			},
			download: {
				title:LANGS.dialog_title_download,
				buttons: {
					ok: function(){ FUNCS.executeDownload(this); $(this).dialog('close'); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			fileproperty: {
				title:LANGS.dialog_title_fileproperty,
				buttons: {
					ok: function(){ FUNCS.executeRenameFile(this); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			dirproperty: {
				title:LANGS.dialog_title_dirproperty,
				buttons: {
					ok: function(){ FUNCS.executeRenameDir(this); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			setperm: {
				title:LANGS.dialog_title_setperm,
				buttons: {
					ok: function(){ FUNCS.executeSetPerm(this); },
					cancel: function(){ $(this).dialog('close'); }
				},
				width: 550,
				height: 400
			},
			makedir: {
				title:LANGS.dialog_title_makedir,
				buttons: {
					ok: function(){ FUNCS.executeMakeDir(this); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			deletefile: {
				title:LANGS.dialog_title_deletefile,
				buttons: {
					ok: function(){ FUNCS.executeDeleteFile(this); $(this).dialog('close'); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			deletedir: {
				title: LANGS.dialog_title_deletedir,
				buttons: {
					ok: function(){ FUNCS.executeDeleteDir(this); $(this).dialog('close'); },
					cancel: function(){ $(this).dialog('close'); }
				}
			},
			about: {
				title:LANGS.dialog_title_about,
				buttons: {
					ok: function(){ $(this).dialog('close'); }
				}
			}
		};

//		if($('body').fileUpload){
			// JavaScript Ok. Flash Ok.
			$('#fcabix-jsui').show();
			<{if !$fcabix.config.dev_dualui}>$('#fcabix-nojsui').hide();<{/if}>
//		}else{
			// Javascript is enabled. But Flash plugin is disabled.
//			$("#fcabix-nojsui-errors-flash").show();
//			return;
//		}
		
		// Flexigrid init.
		$.ajaxSetup({async: false});
		fcabix.flexigrid.ui = $("#fcabix-flexigrid").flexigrid(fcabix.flexigrid.options);

		
		// Treeview init.
		var flexigridOuter = $('#fcabix-jsui .flexigrid'),
			treeview = $('#fcabix-treeview'),
			explorer = $('#fcabix-jsui-explorer'),
			treeviewWidth = <{$fcabix.config.treeview_width|default:25}> - 0,
			flexigridOuterWidth = 100 - treeviewWidth - 1;
		
		FUNCS.refreshFolderTree();
		treeview.show();
		treeview.css('height', flexigridOuter.height()-2+'px');
		
		// Flexigrid and Treeview
		treeview.width(treeviewWidth+'%');
		flexigridOuter.width(flexigridOuterWidth+'%');
		treeview.resizable({
			handles: 'e',
			ghost: true,
			helper: 'ui-state-highlight',
			minWidth: 100,
			maxWidth: 300,
//			resize: function(e, u){
//				flexigridOuter.width(Math.round(explorer.width() - treeview.width() - 10)+'px');
//			},
			stop: function(){
				var tw = treeview.width(),
					ew = explorer.width();
				
				treeview.width(Math.round(tw / ew * 100) + '%');
				flexigridOuter.width((100 - Math.round(tw / ew * 100) - 1) + '%');
			}
		});

		// jQuery UI init.
		var dialogWrapper = $('#fcabix-dialogs');
		for(var n in DIALOGS){
			var options = {
				title: DIALOGS[n].title,
				dialogClass: 'fcabix-dialog fcabix-dialog-' + n,
				modal: true,
				autoOpen: false,
				buttons: {},
				closeOnEscape: false,
				close: function(e, u){ $(this).empty(); }
			}
			options = $.extend(options, DIALOGS[n]);
			for(var i in DIALOGS[n].buttons){
				if(LANGS['dialog_button_'+i]){
					options.buttons[LANGS['dialog_button_'+i]] = DIALOGS[n].buttons[i];
					delete options.buttons[i];
				}
			}
			DIALOGS[n] = $('<div id="fcabix-dialog-' + n + '-content" class="fcabix-dialog-content"></div>').appendTo('body').dialog(options);
		}

		// Uploadify init.
		
		var upLimit = $('<table>'+
			'<tr><th>'+ LANGS.dialog_upload_ext +'</th><td><{$fcabix.config.upload_ext}></td></tr>'+
			'<tr><th>'+ LANGS.dialog_upload_maxsize +'</th><td><{$fcabix.config.upload_maxsize_withunit}></td></tr>'+
			'<tr><th>'+ LANGS.dialog_upload_maxnum +'</th><td><{$fcabix.config.upload_maxnum|default:10}></td></tr>'+
		'</table>');

		// Buttons init.

		var toolbar = $('#fcabix-jsui-buttons'),
			buttons = [
				{label:LANGS.command_download, onpress: FUNCS.download, bclass: 'fcabix-button-download'},
				{label:LANGS.command_upload, onpress: FUNCS.upload, bclass: 'fcabix-button-upload'},
				{}, // separator
				{label:LANGS.command_fileproperty, onpress: FUNCS.fileproperty, bclass: 'fcabix-button-fileproperty'},
				{label:LANGS.command_delete, onpress: FUNCS.deletefile, bclass: 'fcabix-button-deletefile'},
				{}, // separator
				{label:LANGS.command_makedir, onpress: FUNCS.makedir, bclass: 'fcabix-button-makedir'},
		//	{label:LANGS.command_deletedir, onpress: FUNCS.deletedir, bclass: 'fcabix-button-deletedir'},
				{label:LANGS.command_dirproperty, onpress: FUNCS.dirproperty, bclass: 'fcabix-button-dirproperty'}
			]

		for(var i in buttons){
			if(buttons[i].label && buttons[i].onpress && buttons[i].bclass){
				$('<div class="fcabix-button '+buttons[i].bclass+'"><span class="fcabix-button-label">'+buttons[i].label+'</span></div>').appendTo(toolbar).click(buttons[i].onpress);
			}else{
				$('<div class="fcabix-button-separator"></div>').appendTo(toolbar);
			}
		}
		
		var bDiv = $('#fcabix-jsui-explorer .bDiv').css('width', 'auto');
		
		$('#fcabix-jsui-explorer').resizable({
			handles: 's',
			alsoResize: '#fcabix-treeview, #fcabix-jsui-explorer .bDiv',
			minHeight: '200',
			resize: $.browser.opera 
						? function(){ var h = bDiv.height(); bDiv.removeAttr('style').height(h); }
						: undefined
		});

		$('#fcabix-jsui-path').before($('<div id="fcabix-jsui-about">About fcabix</div>').click(function(){ DIALOGS.about.dialog('open'); }));
		
		// for IE
		
		jQuery.ifixpng("js/ifixpng/blank.gif");
		jQuery(".fcabix-button, .pGroup div").ifixpng();
		
		// About
		
		DIALOGS.about
			.append('<p id="fcabix-about-logowm">FcabixShare Ver1.0</p>')
			.append('<p id="fcabix-about-copy">Copyright(c) Bluemoon inc. 2009<br />( <a href="http://www.bluemooninc.biz/" target="_blank">www.bluemooninc.biz</a> )</p>')
			.append('<p class="fcabix-about-h">Author</p>')
			.append('<p>Director and PHP programing by Yoshi Sakai<br />Designer and Javascript programing by makinosuke</p>')
			.append('<p class="fcabix-about-h">Credits</p>')
			.append('<p>Based module as DocManager V1.1 by Jeff Saucier ( <a href="http://www.infostrategique.com/" target="_brank">www.infostrategique.com</a> )<br />'
					+'Icon design by Tango Desktop Project ( <a href="http://tango.freedesktop.org/" target="_brank">tango.freedesktop.org</a> )<br />'
					+'jQuery and plugins ( uplodify, flexigrid, treeview, ifixpng, ui )</p>')
			.append('<p class="fcabix-about-h">License</p>')
			.append('<p>GPL Ver2.0</p>');
		
	}
);

})(jQuery);
