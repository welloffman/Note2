var ROOT = location.href.match(/app_dev\.php/) ? "/app_dev.php/notes/" : "/notes/";

$(function() {
	var data = $(j).data('jdata');
	app = new NoteRouter(data);
	Backbone.history.start({pushState: true, root: ROOT});

	// Перехват ссылок - роутов бекбона для предотвращения перезагрузки всей страницы
	$("body").on('click', '.js-route', function(e) {
		e.preventDefault();
      	app.navigate( $(this).attr('href'), true );
	});

	$("body").on('route', function(e) {
		app.navigate( e.route, e.replace );
	});

	$('body').on('cp-delete', function(){
		if(!app.nav_list.getFirstSelected()) return false;

		var popup = new PopupView({
			model: new Popup({
				title: 'Удаление раздела или записи', 
				content: 'Восстановить удаляемый контент будет невозможно. Вы подтверждаете удаление?',
				ok_callback: function() { app.nav_list.sync('delete'); }
			}),
			className: 'popup'
		});
		$("body").append(popup.render().el);
	});

	$('body').on('cp-edit', function(){
		var path;
		var cur_item = app.nav_list.getFirstSelected();
		if(cur_item) {
			path = 'edit/' + cur_item.get('type') + '/' + cur_item.get('entity').get('id');
		} else if(app.note) {
			path = 'edit/note/' + app.note.get('id');
		}

		if(path) app.navigate( path, true );
	});

	/**
	 * Обновление страницы, в событие должно быть передано в параметре item объект NavListItem
	 */
	$('body').on('refresh', function(e) {
		if(!e.item) return false;
		app.openDir(e.item.get('entity').get('pid'), function() {
			if(e.item.get('type') == 'note') app.openNote(e.item.get('entity').get('pid'), e.item.get('entity').get('id'));
		});

		var hash = "#dir/" + e.item.get('entity').get('pid');
		if(e.item.get('type') == 'note') hash += "/note/" + e.item.get('entity').get('id');
		app.navigate(hash, false);
		$("html,body").animate({scrollTop: 0}, 0);
	});

	// Инициализация текстового редактора
	tinyMCE.init({
		mode: "none",
		theme: "advanced",
		theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,|,bullist,numlist,|,undo,redo,|,forecolor,backcolor",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
		height: "600",
		width: '100%',
		theme_advanced_font_sizes: '14pt,18pt,24pt,32pt,40pt,60pt',
		font_size_style_values: '14pt,18pt,24pt,32pt,40pt,60pt',
		content_css: '/../css/tiny_custom.css'
	});

	function getCurDirId() {
		return app.nav_list_view.options.cur_dir_id;
	}
});