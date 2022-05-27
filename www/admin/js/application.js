// Language
var $frmLang = $('#frmLang'),
    langcode = $frmLang.find('select').val();
$frmLang.find('select').change(function(){
  $frmLang[0].submit();
});

// Loading bar
var text = 'Loading data...';
if(langcode=='vn') text = 'Đang tải dữ liệu...';

var loading = '<p style="color:#f00;padding:10px">'+
	'<i class="icon-spinner icon-spin icon-large"></i> <strong>'+text+'</strong>'+
  '</p>';

function Preloader(vid) {
  var view = $('#'+vid);
  if(view.html()=='') {
  	var height = $('#sidebar').height();
  	view.html('<div class="panel panel-default" style="height:'+height+'px"></div>');
  }

  ShowMask(vid);
}

function BuildMask(vid) {
	var $holder = $('#'+vid),
		mask = $holder.data('mask');

	if(!mask){
		var mask = $('<div>'+loading+'</div>');
		mask
		  .css({
        'display': 'none',
        'position': 'absolute',
        'background': '#fafafa',
        'border-radius': '4px',
        'top': 0,
        'right': '15px',
        'bottom': 0,
        'left': '15px',
        'z-index': 10
		  })
		  .find('p').click(function(){
			  return HideMask(vid);
		  });
		
		$holder
		  .css('position', 'relative')
		  .append(mask)
		  .data('mask',mask);
	}
}

function ShowMask(vid){
	BuildMask(vid);

	var $holder = $('#'+vid),
		mask = $holder.data('mask');
	if(mask) {
		mask.fadeIn(1000);	
		mask.fadeTo("slow",0.7);
	}
	return false;
}

function HideMask(vid){
	var $holder = $('#'+vid),
		mask = $holder.data('mask');

	if(mask) mask.hide();	
	return false;
}


function AddRow(id){
	var tbl = $(document.getElementById(id)),
		tr = tbl.find('tbody tr');
	
	tr.each(function(){
		if($(this).is(':hidden')){
			$(this).show();
			return false;
		}
	});
	return false;
}

function RemoveImage(el){
	var pvr = $(el).parent();
	if(pvr.hasClass('preview')){
		var inp = pvr.parent().find('input');
		inp.val('');
		pvr.html('');
	}
	else if(pvr.parent().hasClass('preview')){
		var inp = pvr.find('input');
		if(inp.length>0) pvr.remove();
	}
	return false;
}

function CheckAll(name, check){
	var list = $('.checkbox input[name^='+name+']');
	list.prop('checked',check);
	return false;
}

function FileBrowser(callback, value, meta) {
	tinymce.activeEditor.windowManager.open({
		file: '/admin/ext/tinyupload/index.html',
		title: 'Tiny Uploadr',
		width: 500,
		height: 120,
		resizable: 'yes'
	},
	{
		oninsert: function (url, info) {
			if (meta.filetype == 'image') callback(url, {alt: info});
			if (meta.filetype == 'file') callback(url, {text: info, title: info});
			if (meta.filetype == 'media') callback(url);
		}
	});

	return false;
}


function BuildForm(id, cb){
	var form = $('#'+id);
	
	var age = form.find('.age'),
		birthday = form.find('.birthday');
	if(age.length>0 && birthday.length>0) {
		age.change(function(){
			var td = new Date(),
				yr = td.getFullYear() - age.val();
			birthday.val(yr + '-01-01');
		});
		birthday.change(function(){
			var td = new Date(),
				bd = new Date(birthday.val());
			age.val(td.getFullYear() - bd.getFullYear());
		});
	}
	if(birthday.length>0) {
		birthday.datepicker({
			dateFormat: "yy-mm-dd",
			onSelect: function (dateText, inst) {
				inst.input.trigger('change');
			}
		});
	}
	
	var date = form.find('.date'),
		time = form.find('.time'),
		datetime = form.find('.datetime');
	if(date.length>0) date.datepicker({dateFormat: "yy-mm-dd"});
	if(time.length>0) time.timepicker({
		timeFormat: "HH:mm:ss",
		stepHour: 1,
		stepMinute: 5,
		stepSecond: 10
	});
	if(datetime.length>0) datetime.datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm:ss",
		stepHour: 1,
		stepMinute: 5,
		stepSecond: 10
	});
	
	var editor = $('#'+id+' .tinymce');
	if(editor.length>0){
		tinymce.init({
			selector: '#'+id+' .tinymce',
			language: langcode=='vn'?'vi_VN':'en_US',
			theme: "modern",
			width: '90%',
			height: 400,
			
			plugins: [
				'autosave advlist autolink lists link image charmap preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars codemirror fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons htmlwidget paste textcolor colorpicker textpattern imagetools'
			],
			toolbar: [
				"undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect removeformat",
				"fontselect fontsizeselect | forecolor backcolor | htmlwidget table | link unlink | image media | preview code"
			],

			widget_list_url: '/process/wglist.php',
			widget_popup_width: 900,
			widget_popup_height: 500,
			
			codemirror: {
		    	indentOnInit: true,
		    	path: 'CodeMirror'
		    },

			content_style: "body{margin:10px}",
			fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 20px 22px 24px",
			table_default_styles: {
				borderCollapse: 'collapse'
			},
			table_class_list: [
				{title: 'Mặc định', value: 'table table-hover highlight table-bordered bordered'},
				{title: 'Có viền', value: 'table table-hover highlight table-bordered bordered'},
				{title: 'Viền ngang', value: 'table table-hover highlight'},
				{title: 'Ngựa vằn', value: 'table table-hover highlight table-striped striped'}
			],
			table_default_attributes: {
			  class: 'table table-hover highlight table-bordered bordered'
			},

		    images_upload_url: '/process/uploadx.php',
		    images_upload_base_path: '',
		    images_upload_credentials: true,

			file_picker_types: 'image',
			file_picker_callback: FileBrowser,

			image_advtab: true,
			convert_urls: true,
			relative_urls: false,
			remove_script_host: true,
      verify_html: false,
      entity_encoding : 'raw'
		});
	}
	
	form.find('.plupload').each(function(){
		var $upload = $(this),
			$pick = $upload.find('a'),
			$status = $upload.find('.status'),
			$preview = $upload.find('.preview');
		
		var uploader = new plupload.Uploader({
			runtimes : 'html5,gears,flash,silverlight',
			browse_button : $pick.attr('id'),
			container: $upload.attr('id'),
			url : '/process/upload.php',
			max_file_size : '32mb',
			chunk_size: '1mb',
			unique_names: false,
			multi_selection: false,
			
			// Specify what files to browse for
			filters: [
				{title: 'Image files', extensions: 'jpg,jpeg,png,gif,svg,psd,ai,cdr,swf'},
				{title: 'Doc files', extensions: 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt'},
				{title: 'Zip files', extensions: 'zip,rar,gz,tar'}
			],

			// Authentication
			multipart_params: {
				"fwjwt": Cookies.get('fwjwt')
			},

			// Flash/Silverlight paths
			flash_swf_url : '/admin/ext/plupload/plupload.flash.swf',
			silverlight_xap_url : '/admin/ext/plupload/plupload.silverlight.xap'
		});
		
		uploader.bind('Init', function(up, params) {
			$status.html('Current runtime: '+params.runtime);
		});
		
		uploader.bind('FilesAdded', function(up, files) {
			$.each(files, function(i, file) {
				$preview.attr('id', file.id);
				$preview.html('Uploading ... <b></b>');
			});
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		uploader.bind('QueueChanged', function(up) {
			up.start(); // Auto start upload
		});
		
		uploader.bind('UploadProgress', function(up, file) {
			$('#'+file.id+' b').html(file.percent+'%');
		});
		
		uploader.bind('Error', function(up, err) {
			$preview.append('Error: '+err.code+', Message: '+err.message+'</div>');
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		uploader.bind('FileUploaded', function(up, file, info) {
			var res = $.parseJSON(info.response),
			value = res.result;
			$upload.find('input[type="hidden"]').val(value);
			
			var fileType = $upload.attr('data-type');
			if(fileType=='image') display = image_display(value,50);
			else display = file_display(value);
			$preview.html(display);
		});
		
		$upload.data('upload',uploader);
		uploader.init();
	});
	
	form.find('.mupload').each(function(){
		var $mupload = $(this),
			$pick = $mupload.find('a'),
			$status = $mupload.find('.status'),
			$preview = $mupload.find('.preview');
		
		// Sortable image
		var $sort = Sortable.create($preview[0], {
			animation: 150,
			handle: ".handler",
			ghostClass: "ghost",
			chosenClass: "chosen",
			dragClass: "drag"
		});

		// Multi Uploadr
		var muploader = new plupload.Uploader({
			runtimes : 'html5,gears,flash,silverlight',
			browse_button : $pick.attr('id'),
			container: $mupload.attr('id'),
			url : '/process/upload.php',
			max_file_size : '32mb',
			chunk_size: '1mb',
			unique_names: false,
			multi_selection: true,
			
			// Specify what files to browse for
			filters: [
				{title: 'Image files', extensions: 'jpg,jpeg,png,gif,svg,psd,ai,cdr,swf'},
				{title: 'Doc files', extensions: 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt'},
				{title: 'Zip files', extensions: 'zip,rar,gz,tar'}
			],

			// Authentication
			multipart_params: {
				"fwjwt": Cookies.get('fwjwt')
			},

			// Flash/Silverlight paths
			flash_swf_url : '/admin/ext/plupload/plupload.flash.swf',
			silverlight_xap_url : '/admin/ext/plupload/plupload.silverlight.xap'
		});
		
		muploader.bind('Init', function(up, params) {
			$status.html('Current runtime: '+params.runtime);
		});
		
		muploader.bind('FilesAdded', function(up, files) {
			$.each(files, function(i, file) {
				$preview.append('<div id="'+file.id+'">Uploading ... <b></b></div>');
			});
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		muploader.bind('QueueChanged', function(up) {
			up.start(); // Auto start upload
		});
		
		muploader.bind('UploadProgress', function(up, file) {
			$('#'+file.id+' b').html(file.percent+'%');
		});
		
		muploader.bind('Error', function(up, err) {
			var mes = 'Error: '+err.code+', Message: '+err.message
			if(err.file) $('#'+err.file.id+' b').html(mes);
			else alert(mes);
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		muploader.bind('FileUploaded', function(up, file, info) {
			var name = $mupload.attr('data-name'),
				type = $mupload.attr('data-type'),
				res = $.parseJSON(info.response),
				value = res.result;
			
			if(type=='image') display = image_display(value,80);
			else display = file_display(value);
			
			$('#'+file.id)
			  .html(display)
			  .append('<input type="hidden" name="'+name+'[]" value="'+value+'">');
		});
		
		$mupload.data('mupload',muploader);
		muploader.init();
	});
	
	if(cb!=null) cb(id);
}

function SubmitForm(id, valid, vid){
	tinymce.triggerSave();
	if(valid!=null && !valid(id)){
		return false;
	}
	
	var form = $('#'+id);
	tinymce.remove('#'+id+' .tinymce');
	
	form.find('.plupload').each(function(){
		var $upload = $(this),
			uploader = $upload.data('upload')
		
		if(typeof(uploader)!='undefined'){
			uploader.destroy();
			$upload.removeData('upload');
		}
	});
	
	form.find('.mupload').each(function(){
		var $mupload = $(this),
			muploader = $mupload.data('mupload')
		
		if(typeof(muploader)!='undefined'){
			muploader.destroy();
			$mupload.removeData('mupload');
		}
	});
	
	var param = form.serialize();
	Router(form.attr('action'), param, vid);
}

function Router(url, post, id, cb){
	if(typeof(post)=='undefined') post = {};
	if(typeof(id)=='undefined') id = 'content';
	
	// Loading page
	Preloader(id);

	// Call Ajax
	$.ajax({
		url: url,
		type: 'post',
		data: post,
		dataType: "html",
	})
	.done(function(msg) {
		$('#'+id).html(msg);
		if(cb!=null) cb();
	})
	.fail(function(jqXHR, status) {
		$('#'+id).html("Request failed: "+status);
	})
	.always(function(jqXHR, status) {
		HideMask(id);
  	});
	
	return false;
}

function Load(module, param, id, cb){
	if(typeof(id)!='undefined') {
		return Router(module, param, id, cb);		
	}

	// Parser URL
	var a = $('<a>', {href:module})[0],
		url = a.pathname,
		get = a.search;
	if(get!='') get = '&'+get.substr(1);

	var post = '';
	if(typeof(param)!='undefined') {
		post = '&'+$.param({'p':param})	
	}
	console.log('URL: '+url+', GET: '+get+', POST: '+post);

	var link = '?u='+url + get + post;
	console.log('Link: '+link);
	window.location = link;

	return false;
}

function Refresh(){
	location.reload();
}

function Delete(url, data, id){
	if($.isNumeric(data)) param = {'id':data};
	else param = data;
	
	if(confirm('Are you sure to delete?')) Load(url,param,id);
	return false;
}


function Fancybox(img){
	$.fancybox.open(img);
	return false;
}

function file_display(file,rm){
	if(file==null || file=='') return '';
	if(rm==null) rm = true;

	var view = '<a href="'+file+'" target="_blank">Download</a>';
	if(rm) {
    var text = langcode=='vn'?'Xóa':'Delete';
		view += '<div class="removef" onclick="RemoveImage(this)"><i class="icon-remove"></i> '+text+'</div>';
		view += '<div class="handler"><i class="icon-move"></i></div>';
	}
	return view;
}

function image_display(src, size, rm) {
	if (src == null || src == '') src = '/files/qsvpro.jpg';
	if (size == null) size = 50;
	if (rm == null) rm = true;

  	var image = '<img src="' + src + '" width="' + size + '" height="' + size + '" alt="" />',
	  view = '<a href="#display" onclick="return Fancybox(\'' + src + '\')">' + image + '</a>';
	if(rm) {
		view += '<div class="removei" onclick="RemoveImage(this)"><i class="icon-remove"></i></div>';
		view += '<div class="handler"><i class="icon-move"></i></div>';
	}
	return view;
}

function number_format (number, decimals, dec_point, thousands_sep) {
	// Strip all characters but numerical ones.
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);
			return '' + Math.round(n * k) / k;
		};
	
  	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	
	return s.join(dec);
}


function Sidebar(type){
	$('#sidebar').show();
	if(type=='mini'){
		$('#sidebar').addClass('mini');
		$('#menu').width(47);
		$('#body').css('margin-left', 47);
		
		$('#sidebar-collapse i').removeClass('icon-double-angle-left');
		$('#sidebar-collapse i').addClass('icon-double-angle-right');
	}
	else{
		$('#sidebar').removeClass('mini');
		$('#menu').width(200);
		$('#body').css('margin-left', 200);
		
		$('#sidebar-collapse i').removeClass('icon-double-angle-right');
		$('#sidebar-collapse i').addClass('icon-double-angle-left');
	}
}

// Menu toggle: show or hide
$('#toggler').click(function(ev){
	ev.preventDefault();
	
	var sidebar = $('#sidebar');
	if(sidebar.is(':hidden')){
		var tog = $(this);
		sidebar.css('top',tog.offset().top+tog.innerHeight()+'px');
		sidebar.show();
	}
	else sidebar.hide();
});

var autohide = false;
$('#sidebar').mouseenter(function(){
	if(xs.matches){
		clearTimeout(autohide);
	}
});
$('#sidebar').mouseleave(function(){
	if(xs.matches){
		autohide = setTimeout(function(){
			$('#sidebar').hide();
		},500);
	}
});

$('#sidebar-collapse').click(function(ev){
	ev.preventDefault();
	if($('#sidebar').hasClass('mini')) Sidebar('full');
	else Sidebar('mini');
});


// Extra small devices (phones, up to 480px)
var xs = window.matchMedia("(max-width: 767px)");
if(xs.matches){
	Sidebar('full');
	$('#sidebar').hide();
	$('#body').css('margin-left', 0);
}
xs.addListener(function(m) {
	if(m.matches){
		Sidebar('full');
		$('#sidebar').hide();
		$('#body').css('margin-left', 0);
	}
});

// Small devices (tablets, 768px and up)
var sm = window.matchMedia("(min-width: 768px) and (max-width: 991px)");
if(sm.matches) Sidebar('mini');
sm.addListener(function(m){if(m.matches) Sidebar('mini')});

// Medium devices (desktops, 992px and up)
var md_lg = window.matchMedia("(min-width: 992px)");
if(md_lg.matches) Sidebar('full');
md_lg.addListener(function(m){if(m.matches) Sidebar('full')});
