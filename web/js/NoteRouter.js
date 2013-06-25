var NoteRouter = Backbone.Router.extend({
	control_panel: undefined,
	control_panel_view: undefined,
	breadcrumbs: undefined,
	breadcrumbs_view: undefined,
	nav_list: undefined,
	nav_list_view: undefined,
	note_view: undefined,
	note: undefined,

	routes: {
		"": "openDir",
		"dir/:dir_id": "openDir",
		"dir/:dir_id/note/:note_id": "openNote",
		"add_dir": "addDir",
		"dir/:dir_id/add_dir": "addDir",
		"add_note": "addNote",
		"dir/:dir_id/add_note": "addNote",
		"edit/dir/:dir_id" : "editDir",
		"edit/note/:note_id" : "editNote"
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
		});
	},

	openDir: function(dir_id, callback) {
		if(!dir_id) dir_id = null;
		this.breadcrumbs.fetch({dir_id: dir_id});
		this.nav_list.fetch({dir_id: dir_id, callback: callback});
		this.control_panel.set('cur_dir_id', dir_id);

		if(this.note_view) this.note_view.remove();
		if(this.note) this.note = undefined;
	},

	openNote: function(dir_id, note_id) {
		var self = this;
		function open() { 
			var item = self.nav_list.getByEntity('note', note_id);
			self.note = item.get('entity');
			self.note_view = new NoteView({model: self.note, className: 'note'});
			self.note.fetch();
		}

		if(dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id, open);
		else open();
	},

	addDir: function(dir_id) {
		if(!dir_id || dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id);

		var editor = new NavListItem({type: 'dir', entity: new Dir({pid: dir_id})});
		var editor_view = new EditorView({model: editor, className: "editor"});
		$(".js-note").html( editor_view.render().el );
		return false;
	},

	addNote: function(dir_id) {
		function open() {
			tinyMCE.execCommand('mceRemoveControl', false, "mce");

			var editor = new NavListItem({type: 'note', entity: new Note({pid: dir_id})});
			var editor_view = new EditorView({model: editor, className: "editor"});
			$(".js-note").html( editor_view.render().el );

			tinyMCE.execCommand("mceAddControl", false, "mce");
		}
		if(!dir_id || dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id, open);
		else open();
		return false;
	},

	editNote: function(note_id) {
		if(this.note_view) this.note_view.remove();
		if(this.note) this.note = undefined;

		var self = this;
		function open() {
			var note = self.nav_list.getByEntity('note', note_id);
			note.get('entity').fetch({callback: function() {
				tinyMCE.execCommand('mceRemoveControl', false, "mce");
				var editor_view = new EditorView({model: note, className: "editor"});
				$(".js-note").html( editor_view.render().el );
				tinyMCE.execCommand("mceAddControl", false, "mce");
			}});
		}

		// Если открываем редактирование по прямой ссылке - сначала получаем объект записи, 
		// потом по pid открываем родительский раздел и потом открываем редактор
		if(!this.nav_list_view.options.cur_dir_id) {
			var n = new Note({id: note_id});
			n.fetch({callback: function() {
				self.openDir(n.get('pid'), open);
			}});
		}
		else open();
	},

	editDir: function(dir_id) {
		if(this.note_view) this.note_view.remove();
		if(this.note) this.note = undefined;

		var self = this;
		function open() {
			var dir = self.nav_list.getByEntity('dir', dir_id);
			var editor_view = new EditorView({model: dir, className: "editor"});
			$(".js-note").html( editor_view.render().el );
		}

		// Если открываем редактирование по прямой ссылке - сначала получаем объект раздела, 
		// потом по pid открываем родительский раздел и потом открываем редактор
		if(!this.nav_list_view.options.cur_dir_id) {
			var d = new Dir({id: dir_id});
			d.fetch({callback: function() { 
				self.openDir(d.get('pid'), open); 
			}});
		}
		else open();
	}
});