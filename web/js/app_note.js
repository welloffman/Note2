$(function() {
	var data = $(j).data('jdata');
	var app = new AppRouter(data);
	Backbone.history.start();

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
		content_css: '../css/tiny_custom.css'
	});

	function getCurDirId() {
		return app.nav_list_view.options.cur_dir_id;
	}
});

var AppRouter = Backbone.Router.extend({
	nav_list: undefined,
	nav_list_view: undefined,
	note_view: undefined,
	note: undefined,

	routes: {
		"": "openDir",
		"dir/:dir_id": "openDir",
		"dir/:dir_id/note/:note_id": "openNote",
		"add_note": "addNote",
		"dir/:dir_id/add_note": "addNote"
	},

	initialize: function(options) {
		var self = this;

		this.control_panel = new ControlPanel();
		this.control_panel_view = new ControlPanelView({model: this.control_panel});
		$(".js-control-panel").html( this.control_panel_view.render().el );

		this.breadcrumbs = new Breadcrumbs();
		this.breadcrumbs_view = new BreadcrumbsView({model: this.breadcrumbs});

		this.nav_list = new NavList([]);
		this.nav_list_view = new NavListView({collection: this.nav_list});

		this.nav_list.on('reset	', function(collection, options) {
			self.nav_list_view.options.cur_dir_id = options.dir_id;
			$(".js-nav-list").html( self.nav_list_view.render().el );
			self.nav_list_view.bindEvents();
			//if(self.note) self.note.updateModel({});
		});
	},

	openDir: function(dir_id) {
		if(!dir_id) dir_id = null;
		this.breadcrumbs.fetch({dir_id: dir_id});
		this.nav_list.fetch({dir_id: dir_id});
		this.control_panel.set('cur_dir_id', dir_id);
	},

	openNote: function(dir_id, note_id) {
		if(dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id);

		this.note = new Note({
			id: note_id
		});

		this.note_view = new NoteView({model: this.note, className: 'note-content'});
		this.note.fetch();
	},

	addNote: function(dir_id) {
		if(!dir_id || dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id);

		tinyMCE.execCommand('mceRemoveControl', false, "mce");

		var editor = new Editor({type: 'note', entity: new Note({parent_dir: dir_id})});
		var editor_view = new EditorView({model: editor, className: "editor"});
		$(".js-note").html( editor_view.render().el );

		tinyMCE.execCommand("mceAddControl", false, "mce");
		return false;
	}
});