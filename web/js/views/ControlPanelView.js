/**
 * Вид для контрольной панели
 */
var ControlPanelView = Backbone.View.extend({
	events: {
		'click .cp-delete': 'delete',
		'click .cp-edit': 'edit',
		'click .cp-copy': 'copy',
		'click .cp-cut': 'cut',
		'click .cp-paste': 'paste',
		'click .cp-search': 'search',
		'keypress .js-search' : 'keypress'
	},

	delete: function() { 
		$('body').trigger('cp-delete'); 
		return false; 
	},

	edit: function() { 
		$('body').trigger('cp-edit'); 
		return false; 
	},

	copy: function() {
		$('body').trigger('cp-copy'); 
		return false;
	},

	cut: function() {
		$('body').trigger('cp-cut'); 
		return false;
	},

	paste: function() {
		$('body').trigger('cp-paste'); 
		return false;
	},

	search: function() {
		var string = $(this.el).find('.js-search').val();
		if(string.length > 1) $('body').trigger('cp-search', [string]);
		return false;
	},

	keypress: function(e) {
		if(e.which == 13) this.search();
	},

	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#cpanel').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {model: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}

		this.model.bind('change', function() {
			$(".js-control-panel").html(self.render().el);
		});
	}
});