$(function() {
	var data = $(j).data('jdata');
	var app = new AppRouter(data);
	Backbone.history.start();
});

var AppRouter = Backbone.Router.extend({
	nav_list: undefined,
	nav_list_view: undefined,
	note_view: undefined,
	note: undefined,

	routes: {
		"": "openDir",
		"dir/:dir_id": "openDir",
		"dir/:dir_id/note/:note_id": "openNote"
	},

	initialize: function(options) {
		var self = this;

		this.control_panel = new ControlPanelView();
		$(".js-control-panel").html( this.control_panel.render().el );

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
	},

	openNote: function(dir_id, note_id) {
		if(dir_id != this.nav_list_view.options.cur_dir_id) this.openDir(dir_id);

		this.note = new Note({
			id: note_id
		});

		this.note_view = new NoteView({model: this.note, className: 'note-content'});
		this.note.fetch();
	},
});