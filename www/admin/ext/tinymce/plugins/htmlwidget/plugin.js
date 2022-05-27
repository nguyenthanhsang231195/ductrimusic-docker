tinymce.PluginManager.add('htmlwidget', function(editor, url) {
	// Open widget browser dialog
	function ViewWidget() {
      editor.windowManager.open({
        title: 'Danh sách Widget',
        url: url + '/dialog.html',
		width: editor.getParam('widget_popup_width', 700),
		height: editor.getParam('widget_popup_height', 400),
        
		buttons: [{
			text: 'Thêm widget',
			onclick: function() {
				var win = editor.windowManager.getWindows()[0],
					ifr = win.getContentWindow().document,
					widget = $(ifr).find('#content').html();

				if(widget!='') {
					// Insert the widget into the editor
					var html = widget + '<p>&nbsp;</p>';
					editor.insertContent(html);

					// Close the window
					win.close();
				}
				else {
					editor.windowManager.alert('Vui lòng chọn widget!');
				}
			}
		},
		{
			text: 'Close',
			onclick: 'close'
		}]
      }, {
		editor: editor,
		wglist: editor.getParam('widget_list_url')
	  });
    }

	// Load style to editor
	editor.on('init', function(){
		var wglist = editor.getParam('widget_list_url');
		$.get(wglist, function(data) {
			var style = '';
			$.each(data.style, function(i, url){
				editor.contentCSS.push(url);
				style += '<link href="' + url + '" rel="stylesheet">';
			});

			if(style!='') {
				var link = $(editor.getDoc()).find('head link:first');
				link.before(style);
			}
		}, "json");
	});

	// Add a toolbar button
	editor.addButton('htmlwidget', {
		icon: 'template',
		tooltip: 'Thêm widget',
		onclick: ViewWidget
	});

	// Adds a menu item
	editor.addMenuItem('htmlwidget', {
		icon: 'template',
		text: 'Thêm widget',
		context: 'insert',
		onclick: ViewWidget
	});
});
